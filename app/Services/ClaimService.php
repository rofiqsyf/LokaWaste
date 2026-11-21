<?php

namespace App\Services;

use App\Enums\ClaimStatus;
use App\Enums\ListingStatus;
use App\Models\Claim;
use App\Models\FoodCampaign;
use App\Models\FoodListing;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;

class ClaimService
{
    /**
     * Create a new food claim.
     * SDD 8.4: Lock order is ALWAYS: User, then Listing, to prevent deadlocks.
     */
    public function create(User $user, FoodListing $listing, int $portions, ?int $campaignId = null): Claim
    {
        return DB::transaction(function () use ($user, $listing, $portions, $campaignId) {
            // Serialize and lock user to enforce max 3 active claims limit safely (TC-10)
            User::whereKey($user->id)->lockForUpdate()->first();
            $listing = FoodListing::with('district.regency')->lockForUpdate()->findOrFail($listing->id);

            abort_if($listing->user_id === $user->id, 403, 'Ini listing Anda.');
            abort_unless($listing->isClaimableBy($user), 422, 'Listing tidak dapat diklaim saat ini.');
            abort_if($portions < 1 || $portions > min($listing->max_portions_per_claim, $listing->portions_left), 422, 'Jumlah porsi melebihi batas.');
            abort_if(Claim::active()->where('user_id', $user->id)->count() >= 3, 422, 'Maksimal 3 klaim aktif.');

            if ($campaignId) {
                $campaign = FoodCampaign::with('district.regency')->findOrFail($campaignId);
                abort_unless($campaign->status === 'published', 422, 'Galang makanan belum aktif atau telah ditutup.');
                abort_unless($user->isVolunteerIn($campaign->district_id), 403, 'Anda bukan relawan terdaftar di kecamatan galang ini.');
                abort_unless(
                    $listing->district->regency_id === $campaign->district->regency_id,
                    422,
                    'Listing dan galang harus berada dalam satu kabupaten/kota.'
                );
                if ($listing->campaign_priority_until && $listing->campaign_priority_until->isFuture()) {
                    abort_if($listing->campaign_id !== $campaign->id, 403, 'Listing ini diprioritaskan untuk galang lain.');
                }
            }

            return Claim::create([
                'listing_id' => $listing->id,
                'user_id' => $user->id,
                'campaign_id' => $campaignId,
                'portions_requested' => $portions,
                'status' => ClaimStatus::PENDING->value,
                'pickup_code' => PickupCode::generate($listing->id),
            ]);
        });
    }

    /**
     * Owner approves claim (partial approval allowed).
     */
    public function approve(Claim $claim, int $portions): void
    {
        DB::transaction(function () use ($claim, $portions) {
            $listing = FoodListing::lockForUpdate()->findOrFail($claim->listing_id);
            $claim = Claim::lockForUpdate()->findOrFail($claim->id);

            abort_unless($claim->status === ClaimStatus::PENDING->value || $claim->status === 'pending', 409, 'Klaim sudah diproses.');
            abort_if($listing->expires_at->isPast(), 422, 'Listing sudah kedaluwarsa.');

            $portions = min($portions, $claim->portions_requested);
            abort_if($portions < 1 || $listing->portions_left < $portions, 422, 'Porsi tidak mencukupi.');

            $listing->decrement('portions_left', $portions);
            $claim->update([
                'status' => ClaimStatus::APPROVED->value,
                'portions_approved' => $portions,
                'approved_at' => now(),
            ]);
            $this->syncListingStatus($listing->refresh());
        });
    }

    /**
     * Reject pending claim.
     */
    public function reject(Claim $claim, ?string $reason = null): void
    {
        DB::transaction(function () use ($claim, $reason) {
            $claim = Claim::lockForUpdate()->findOrFail($claim->id);
            abort_unless($claim->status === ClaimStatus::PENDING->value || $claim->status === 'pending', 409, 'Klaim sudah diproses.');

            $claim->update([
                'status' => ClaimStatus::REJECTED->value,
                'cancel_reason' => $reason,
            ]);
        });
    }

