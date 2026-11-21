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

        // Stats
        $portionsShared = (int) $user->listings()->sum('portions_total');
        $portionsTaken = (int) $user->claims()->where('status', 'picked_up')->sum('portions_approved');

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
            'incomingPendingClaims',
            'myListings',
            'myClaims',
            'myCampaigns'
        ));
    }
}
