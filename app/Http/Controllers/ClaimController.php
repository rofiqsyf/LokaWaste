<?php

namespace App\Http\Controllers;

use App\Models\Claim;
use App\Models\FoodListing;
use App\Models\Review;
use App\Services\ClaimService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClaimController extends Controller
{
    public function __construct(
        protected ClaimService $claimService
    ) {}

    public function store(Request $request, FoodListing $listing)
    {
        $validated = $request->validate([
            'portions' => ['required', 'integer', 'min:1'],
            'campaign_id' => ['nullable', 'exists:food_campaigns,id'],
        ]);

        $user = Auth::user();

        try {
            $claim = $this->claimService->create(
                $user,
                $listing,
                $validated['portions'],
                $validated['campaign_id'] ?? null
            );

            return redirect()->route('listings.show', $listing->id)
                ->with('success', 'Klaim berhasil diajukan! Tunggu persetujuan dari pemberi makanan.');
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function approve(Claim $claim, Request $request)
    {
        $listing = $claim->listing;
        abort_unless(Auth::id() === $listing->user_id, 403, 'Akses ditolak.');

        $portions = (int) $request->input('portions', $claim->portions_requested);

        try {
            $this->claimService->approve($claim, $portions);
            return back()->with('success', 'Klaim disetujui! Pengambil telah diberi tahu beserta kode ambil.');
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function reject(Claim $claim, Request $request)
    {
        $listing = $claim->listing;
        abort_unless(Auth::id() === $listing->user_id, 403, 'Akses ditolak.');

        try {
            $this->claimService->reject($claim, $request->input('reason'));
            return back()->with('info', 'Klaim telah ditolak.');
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function cancel(Claim $claim, Request $request)
    {
        $user = Auth::user();
        abort_unless($user->id === $claim->user_id || $user->id === $claim->listing->user_id, 403, 'Akses ditolak.');

        try {
            if ($claim->status === 'approved') {
                $this->claimService->cancelApproved($claim, $user, $request->input('reason'));
                return back()->with('info', 'Klaim disetujui dibatalkan. Porsi telah dikembalikan ke listing.');
            } else {
                $this->claimService->cancelPending($claim, $user, $request->input('reason'));
                return back()->with('info', 'Klaim berhasil dibatalkan.');
            }
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function verifyPickup(Claim $claim, Request $request)
    {
        $listing = $claim->listing;
        abort_unless(Auth::id() === $listing->user_id, 403, 'Hanya pemberi makanan yang dapat memverifikasi kode ambil.');

        $code = (string) $request->input('pickup_code');

        try {
            $success = $this->claimService->verifyPickup($claim, $code);

            if ($success) {
                return back()->with('success', 'Verifikasi berhasil! Makanan telah resmi diserahterimakan.');
            } else {
                $remaining = max(0, 5 - $claim->refresh()->pickup_attempts);
                return back()->with('error', "Kode ambil salah! Sisa {$remaining} percobaan sebelum terkunci.");
            }
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Smart pickup verification from QR code scan or manual code lookup
     */
    public function quickVerify(Request $request)
    {
        $user = Auth::user();
        $rawInput = trim((string) ($request->input('qr_payload') ?? $request->input('pickup_code') ?? ''));
        $claimId = $request->input('claim_id');

        // Parse LOKAWASTE:CLAIM:{id}:{code} or LOCABITE:CLAIM:{id}:{code} if scanned from smart QR code
        $code = $rawInput;
        if (preg_match('/(?:(?:LOCABITE|LOKAWASTE):CLAIM:)?(\d+):([A-Za-z0-9]+)/i', $rawInput, $matches)) {
            $claimId = (int) $matches[1];
            $code = strtoupper($matches[2]);
        } else {
            $code = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $rawInput));
        }

        if (empty($code)) {
            $msg = 'Kode atau format QR Code tidak valid.';
            return $request->wantsJson() ? response()->json(['success' => false, 'message' => $msg], 422) : back()->with('error', $msg);
        }

        // Find active approved claim belonging to user's listings
        $query = Claim::with(['listing', 'user'])
            ->whereHas('listing', fn($q) => $q->where('user_id', $user->id))
            ->where('status', 'approved');

        if ($claimId) {
            $query->where('id', $claimId);
        }

        $claim = $query->where('pickup_code', $code)->first();

        if (!$claim) {
            // Check if this claim was already picked up
            $alreadyPickedUp = Claim::whereHas('listing', fn($q) => $q->where('user_id', $user->id))
                ->where('pickup_code', $code)
                ->where('status', 'picked_up')
                ->first();

            if ($alreadyPickedUp) {
                $time = $alreadyPickedUp->picked_up_at ? $alreadyPickedUp->picked_up_at->format('d M Y H:i') : 'sebelumnya';
                $msg = "Kode ini sudah pernah diverifikasi pada {$time}.";
                return $request->wantsJson() ? response()->json(['success' => false, 'message' => $msg], 409) : back()->with('error', $msg);
            }

            $msg = 'Kode pengambilan tidak cocok dengan daftar makanan Anda yang sedang menunggu serah terima.';
            return $request->wantsJson() ? response()->json(['success' => false, 'message' => $msg], 404) : back()->with('error', $msg);
        }

        try {
            $success = $this->claimService->verifyPickup($claim, $code);

            if ($success) {
                $msg = "Verifikasi Berhasil! {$claim->portions_approved} porsi \"{$claim->listing->title}\" telah resmi diserahkan kepada {$claim->user->name}.";
                if ($request->wantsJson()) {
                    return response()->json([
                        'success' => true,
                        'message' => $msg,
                        'claim' => [
                            'id' => $claim->id,
                            'recipient' => $claim->user->name,
                            'listing_title' => $claim->listing->title,
                            'portions' => $claim->portions_approved,
                        ]
                    ]);
                }
                return back()->with('success', $msg);
            } else {
                $remaining = max(0, 5 - $claim->refresh()->pickup_attempts);
                $msg = "Kode ambil salah! Sisa {$remaining} percobaan sebelum terkunci.";
                return $request->wantsJson() ? response()->json(['success' => false, 'message' => $msg], 422) : back()->with('error', $msg);
            }
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            return $request->wantsJson() ? response()->json(['success' => false, 'message' => $e->getMessage()], 422) : back()->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            return $request->wantsJson() ? response()->json(['success' => false, 'message' => $e->getMessage()], 422) : back()->with('error', $e->getMessage());
        }
    }

    /**
     * API: Pickup details (reveals exact address and coordinates only to approved claimer or owner)
     * SDD 6.5, FR-37, TC-18
     */
    public function pickupDetails(Claim $claim)
    {
        $user = Auth::user();
        $listing = $claim->listing;

        $isOwner = $user && $user->id === $listing->user_id;
        $isApprovedClaimer = $user && $user->id === $claim->user_id && in_array($claim->status, ['approved', 'picked_up']);

        abort_unless($isOwner || $isApprovedClaimer, 403, 'Detail pengambilan hanya dapat dilihat oleh pengambil yang disetujui atau pemilik.');

        return response()->json([
            'listing_id' => $listing->id,
            'title' => $listing->title,
            'pickup_address' => $listing->pickup_address,
            'exact_latitude' => $listing->latitude,
            'exact_longitude' => $listing->longitude,
            'pickup_code' => $claim->pickup_code,
            'contact_phone' => $listing->user?->phone,
            'provider_name' => $listing->user?->name,
        ]);
    }

    public function review(Claim $claim, Request $request)
    {
        abort_unless(Auth::id() === $claim->user_id, 403, 'Akses ditolak.');
        abort_unless($claim->status === 'picked_up', 422, 'Ulasan hanya dapat diberikan setelah makanan diambil.');

        $validated = $request->validate([
            'rating' => ['required', 'integer', 'between:1,5'],
            'comment' => ['nullable', 'string', 'max:500'],
        ]);

        Review::updateOrCreate(
            ['claim_id' => $claim->id],
            [
                'rating' => $validated['rating'],
                'comment' => $validated['comment'] ?? null,
            ]
        );

        return back()->with('success', 'Terima kasih atas ulasan Anda!');
    }
}
