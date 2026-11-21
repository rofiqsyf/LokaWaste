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
            'myCampaigns'
        ));
    }
}
