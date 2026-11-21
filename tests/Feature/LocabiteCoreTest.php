<?php

namespace Tests\Feature;

use App\Enums\AreaStatus;
use App\Models\Category;
use App\Models\Claim;
use App\Models\District;
use App\Models\FoodCampaign;
use App\Models\FoodListing;
use App\Models\Province;
use App\Models\Regency;
use App\Models\User;
use App\Models\VolunteerApplication;
use App\Models\VolunteerArea;
use App\Services\ClaimService;
use App\Services\NearbyListingService;
use App\Services\VolunteerApplicationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LocabiteCoreTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $user1;
    protected User $user2;
    protected User $volunteer;
    protected District $tembalang;
    protected District $banyumanik;
    protected District $mranggen;
    protected Regency $kotaSmg;
    protected Regency $kabDemak;
    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed regions
        $jateng = Province::create(['code' => '33', 'name' => 'Jawa Tengah', 'timezone' => 'Asia/Jakarta']);
        $papua = Province::create(['code' => '94', 'name' => 'Papua', 'timezone' => 'Asia/Jayapura']);

        $this->kotaSmg = Regency::create(['province_id' => $jateng->id, 'code' => '3374', 'name' => 'Kota Semarang', 'type' => 'kota']);
        $this->kabDemak = Regency::create(['province_id' => $jateng->id, 'code' => '3321', 'name' => 'Kabupaten Demak', 'type' => 'kabupaten']);

        $this->tembalang = District::create(['regency_id' => $this->kotaSmg->id, 'code' => '337401', 'name' => 'Tembalang', 'latitude' => -7.05194, 'longitude' => 110.43778]);
        $this->banyumanik = District::create(['regency_id' => $this->kotaSmg->id, 'code' => '337402', 'name' => 'Banyumanik', 'latitude' => -7.06722, 'longitude' => 110.41444]);
        $this->mranggen = District::create(['regency_id' => $this->kabDemak->id, 'code' => '332101', 'name' => 'Mranggen', 'latitude' => -7.02611, 'longitude' => 110.51472]);

        $this->category = Category::create(['name' => 'Makanan Berat', 'slug' => 'makanan-berat', 'icon' => 'rice']);

        $this->admin = User::create([
            'name' => 'Admin Test',
            'email' => 'admin@test.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);
        $this->admin->role = 'admin';
        $this->admin->save();

        $this->user1 = User::create([
            'name' => 'Pemberi Makanan',
            'email' => 'user1@test.com',
            'password' => Hash::make('password'),
            'role' => 'user',
            'email_verified_at' => now(),
            'district_id' => $this->tembalang->id,
        ]);

        $this->user2 = User::create([
            'name' => 'Pengambil Makanan',
            'email' => 'user2@test.com',
            'password' => Hash::make('password'),
            'role' => 'user',
            'email_verified_at' => now(),
            'district_id' => $this->tembalang->id,
        ]);

        $this->volunteer = User::create([
            'name' => 'Relawan Tembalang',
            'email' => 'volunteer@test.com',
            'password' => Hash::make('password'),
            'role' => 'user',
            'email_verified_at' => now(),
            'district_id' => $this->tembalang->id,
        ]);

        $app = VolunteerApplication::create([
            'user_id' => $this->volunteer->id,
            'regency_id' => $this->kotaSmg->id,
            'motivation' => 'Peduli pangan sekitar Tembalang.',
        ]);

        VolunteerArea::create([
            'application_id' => $app->id,
            'user_id' => $this->volunteer->id,
            'regency_id' => $this->kotaSmg->id,
            'district_id' => $this->tembalang->id,
            'status' => AreaStatus::APPROVED->value,
            'decided_by' => $this->admin->id,
            'decided_at' => now(),
        ]);
    }

    /**
     * TC-01: User creates valid listing with permanent public jittered coordinates
     */
    public function test_tc01_user_creates_valid_listing()
    {
        $response = $this->actingAs($this->user1)->post(route('listings.store'), [
            'title' => 'Nasi Gudeg Komplit 10 Porsi',
            'category_id' => $this->category->id,
            'portions_total' => 10,
            'max_portions_per_claim' => 3,
            'food_type' => 'cooked',
            'hours_valid' => 3,
            'pickup_address' => 'Jl. Banjarsari No. 10',
            'latitude' => -7.0520000,
            'longitude' => 110.4380000,
            'district_id' => $this->tembalang->id,
            'halal_claim' => 1,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('food_listings', [
            'title' => 'Nasi Gudeg Komplit 10 Porsi',
            'user_id' => $this->user1->id,
            'portions_left' => 10,
            'status' => 'available',
        ]);

        $listing = FoodListing::first();
        // Public coordinate is jittered from exact coordinate
        $this->assertNotEquals($listing->latitude, $listing->public_latitude);
        $this->assertNotEquals($listing->longitude, $listing->public_longitude);
    }

    /**
     * TC-05: Claiming own listing is rejected (403)
     */
    public function test_tc05_claiming_own_listing_is_rejected()
    {
        $listing = FoodListing::create([
            'user_id' => $this->user1->id,
            'category_id' => $this->category->id,
            'district_id' => $this->tembalang->id,
            'title' => 'Nasi Kotak Sendiri',
            'description' => 'Test',
            'food_type' => 'cooked',
            'portions_total' => 5,
            'portions_left' => 5,
            'max_portions_per_claim' => 2,
            'pickup_address' => 'Alamat Pribadi',
            'latitude' => -7.0520,
            'longitude' => 110.4380,
            'public_latitude' => -7.0530,
            'public_longitude' => 110.4390,
            'available_from' => now()->subMinute(),
            'expires_at' => now()->addHours(2),
            'status' => 'available',
        ]);

        $response = $this->actingAs($this->user1)->post(route('claims.store', $listing->id), [
            'portions' => 1,
        ]);

        $response->assertSessionHas('error', 'Ini listing Anda.');
        $this->assertDatabaseCount('claims', 0);
    }

    /**
     * TC-06: Single account can share and claim (two sides of one account)
     */
    public function test_tc06_single_account_can_share_and_claim()
    {
        // User 2 creates listing
        $listing = FoodListing::create([
            'user_id' => $this->user2->id,
            'category_id' => $this->category->id,
            'district_id' => $this->tembalang->id,
            'title' => 'Roti Manis',
            'description' => 'Test',
            'food_type' => 'packaged',
            'portions_total' => 5,
            'portions_left' => 5,
            'max_portions_per_claim' => 2,
            'pickup_address' => 'Jl. Pemuda',
            'latitude' => -7.0520,
            'longitude' => 110.4380,
            'public_latitude' => -7.0530,
            'public_longitude' => 110.4390,
            'available_from' => now()->subMinute(),
            'expires_at' => now()->addHours(2),
            'status' => 'available',
        ]);

        // User 1 claims User 2's listing
        $this->actingAs($this->user1)->post(route('claims.store', $listing->id), [
            'portions' => 2,
        ]);

        $this->assertDatabaseHas('claims', [
            'listing_id' => $listing->id,
            'user_id' => $this->user1->id,
            'portions_requested' => 2,
            'status' => 'pending',
        ]);
    }

    /**
     * TC-07 & TC-08: Haversine distance search with acos safety on identical points
     */
    public function test_tc07_tc08_haversine_distance_search_and_acos_safety()
    {
        $listing = FoodListing::create([
            'user_id' => $this->user1->id,
            'category_id' => $this->category->id,
            'district_id' => $this->tembalang->id,
            'title' => 'Titik Identik Test',
            'description' => 'Test',
            'food_type' => 'cooked',
            'portions_total' => 5,
            'portions_left' => 5,
            'max_portions_per_claim' => 2,
            'pickup_address' => 'Jl. Banjarsari',
            'latitude' => -7.0520000,
            'longitude' => 110.4380000,
            'public_latitude' => -7.0520000,
            'public_longitude' => 110.4380000,
            'available_from' => now()->subMinute(),
            'expires_at' => now()->addHours(2),
            'status' => 'available',
        ]);

        $service = new NearbyListingService();

        // Search from EXACT same point: acos(1.00000000001) is safely clamped to 1.0 (TC-08)
        $results = $service->search(-7.0520000, 110.4380000, 3);
        $this->assertCount(1, $results);
        $this->assertEquals(0.0, $results->first()->distance_km);
    }

    /**
     * TC-09: Duplicate active claim by same user on same listing rejected
     */
    public function test_tc09_duplicate_active_claim_rejected()
    {
        $listing = FoodListing::create([
            'user_id' => $this->user1->id,
            'category_id' => $this->category->id,
            'district_id' => $this->tembalang->id,
            'title' => 'Roti Gandum',
            'description' => 'Test',
            'food_type' => 'packaged',
            'portions_total' => 10,
            'portions_left' => 10,
            'max_portions_per_claim' => 3,
            'pickup_address' => 'Jl. Diponegoro',
            'latitude' => -7.0520,
            'longitude' => 110.4380,
            'public_latitude' => -7.0530,
            'public_longitude' => 110.4390,
            'available_from' => now()->subMinute(),
            'expires_at' => now()->addHours(2),
            'status' => 'available',
        ]);

        $claimService = new ClaimService();
        $claim1 = $claimService->create($this->user2, $listing, 2);
        $this->assertNotNull($claim1->id);

        // Attempt second active claim by user 2 on the same listing fails unique active_key
        $this->expectException(\Throwable::class);
        $claimService->create($this->user2, $listing, 1);
    }

    /**
     * TC-10: Max 3 active claims per user
     */
    public function test_tc10_max_3_active_claims_limit()
    {
        $claimService = new ClaimService();

        // Create 4 listings
        $listings = [];
        for ($i = 1; $i <= 4; $i++) {
            $listings[] = FoodListing::create([
                'user_id' => $this->user1->id,
                'category_id' => $this->category->id,
                'district_id' => $this->tembalang->id,
                'title' => "Listing {$i}",
                'description' => 'Test',
                'food_type' => 'packaged',
                'portions_total' => 5,
                'portions_left' => 5,
                'max_portions_per_claim' => 2,
                'pickup_address' => 'Jl. Test',
                'latitude' => -7.0520,
                'longitude' => 110.4380,
                'public_latitude' => -7.0530,
                'public_longitude' => 110.4390,
                'available_from' => now()->subMinute(),
                'expires_at' => now()->addHours(2),
                'status' => 'available',
            ]);
        }

        // Claim 1, 2, 3 succeed
        $claimService->create($this->user2, $listings[0], 1);
        $claimService->create($this->user2, $listings[1], 1);
        $claimService->create($this->user2, $listings[2], 1);

        // Claim 4 fails with 422 max 3 klaim aktif
        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        $claimService->create($this->user2, $listings[3], 1);
    }

    /**
     * TC-11: Provider approves partial portions
     */
    public function test_tc11_partial_portion_approval()
    {
        $listing = FoodListing::create([
            'user_id' => $this->user1->id,
            'category_id' => $this->category->id,
            'district_id' => $this->tembalang->id,
            'title' => 'Sayur Sop',
            'description' => 'Test',
            'food_type' => 'cooked',
            'portions_total' => 10,
            'portions_left' => 10,
            'max_portions_per_claim' => 5,
            'pickup_address' => 'Jl. Test',
            'latitude' => -7.0520,
            'longitude' => 110.4380,
            'public_latitude' => -7.0530,
            'public_longitude' => 110.4390,
            'available_from' => now()->subMinute(),
            'expires_at' => now()->addHours(2),
            'status' => 'available',
        ]);

        $claimService = new ClaimService();
        $claim = $claimService->create($this->user2, $listing, 4);

        // Owner approves 2 out of 4 requested
        $claimService->approve($claim, 2);

        $listing->refresh();
        $claim->refresh();

        $this->assertEquals('approved', $claim->status);
        $this->assertEquals(2, $claim->portions_approved);
        $this->assertEquals(8, $listing->portions_left);
    }

    /**
     * TC-12: Cancelling approved claim restores portions and resets listing status
     */
    public function test_tc12_cancelling_approved_claim_restores_portions()
    {
        $listing = FoodListing::create([
            'user_id' => $this->user1->id,
            'category_id' => $this->category->id,
            'district_id' => $this->tembalang->id,
            'title' => 'Porsi Terakhir',
            'description' => 'Test',
            'food_type' => 'cooked',
            'portions_total' => 2,
            'portions_left' => 2,
            'max_portions_per_claim' => 2,
            'pickup_address' => 'Jl. Test',
            'latitude' => -7.0520,
            'longitude' => 110.4380,
            'public_latitude' => -7.0530,
            'public_longitude' => 110.4390,
            'available_from' => now()->subMinute(),
            'expires_at' => now()->addHours(2),
            'status' => 'available',
        ]);

        $claimService = new ClaimService();
        $claim = $claimService->create($this->user2, $listing, 2);
        $claimService->approve($claim, 2);

        $listing->refresh();
        $this->assertEquals(0, $listing->portions_left);
        $this->assertEquals('reserved', $listing->status);

        // Cancel approved claim: portions restored!
        $claimService->cancelApproved($claim, $this->user2, 'Berhalangan hadir');

        $listing->refresh();
        $claim->refresh();

        $this->assertEquals('cancelled', $claim->status);
        $this->assertEquals(2, $listing->portions_left);
        $this->assertEquals('available', $listing->status);
    }

    /**
     * TC-13 & TC-14: Expiration scheduler transitions pending -> expired, approved -> no_show
     */
    public function test_tc13_tc14_expiration_scheduler_and_no_show()
    {
        $listing = FoodListing::create([
            'user_id' => $this->user1->id,
            'category_id' => $this->category->id,
            'district_id' => $this->tembalang->id,
            'title' => 'Makanan Segera Kedaluwarsa',
            'description' => 'Test',
            'food_type' => 'cooked',
            'portions_total' => 6,
            'portions_left' => 4,
            'max_portions_per_claim' => 3,
            'pickup_address' => 'Jl. Test',
            'latitude' => -7.0520,
            'longitude' => 110.4380,
            'public_latitude' => -7.0530,
            'public_longitude' => 110.4390,
            'available_from' => now()->subHours(2),
            'expires_at' => now()->subMinute(), // ALREADY EXPIRED
            'status' => 'available',
        ]);

        // Pending claim (User 2)
        $pendingClaim = Claim::create([
            'listing_id' => $listing->id,
            'user_id' => $this->user2->id,
            'portions_requested' => 1,
            'status' => 'pending',
            'pickup_code' => 'ABC123',
        ]);

        $user3 = User::create([
            'name' => 'Pengambil Ketiga',
            'email' => 'user3@test.com',
            'password' => Hash::make('password'),
            'role' => 'user',
            'email_verified_at' => now(),
        ]);

        // Approved claim (User 3 didn't show up before expiration)
        $approvedClaim = Claim::create([
            'listing_id' => $listing->id,
            'user_id' => $user3->id,
            'portions_requested' => 2,
            'portions_approved' => 2,
            'status' => 'approved',
            'pickup_code' => 'XYZ789',
        ]);

        Artisan::call('listings:expire');

        $listing->refresh();
        $pendingClaim->refresh();
        $approvedClaim->refresh();
        $user3->refresh();

        $this->assertEquals('expired', $listing->status);
        $this->assertEquals('expired', $pendingClaim->status);
        $this->assertEquals('no_show', $approvedClaim->status);
        $this->assertEquals(1, $user3->no_show_count);
    }

    /**
     * TC-18: Pickup details private; exact coordinates only for approved claimer or owner
     */
    public function test_tc18_pickup_details_privacy()
    {
        $listing = FoodListing::create([
            'user_id' => $this->user1->id,
            'category_id' => $this->category->id,
            'district_id' => $this->tembalang->id,
            'title' => 'Rahasia Alamat',
            'description' => 'Test',
            'food_type' => 'cooked',
            'portions_total' => 2,
            'portions_left' => 1,
            'max_portions_per_claim' => 2,
            'pickup_address' => 'Jl. Rahasia Nomor 999',
            'latitude' => -7.0520000,
            'longitude' => 110.4380000,
            'public_latitude' => -7.0540000,
            'public_longitude' => 110.4400000,
            'available_from' => now()->subMinute(),
            'expires_at' => now()->addHours(2),
            'status' => 'available',
        ]);

        $claim = Claim::create([
            'listing_id' => $listing->id,
            'user_id' => $this->user2->id,
            'portions_requested' => 1,
            'status' => 'pending',
            'pickup_code' => 'SECRET',
        ]);

        // Pending claimer cannot access pickup details (403)
        $response = $this->actingAs($this->user2)->get(route('claims.pickup-details', $claim->id));
        $response->assertStatus(403);

        // Once approved, claimer CAN access exact address and exact coordinates
        $claim->update(['status' => 'approved', 'portions_approved' => 1]);
        $responseApproved = $this->actingAs($this->user2)->get(route('claims.pickup-details', $claim->id));
        $responseApproved->assertStatus(200);
        $responseApproved->assertJsonFragment([
            'pickup_address' => 'Jl. Rahasia Nomor 999',
            'pickup_code' => 'SECRET',
        ]);
    }

    /**
     * TC-26: Mass assignment role=admin during registration is ignored
     */
    public function test_tc26_mass_assignment_role_protection()
    {
        $this->post(route('register'), [
            'name' => 'Attacker',
            'email' => 'attacker@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'admin', // Attack attempt
        ]);

        $attacker = User::where('email', 'attacker@test.com')->first();
        $this->assertNotNull($attacker);
        $this->assertEquals('user', $attacker->role);
        $this->assertFalse($attacker->isAdmin());
    }

    /**
     * TC-28: Volunteer cannot create campaign in non-approved district
     */
    public function test_tc28_volunteer_cannot_create_campaign_outside_approved_district()
    {
        // $this->volunteer is only approved in Tembalang, NOT in Banyumanik
        $response = $this->actingAs($this->volunteer)->post(route('volunteer.campaigns.store'), [
            'title' => 'Galang di Luar Wilayah',
            'district_id' => $this->banyumanik->id, // Not approved!
            'beneficiary_type' => 'panti_asuhan',
            'beneficiary_name' => 'Panti Luar Wilayah',
            'address' => 'Jl. Banyumanik',
            'latitude' => -7.06722,
            'longitude' => 110.41444,
            'location_precision' => 'exact',
            'target_portions' => 100,
            'starts_at' => now()->format('Y-m-d'),
            'ends_at' => now()->addDays(10)->format('Y-m-d'),
            'body' => 'Cerita kebutuhan panjang minimal 20 karakter di sini.',
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseCount('food_campaigns', 0);
    }

    /**
     * TC-02: Validation fails when invalid hours or past time
     */
    public function test_tc02_invalid_hours_fails_validation()
    {
        $response = $this->actingAs($this->user1)->post(route('listings.store'), [
            'title' => 'Nasi Basi',
            'category_id' => $this->category->id,
            'portions_total' => 5,
            'max_portions_per_claim' => 2,
            'food_type' => 'cooked',
            'hours_valid' => 30, // Exceeds max 24 hours
            'pickup_address' => 'Jl. Test',
            'latitude' => -7.0520,
            'longitude' => 110.4380,
            'district_id' => $this->tembalang->id,
        ]);

        $response->assertSessionHasErrors('hours_valid');
        $this->assertDatabaseCount('food_listings', 0);
    }

    /**
     * TC-03: Claim exceeding max_portions_per_claim rejected
     */
    public function test_tc03_claim_exceeding_max_portions_rejected()
    {
        $listing = FoodListing::create([
            'user_id' => $this->user1->id,
            'category_id' => $this->category->id,
            'district_id' => $this->tembalang->id,
            'title' => 'Nasi Kotak 10 Porsi',
            'description' => 'Test',
            'food_type' => 'cooked',
            'portions_total' => 10,
            'portions_left' => 10,
            'max_portions_per_claim' => 3, // Max 3 per claim
            'pickup_address' => 'Jl. Test',
            'latitude' => -7.0520,
            'longitude' => 110.4380,
            'public_latitude' => -7.0530,
            'public_longitude' => 110.4390,
            'available_from' => now()->subMinute(),
            'expires_at' => now()->addHours(2),
            'status' => 'available',
        ]);

        $claimService = new ClaimService();
        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        $claimService->create($this->user2, $listing, 4); // Requests 4 > 3
    }

    /**
     * TC-15: Scheduler listings:expire is idempotent
     */
    public function test_tc15_scheduler_listings_expire_is_idempotent()
    {
        $listing = FoodListing::create([
            'user_id' => $this->user1->id,
            'category_id' => $this->category->id,
            'district_id' => $this->tembalang->id,
            'title' => 'Expired Listing',
            'description' => 'Test',
            'food_type' => 'cooked',
            'portions_total' => 2,
            'portions_left' => 2,
            'max_portions_per_claim' => 2,
            'pickup_address' => 'Jl. Test',
            'latitude' => -7.0520,
            'longitude' => 110.4380,
            'public_latitude' => -7.0530,
            'public_longitude' => 110.4390,
            'available_from' => now()->subHours(2),
            'expires_at' => now()->subMinutes(10),
            'status' => 'available',
        ]);

        Artisan::call('listings:expire');
        $this->assertEquals('expired', $listing->refresh()->status);

        // Run second time: should succeed without error or change
        Artisan::call('listings:expire');
        $this->assertEquals('expired', $listing->refresh()->status);
    }

    /**
     * TC-20: Pickup verification rate limiting after 5 attempts
     */
    public function test_tc20_pickup_verification_rate_limiting()
    {
        $listing = FoodListing::create([
            'user_id' => $this->user1->id,
            'category_id' => $this->category->id,
            'district_id' => $this->tembalang->id,
            'title' => 'Pickup Listing',
            'description' => 'Test',
            'food_type' => 'cooked',
            'portions_total' => 2,
            'portions_left' => 1,
            'max_portions_per_claim' => 2,
            'pickup_address' => 'Jl. Test',
            'latitude' => -7.0520,
            'longitude' => 110.4380,
            'public_latitude' => -7.0530,
            'public_longitude' => 110.4390,
            'available_from' => now()->subMinute(),
            'expires_at' => now()->addHours(2),
            'status' => 'available',
        ]);

        $claim = Claim::create([
            'listing_id' => $listing->id,
            'user_id' => $this->user2->id,
            'portions_requested' => 1,
            'portions_approved' => 1,
            'status' => 'approved',
            'pickup_code' => 'CORRECT',
        ]);

        $claimService = new ClaimService();

        // 5 wrong attempts
        for ($i = 0; $i < 5; $i++) {
            $result = $claimService->verifyPickup($claim, 'WRONG1');
            $this->assertFalse($result);
        }

        // 6th attempt should be blocked with 429
        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        $claimService->verifyPickup($claim, 'CORRECT');
    }

    /**
     * TC-21: Papua timezone (WIT) calculation
     */
    public function test_tc21_papua_wit_timezone()
    {
        $papuaRegency = Regency::create([
            'province_id' => Province::where('code', '94')->first()->id,
            'code' => '9471',
            'name' => 'Kota Jayapura',
            'type' => 'kota',
        ]);

        $jayapura = District::create([
            'regency_id' => $papuaRegency->id,
            'code' => '947101',
            'name' => 'Jayapura Selatan',
            'latitude' => -2.57194,
            'longitude' => 140.70472,
        ]);

        $listing = FoodListing::create([
            'user_id' => $this->user1->id,
            'category_id' => $this->category->id,
            'district_id' => $jayapura->id,
            'title' => 'Papua Food',
            'description' => 'Test',
            'food_type' => 'cooked',
            'portions_total' => 2,
            'portions_left' => 2,
            'max_portions_per_claim' => 2,
            'pickup_address' => 'Jl. Papua',
            'latitude' => -2.57194,
            'longitude' => 140.70472,
            'public_latitude' => -2.57194,
            'public_longitude' => 140.70472,
            'available_from' => now()->subMinute(),
            'expires_at' => now()->addHours(2),
            'status' => 'available',
        ]);

        $this->assertEquals('Asia/Jayapura', $listing->timezone());
        $this->assertEquals('WIT', $listing->tzLabel());
    }

    /**
     * TC-24: Volunteer area reapplication cooldown (7 days)
     */
    public function test_tc24_volunteer_area_cooldown()
    {
        $volService = new VolunteerApplicationService();

        // 1. First submission for Banyumanik
        $volService->submit($this->user2, $this->kotaSmg->id, [$this->banyumanik->id], 'Ingin membantu');

        $area = VolunteerArea::where('user_id', $this->user2->id)->where('district_id', $this->banyumanik->id)->first();
        $this->assertNotNull($area);

        // Admin rejects area -> 7 days cooldown applied (TC-24)
        $volService->rejectArea($area, $this->admin, 'Kapasitas penuh');
        $area->refresh();
        $this->assertEquals('rejected', $area->status);
        $this->assertNotNull($area->cooldown_until);
        $this->assertTrue($area->cooldown_until->isFuture());

        // Reapplication before cooldown expires should be ignored (area status stays rejected)
        $volService->submit($this->user2, $this->kotaSmg->id, [$this->banyumanik->id], 'Mencoba lagi terlalu cepat');
        $this->assertEquals('rejected', $area->refresh()->status);

        // Fast forward past cooldown (7 days)
        $area->cooldown_until = now()->subHour();
        $area->save();

        // Now reapplication succeeds and moves area back to pending!
        $volService->submit($this->user2, $this->kotaSmg->id, [$this->banyumanik->id], 'Mencoba lagi setelah 7 hari');
        $this->assertEquals('pending', $area->refresh()->status);
    }

    /**
     * TC-30: Revoking volunteer pauses their active campaigns in that district
     */
    public function test_tc30_revoking_volunteer_pauses_campaigns()
    {
        $campaign = FoodCampaign::create([
            'user_id' => $this->volunteer->id,
            'district_id' => $this->tembalang->id,
            'title' => 'Galang Tembalang',
            'slug' => 'galang-tembalang',
            'body' => 'Cerita galang kebutuhan.',
            'beneficiary_type' => 'panti_asuhan',
            'beneficiary_name' => 'Panti Asuhan Berkah',
            'address' => 'Jl. Tembalang',
            'latitude' => -7.05194,
            'longitude' => 110.43778,
            'public_latitude' => -7.05294,
            'public_longitude' => 110.43878,
            'target_portions' => 100,
            'collected_portions' => 20,
            'starts_at' => now(),
            'ends_at' => now()->addDays(30),
            'status' => 'published',
        ]);

        $volService = new VolunteerApplicationService();
        $area = VolunteerArea::where('user_id', $this->volunteer->id)->where('district_id', $this->tembalang->id)->first();

        // Admin revokes volunteer from Tembalang
        $volService->revokeArea($area, $this->admin, 'Pelanggaran SOP');

        $campaign->refresh();
        $this->assertEquals('paused', $campaign->status);
        $this->assertNotNull($campaign->paused_reason);
    }

    /**
     * TC-34: Claim for campaign completed increments collected_portions
     */
    public function test_tc34_claim_for_campaign_increments_collected_portions()
    {
        $campaign = FoodCampaign::create([
            'user_id' => $this->volunteer->id,
            'district_id' => $this->tembalang->id,
            'title' => 'Panti Tembalang',
            'slug' => 'panti-tembalang-1',
            'body' => 'Cerita galang kebutuhan.',
            'beneficiary_type' => 'panti_asuhan',
            'beneficiary_name' => 'Panti Tembalang',
            'address' => 'Jl. Tembalang',
            'latitude' => -7.05194,
            'longitude' => 110.43778,
            'public_latitude' => -7.05294,
            'public_longitude' => 110.43878,
            'target_portions' => 100,
            'collected_portions' => 10,
            'starts_at' => now(),
            'ends_at' => now()->addDays(30),
            'status' => 'published',
        ]);

        $listing = FoodListing::create([
            'user_id' => $this->user1->id,
            'category_id' => $this->category->id,
            'district_id' => $this->tembalang->id,
            'title' => 'Nasi untuk Panti',
            'description' => 'Test',
            'food_type' => 'cooked',
            'portions_total' => 10,
            'portions_left' => 10,
            'max_portions_per_claim' => 10,
            'pickup_address' => 'Jl. Test',
            'latitude' => -7.0520,
            'longitude' => 110.4380,
            'public_latitude' => -7.0530,
            'public_longitude' => 110.4390,
            'available_from' => now()->subMinute(),
            'expires_at' => now()->addHours(2),
            'status' => 'available',
        ]);

        $claimService = new ClaimService();

        // Volunteer claims 5 portions on behalf of campaign
        $claim = $claimService->create($this->volunteer, $listing, 5, $campaign->id);
        $this->assertEquals($campaign->id, $claim->campaign_id);

        // Owner approves
        $claimService->approve($claim, 5);

        // Pickup verification succeeds
        $claimService->verifyPickup($claim, $claim->pickup_code);

        // Collected portions on campaign incremented by 5! (10 + 5 = 15)
        $campaign->refresh();
        $this->assertEquals(15, $campaign->collected_portions);
    }
}
