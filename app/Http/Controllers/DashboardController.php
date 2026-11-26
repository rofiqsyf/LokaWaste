<?php

namespace App\Http\Controllers;

use App\Models\Claim;
use App\Models\FoodCampaign;
use App\Models\FoodListing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $tab = $request->input('tab', 'ringkasan');
        if ($tab === 'dibagikan') {
            $tab = 'bagikan';
        }
        if (!in_array($tab, ['ringkasan', 'bagikan', 'ambil', 'galang'])) {
            $tab = 'ringkasan';
        }

        // Stats & Impact Engine (design.md 1.F & 5.3)
        $portionsShared = (int) $user->listings()->sum('portions_total');
        $portionsTaken = (int) $user->claims()->where('status', 'picked_up')->sum('portions_approved');
        $totalPortions = $portionsShared + $portionsTaken;

        $co2Avoided = round($totalPortions * 0.85, 1);
        $waterSaved = round($totalPortions * 320);
        $economicValue = $totalPortions * 15000;

        // Level Penyelamat Pangan
        $impactLevel = 'Penyelamat Pangan Pemula';
        if ($totalPortions >= 50) {
            $impactLevel = 'Pahlawan Pangan Emas';
        } elseif ($totalPortions >= 20) {
            $impactLevel = 'Penyelamat Pangan Perak';
        } elseif ($totalPortions >= 5) {
            $impactLevel = 'Penyelamat Pangan Perunggu';
        }

        // Incoming pending claims on my listings (placed at top of "Yang saya bagikan")
        $incomingPendingClaims = Claim::with(['listing', 'user', 'campaign'])
            ->whereHas('listing', fn($q) => $q->where('user_id', $user->id))
            ->where('status', 'pending')
            ->latest()
            ->get();

        // My listings
        $myListings = FoodListing::with(['category', 'claims'])
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        // My claims ("Yang saya ambil")
        $myClaims = Claim::with(['listing.user', 'review'])
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        // Volunteer campaigns (if volunteer)
        $myCampaigns = collect();
        if ($user->isVolunteer()) {
            $myCampaigns = FoodCampaign::with('district.regency')
                ->where('user_id', $user->id)
                ->latest()
                ->get();
        }

        // Granular dataset for real-time dynamic date-filtered analytics charts
        $analyticsData = [
            'listings' => $myListings->map(fn($l) => [
                'id' => $l->id,
                'title' => $l->title,
                'category' => $l->category?->name ?? 'Lainnya',
                'portions_total' => (int) $l->portions_total,
                'portions_left' => (int) $l->portions_left,
                'portions_shared' => (int) $l->portions_total,
                'portions_taken' => (int) max(0, $l->portions_total - $l->portions_left),
                'date' => $l->created_at->format('Y-m-d'),
                'created_at' => $l->created_at->toIso8601String(),
                'status' => $l->status,
            ])->values(),
            'claims' => $myClaims->map(fn($c) => [
                'id' => $c->id,
                'listing_title' => $c->listing?->title ?? 'Makanan',
                'category' => $c->listing?->category?->name ?? 'Lainnya',
                'portions' => (int) ($c->portions_approved ?? $c->portions_requested),
                'status' => $c->status,
                'date' => $c->created_at->format('Y-m-d'),
                'created_at' => $c->created_at->toIso8601String(),
                'picked_up_at' => $c->picked_up_at?->toIso8601String(),
            ])->values(),
        ];

        return view('dashboard.index', compact(
            'user',
            'tab',
            'portionsShared',
            'portionsTaken',
            'co2Avoided',
            'waterSaved',
            'economicValue',
            'impactLevel',
            'incomingPendingClaims',
            'myListings',
            'myClaims',
            'myCampaigns',
            'analyticsData'
        ));
    }
}
