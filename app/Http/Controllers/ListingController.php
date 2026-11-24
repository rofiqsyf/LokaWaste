<?php

namespace App\Http\Controllers;

use App\Enums\FoodType;
use App\Enums\ListingStatus;
use App\Models\Category;
use App\Models\District;
use App\Models\FoodCampaign;
use App\Models\FoodListing;
use App\Services\EmsifaWilayahService;
use App\Services\ImageUploadService;
use App\Services\LocationPrivacyService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ListingController extends Controller
{
    public function __construct(
        protected LocationPrivacyService $privacyService,
        protected ImageUploadService $imageService,
        protected EmsifaWilayahService $wilayahService
    ) {}

    /**
     * E-Commerce Food Catalog (SDD FR-45, FR-46, SDD 8.8)
     */
    public function catalog(Request $request)
    {
        $query = FoodListing::query()
            ->visibleToMap()
            ->with(['category', 'user', 'district.regency.province', 'campaign']);

        // Search query (keyword in title, description, provider, or location)
        if ($request->filled('q')) {
            $keyword = trim($request->input('q'));
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                  ->orWhere('description', 'like', "%{$keyword}%")
                  ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$keyword}%")->orWhere('organization_name', 'like', "%{$keyword}%"))
                  ->orWhereHas('district', fn($d) => $d->where('name', 'like', "%{$keyword}%"))
                  ->orWhereHas('district.regency', fn($r) => $r->where('name', 'like', "%{$keyword}%"))
                  ->orWhereHas('district.regency.province', fn($p) => $p->where('name', 'like', "%{$keyword}%"));
            });
        }

        // Category filter
        if ($request->filled('category')) {
            $cat = $request->input('category');
            $query->whereHas('category', function ($q) use ($cat) {
                $q->where('slug', $cat)->orWhere('id', $cat);
            });
        }

        // Halal filter
        if ($request->boolean('halal')) {
            $query->where('halal_claim', true);
        }

        // Vegetarian filter
        if ($request->boolean('vegetarian')) {
            $query->where('is_vegetarian', true);
        }

        // Food type filter (cooked, packaged, raw)
        if ($request->filled('food_type') && in_array($request->input('food_type'), ['cooked', 'packaged', 'raw'])) {
            $query->where('food_type', $request->input('food_type'));
        }

        // Urgency filter: 'critical' (< 1 hour), 'urgent' (< 2 hours)
        if ($request->input('urgency') === 'critical') {
            $query->where('expires_at', '<=', now()->addHour());
        } elseif ($request->input('urgency') === 'urgent') {
            $query->where('expires_at', '<=', now()->addHours(2));
        }

        // Wilayah filters (Provinsi, Kab/Kota, Kecamatan) - harmonized with EMSIFA API & database
        if ($request->filled('district_id')) {
            $distParam = $request->input('district_id');
            $distName = $request->input('district_name');
            $query->whereHas('district', function ($d) use ($distParam, $distName) {
                $d->where(function ($sq) use ($distParam, $distName) {
                    $sq->where('id', $distParam)
                       ->orWhere('code', (string) $distParam)
                       ->orWhere('code', 'like', substr((string) $distParam, 0, 6) . '%');
                    if (!empty($distName)) {
                        $sq->orWhere('name', 'like', '%' . trim($distName) . '%');
                    }
                });
            });
        } elseif ($request->filled('regency_id')) {
            $regParam = $request->input('regency_id');
            $regName = $request->input('regency_name');
            $query->whereHas('district.regency', function ($r) use ($regParam, $regName) {
                $r->where(function ($sq) use ($regParam, $regName) {
                    $sq->where('id', $regParam)
                       ->orWhere('code', (string) $regParam);
                    if (!empty($regName)) {
                        $sq->orWhere('name', 'like', '%' . trim($regName) . '%');
                    }
                });
            });
        } elseif ($request->filled('province_id')) {
            $provParam = $request->input('province_id');
            $provName = $request->input('province_name');
            $query->whereHas('district.regency.province', function ($p) use ($provParam, $provName) {
                $p->where(function ($sq) use ($provParam, $provName) {
                    $sq->where('id', $provParam)
                       ->orWhere('code', (string) $provParam);
                    if (!empty($provName)) {
                        $sq->orWhere('name', 'like', '%' . trim($provName) . '%');
                    }
                });
            });
        }

        // Coordinates for distance calculations if user provided
        $userLat = $request->filled('lat') ? (float) $request->input('lat') : null;
        $userLng = $request->filled('lng') ? (float) $request->input('lng') : null;

        // Radius filter if user coordinates and radius provided
        if ($userLat && $userLng && $request->filled('radius')) {
            $radius = (int) $request->input('radius');
            $latDelta = $radius / 111.0;
            $lngDelta = $radius / (111.0 * max(cos(deg2rad($userLat)), 0.01));
            $query->whereBetween('public_latitude', [$userLat - $latDelta, $userLat + $latDelta])
                  ->whereBetween('public_longitude', [$userLng - $lngDelta, $userLng + $lngDelta]);
        }

        // Sort order
        $sort = $request->input('sort', 'expiring_soon');
        if ($sort === 'newest') {
            $query->orderBy('created_at', 'desc');
        } elseif ($sort === 'portions_high') {
            $query->orderBy('portions_left', 'desc');
        } elseif ($sort === 'nearest' && $userLat && $userLng) {
            $distanceFormula = "(6371 * acos(LEAST(1.0, GREATEST(-1.0,
                cos(radians(?)) * cos(radians(public_latitude)) *
                cos(radians(public_longitude) - radians(?)) +
                sin(radians(?)) * sin(radians(public_latitude))
            ))))";
            $query->selectRaw("food_listings.*, {$distanceFormula} AS distance_km", [$userLat, $userLng, $userLat])
                  ->orderBy('distance_km', 'asc');
        } else {
            // Default: Food Rescue Urgency Principle (makanan yang segera kedaluwarsa diprioritaskan)
            $query->orderBy('expires_at', 'asc');
        }

        // Paginate results with query strings preserved
        $listings = $query->paginate(12)->withQueryString();

        // Calculate distance on collection if user coordinates provided and distance_km not yet calculated
        if ($userLat && $userLng) {
            $listings->getCollection()->transform(function ($l) use ($userLat, $userLng) {
                if (!isset($l->distance_km)) {
                    $dLat = deg2rad($l->public_latitude - $userLat);
                    $dLng = deg2rad($l->public_longitude - $userLng);
                    $a = sin($dLat / 2) * sin($dLat / 2) +
                         cos(deg2rad($userLat)) * cos(deg2rad($l->public_latitude)) *
                         sin($dLng / 2) * sin($dLng / 2);
                    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
                    $l->distance_km = round(6371 * $c, 1);
                }
                return $l;
            });
        }

        // Categories with active listings count
        $categories = Category::withCount(['listings' => fn($q) => $q->visibleToMap()])->get();

        // Aggregates for header stats
        $totalActiveListings = FoodListing::visibleToMap()->count();
        $totalPortionsAvailable = FoodListing::visibleToMap()->sum('portions_left');

        $districts = District::with('regency.province')->orderBy('name')->get();
        $provinces = $this->wilayahService->getProvinces();

        $selectedProvince = (string) $request->input('province_id', '');
        $selectedRegency = (string) $request->input('regency_id', '');
        $selectedDistrict = (string) $request->input('district_id', '');
        $selectedProvinceName = (string) $request->input('province_name', '');
        $selectedRegencyName = (string) $request->input('regency_name', '');
        $selectedDistrictName = (string) $request->input('district_name', '');

        $initialRegencies = $selectedProvince ? $this->wilayahService->getRegencies($selectedProvince) : [];
        $initialDistricts = $selectedRegency ? $this->wilayahService->getDistricts($selectedRegency) : [];

        // Active districts with available surplus food for quick shortcut chips
        $activeListingDistricts = District::whereHas('listings', fn($q) => $q->visibleToMap())
            ->with('regency.province')
            ->orderBy('name')
            ->get();

        return view('catalog.index', compact(
            'listings',
            'categories',
            'totalActiveListings',
            'totalPortionsAvailable',
            'districts',
            'provinces',
            'initialRegencies',
            'initialDistricts',
            'selectedProvince',
            'selectedRegency',
            'selectedDistrict',
            'selectedProvinceName',
            'selectedRegencyName',
            'selectedDistrictName',
            'activeListingDistricts',
            'userLat',
            'userLng',
            'sort'
        ));
    }

    public function create()
    {
        $categories = Category::all();
        $districts = District::with('regency')->orderBy('name')->get();
        $user = Auth::user();

        // Published campaigns in user's region or nearby
        $campaigns = FoodCampaign::where('status', 'published')->get();

        return view('listings.create', compact('categories', 'districts', 'campaigns', 'user'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'portions_total' => ['required', 'integer', 'min:1'],
            'max_portions_per_claim' => ['nullable', 'integer', 'min:1', 'max:255'],
            'food_type' => ['required', 'in:cooked,packaged,raw'],
            'prepared_at' => ['nullable', 'date'],
            'hours_valid' => ['required', 'integer', 'min:1', 'max:24'],
            'expires_time' => ['nullable', 'date_format:H:i'],
            'pickup_address' => ['required', 'string'],
            'latitude' => ['required', 'numeric', 'between:-11.5,6.5'],
            'longitude' => ['required', 'numeric', 'between:94.5,141.5'],
            'district_id' => ['nullable', 'exists:districts,id'],
            'photo' => ['nullable', 'image', 'max:2048'],
            'allergens' => ['nullable', 'array'],
            'storage_note' => ['nullable', 'string', 'max:255'],
            'halal_claim' => ['nullable', 'boolean'],
            'is_vegetarian' => ['nullable', 'boolean'],
            'description' => ['nullable', 'string'],
            'campaign_id' => ['nullable', 'exists:food_campaigns,id'],
            'show_exact_location' => ['nullable', 'boolean'],
        ]);

        $user = Auth::user();

        // Process photo with EXIF stripping and WebP encoding (TC-19, SDD 8.7)
        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $this->imageService->processAndStore($request->file('photo'), 'listings');
        }

        $now = now();
        $expiresAt = $now->copy()->addHours((int) $validated['hours_valid']);

        if ($request->filled('expires_time') && preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $request->input('expires_time'))) {
            $tz = $user->district?->regency?->province?->timezone ?? 'Asia/Jakarta';
            $nowInTz = $now->copy()->setTimezone($tz);
            [$targetH, $targetM] = explode(':', $request->input('expires_time'));
            $targetTime = $nowInTz->copy()->setTime((int) $targetH, (int) $targetM, 0);

            // If selected time today is already in the past or within next 10 minutes, it applies to tomorrow
            if ($targetTime->isPast() || $nowInTz->diffInMinutes($targetTime, false) < 10) {
                $targetTime->addDay();
            }

            // Expiration cannot be more than 24 hours into the future for surplus food safety
            if ($nowInTz->diffInHours($targetTime) <= 24) {
                $expiresAt = $targetTime->setTimezone('UTC');
            }
        }

        // SDD 3.7 Rule 26: Campaign priority calculation
        $priorityUntil = null;
        if (!empty($validated['campaign_id'])) {
            $campaign = FoodCampaign::find($validated['campaign_id']);
            if ($campaign && $campaign->status === 'published') {
                $candidate = min($now->copy()->addHours(2), $expiresAt->copy()->subMinutes(60));
                if ($candidate > $now->copy()->addMinutes(15)) {
                    $priorityUntil = $candidate;
                }
            }
        }

        $lat = (float) $validated['latitude'];
        $lng = (float) $validated['longitude'];

        // Only verified business can opt to show exact coordinates (SDD 6.5)
        $showExact = $user->is_verified && $request->boolean('show_exact_location');
        if ($showExact) {
            $publicLat = $lat;
            $publicLng = $lng;
        } else {
            [$publicLat, $publicLng] = $this->privacyService->publicPoint($lat, $lng);
        }

        // Resolve district automatically in background (user district, closest district, or default)
        $districtId = $validated['district_id'] ?? null;
        if (!$districtId && $user->district_id) {
            $districtId = $user->district_id;
        }
        if (!$districtId) {
            $districts = District::whereNotNull('latitude')->whereNotNull('longitude')->get();
            $bestDist = PHP_FLOAT_MAX;
            foreach ($districts as $d) {
                $dLat = deg2rad($d->latitude - $lat);
                $dLng = deg2rad($d->longitude - $lng);
                $a = sin($dLat / 2) * sin($dLat / 2) +
                     cos(deg2rad($lat)) * cos(deg2rad($d->latitude)) *
                     sin($dLng / 2) * sin($dLng / 2);
                $dist = 2 * atan2(sqrt($a), sqrt(1 - $a));
                if ($dist < $bestDist) {
                    $bestDist = $dist;
                    $districtId = $d->id;
                }
            }
        }
        if (!$districtId) {
            $districtId = District::first()?->id ?? 1;
        }

        $listing = FoodListing::create([
            'user_id' => $user->id,
            'category_id' => $validated['category_id'],
            'campaign_id' => $validated['campaign_id'] ?? null,
            'district_id' => $districtId,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? 'Surplus makanan layak dan bersih siap dibagikan.',
            'photo_path' => $photoPath,
            'food_type' => $validated['food_type'],
            'prepared_at' => $validated['prepared_at'] ?? ($validated['food_type'] === 'cooked' ? $now : null),
            'allergens' => $validated['allergens'] ?? [],
            'storage_note' => $validated['storage_note'] ?? null,
            'halal_claim' => $request->boolean('halal_claim'),
            'is_vegetarian' => $request->boolean('is_vegetarian'),
            'portions_total' => $validated['portions_total'],
            'portions_left' => $validated['portions_total'],
            'max_portions_per_claim' => !empty($validated['max_portions_per_claim']) ? (int) $validated['max_portions_per_claim'] : min((int) $validated['portions_total'], 255),
            'pickup_address' => $validated['pickup_address'],
            'latitude' => $lat,
            'longitude' => $lng,
            'public_latitude' => $publicLat,
            'public_longitude' => $publicLng,
            'show_exact_location' => $showExact,
            'available_from' => $now,
            'expires_at' => $expiresAt,
            'campaign_priority_until' => $priorityUntil,
            'status' => ListingStatus::AVAILABLE->value,
        ]);

        return redirect()->route('listings.show', $listing->id)
            ->with('success', 'Makanan berhasil dibagikan! Terima kasih atas kebaikan Anda.');
    }

    public function show(FoodListing $listing)
    {
        $listing->load(['user', 'category', 'campaign', 'district.regency.province', 'claims.user']);
        $currentUser = Auth::user();

        // Check if user has active claim on this listing
        $userActiveClaim = null;
        if ($currentUser) {
            $userActiveClaim = $listing->claims()
                ->where('user_id', $currentUser->id)
                ->whereIn('status', ['pending', 'approved', 'picked_up'])
                ->first();
        }

        // Privacy location logic (SDD 6.5, FR-37, TC-18):
        // Exact address and coordinates are visible ONLY to owner or approved/picked_up claimer
        $canSeeExact = false;
        if ($currentUser) {
            if ($listing->user_id === $currentUser->id) {
                $canSeeExact = true;
            } elseif ($userActiveClaim && in_array($userActiveClaim->status, ['approved', 'picked_up'])) {
                $canSeeExact = true;
            }
        }

        // Check if volunteer can claim on behalf of a campaign
        $volunteerCampaigns = collect();
        if ($currentUser && $currentUser->isVolunteerIn($listing->district_id)) {
            $volunteerCampaigns = FoodCampaign::where('user_id', $currentUser->id)
                ->where('status', 'published')
                ->where('district_id', $listing->district_id)
                ->get();
        }

        return view('listings.show', compact('listing', 'userActiveClaim', 'canSeeExact', 'volunteerCampaigns'));
    }

    public function cancel(FoodListing $listing)
    {
        abort_unless(Auth::id() === $listing->user_id || Auth::user()?->isAdmin(), 403, 'Akses ditolak.');

        $listing->update(['status' => ListingStatus::CANCELLED->value]);

        // Cancel all pending claims
        $listing->claims()->where('status', 'pending')->update(['status' => 'cancelled', 'cancel_reason' => 'Listing dibatalkan pemilik']);

        return back()->with('info', 'Listing makanan berhasil dibatalkan.');
    }

    /**
     * "Bagikan lagi" (duplicate listing - FR-42, SDD 3.8)
     */
    public function duplicate(FoodListing $listing)
    {
        abort_unless(Auth::id() === $listing->user_id, 403, 'Akses ditolak.');

        $categories = Category::all();
        $districts = District::with('regency')->orderBy('name')->get();
        $campaigns = FoodCampaign::where('status', 'published')->get();
        $user = Auth::user();

        return view('listings.create', [
            'categories' => $categories,
            'districts' => $districts,
            'campaigns' => $campaigns,
            'user' => $user,
            'duplicate' => $listing,
        ]);
    }
}
