<?php

namespace App\Services;

use App\Models\FoodListing;
use Illuminate\Database\Eloquent\Collection;

class NearbyListingService
{
    /**
     * Search nearby listings using Haversine formula on public jittered coordinates.
     * SDD 6.2: LEAST/GREATEST prevents acos(>1) from returning NULL on identical points.
     */
    public function search(float $lat, float $lng, int $radiusKm = 5, array $filters = []): Collection
    {
        $latDelta = $radiusKm / 111.0;
        $lngDelta = $radiusKm / (111.0 * max(cos(deg2rad($lat)), 0.01));

        // LEAST/GREATEST prevents acos(>1) resulting in NULL on very close/identical points (Audit #1, TC-08)
        $distance = "(6371 * acos(LEAST(1.0, GREATEST(-1.0,
            cos(radians(?)) * cos(radians(public_latitude)) *
            cos(radians(public_longitude) - radians(?)) +
            sin(radians(?)) * sin(radians(public_latitude))
        ))))";

        $listings = FoodListing::query()
            ->with(['category', 'user', 'campaign', 'district.regency.province'])
            ->visibleToMap()
            ->whereBetween('public_latitude', [$lat - $latDelta, $lat + $latDelta])
            ->whereBetween('public_longitude', [$lng - $lngDelta, $lng + $lngDelta])
            ->when($filters['category'] ?? null, fn($q, $v) => $q->where('category_id', $v))
            ->when($filters['halal'] ?? null, fn($q) => $q->where('halal_claim', true))
            ->when($filters['district_id'] ?? null, fn($q, $v) => $q->where('district_id', $v))
            ->selectRaw("food_listings.*, {$distance} AS distance_km", [$lat, $lng, $lat])
            ->orderBy('distance_km')
            ->limit(100)
            ->get();

        // Filter by exact radius and round distance to 0.1 km (SDD 6.2 & 6.5)
        return $listings->filter(function ($listing) use ($radiusKm) {
            $listing->distance_km = round((float) $listing->distance_km, 1);
            return $listing->distance_km <= $radiusKm;
        })->values();
    }
}
