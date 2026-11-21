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
