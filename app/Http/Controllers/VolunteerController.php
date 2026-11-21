<?php

namespace App\Http\Controllers;

use App\Models\Regency;
use App\Models\VolunteerArea;
use App\Services\VolunteerApplicationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VolunteerController extends Controller
{
    public function __construct(
        protected VolunteerApplicationService $volunteerService
    ) {}

    public function showApplyForm()
    {
        $regencies = Regency::with('districts', 'province')->orderBy('name')->get();
        $user = Auth::user();
        $myAreas = $user->volunteerAreas()->with('district.regency')->get();

        return view('volunteer.apply', compact('regencies', 'myAreas', 'user'));
    }

    public function apply(Request $request)
    {
        $validated = $request->validate([
            'regency_id' => ['required', 'exists:regencies,id'],
            'district_ids' => ['required', 'array', 'min:1'],
            'district_ids.*' => ['exists:districts,id'],
            'motivation' => ['required', 'string', 'min:10', 'max:1000'],
        ]);

        $user = Auth::user();

        try {
            $this->volunteerService->submit(
                $user,
                (int) $validated['regency_id'],
                $validated['district_ids'],
                $validated['motivation']
            );

            return redirect()->route('volunteer.areas')
                ->with('success', 'Pengajuan relawan berhasil dikirim! Admin akan meninjau setiap kecamatan yang Anda pilih.');
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function myAreas()
    {
        $user = Auth::user();
        $areas = $user->volunteerAreas()
            ->with(['district.regency.province', 'decidedByUser'])
            ->get()
            ->groupBy(fn($a) => $a->regency->name);

        return view('volunteer.areas', compact('areas', 'user'));
    }

    public function resign(VolunteerArea $area)
    {
        $user = Auth::user();

        try {
            $this->volunteerService->resignArea($area, $user);
            return back()->with('info', "Anda telah mundur sebagai relawan dari Kecamatan {$area->district->name}.");
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
