<?php

namespace Tests\Feature;

use App\Enums\ListingStatus;
use App\Models\Category;
use App\Models\District;
use App\Models\FoodListing;
use App\Models\Province;
use App\Models\Regency;
use App\Models\SiteSetting;
use App\Models\User;
use App\Services\ClaimService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class AdminSettingsTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $regularUser;
    protected User $donorUser;
    protected District $district;
    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();
        SiteSetting::clearCache();

        $province = Province::create(['code' => '33', 'name' => 'Jawa Tengah', 'timezone' => 'Asia/Jakarta']);
        $regency = Regency::create(['province_id' => $province->id, 'code' => '3374', 'name' => 'Kota Semarang', 'type' => 'kota']);
        $this->district = District::create([
            'regency_id' => $regency->id,
            'code' => '337401',
            'name' => 'Tembalang',
            'latitude' => -7.05194,
            'longitude' => 110.43778,
        ]);

        $this->category = Category::create([
            'name' => 'Makanan Berat & Nasi',
            'slug' => 'makanan-berat',
            'icon' => '🍚',
            'is_active' => true,
        ]);

        $this->admin = User::create([
            'name' => 'Admin Website',
            'email' => 'admin@lokawaste.test',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'district_id' => $this->district->id,
        ]);
        $this->admin->role = 'admin';
        $this->admin->is_active = true;
        $this->admin->save();

        $this->regularUser = User::create([
            'name' => 'Warga Biasa',
            'email' => 'warga@lokawaste.test',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'district_id' => $this->district->id,
        ]);
        $this->regularUser->is_active = true;
        $this->regularUser->save();

        $this->donorUser = User::create([
            'name' => 'Donatur Resto',
            'email' => 'donatur@lokawaste.test',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'district_id' => $this->district->id,
        ]);
        $this->donorUser->is_active = true;
        $this->donorUser->save();
    }

    public function test_non_admin_cannot_access_or_update_site_settings(): void
    {
        $this->actingAs($this->regularUser)
            ->get(route('admin.settings'))
            ->assertForbidden();

        $this->actingAs($this->regularUser)
            ->post(route('admin.settings.update'), [
                'site_name' => 'HackedName',
            ])
            ->assertForbidden();
    }

    public function test_admin_can_update_site_settings_and_reflect_across_pages(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.settings'))
            ->assertOk()
            ->assertSee('Pengaturan Website');

        $payload = [
            'site_name' => 'SajiLokal',
            'site_tagline' => 'Pangan Komunitas Bersih',
            'site_description' => 'Platform pangan komunitas bebas pemborosan.',
            'home_title' => 'SajiLokal — Peta Berbagi Makanan',
            'home_sidebar_title' => 'Stok Pangan Sekitar',
            'home_search_placeholder' => 'Cari area di Nusantara...',
            'default_map_lat' => '-6.20000',
            'default_map_lng' => '106.81666',
            'default_map_radius_km' => '10',
            'catalog_hero_title' => 'Etalase Surplus Komunitas',
            'catalog_hero_subtitle' => 'Ambil porsi layak sebelum kedaluwarsa.',
            'campaigns_hero_title' => 'Aksi Galang Pangan Daerah',
            'campaigns_hero_subtitle' => 'Distribusi pangan bersama relawan aktif.',
            'max_active_claims_per_user' => 2,
            'no_show_penalty_threshold' => 4,
            'no_show_restriction_days' => 14,
            'esg_co2_per_portion' => 1.25,
            'esg_water_per_portion' => 400,
            'esg_value_per_portion' => 20000,
            'contact_email' => 'cs@sajilokal.id',
            'contact_whatsapp' => '6281234567890',
            'announcement_enabled' => '1',
            'announcement_text' => 'Kegiatan berbagi akbar akhir pekan ini!',
            'announcement_url' => 'https://example.com/info',
        ];

        $this->actingAs($this->admin)
            ->post(route('admin.settings.update'), $payload)
            ->assertRedirect();

        $this->assertSame('SajiLokal', site_setting('site_name'));
        $this->assertSame('10', site_setting('default_map_radius_km'));

        // Public pages immediately show updated branding, announcement banner, and hero texts
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('SajiLokal — Peta Berbagi Makanan')
            ->assertSee('Kegiatan berbagi akbar akhir pekan ini!')
            ->assertSee('Pangan Komunitas Bersih');

        $this->get(route('catalog.index'))
            ->assertOk()
            ->assertSee('Etalase Surplus Komunitas')
            ->assertSee('Ambil porsi layak sebelum kedaluwarsa.')
            ->assertSee('cs@sajilokal.id');

        $this->get(route('campaigns.index'))
            ->assertOk()
            ->assertSee('Aksi Galang Pangan Daerah')
            ->assertSee('Distribusi pangan bersama relawan aktif.');

        // Logout to test guest login page
        auth()->logout();
        $this->get(route('login'))
            ->assertOk()
            ->assertSee('Masuk ke SajiLokal');
    }

    public function test_claim_service_respects_dynamic_max_active_claims_setting(): void
    {
        SiteSetting::setMany(['max_active_claims_per_user' => '1'], 'operational');

        $listing1 = FoodListing::create([
            'user_id' => $this->donorUser->id,
            'category_id' => $this->category->id,
            'district_id' => $this->district->id,
            'title' => 'Nasi Kotak Pertama',
            'description' => 'Layak makan',
            'food_type' => 'masakan',
            'portions_total' => 5,
            'portions_left' => 5,
            'max_portions_per_claim' => 2,
            'halal_claim' => true,
            'available_from' => now()->subMinute(),
            'expires_at' => now()->addHours(3),
            'pickup_address' => 'Jl. Prof Sudarto',
            'latitude' => -7.05194,
            'longitude' => 110.43778,
            'public_latitude' => -7.05194,
            'public_longitude' => 110.43778,
            'status' => ListingStatus::AVAILABLE->value,
        ]);

        $listing2 = FoodListing::create([
            'user_id' => $this->donorUser->id,
            'category_id' => $this->category->id,
            'district_id' => $this->district->id,
            'title' => 'Nasi Kotak Kedua',
            'description' => 'Layak makan',
            'food_type' => 'masakan',
            'portions_total' => 5,
            'portions_left' => 5,
            'max_portions_per_claim' => 2,
            'halal_claim' => true,
            'available_from' => now()->subMinute(),
            'expires_at' => now()->addHours(3),
            'pickup_address' => 'Jl. Banjarsari',
            'latitude' => -7.05194,
            'longitude' => 110.43778,
            'public_latitude' => -7.05194,
            'public_longitude' => 110.43778,
            'status' => ListingStatus::AVAILABLE->value,
        ]);

        $service = app(ClaimService::class);
        $service->create($this->regularUser, $listing1, 1);

        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('Maksimal 1 klaim aktif.');

        $service->create($this->regularUser, $listing2, 1);
    }

    public function test_admin_can_upload_reset_logo_and_reset_all_settings(): void
    {
        Storage::fake('public');

        $payload = [
            'site_name' => 'CustomBrand',
            'site_tagline' => 'Custom Tagline',
            'site_description' => 'Deskripsi custom.',
            'site_logo' => UploadedFile::fake()->image('logo.png', 180, 180),
            'site_favicon' => UploadedFile::fake()->image('favicon.png', 64, 64),
            'home_title' => 'CustomBrand — Peta',
            'home_sidebar_title' => 'Pangan',
            'home_search_placeholder' => 'Cari...',
            'default_map_lat' => '-7.05194',
            'default_map_lng' => '110.43778',
            'default_map_radius_km' => '5',
            'catalog_hero_title' => 'Katalog Custom',
            'catalog_hero_subtitle' => 'Sub',
            'campaigns_hero_title' => 'Galang Custom',
            'campaigns_hero_subtitle' => 'Sub',
            'max_active_claims_per_user' => 3,
            'no_show_penalty_threshold' => 3,
            'no_show_restriction_days' => 7,
            'esg_co2_per_portion' => 0.85,
            'esg_water_per_portion' => 320,
            'esg_value_per_portion' => 15000,
        ];

        $this->actingAs($this->admin)
            ->post(route('admin.settings.update'), $payload)
            ->assertRedirect();

        $logoPath = SiteSetting::get('site_logo_path');
        $faviconPath = SiteSetting::get('site_favicon_path');

        $this->assertNotNull($logoPath);
        $this->assertNotNull($faviconPath);
        Storage::disk('public')->assertExists($logoPath);
        Storage::disk('public')->assertExists($faviconPath);

        // BrandLogo component renders uploaded image instead of fallback SVG
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('storage/' . $logoPath);

        // Reset logo + favicon back to SVG default
        $this->actingAs($this->admin)
            ->post(route('admin.settings.reset-logo'), ['target' => 'both'])
            ->assertRedirect();

        $this->assertNull(SiteSetting::logoUrl());
        $this->assertNull(SiteSetting::faviconUrl());
        Storage::disk('public')->assertMissing($logoPath);
        Storage::disk('public')->assertMissing($faviconPath);

        // Reset all settings restores factory default site name
        $this->actingAs($this->admin)
            ->post(route('admin.settings.reset-all'))
            ->assertRedirect();

        $this->assertSame('LokaWaste', site_setting('site_name'));
    }
}
