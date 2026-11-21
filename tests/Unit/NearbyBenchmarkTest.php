<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\District;
use App\Models\FoodListing;
use App\Models\Province;
use App\Models\Regency;
use App\Models\User;
use App\Services\NearbyListingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class NearbyBenchmarkTest extends TestCase
{
    use RefreshDatabase;

    public function test_nfr01_nearby_search_performance_benchmark()
    {
        // Setup base data
        $jateng = Province::create(['code' => '33', 'name' => 'Jawa Tengah', 'timezone' => 'Asia/Jakarta']);
        $kotaSmg = Regency::create(['province_id' => $jateng->id, 'code' => '3374', 'name' => 'Kota Semarang', 'type' => 'kota']);
        $district = District::create(['regency_id' => $kotaSmg->id, 'code' => '337401', 'name' => 'Tembalang', 'latitude' => -7.05194, 'longitude' => 110.43778]);
        $cat = Category::create(['name' => 'Nasi', 'slug' => 'nasi', 'icon' => 'rice']);
        $user = User::create(['name' => 'User', 'email' => 'u@t.com', 'password' => Hash::make('password'), 'role' => 'user']);

        // Insert 100 listings around Tembalang
        $now = now();
        $batch = [];
        for ($i = 0; $i < 100; $i++) {
            $batch[] = [
                'user_id' => $user->id,
                'category_id' => $cat->id,
                'district_id' => $district->id,
                'title' => "Makanan {$i}",
                'description' => "Deskripsi {$i}",
                'food_type' => 'cooked',
                'portions_total' => 10,
                'portions_left' => 10,
                'max_portions_per_claim' => 3,
                'pickup_address' => 'Jl. Test',
                'latitude' => -7.0520 + (random_int(-50, 50) / 1000),
                'longitude' => 110.4380 + (random_int(-50, 50) / 1000),
                'public_latitude' => -7.0520 + (random_int(-50, 50) / 1000),
                'public_longitude' => 110.4380 + (random_int(-50, 50) / 1000),
                'available_from' => $now->toDateTimeString(),
                'expires_at' => $now->copy()->addHours(3)->toDateTimeString(),
                'status' => 'available',
                'created_at' => $now->toDateTimeString(),
                'updated_at' => $now->toDateTimeString(),
            ];
        }
        FoodListing::insert($batch);

        $service = new NearbyListingService();

        $start = microtime(true);
        $results = $service->search(-7.05194, 110.43778, 5);
        $durationMs = (microtime(true) - $start) * 1000;

        // Target: NFR-01 < 500 ms
        $this->assertLessThan(500, $durationMs, "Query nearby took {$durationMs}ms which exceeds 500ms target.");
        $this->assertNotEmpty($results);
    }
}
