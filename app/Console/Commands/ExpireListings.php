<?php

namespace App\Console\Commands;

use App\Enums\ClaimStatus;
use App\Enums\ListingStatus;
use App\Models\Claim;
use App\Models\FoodListing;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ExpireListings extends Command
{
    protected $signature = 'listings:expire';
    protected $description = 'Expire past listings and update claims to expired/no_show idempotently';

    public function handle(): int
    {
        $expiredListings = FoodListing::whereIn('status', [ListingStatus::AVAILABLE->value, ListingStatus::RESERVED->value])
            ->where('expires_at', '<=', now())
            ->get();

        $count = 0;

        foreach ($expiredListings as $listing) {
            DB::transaction(function () use ($listing) {
                $listing = FoodListing::lockForUpdate()->find($listing->id);
                if (!$listing || !in_array($listing->status, [ListingStatus::AVAILABLE->value, ListingStatus::RESERVED->value])) {
                    return;
                }

                // 1. Pending claims become expired (TC-13)
                $listing->claims()
                    ->where('status', ClaimStatus::PENDING->value)
                    ->update([
                        'status' => ClaimStatus::EXPIRED->value,
                    ]);

                // 2. Approved claims become no_show (TC-13, TC-14)
                $approvedClaims = $listing->claims()
                    ->where('status', ClaimStatus::APPROVED->value)
                    ->get();

                foreach ($approvedClaims as $claim) {
                    $claim->update([
                        'status' => ClaimStatus::NO_SHOW->value,
                    ]);

                    // Count no-shows in last 30 days for this user
                    $user = User::lockForUpdate()->find($claim->user_id);
                    if ($user) {
                        $noShowsIn30Days = Claim::where('user_id', $user->id)
                            ->where('status', ClaimStatus::NO_SHOW->value)
                            ->where('updated_at', '>=', now()->subDays(30))
                            ->count();

                        $user->no_show_count = $noShowsIn30Days;

                        // Rule 12 / TC-14: Configurable no-shows threshold in 30 days restricts claiming
                        $noShowThreshold = max((int) site_setting('no_show_penalty_threshold', 3), 1);
                        $restrictionDays = max((int) site_setting('no_show_restriction_days', 7), 1);
                        if ($noShowsIn30Days >= $noShowThreshold) {
                            $user->claim_restricted_until = now()->addDays($restrictionDays);
                        }
                        $user->save();
                    }
                }

                // 3. Listing becomes expired
                $listing->status = ListingStatus::EXPIRED->value;
                $listing->save();
            });

            $count++;
        }

        $this->info("Successfully expired {$count} listings.");
        return Command::SUCCESS;
    }
}
