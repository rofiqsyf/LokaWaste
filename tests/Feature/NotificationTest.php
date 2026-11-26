<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\District;
use App\Models\FoodListing;
use App\Models\Province;
use App\Models\Regency;
use App\Models\User;
use App\Notifications\LokaNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected User $owner;
    protected FoodListing $listing;

    protected function setUp(): void
    {
        parent::setUp();

        $province = Province::create(['code' => '33', 'name' => 'Jawa Tengah', 'timezone' => 'Asia/Jakarta']);
        $regency = Regency::create(['province_id' => $province->id, 'code' => '3307', 'name' => 'Kabupaten Wonosobo', 'type' => 'kabupaten']);
        $district = District::create(['regency_id' => $regency->id, 'code' => '330701', 'name' => 'Wonosobo']);

        $category = Category::create([
            'name' => 'Makanan Siap Santap',
            'slug' => 'makanan-siap-santap',
            'icon' => '🍲',
            'is_active' => true,
        ]);

        $this->user = User::factory()->create([
            'district_id' => $district->id,
            'role' => 'user',
            'is_active' => true,
        ]);

        $this->owner = User::factory()->create([
            'district_id' => $district->id,
            'role' => 'user',
            'is_active' => true,
        ]);

        $this->listing = FoodListing::create([
            'user_id' => $this->owner->id,
            'category_id' => $category->id,
            'district_id' => $district->id,
            'title' => 'Nasi Kotak Berkah',
            'description' => 'Nasi kotak surplus acara.',
            'food_type' => 'cooked',
            'portions_total' => 10,
            'portions_left' => 10,
            'max_portions_per_claim' => 3,
            'status' => 'available',
            'is_halal' => true,
            'pickup_address' => 'Jl. Merdeka No. 10',
            'public_latitude' => -7.36,
            'public_longitude' => 109.90,
            'latitude' => -7.363,
            'longitude' => 109.902,
            'available_from' => now()->subMinutes(10),
            'expires_at' => now()->addHours(6),
        ]);
    }

    public function test_guest_is_redirected_from_notifications_page(): void
    {
        $response = $this->get(route('notifications.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_notifications_page(): void
    {
        $response = $this->actingAs($this->user)->get(route('notifications.index'));
        $response->assertStatus(200);
        $response->assertSee('Notifikasi & Pembaruan');
        $response->assertSee('Selamat Datang di LokaWaste!');
    }

    public function test_user_can_mark_single_notification_as_read(): void
    {
        $this->user->notify(new LokaNotification(
            title: 'Klaim Disetujui',
            message: 'Klaim Anda telah disetujui.',
            category: 'claim',
            icon: 'check-circle'
        ));

        $notification = $this->user->unreadNotifications->first();
        $this->assertNotNull($notification);

        $response = $this->actingAs($this->user)->post(route('notifications.mark-read', $notification->id));
        $response->assertSessionHas('success');

        $this->assertNull($this->user->fresh()->unreadNotifications->first());
    }

    public function test_user_can_mark_all_notifications_as_read(): void
    {
        $this->user->notify(new LokaNotification(title: 'Notif 1', message: 'Pesan 1'));
        $this->user->notify(new LokaNotification(title: 'Notif 2', message: 'Pesan 2'));

        $this->assertEquals(2, $this->user->unreadNotifications()->count());

        $response = $this->actingAs($this->user)->post(route('notifications.mark-all-read'));
        $response->assertSessionHas('success');

        $this->assertEquals(0, $this->user->fresh()->unreadNotifications()->count());
    }

    public function test_user_can_delete_notification(): void
    {
        $this->user->notify(new LokaNotification(title: 'Hapus Saya', message: 'Test'));
        $notification = $this->user->notifications()->first();

        $response = $this->actingAs($this->user)->delete(route('notifications.destroy', $notification->id));
        $response->assertSessionHas('success');

        $this->assertNull($this->user->fresh()->notifications()->find($notification->id));
    }

    public function test_claim_creation_triggers_notification_for_listing_owner(): void
    {
        $this->actingAs($this->user)->post(route('claims.store', $this->listing->id), [
            'portions' => 2,
        ]);

        $ownerNotification = $this->owner->unreadNotifications()->latest()->first();
        $this->assertNotNull($ownerNotification);
        $this->assertEquals('claim', $ownerNotification->data['category']);
        $this->assertStringContainsString('Nasi Kotak Berkah', $ownerNotification->data['title']);
    }
}
