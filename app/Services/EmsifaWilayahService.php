<?php

namespace App\Services;

use App\Models\District;
use App\Models\Province;
use App\Models\Regency;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class EmsifaWilayahService
{
    protected string $baseUrl = 'https://emsifa.github.io/api-wilayah-indonesia/api';

    /**
     * Get all provinces from EMSIFA API with caching
     */
    public function getProvinces(): array
    {
        return Cache::remember('emsifa_provinces', 86400 * 30, function () {
            try {
                $response = Http::withoutVerifying()->timeout(8)->get("{$this->baseUrl}/provinces.json");
                if ($response->successful()) {
                    return $this->formatItems($response->json());
                }
            } catch (\Throwable $e) {
                // Ignore and fall back to local database
            }

            return Province::orderBy('name')->get()->map(fn($p) => [
                'id' => (string) $p->code,
                'name' => $p->name,
            ])->toArray();
        });
    }

    /**
     * Get regencies by province ID from EMSIFA API with caching
     */
    public function getRegencies(string $provinceId): array
    {
        return Cache::remember("emsifa_regencies_{$provinceId}", 86400 * 30, function () use ($provinceId) {
            try {
                $response = Http::withoutVerifying()->timeout(8)->get("{$this->baseUrl}/regencies/{$provinceId}.json");
                if ($response->successful()) {
                    return $this->formatItems($response->json());
                }
            } catch (\Throwable $e) {
                // Ignore and fall back to local database
            }

            $province = Province::where('code', $provinceId)->orWhere('id', $provinceId)->first();
            if ($province) {
                return Regency::where('province_id', $province->id)->orderBy('name')->get()->map(fn($r) => [
                    'id' => (string) $r->code,
                    'province_id' => (string) $province->code,
                    'name' => $r->name,
                ])->toArray();
            }

            return [];
        });
    }

    /**
     * Get districts by regency ID from EMSIFA API with caching
     */
    public function getDistricts(string $regencyId): array
    {
        return Cache::remember("emsifa_districts_{$regencyId}", 86400 * 30, function () use ($regencyId) {
            try {
                $response = Http::withoutVerifying()->timeout(8)->get("{$this->baseUrl}/districts/{$regencyId}.json");
                if ($response->successful()) {
                    return $this->formatItems($response->json());
                }
            } catch (\Throwable $e) {
                // Ignore and fall back to local database
            }

            $regency = Regency::where('code', $regencyId)->orWhere('id', $regencyId)->first();
            if ($regency) {
                return District::where('regency_id', $regency->id)->orderBy('name')->get()->map(fn($d) => [
                    'id' => (string) $d->code,
                    'regency_id' => (string) $regency->code,
                    'name' => $d->name,
                ])->toArray();
            }

            return [];
        });
    }

    /**
     * Format raw items into proper title case for elegant UI display
     */
    protected function formatItems(array $items): array
    {
        return array_map(function ($item) {
            return [
                'id' => (string) $item['id'],
                'province_id' => isset($item['province_id']) ? (string) $item['province_id'] : null,
                'regency_id' => isset($item['regency_id']) ? (string) $item['regency_id'] : null,
                'name' => Str::title(strtolower(trim($item['name']))),
                'raw_name' => $item['name'],
            ];
        }, $items);
    }

    /**
     * Determine Indonesian timezone based on 2-digit province code
     */
    public static function getTimezoneByProvinceCode(string $code): string
    {
        $prefix = substr($code, 0, 2);

        // WIT (Maluku, Papua)
        if (in_array($prefix, ['81', '82', '91', '92', '93', '94', '95', '96'])) {
            return 'Asia/Jayapura';
        }

        // WITA (Bali, Nusa Tenggara, Kalimantan Selatan/Timur/Utara, Sulawesi)
        if (in_array($prefix, ['51', '52', '53', '63', '64', '65', '71', '72', '73', '74', '75', '76'])) {
            return 'Asia/Makassar';
        }

        // WIB (Sumatera, Jawa, Kalimantan Barat, Kalimantan Tengah)
        return 'Asia/Jakarta';
    }

    /**
     * Sync or create Province, Regency, and District from EMSIFA selection
     */
    public function syncOrCreateDistrict(
        string|int $districtCodeOrId,
        ?string $districtName = null,
        ?string $regencyCode = null,
        ?string $regencyName = null,
        ?string $provinceCode = null,
        ?string $provinceName = null
    ): ?District {
        if (empty($districtCodeOrId)) {
            return null;
        }

        // Check if district already exists by local primary ID or code
        $district = District::where('id', $districtCodeOrId)
            ->orWhere('code', (string) $districtCodeOrId)
            ->first();

        if ($district) {
            return $district;
        }

        // If not found and we don't have basic hierarchy details, fetch from EMSIFA
        if (empty($districtName) || empty($regencyCode)) {
            return null;
        }

        $districtTitle = Str::title(strtolower(trim($districtName)));
        $regencyTitle = Str::title(strtolower(trim($regencyName ?? 'Kabupaten/Kota')));
        $provinceTitle = Str::title(strtolower(trim($provinceName ?? 'Provinsi')));
        $provinceCode = $provinceCode ?: substr((string) $regencyCode, 0, 2);

        // 1. Ensure Province exists
        $province = Province::firstOrCreate(
            ['code' => (string) $provinceCode],
            [
                'name' => $provinceTitle,
                'timezone' => self::getTimezoneByProvinceCode((string) $provinceCode),
            ]
        );

        // 2. Ensure Regency exists
        $isKota = str_starts_with(strtoupper($regencyName ?? ''), 'KOTA');
        $regency = Regency::firstOrCreate(
            ['code' => (string) $regencyCode],
            [
                'province_id' => $province->id,
                'name' => $regencyTitle,
                'type' => $isKota ? 'kota' : 'kabupaten',
            ]
        );

        // 3. Ensure District exists
        return District::firstOrCreate(
            ['code' => (string) $districtCodeOrId],
            [
                'regency_id' => $regency->id,
                'name' => $districtTitle,
            ]
        );
    }
}
