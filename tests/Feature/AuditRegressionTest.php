<?php

namespace Tests\Feature;

use App\Enums\CampaignStatus;
use App\Enums\ListingStatus;
use App\Models\Category;
use App\Models\Claim;
use App\Models\District;
use App\Models\FoodCampaign;
use App\Models\FoodListing;
use App\Models\Province;
use App\Models\Regency;
use App\Models\Report;
use App\Models\User;
use App\Services\CampaignService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuditRegressionTest extends TestCase
{
    use RefreshDatabase;

    protected District $district;
    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $province = Province::create([
            'code' => '33',
            'name' => 'Jawa Tengah',
            'timezone' => 'Asia/Jakarta',
        ]);

        $regency = Regency::create([
            'province_id' => $province->id,
            'code' => '3374',
            'name' => 'Kota Semarang',
            'type' => 'kota',
        ]);

        $this->district = District::create([
            'regency_id' => $regency->id,
            'code' => '3374010',
            'name' => 'Tembalang',
            'latitude' => -7.05194,
            'longitude' => 110.43778,
        ]);

        $this->category = Category::create([
            'name' => 'Makanan Siap Santap',
            'slug' => 'siap-santap',
            'icon' => 'meal',
        ]);
    }

    public function test_deactivated_user_cannot_login_or_access_authenticated_routes(): void
    {
        $user = User::factory()->create([
            'district_id' => $this->district->id,
        ]);
        $user->is_active = false;
        $user->save();

        $loginRes = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $loginRes->assertSessionHasErrors('email');
        $this->assertGuest();

        // Even if already logged in when deactivated mid-session, middleware logs them out
        $this->actingAs($user)->get('/dashboard')
            ->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_admin_cannot_deactivate_own_account(): void
    {
        $admin = User::factory()->create(['district_id' => $this->district->id]);
        $admin->role = 'admin';
        $admin->save();

        $this->actingAs($admin)
            ->post(route('admin.users.toggle-active', $admin->id))
            ->assertSessionHas('error');

        $this->assertTrue($admin->fresh()->is_active);
    }

    public function test_nearby_campaigns_api_returns_label_and_excerpt(): void
    {
        $user = User::factory()->create(['district_id' => $this->district->id]);

        FoodCampaign::create([
            'user_id' => $user->id,
            'district_id' => $this->district->id,
            'title' => 'Berbagi Panti Asuhan',
            'slug' => 'berbagi-panti-asuhan',
            'body' => 'Kebutuhan makan malam untuk anak-anak panti asuhan setiap hari.',
            'beneficiary_type' => 'panti_asuhan',
            'beneficiary_name' => 'Panti Sejahtera',
            'address' => 'Jl. Tembalang No. 1',
            'latitude' => -7.05194,
            'longitude' => 110.43778,
            'public_latitude' => -7.05194,
            'public_longitude' => 110.43778,
            'location_precision' => 'exact',
            'target_portions' => 100,
            'collected_portions' => 25,
            'starts_at' => now(),
            'ends_at' => now()->addDays(10),
            'status' => CampaignStatus::PUBLISHED->value,
        ]);

        $res = $this->getJson(route('api.campaigns.nearby'));
        $res->assertOk()
            ->assertJsonPath('campaigns.0.beneficiary_type_label', 'Panti Asuhan')
            ->assertJsonStructure([
                'campaigns' => [
                    ['id', 'title', 'excerpt', 'beneficiary_type_label', 'progress_percent', 'url'],
                ],
            ]);
    }

    public function test_taken_down_listing_is_blocked_from_public_view(): void
    {
        $owner = User::factory()->create(['district_id' => $this->district->id]);
        $viewer = User::factory()->create(['district_id' => $this->district->id]);

        $listing = FoodListing::create([
            'user_id' => $owner->id,
            'category_id' => $this->category->id,
            'district_id' => $this->district->id,
            'title' => 'Nasi Goreng Surplus',
            'description' => 'Layak konsumsi',
            'food_type' => 'cooked',
            'portions_total' => 5,
            'portions_left' => 5,
            'max_portions_per_claim' => 2,
            'pickup_address' => 'Jl. Mawar No. 5',
            'latitude' => -7.05194,
            'longitude' => 110.43778,
            'public_latitude' => -7.0520,
            'public_longitude' => 110.4378,
            'available_from' => now(),
            'expires_at' => now()->addHours(4),
            'status' => 'taken_down',
        ]);

        $this->get(route('listings.show', $listing->id))->assertForbidden();
        $this->actingAs($viewer)->get(route('listings.show', $listing->id))->assertForbidden();
        $this->actingAs($owner)->get(route('listings.show', $listing->id))->assertOk();
    }

    public function test_admin_resolve_report_validates_status_and_claim_quick_verify_notifies_both_parties(): void
    {
        $admin = User::factory()->create(['district_id' => $this->district->id]);
        $admin->role = 'admin';
        $admin->save();

        $owner = User::factory()->create(['district_id' => $this->district->id]);
        $claimer = User::factory()->create(['district_id' => $this->district->id]);

        $listing = FoodListing::create([
            'user_id' => $owner->id,
            'category_id' => $this->category->id,
            'district_id' => $this->district->id,
            'title' => 'Roti Manis Sore',
            'description' => 'Segar',
            'food_type' => 'packaged',
            'portions_total' => 5,
            'portions_left' => 5,
            'max_portions_per_claim' => 3,
            'pickup_address' => 'Jl. Bulusan',
            'latitude' => -7.05194,
            'longitude' => 110.43778,
            'public_latitude' => -7.0520,
            'public_longitude' => 110.4378,
            'available_from' => now(),
            'expires_at' => now()->addHours(4),
            'status' => ListingStatus::AVAILABLE->value,
        ]);

        $report = Report::create([
            'reporter_id' => $claimer->id,
            'listing_id' => $listing->id,
            'reason' => 'other',
            'detail' => 'Tes',
            'status' => 'open',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.reports.resolve', $report->id), ['status' => 'invalid_hack_status'])
            ->assertSessionHasErrors('status');

        // Approve claim with clamped portion & quick-verify notification check
        $this->actingAs($claimer)
            ->post(route('claims.store', $listing->id), ['portions' => 2]);

        $claim = Claim::firstOrFail();

        $this->actingAs($owner)
            ->post(route('claims.approve', $claim->id), ['portions' => 99]);

        $claim->refresh();
        $this->assertEquals(2, $claim->portions_approved);
        $this->assertStringContainsString('2 porsi', $claimer->notifications()->latest()->first()->data['message']);

        $ownerCountBefore = $owner->notifications()->count();
        $claimerCountBefore = $claimer->notifications()->count();

        $this->actingAs($owner)
            ->postJson(route('claims.quick-verify'), [
                'qr_payload' => "LOKAWASTE:CLAIM:{$claim->id}:{$claim->pickup_code}",
            ])
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->assertGreaterThan($ownerCountBefore, $owner->notifications()->count());
        $this->assertGreaterThan($claimerCountBefore, $claimer->notifications()->count());
    }

    public function test_campaign_body_strips_html_and_escapes_xss_on_render(): void
    {
        $volunteer = User::factory()->create(['district_id' => $this->district->id]);
        $app = \App\Models\VolunteerApplication::create([
            'user_id' => $volunteer->id,
            'regency_id' => $this->district->regency_id,
            'motivation' => 'Siap membantu penyaluran pangan di Tembalang',
        ]);
        \App\Models\VolunteerArea::create([
            'application_id' => $app->id,
            'user_id' => $volunteer->id,
            'regency_id' => $this->district->regency_id,
            'district_id' => $this->district->id,
            'status' => 'approved',
        ]);

        $campaignService = app(CampaignService::class);
        $campaign = $campaignService->create($volunteer, [
            'district_id' => $this->district->id,
            'title' => 'Galang Uji XSS',
            'beneficiary_type' => 'panti_asuhan',
            'beneficiary_name' => 'Panti Uji',
            'address' => 'Jl. Uji',
            'latitude' => -7.05194,
            'longitude' => 110.43778,
            'target_portions' => 50,
            'starts_at' => now(),
            'ends_at' => now()->addDays(5),
            'body' => '<p onmouseover="alert(1)">Halo semua</p><script>alert(2)</script>',
        ]);

        $campaign->update(['status' => CampaignStatus::PUBLISHED->value]);

        $this->assertStringNotContainsString('onmouseover', $campaign->body);
        $this->assertStringNotContainsString('<script>', $campaign->body);

        $this->get(route('campaigns.show', $campaign->slug))
            ->assertOk()
            ->assertSee('Halo semua')
            ->assertDontSee('onmouseover=', false);
    }
}
