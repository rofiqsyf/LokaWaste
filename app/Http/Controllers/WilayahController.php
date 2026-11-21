<?php

namespace App\Http\Controllers;

use App\Services\EmsifaWilayahService;
use Illuminate\Http\JsonResponse;

class WilayahController extends Controller
{
    public function __construct(
        protected EmsifaWilayahService $wilayahService
    ) {}

    public function provinces(): JsonResponse
    {
        $provinces = $this->wilayahService->getProvinces();
        return response()->json($provinces);
    }

    public function regencies(string $provinceId): JsonResponse
    {
        $regencies = $this->wilayahService->getRegencies($provinceId);
        return response()->json($regencies);
    }

    public function districts(string $regencyId): JsonResponse
    {
        $districts = $this->wilayahService->getDistricts($regencyId);
        return response()->json($districts);
    }
}
