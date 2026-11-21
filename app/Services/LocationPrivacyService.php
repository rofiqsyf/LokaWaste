<?php

namespace App\Services;

class LocationPrivacyService
{
    /**
     * Generate permanent public jittered coordinate (150 - 400 meters).
     * SDD 6.5: publicPoint shifts exact coordinates and is stored permanently.
     */
    public function publicPoint(float $lat, float $lng): array
    {
        $distanceKm = random_int(150, 400) / 1000;
        $bearing = random_int(0, 359) * M_PI / 180;
        $dLat = ($distanceKm * cos($bearing)) / 111.19;
        $dLng = ($distanceKm * sin($bearing)) / (111.19 * max(cos(deg2rad($lat)), 0.01));

        return [
            round($lat + $dLat, 7),
            round($lng + $dLng, 7),
        ];
    }
}
