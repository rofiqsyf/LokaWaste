<?php

namespace App\Services;

use App\Models\Claim;

class PickupCode
{
    // Characters without ambiguous letters/numbers: 0, O, 1, I, L excluded
    private const CHARACTERS = '23456789ABCDEFGHJKMNPQRSTUVWXYZ';

    public static function generate(int $listingId, int $length = 6): string
    {
        $maxAttempts = 10;
        $charLength = strlen(self::CHARACTERS);

        for ($attempt = 0; $attempt < $maxAttempts; $attempt++) {
            $code = '';
            for ($i = 0; $i < $length; $i++) {
                $code .= self::CHARACTERS[random_int(0, $charLength - 1)];
            }

            // Ensure unique pickup code per listing
            $exists = Claim::where('listing_id', $listingId)
                ->where('pickup_code', $code)
                ->exists();

            if (!$exists) {
                return $code;
            }
        }

        // Fallback with timestamp suffix if collisions occur
        return strtoupper(substr(uniqid(), -6));
    }
}
