<?php

namespace App\Http\Controllers;

use App\Enums\CampaignStatus;
use App\Models\Category;
use App\Models\Claim;
use App\Models\District;
use App\Models\FoodCampaign;
use App\Models\Regency;
use App\Services\NearbyListingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function __construct(
        protected NearbyListingService $nearbyService
    ) {}

    public function index(Request $request)
    {
        $categories = Category::all();
        $user = auth()->user();

        // Default center: Tembalang, Kota Semarang (-7.05194, 110.43778)
        $initialLat = -7.05194;
        $initialLng = 110.43778;

        // If lat/lng passed from catalog hand-off (SDD FR-46, SDD 8.8)
        if ($request->filled('lat') && $request->filled('lng')) {
            $initialLat = (float) $request->input('lat');
            $initialLng = (float) $request->input('lng');
        } elseif ($user && $user->district && $user->district->latitude && $user->district->longitude) {
            $initialLat = (float) $user->district->latitude;
            $initialLng = (float) $user->district->longitude;
        }

        $focusId = $request->input('focus');

        return view('home', compact('categories', 'initialLat', 'initialLng', 'focusId'));
    }

    /**
     * API: Nearby listings endpoint
     * SDD 6.3: Returns JSON with ONLY public coordinates, rounded distance, server_now, tz_label
     */
    public function nearbyListings(Request $request): JsonResponse
    {
        $lat = (float) $request->input('lat', -7.05194);
        $lng = (float) $request->input('lng', 110.43778);
        $radius = (int) $request->input('radius', 5);

        $filters = [
            'category' => $request->input('category'),
            'halal' => $request->boolean('halal'),
            'district_id' => $request->input('district_id'),
        ];

        $listings = $this->nearbyService->search($lat, $lng, $radius, $filters);

        $currentUserId = auth()->id();

        // Fetch current user's claims for these listings
        $userClaims = $currentUserId
            ? Claim::where('user_id', $currentUserId)
                ->whereIn('listing_id', $listings->pluck('id'))
                ->orderBy('created_at', 'desc')
                ->get()
                ->keyBy('listing_id')
            : collect();

        $data = $listings->map(function ($l) use ($currentUserId, $userClaims) {
            $totalMins = (int) round($l->windowMinutes());
            $remainingMins = max(0, (int) round(now()->diffInMinutes($l->expires_at, false)));

            $pickupAddress = $l->show_exact_location
                ? ($l->pickup_address ?: ('Kec. ' . ($l->district?->name ?? '') . ', ' . ($l->district?->regency?->name ?? '')))
                : ('Area Kec. ' . ($l->district?->name ?? '') . ', ' . ($l->district?->regency?->name ?? '') . ' (Alamat presisi dibuka setelah klaim disetujui)');

            $myClaim = $userClaims->get($l->id);

            return [
                'id' => $l->id,
                'title' => $l->title,
                'description' => $l->description,
                'category_name' => $l->category?->name,
                'category_icon' => $l->category?->icon,
                'user_name' => $l->user?->name,
                'is_verified' => (bool) $l->user?->is_verified,
                'is_volunteer' => (bool) $l->user?->isVolunteer(),
                'is_owner' => $currentUserId && $l->user_id === $currentUserId,
                'portions_left' => (int) $l->portions_left,
                'portions_total' => (int) $l->portions_total,
                'max_portions_per_claim' => (int) $l->max_portions_per_claim,
                'distance_km' => round((float) $l->distance_km, 1),
                'halal_claim' => (bool) $l->halal_claim,
                'food_type' => $l->food_type,
                'photo_url' => $l->photo_path ? asset('storage/' . $l->photo_path) : null,
                'lat' => (float) $l->public_latitude,
                'lng' => (float) $l->public_longitude,
                'show_exact' => (bool) $l->show_exact_location,
                'district_name' => $l->district?->name,
                'regency_name' => $l->district?->regency?->name,
                'pickup_address' => $pickupAddress,
                'available_from' => $l->available_from->toIso8601String(),
                'expires_at' => $l->expires_at->toIso8601String(),
                'window_minutes' => $totalMins,
                'remaining_minutes' => $remainingMins,
                'tz_label' => $l->tzLabel(),
                'timezone' => $l->timezone(),
                'detail_url' => route('listings.show', $l->id),
                'campaign_id' => $l->campaign_id,
                'campaign_title' => $l->campaign?->title,
                'campaign_priority_active' => (bool) ($l->campaign_priority_until && $l->campaign_priority_until->isFuture()),
                'my_claim' => $myClaim ? [
                    'id' => $myClaim->id,
                    'status' => $myClaim->status,
                    'portions_requested' => (int) $myClaim->portions_requested,
                    'portions_approved' => (int) ($myClaim->portions_approved ?? $myClaim->portions_requested),
                    'pickup_code' => $myClaim->status === 'approved' ? $myClaim->pickup_code : null,
                    'note' => $myClaim->note,
                    'created_at' => $myClaim->created_at->toIso8601String(),
                ] : null,
            ];
        });

        return response()->json([
            'listings' => $data,
            'server_now' => now()->toIso8601String(),
            'total' => $data->count(),
        ]);
    }

    /**
     * API: Nearby published campaigns for map layer toggle
     */
    public function nearbyCampaigns(Request $request): JsonResponse
    {
        $campaigns = FoodCampaign::with('district.regency')
            ->where('status', CampaignStatus::PUBLISHED->value)
            ->get()
            ->map(function ($c) {
                return [
                    'id' => $c->id,
                    'title' => $c->title,
                    'slug' => $c->slug,
                    'beneficiary_name' => $c->beneficiary_name,
                    'beneficiary_type' => $c->beneficiary_type,
                    'target_portions' => $c->target_portions,
                    'collected_portions' => $c->collected_portions,
                    'progress_percent' => $c->progressPercent(),
                    'district_name' => $c->district?->name,
                    'regency_name' => $c->district?->regency?->name,
                    'pickup_address' => 'Penyaluran: ' . $c->beneficiary_name . ', Kec. ' . ($c->district?->name ?? ''),
                    'lat' => $c->public_latitude,
                    'lng' => $c->public_longitude,
                    'precision' => $c->location_precision,
                    'url' => route('campaigns.show', $c->slug),
                ];
            });

        return response()->json([
            'campaigns' => $campaigns,
            'server_now' => now()->toIso8601String(),
        ]);
    }

    public function regencies(Request $request): JsonResponse
    {
        $q = $request->input('q');
        $regencies = Regency::query()
            ->when($q, fn($query, $search) => $query->where('name', 'like', "%{$search}%"))
            ->with('province')
            ->orderBy('name')
            ->get(['id', 'province_id', 'code', 'name', 'type']);

        return response()->json($regencies);
    }

    public function districtsByRegency(int $regencyId): JsonResponse
    {
        $districts = District::where('regency_id', $regencyId)
            ->orderBy('name')
            ->get(['id', 'regency_id', 'code', 'name', 'latitude', 'longitude']);

        return response()->json($districts);
    }
}
