<?php

namespace Tests\Feature;

use App\Models\District;
use App\Models\Province;
use App\Models\Regency;
use App\Models\User;
use App\Services\EmsifaWilayahService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WilayahEmsifaTest extends TestCase
{
    use RefreshDatabase;

    public function test_wilayah_api_provinces_endpoint_returns_json()
    {
        $response = $this->getJson(route('api.wilayah.provinces'));

        $response->assertStatus(200);
        $data = $response->json();
        $this->assertIsArray($data);
        $this->assertNotEmpty($data);
        $this->assertArrayHasKey('id', $data[0]);
        $this->assertArrayHasKey('name', $data[0]);
    }

    public function test_wilayah_api_regencies_and_districts_endpoints()
    {
        // Central Java (code: 33)
        $regResponse = $this->getJson(route('api.wilayah.regencies', ['provinceId' => '33']));
        $regResponse->assertStatus(200);
        $regData = $regResponse->json();
        $this->assertIsArray($regData);
        $this->assertNotEmpty($regData);

        // Kota Semarang (code: 3374)
        $distResponse = $this->getJson(route('api.wilayah.districts', ['regencyId' => '3374']));
        $distResponse->assertStatus(200);
        $distData = $distResponse->json();
        $this->assertIsArray($distData);
        $this->assertNotEmpty($distData);
    }

    public function test_user_can_register_with_emsifa_district_selection()
    {
        $payload = [
            'name' => 'Budi Santoso',
            'email' => 'budi.santoso@locabite.test',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'phone' => '081234567890',
            'province_id' => '33',
            'province_name' => 'Jawa Tengah',
            'regency_id' => '3374',
            'regency_name' => 'Kota Semarang',
            'district_id' => '3374010',
            'district_code' => '3374010',
            'district_name' => 'Mijen',
            'organization_name' => 'Warung Berkah Budi',
        ];

        $response = $this->post(route('register'), $payload);

        $response->assertRedirect(route('home'));
        $this->assertAuthenticated();

        $user = User::where('email', 'budi.santoso@locabite.test')->first();
        $this->assertNotNull($user);
        $this->assertNotNull($user->district_id);

        $district = District::find($user->district_id);
        $this->assertNotNull($district);
        $this->assertEquals('3374010', $district->code);
        $this->assertEquals('Mijen', $district->name);

        $regency = $district->regency;
        $this->assertNotNull($regency);
        $this->assertEquals('3374', $regency->code);
        $this->assertEquals('kota', $regency->type);

        $province = $regency->province;
        $this->assertNotNull($province);
        $this->assertEquals('33', $province->code);
        $this->assertEquals('Asia/Jakarta', $province->timezone);
    }

    public function test_timezone_mapping_for_indonesian_provinces()
    {
        // WIB: Jawa Tengah (33)
        $this->assertEquals('Asia/Jakarta', EmsifaWilayahService::getTimezoneByProvinceCode('33'));
        // WITA: Bali (51)
        $this->assertEquals('Asia/Makassar', EmsifaWilayahService::getTimezoneByProvinceCode('51'));
        // WIT: Papua (94)
        $this->assertEquals('Asia/Jayapura', EmsifaWilayahService::getTimezoneByProvinceCode('94'));
    }
}
