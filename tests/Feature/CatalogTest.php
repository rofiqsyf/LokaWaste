<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\District;
use App\Models\FoodListing;
use App\Models\Province;
use App\Models\Regency;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Category $catBread;
    protected Category $catRice;
    protected District $district;

    protected function setUp(): void
    {
        parent::setUp();

        $prov = Province::firstOrCreate(
            ['code' => '33'],
            ['name' => 'Jawa Tengah', 'timezone' => 'Asia/Jakarta']
        );

        $reg = Regency::firstOrCreate(
            ['code' => '3374'],
            ['province_id' => $prov->id, 'name' => 'Kota Semarang', 'type' => 'kota', 'latitude' => -7.005, 'longitude' => 110.438]
        );

        $this->district = District::firstOrCreate(
            ['code' => '337401'],
            ['regency_id' => $reg->id, 'name' => 'Tembalang', 'latitude' => -7.05194, 'longitude' => 110.43778]
        );

        $this->catBread = Category::firstOrCreate(
            ['slug' => 'roti-kue'],
            ['name' => 'Roti & Kue', 'icon' => 'bread']
        );

        $this->catRice = Category::firstOrCreate(
            ['slug' => 'makanan-berat'],
            ['name' => 'Makanan Berat', 'icon' => 'rice']
        );

        $this->user = User::factory()->create([
            'district_id' => $this->district->id,
            'role' => 'user',
            'is_active' => true,
        ]);
    }

    public function test_catalog_page_loads_successfully(): void
    {
        $response = $this->get(route('catalog.index'));
        $response->assertStatus(200);
        $response->assertSee('Katalog Surplus Pangan');
        $response->assertSee('Ambil di Peta');
    }

    public function test_listings_index_alias_works(): void
    {
        $response = $this->get('/listings');
        $response->assertStatus(200);
        $response->assertSee('Katalog Surplus Pangan');
    }

    public function test_catalog_shows_active_listings_only(): void
    {
        // 1. Available listing
        $activeListing = FoodListing::create([
            'user_id' => $this->user->id,
            'category_id' => $this->catBread->id,
            'district_id' => $this->district->id,
            'title' => 'Roti Sobek Cokelat',
            'description' => 'Masih fresh hari ini',
            'food_type' => 'cooked',
            'portions_total' => 10,
            'portions_left' => 10,
            'max_portions_per_claim' => 2,
            'pickup_address' => 'Jl. Banjarsari No. 10',
            'latitude' => -7.052,
            'longitude' => 110.438,
            'public_latitude' => -7.053,
            'public_longitude' => 110.439,
            'available_from' => now()->subMinutes(10),
            'expires_at' => now()->addHours(3),
            'status' => 'available',
        ]);

        // 2. Expired listing
        $expiredListing = FoodListing::create([
            'user_id' => $this->user->id,
            'category_id' => $this->catBread->id,
            'district_id' => $this->district->id,
            'title' => 'Roti Tawar Kadaluarsa',
            'description' => 'Kadaluarsa kemarin',
            'food_type' => 'cooked',
            'portions_total' => 5,
            'portions_left' => 5,
            'max_portions_per_claim' => 1,
            'pickup_address' => 'Jl. Banjarsari No. 12',
            'latitude' => -7.052,
            'longitude' => 110.438,
            'public_latitude' => -7.053,
            'public_longitude' => 110.439,
            'available_from' => now()->subHours(5),
            'expires_at' => now()->subHour(),
            'status' => 'expired',
        ]);

        $response = $this->get(route('catalog.index'));
        $response->assertStatus(200);
        $response->assertSee('Roti Sobek Cokelat');
        $response->assertDontSee('Roti Tawar Kadaluarsa');
    }

    public function test_catalog_search_and_filters(): void
    {
        // Bread listing (halal)
        FoodListing::create([
            'user_id' => $this->user->id,
            'category_id' => $this->catBread->id,
            'district_id' => $this->district->id,
            'title' => 'Croissant Almond Premium',
            'description' => 'Kue manis',
            'food_type' => 'cooked',
            'halal_claim' => true,
            'is_vegetarian' => true,
            'portions_total' => 4,
            'portions_left' => 4,
            'max_portions_per_claim' => 2,
            'pickup_address' => 'Jl. Mulawarman',
            'latitude' => -7.052,
            'longitude' => 110.438,
            'public_latitude' => -7.053,
            'public_longitude' => 110.439,
            'available_from' => now()->subMinutes(10),
            'expires_at' => now()->addHours(2),
            'status' => 'available',
        ]);

        // Rice listing
        FoodListing::create([
            'user_id' => $this->user->id,
            'category_id' => $this->catRice->id,
            'district_id' => $this->district->id,
            'title' => 'Nasi Kotak Ayam Bakar',
            'description' => 'Menu katering',
            'food_type' => 'cooked',
            'halal_claim' => true,
            'is_vegetarian' => false,
            'portions_total' => 8,
            'portions_left' => 8,
            'max_portions_per_claim' => 2,
            'pickup_address' => 'Jl. Prof Soedarto',
            'latitude' => -7.051,
            'longitude' => 110.437,
            'public_latitude' => -7.052,
            'public_longitude' => 110.438,
            'available_from' => now()->subMinutes(10),
            'expires_at' => now()->addHours(4),
            'status' => 'available',
        ]);

        // Filter by category
        $resCat = $this->get(route('catalog.index', ['category' => 'roti-kue']));
        $resCat->assertSee('Croissant Almond Premium');
        $resCat->assertDontSee('Nasi Kotak Ayam Bakar');

        // Filter by search query
        $resQ = $this->get(route('catalog.index', ['q' => 'Nasi Kotak']));
        $resQ->assertSee('Nasi Kotak Ayam Bakar');
        $resQ->assertDontSee('Croissant Almond');

        // Filter vegetarian
        $resVeg = $this->get(route('catalog.index', ['vegetarian' => 1]));
        $resVeg->assertSee('Croissant Almond Premium');
        $resVeg->assertDontSee('Nasi Kotak Ayam Bakar');
    }

    public function test_home_page_accepts_focus_parameter_handoff(): void
    {
        $listing = FoodListing::create([
            'user_id' => $this->user->id,
            'category_id' => $this->catBread->id,
            'district_id' => $this->district->id,
            'title' => 'Donat Kentang Spesial',
            'description' => 'Donat tabur gula',
            'food_type' => 'cooked',
            'portions_total' => 6,
            'portions_left' => 6,
            'max_portions_per_claim' => 2,
            'pickup_address' => 'Jl. Sirojudin',
            'latitude' => -7.052,
            'longitude' => 110.438,
            'public_latitude' => -7.053,
            'public_longitude' => 110.439,
            'available_from' => now()->subMinutes(5),
            'expires_at' => now()->addHours(2),
            'status' => 'available',
        ]);

        // Hand-off redirect test
        $response = $this->get(route('home', [
            'focus' => $listing->id,
            'lat' => $listing->public_latitude,
            'lng' => $listing->public_longitude,
        ]));

        $response->assertStatus(200);
        $response->assertViewHas('focusId', (string)$listing->id);
        $response->assertViewHas('initialLat', (float)$listing->public_latitude);
        $response->assertViewHas('initialLng', (float)$listing->public_longitude);
        $response->assertSee('Titik Penjemputan Makanan Aktif');
    }
}
