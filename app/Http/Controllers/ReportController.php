<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'listing_id' => ['nullable', 'exists:food_listings,id'],
            'campaign_id' => ['nullable', 'exists:food_campaigns,id'],
            'reported_user_id' => ['nullable', 'exists:users,id'],
            'reason' => ['required', 'in:expired_food,fake_listing,fake_campaign,unsafe_food,harassment,other'],
            'detail' => ['nullable', 'string', 'max:1000'],
        ]);

        Report::create([
            'reporter_id' => Auth::id(),
            'listing_id' => $validated['listing_id'] ?? null,
            'campaign_id' => $validated['campaign_id'] ?? null,
            'reported_user_id' => $validated['reported_user_id'] ?? null,
            'reason' => $validated['reason'],
            'detail' => $validated['detail'] ?? null,
            'status' => 'open',
        ]);

        return back()->with('info', 'Laporan Anda telah diterima. Tim admin akan meninjaunya sesegera mungkin.');
    }
}
