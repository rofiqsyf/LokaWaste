<?php

namespace Tests\Feature;

use App\Models\District;
use App\Models\Province;
use App\Models\Regency;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GeocodingTest extends TestCase
{
    use RefreshDatabase;

    public function test_geocoding_search_returns_multiple_suggestions()
    {
        // Seed database fallback
        $jateng = Province::create(['code' => '33', 'name' => 'Jawa Tengah']);
        $smg = Regency::create(['province_id' => $jateng->id, 'code' => '3374', 'name' => 'Kota Semarang', 'type' => 'kota']);
        District::create(['regency_id' => $smg->id, 'code' => '337401', 'name' => 'Tembalang', 'latitude' => -7.05194, 'longitude' => 110.43778]);

        $response = $this->getJson(route('api.geocoding.search', ['q' => 'Tembalang']));

        $response->assertStatus(200);
        $data = $response->json();
        $this->assertIsArray($data);
        $this->assertNotEmpty($data);

        $first = $data[0];
        $this->assertArrayHasKey('title', $first);
        $this->assertArrayHasKey('lat', $first);
        $this->assertArrayHasKey('lng', $first);
    }

    public function test_geocoding_search_short_query_returns_empty_array()
    {
        $response = $this->getJson(route('api.geocoding.search', ['q' => 'a']));
        $response->assertStatus(200);
        $this->assertEquals([], $response->json());
    }

    public function test_geocoding_reverse_returns_readable_address()
    {
        $response = $this->getJson(route('api.geocoding.reverse', [
            'lat' => -7.05194,
            'lng' => 110.43778,
        ]));

        $response->assertStatus(200);
        $data = $response->json();
        $this->assertArrayHasKey('short_address', $data);
        $this->assertArrayHasKey('display_name', $data);
        $this->assertNotEmpty($data['short_address']);
    }
}
