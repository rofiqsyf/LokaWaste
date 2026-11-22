<?php

namespace App\Http\Controllers;

use App\Models\District;
use App\Models\Province;
use App\Models\Regency;
use App\Models\VolunteerArea;
use App\Services\EmsifaWilayahService;
use App\Services\VolunteerApplicationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class VolunteerController extends Controller
{
    public function __construct(
        protected VolunteerApplicationService $volunteerService
    ) {}

    public function showApplyForm(EmsifaWilayahService $wilayahService)
    {
        $provinces = $wilayahService->getProvinces();
        $user = Auth::user()->load('district.regency.province');
        $myAreas = $user->volunteerAreas()->with('district.regency')->get();

        return view('volunteer.apply', compact('provinces', 'myAreas', 'user'));
    }

    public function apply(Request $request, EmsifaWilayahService $wilayahService)
    {
        $validated = $request->validate([
            'regency_id' => ['required'],
            'district_ids' => ['required', 'array', 'min:1'],
            'motivation' => ['required', 'string', 'min:10', 'max:2000'],
            'phone' => ['nullable', 'string', 'max:25'],
            'availability' => ['nullable', 'string', 'max:100'],
            'transportation' => ['nullable', 'string', 'max:100'],
            'experience' => ['nullable', 'string', 'max:1000'],
            'target_beneficiaries' => ['nullable'],
            'custom_target_beneficiaries' => ['nullable', 'string', 'max:1000'],
            'has_food_safety_commitment' => ['nullable'],
        ]);

        $user = Auth::user();

        // 1. Resolve or Sync Regency (Supports both local ID and EMSIFA code)
        $regencyInput = $validated['regency_id'];
        $regency = Regency::where('id', $regencyInput)
            ->orWhere('code', (string) $regencyInput)
            ->first();

        if (!$regency) {
            $regencyName = $request->input('regency_name');
            $provinceCode = $request->input('province_id');
            $provinceName = $request->input('province_name');

            $province = Province::where('code', (string) $provinceCode)
                ->orWhere('id', $provinceCode)
                ->first();

            if (!$province && $provinceCode) {
                $province = Province::create([
                    'code' => (string) $provinceCode,
                    'name' => Str::title(strtolower(trim($provinceName ?? 'Provinsi'))),
                    'timezone' => EmsifaWilayahService::getTimezoneByProvinceCode((string) $provinceCode),
                ]);
            }

            $isKota = str_starts_with(strtoupper($regencyName ?? ''), 'KOTA');
            $regency = Regency::create([
                'province_id' => $province?->id ?? 1,
                'code' => (string) $regencyInput,
                'name' => Str::title(strtolower(trim($regencyName ?? 'Kabupaten/Kota'))),
                'type' => $isKota ? 'kota' : 'kabupaten',
            ]);
        }

        // 2. Resolve Districts (Supports local IDs or EMSIFA codes)
        $resolvedDistrictIds = [];
        $districtNamesMap = $request->input('district_names', []);
        if (is_string($districtNamesMap)) {
            $districtNamesMap = json_decode($districtNamesMap, true) ?: [];
        }

        $emsifaDistricts = null;

        foreach ($validated['district_ids'] as $distInput) {
            $dist = District::where('id', $distInput)
                ->orWhere('code', (string) $distInput)
                ->first();

            if (!$dist) {
                $dName = $districtNamesMap[$distInput] ?? null;
                if (!$dName) {
                    if ($emsifaDistricts === null) {
                        $emsifaDistricts = $wilayahService->getDistricts((string) ($regency->code ?? $regency->id));
                    }
                    foreach ($emsifaDistricts as $ed) {
                        if ((string) $ed['id'] === (string) $distInput) {
                            $dName = $ed['name'];
                            break;
                        }
                    }
                }

                $dist = $wilayahService->syncOrCreateDistrict(
                    $distInput,
                    $dName ?: "Kecamatan {$distInput}",
                    $regency->code ?? $regency->id,
                    $regency->name,
                    $regency->province?->code,
                    $regency->province?->name
                );
            }

            if ($dist) {
                $resolvedDistrictIds[] = $dist->id;
            }
        }

        abort_if(empty($resolvedDistrictIds), 422, 'Pilih setidaknya satu kecamatan yang valid.');

        // Update user phone if provided and not yet set
        if (!empty($validated['phone']) && empty($user->phone)) {
            $user->update(['phone' => $validated['phone']]);
        }

        $targetBeneficiaries = $validated['target_beneficiaries'] ?? [];
        $customTargets = $validated['custom_target_beneficiaries'] ?? null;

        $allTargets = [];
        if (is_array($targetBeneficiaries)) {
            $allTargets = array_filter(array_map('trim', $targetBeneficiaries));
        } elseif (is_string($targetBeneficiaries) && trim($targetBeneficiaries) !== '') {
            $allTargets[] = trim($targetBeneficiaries);
        }

        if (!empty($customTargets)) {
            $customItems = explode(',', $customTargets);
            foreach ($customItems as $c) {
                $trimmed = trim($c);
                if ($trimmed !== '' && !in_array($trimmed, $allTargets)) {
                    $allTargets[] = $trimmed;
                }
            }
        }

        $targetBeneficiariesString = !empty($allTargets) ? implode(', ', $allTargets) : null;

        $details = [
            'phone' => $validated['phone'] ?? $user->phone,
            'availability' => $validated['availability'] ?? null,
            'transportation' => $validated['transportation'] ?? null,
            'experience' => $validated['experience'] ?? null,
            'target_beneficiaries' => $targetBeneficiariesString,
            'has_food_safety_commitment' => (bool) $request->has('has_food_safety_commitment'),
        ];

        try {
            $this->volunteerService->submit(
                $user,
                (int) $regency->id,
                $resolvedDistrictIds,
                $validated['motivation'],
                $details
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
