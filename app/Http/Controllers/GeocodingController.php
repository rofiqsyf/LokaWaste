<?php

namespace App\Http\Controllers;

use App\Models\District;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class GeocodingController extends Controller
{
    /**
     * Search location autocomplete with multiple matching suggestions (Google Maps style)
     */
    public function search(Request $request): JsonResponse
    {
        $query = trim($request->input('q', ''));
        if (mb_strlen($query) < 2) {
            return response()->json([]);
        }

        $cacheKey = 'geocode_search_' . md5(strtolower($query));

        $results = Cache::remember($cacheKey, 86400 * 7, function () use ($query) {
            $suggestions = [];

            // 1. Fetch from Nominatim OpenStreetMap with custom User-Agent
            try {
                $response = Http::withoutVerifying()
                    ->timeout(6)
                    ->withHeaders([
                        'User-Agent' => 'LokaWaste/1.0 (contact@lokawaste.test)',
                        'Accept-Language' => 'id,en;q=0.9',
                    ])
                    ->get('https://nominatim.openstreetmap.org/search', [
                        'format' => 'json',
                        'q' => $query,
                        'countrycodes' => 'id',
                        'addressdetails' => 1,
                        'limit' => 6,
                    ]);

                if ($response->successful()) {
                    foreach ($response->json() as $item) {
                        $displayName = $item['display_name'] ?? '';
                        $parts = array_map('trim', explode(',', $displayName));
                        $title = $parts[0] ?? $query;
                        $subtitle = count($parts) > 1 ? implode(', ', array_slice($parts, 1, 4)) : '';

                        $rawType = $item['addresstype'] ?? $item['type'] ?? 'place';
                        $typeLabel = match ($rawType) {
                            'city', 'administrative' => 'Kota/Kabupaten',
                            'district', 'suburb' => 'Kecamatan',
                            'village', 'quarter', 'neighbourhood' => 'Kelurahan/Desa',
                            'road', 'residential', 'pedestrian' => 'Jalan/Kawasan',
                            'amenity', 'tourism', 'leisure', 'park' => 'Fasilitas/Alun-alun',
                            default => 'Wilayah'
                        };

                        $suggestions[] = [
                            'id' => $item['place_id'] ?? Str::random(8),
                            'title' => $title,
                            'subtitle' => $subtitle,
                            'display_name' => $displayName,
                            'lat' => (float) $item['lat'],
                            'lng' => (float) $item['lon'],
                            'type' => $rawType,
                            'type_label' => $typeLabel,
                        ];
                    }
                }
            } catch (\Throwable $e) {
                // Ignore external failures and use database fallback
            }

            // 2. Database Fallback if Nominatim has few or no results
            if (count($suggestions) < 3) {
                $dbDistricts = District::with('regency.province')
                    ->where('name', 'like', "%{$query}%")
                    ->limit(5)
                    ->get();

                foreach ($dbDistricts as $d) {
                    $alreadyIn = collect($suggestions)->contains(fn($s) => Str::contains(strtolower($s['title']), strtolower($d->name)));
                    if (!$alreadyIn && $d->latitude && $d->longitude) {
                        $suggestions[] = [
                            'id' => 'db_' . $d->id,
                            'title' => 'Kec. ' . $d->name,
                            'subtitle' => ($d->regency->name ?? '') . ', ' . ($d->regency->province->name ?? ''),
                            'display_name' => "Kecamatan {$d->name}, {$d->regency->name}, {$d->regency->province->name}",
                            'lat' => (float) $d->latitude,
                            'lng' => (float) $d->longitude,
                            'type' => 'district',
                            'type_label' => 'Kecamatan',
                        ];
                    }
                }
            }

            return $suggestions;
        });

        return response()->json($results);
    }

    /**
     * Reverse geocode coordinates to human-readable address
     */
    public function reverse(Request $request): JsonResponse
    {
        $lat = (float) $request->input('lat');
        $lng = (float) $request->input('lng');

        if (!$lat || !$lng) {
            return response()->json(['error' => 'Invalid coordinates'], 400);
        }

        $roundedLat = round($lat, 4);
        $roundedLng = round($lng, 4);
        $cacheKey = "geocode_reverse_{$roundedLat}_{$roundedLng}";

        $result = Cache::remember($cacheKey, 86400 * 14, function () use ($lat, $lng) {
            try {
                $response = Http::withoutVerifying()
                    ->timeout(6)
                    ->withHeaders([
                        'User-Agent' => 'LokaWaste/1.0 (contact@lokawaste.test)',
                        'Accept-Language' => 'id,en;q=0.9',
                    ])
                    ->get('https://nominatim.openstreetmap.org/reverse', [
                        'format' => 'json',
                        'lat' => $lat,
                        'lon' => $lng,
                        'addressdetails' => 1,
                    ]);

                if ($response->successful()) {
                    $data = $response->json();
                    $address = $data['address'] ?? [];
                    
                    // Build concise road/neighborhood address
                    $road = $address['road'] ?? $address['pedestrian'] ?? $address['suburb'] ?? $address['neighbourhood'] ?? '';
                    $city = $address['city'] ?? $address['town'] ?? $address['county'] ?? $address['state_district'] ?? '';
                    $postcode = $address['postcode'] ?? '';

                    $shortAddress = trim(($road ? $road . ', ' : '') . $city . ($postcode ? ' ' . $postcode : ''));

                    return [
                        'display_name' => $data['display_name'] ?? '',
                        'short_address' => $shortAddress ?: ($data['display_name'] ?? "Titik ({$lat}, {$lng})"),
                        'road' => $road,
                        'district' => $address['suburb'] ?? $address['district'] ?? '',
                        'city' => $city,
                        'state' => $address['state'] ?? '',
                    ];
                }
            } catch (\Throwable $e) {
                // Fallback
            }

            return [
                'display_name' => "Koordinat ({$lat}, {$lng})",
                'short_address' => "Koordinat ({$lat}, {$lng})",
                'road' => '',
                'district' => '',
                'city' => '',
                'state' => '',
            ];
        });

        // Match closest district from database for automatic district synchronization
        $districts = District::with('regency')->get();
        $matchedDistrict = null;
        $minDist = PHP_FLOAT_MAX;

        foreach ($districts as $d) {
            if ($d->latitude && $d->longitude) {
                $dLat = deg2rad($d->latitude - $lat);
                $dLng = deg2rad($d->longitude - $lng);
                $a = sin($dLat / 2) * sin($dLat / 2) +
                     cos(deg2rad($lat)) * cos(deg2rad($d->latitude)) *
                     sin($dLng / 2) * sin($dLng / 2);
                $dist = 2 * atan2(sqrt($a), sqrt(1 - $a));
                if ($dist < $minDist) {
                    $minDist = $dist;
                    $matchedDistrict = $d;
                }
            }
        }

        $result['district_id'] = $matchedDistrict?->id;
        $result['district_name'] = $matchedDistrict?->name;
        $result['regency_name'] = $matchedDistrict?->regency?->name;

        return response()->json($result);
    }
}