    /**
     * Cancel an approved claim: RESTORES PORTIONS to listing!
     * SDD 8.4: Restores portions and recalculates listing status.
     */
    public function cancelApproved(Claim $claim, User $by, ?string $reason = null): void
    {
        DB::transaction(function () use ($claim, $by, $reason) {
            $listing = FoodListing::lockForUpdate()->findOrFail($claim->listing_id);
            $claim = Claim::lockForUpdate()->findOrFail($claim->id);

            abort_unless($claim->status === ClaimStatus::APPROVED->value || $claim->status === 'approved', 409, 'Klaim tidak dalam status disetujui.');

            $listing->increment('portions_left', $claim->portions_approved ?? 0);
            $claim->update([
                'status' => ClaimStatus::CANCELLED->value,
                'cancelled_by' => $by->id,
                'cancel_reason' => $reason,
            ]);
            $this->syncListingStatus($listing->refresh());
        });
    }

    /**
     * Cancel pending claim (no portions restored since none were deducted yet).
     */
    public function cancelPending(Claim $claim, User $by, ?string $reason = null): void
    {
        DB::transaction(function () use ($claim, $by, $reason) {
            $claim = Claim::lockForUpdate()->findOrFail($claim->id);
            abort_unless($claim->status === ClaimStatus::PENDING->value || $claim->status === 'pending', 409, 'Klaim sudah diproses.');

            $claim->update([
                'status' => ClaimStatus::CANCELLED->value,
                'cancelled_by' => $by->id,
                'cancel_reason' => $reason,
            ]);
        });
    }

    /**
     * Synchronize listing status based on portions and expires_at.
     */
    public function syncListingStatus(FoodListing $l): void
    {
        if (in_array($l->status, [ListingStatus::CANCELLED->value, ListingStatus::DRAFT->value, 'cancelled', 'draft'])) {
            return;
        }

        if ($l->expires_at->isPast()) {
            $l->status = ListingStatus::EXPIRED->value;
        } elseif ($l->portions_left > 0) {
            $l->status = ListingStatus::AVAILABLE->value;
        } else {
            $hasOpenApproved = $l->claims()->where('status', ClaimStatus::APPROVED->value)->exists();
            $l->status = $hasOpenApproved ? ListingStatus::RESERVED->value : ListingStatus::COMPLETED->value;
        }
        $l->save();
    }

    /**
     * Verify pickup code with 5-attempt rate limiter and 15-minute lock.
     * SDD 8.4: Verification is rate limited to 5 attempts.
     */
    public function verifyPickup(Claim $claim, string $code): bool
    {
        $key = "pickup:{$claim->id}";
        abort_if(RateLimiter::tooManyAttempts($key, 5), 429, 'Terlalu banyak percobaan. Coba lagi dalam 15 menit.');

        if (!hash_equals($claim->pickup_code, strtoupper(trim($code)))) {
            RateLimiter::hit($key, 900); // 15 minutes lockout
            $claim->increment('pickup_attempts');
            return false;
        }

        DB::transaction(function () use ($claim) {
            $listing = FoodListing::lockForUpdate()->findOrFail($claim->listing_id);
            $claim = Claim::lockForUpdate()->findOrFail($claim->id);
            abort_unless($claim->status === ClaimStatus::APPROVED->value || $claim->status === 'approved', 409, 'Klaim belum disetujui.');

            $claim->update([
                'status' => ClaimStatus::PICKED_UP->value,
                'picked_up_at' => now(),
            ]);

            if ($claim->campaign_id) {
                FoodCampaign::whereKey($claim->campaign_id)->increment('collected_portions', $claim->portions_approved ?? 0);
            }

            $this->syncListingStatus($listing->refresh());
        });

        RateLimiter::clear($key);
        return true;
    }
}
