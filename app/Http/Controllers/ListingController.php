<?php

namespace App\Http\Controllers;

use App\Enums\FoodType;
use App\Enums\ListingStatus;
use App\Models\Category;
use App\Models\District;
use App\Models\FoodCampaign;
use App\Models\FoodListing;
use App\Services\ImageUploadService;
use App\Services\LocationPrivacyService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ListingController extends Controller
{
    public function __construct(
        protected LocationPrivacyService $privacyService,
        protected ImageUploadService $imageService
    ) {}

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
            'max_portions_per_claim' => ['required', 'integer', 'min:1', 'max:20'],
            'food_type' => ['required', 'in:cooked,packaged,raw'],
            'prepared_at' => ['nullable', 'date'],
            'hours_valid' => ['required', 'integer', 'min:1', 'max:24'],
            'pickup_address' => ['required', 'string'],
            'latitude' => ['required', 'numeric', 'between:-11.5,6.5'],
            'longitude' => ['required', 'numeric', 'between:94.5,141.5'],
            'district_id' => ['required', 'exists:districts,id'],
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

        $listing = FoodListing::create([
            'user_id' => $user->id,
            'category_id' => $validated['category_id'],
            'campaign_id' => $validated['campaign_id'] ?? null,
            'district_id' => $validated['district_id'],
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
            'max_portions_per_claim' => $validated['max_portions_per_claim'],
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
