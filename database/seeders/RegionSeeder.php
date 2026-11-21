<?php

namespace Database\Seeders;

use App\Models\District;
use App\Models\Province;
use App\Models\Regency;
use Illuminate\Database\Seeder;

class RegionSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Jawa Tengah (WIB)
        $jateng = Province::firstOrCreate(
            ['code' => '33'],
            ['name' => 'Jawa Tengah', 'timezone' => 'Asia/Jakarta']
        );

        // 2. Bali (WITA)
        $bali = Province::firstOrCreate(
            ['code' => '51'],
            ['name' => 'Bali', 'timezone' => 'Asia/Makassar']
        );

        // 3. Papua (WIT)
        $papua = Province::firstOrCreate(
            ['code' => '94'],
            ['name' => 'Papua', 'timezone' => 'Asia/Jayapura']
        );

        // Regencies
        $kotaSmg = Regency::firstOrCreate(
            ['code' => '3374'],
            ['province_id' => $jateng->id, 'name' => 'Kota Semarang', 'type' => 'kota']
        );

        $kabDemak = Regency::firstOrCreate(
            ['code' => '3321'],
            ['province_id' => $jateng->id, 'name' => 'Kabupaten Demak', 'type' => 'kabupaten']
        );

        $kotaDps = Regency::firstOrCreate(
            ['code' => '5171'],
            ['province_id' => $bali->id, 'name' => 'Kota Denpasar', 'type' => 'kota']
        );

        $kotaJpr = Regency::firstOrCreate(
            ['code' => '9471'],
            ['province_id' => $papua->id, 'name' => 'Kota Jayapura', 'type' => 'kota']
        );

        // Districts for Kota Semarang
        $districtsSmg = [
            ['code' => '337401', 'name' => 'Tembalang', 'latitude' => -7.0519400, 'longitude' => 110.4377800],
            ['code' => '337402', 'name' => 'Banyumanik', 'latitude' => -7.0672200, 'longitude' => 110.4144400],
            ['code' => '337403', 'name' => 'Candisari', 'latitude' => -7.0097200, 'longitude' => 110.4288900],
            ['code' => '337404', 'name' => 'Gajahmungkur', 'latitude' => -7.0163900, 'longitude' => 110.4072200],
            ['code' => '337405', 'name' => 'Semarang Barat', 'latitude' => -6.9855600, 'longitude' => 110.3861100],
            ['code' => '337406', 'name' => 'Semarang Tengah', 'latitude' => -6.9819400, 'longitude' => 110.4222200],
        ];

        foreach ($districtsSmg as $d) {
            District::firstOrCreate(
                ['code' => $d['code']],
                ['regency_id' => $kotaSmg->id, 'name' => $d['name'], 'latitude' => $d['latitude'], 'longitude' => $d['longitude']]
            );
        }

        // Kabupaten Demak
        District::firstOrCreate(
            ['code' => '332101'],
            ['regency_id' => $kabDemak->id, 'name' => 'Mranggen', 'latitude' => -7.0261100, 'longitude' => 110.5147200]
        );

        // Bali
        District::firstOrCreate(
            ['code' => '517101'],
            ['regency_id' => $kotaDps->id, 'name' => 'Denpasar Selatan', 'latitude' => -8.6947200, 'longitude' => 115.2280600]
        );

        // Papua
        District::firstOrCreate(
            ['code' => '947101'],
            ['regency_id' => $kotaJpr->id, 'name' => 'Jayapura Selatan', 'latitude' => -2.5719400, 'longitude' => 140.7047200]
        );
    }
}
