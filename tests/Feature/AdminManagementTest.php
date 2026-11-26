<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\District;
use App\Models\FoodListing;
use App\Models\Province;
use App\Models\Regency;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $regularUser;
    protected FoodListing $listing;

    protected function setUp(): void
    {
        parent::setUp();

        $province = Province::create(['code' => '33', 'name' => 'Jawa Tengah', 'timezone' => 'Asia/Jakarta']);
        $regency = Regency::create(['province_id' => $province->id, 'code' => '3374', 'name' => 'Kota Semarang', 'type' => 'kota']);
        $district = District::create(['regency_id' => $regency->id, 'code' => '337401', 'name' => 'Tembalang']);

        $category = Category::create([
            'name' => 'Makanan Berat & Nasi',
            'slug' => 'makanan-berat',
            'icon' => '🍚',
            'is_active' => true,
        ]);

        $this->admin = User::create([
            'name' => 'Super Administrator',
            'email' => 'admin@lokawaste.test',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'email_verified_at' => now(),
            'district_id' => $district->id,
            'is_active' => true,
        ]);
        $this->admin->role = 'admin';
        $this->admin->save();

        $this->regularUser = User::create([
            'name' => 'Warga Tembalang',
            'email' => 'warga@lokawaste.test',
            'password' => Hash::make('password'),
            'role' => 'user',
            'email_verified_at' => now(),
            'district_id' => $district->id,
        ]);
        $this->regularUser->is_active = true;
        $this->regularUser->no_show_count = 2;
        $this->regularUser->claim_restricted_until = now()->addDays(3);
        $this->regularUser->save();

        $this->listing = FoodListing::create([
            'user_id' => $this->regularUser->id,
            'category_id' => $category->id,
            'district_id' => $district->id,
            'title' => 'Nasi Gudeg Surplus',
            'description' => 'Gudeg komplit siap santap.',
            'food_type' => 'cooked',
            'portions_total' => 15,
            'portions_left' => 15,
            'max_portions_per_claim' => 3,
            'pickup_address' => 'Jl. Sirojudin No. 5',
            'public_latitude' => -7.05,
            'public_longitude' => 110.43,
            'latitude' => -7.052,
            'longitude' => 110.438,
            'available_from' => now()->subMinutes(15),
            'expires_at' => now()->addHours(5),
            'status' => 'available',
        ]);
    }

    public function test_non_admin_cannot_access_admin_portal(): void
    {
        $response = $this->actingAs($this->regularUser)->get(route('admin.dashboard'));
        $response->assertStatus(403);

        $responseListings = $this->actingAs($this->regularUser)->get(route('admin.listings'));
        $responseListings->assertStatus(403);

        $responseUsers = $this->actingAs($this->regularUser)->get(route('admin.users'));
        $responseUsers->assertStatus(403);
    }

    public function test_admin_can_view_users_management(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.users'));
        $response->assertStatus(200);
        $response->assertSee('Manajemen Pengguna');
        $response->assertSee('Warga Tembalang');
    }

    public function test_admin_can_toggle_user_verification_and_active_status(): void
    {
        $this->assertFalse((bool) $this->regularUser->is_verified);

        // Toggle Verify
        $this->actingAs($this->admin)->post(route('admin.users.toggle-verify', $this->regularUser->id));
        $this->assertTrue((bool) $this->regularUser->fresh()->is_verified);

        // Toggle Active
        $this->assertTrue((bool) $this->regularUser->is_active);
        $this->actingAs($this->admin)->post(route('admin.users.toggle-active', $this->regularUser->id));
        $this->assertFalse((bool) $this->regularUser->fresh()->is_active);
    }

    public function test_admin_can_reset_user_penalties(): void
    {
        $this->assertEquals(2, $this->regularUser->no_show_count);
        $this->assertTrue($this->regularUser->isRestrictedFromClaiming());

        $this->actingAs($this->admin)->post(route('admin.users.reset-penalties', $this->regularUser->id));

        $freshUser = $this->regularUser->fresh();
        $this->assertEquals(0, $freshUser->no_show_count);
        $this->assertNull($freshUser->claim_restricted_until);
        $this->assertFalse($freshUser->isRestrictedFromClaiming());
    }

    public function test_admin_can_view_and_moderate_food_listings(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.listings'));
        $response->assertStatus(200);
        $response->assertSee('Manajemen Sumber Daya Pangan');
        $response->assertSee('Nasi Gudeg Surplus');

        // Toggle takedown
        $this->actingAs($this->admin)->post(route('admin.listings.toggle-takedown', $this->listing->id));
        $this->assertEquals('taken_down', $this->listing->fresh()->status);

        // Restore takedown
        $this->actingAs($this->admin)->post(route('admin.listings.toggle-takedown', $this->listing->id));
        $this->assertEquals('available', $this->listing->fresh()->status);

        // Delete listing
        $this->actingAs($this->admin)->delete(route('admin.listings.destroy', $this->listing->id));
        $this->assertNull(FoodListing::find($this->listing->id));
    }
}
