<?php

namespace Tests\Feature;

use App\Models\District;
use App\Models\Province;
use App\Models\Regency;
use App\Models\User;
use App\Models\VolunteerApplication;
use App\Models\VolunteerArea;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class VolunteerApplicationEmsifaTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Province $jateng;
    protected Regency $kotaSmg;
    protected District $tembalang;
    protected District $banyumanik;

    protected function setUp(): void
    {
        parent::setUp();

        $this->jateng = Province::create([
            'code' => '33',
            'name' => 'Jawa Tengah',
            'timezone' => 'Asia/Jakarta',
        ]);

        $this->kotaSmg = Regency::create([
            'province_id' => $this->jateng->id,
            'code' => '3374',
            'name' => 'Kota Semarang',
            'type' => 'kota',
        ]);

        $this->tembalang = District::create([
            'regency_id' => $this->kotaSmg->id,
            'code' => '3374010',
            'name' => 'Tembalang',
            'latitude' => -7.0500,
            'longitude' => 110.4400,
        ]);

        $this->banyumanik = District::create([
            'regency_id' => $this->kotaSmg->id,
            'code' => '3374020',
            'name' => 'Banyumanik',
            'latitude' => -7.0600,
            'longitude' => 110.4100,
        ]);

        $this->user = User::create([
            'name' => 'Calon Relawan',
            'email' => 'calon_relawan@test.com',
            'password' => Hash::make('password'),
            'role' => 'user',
            'phone' => '081234567890',
            'district_id' => $this->tembalang->id,
            'email_verified_at' => now(),
        ]);
    }

    public function test_volunteer_apply_page_renders_successfully(): void
    {
        $response = $this->actingAs($this->user)->get(route('volunteer.apply'));
        $response->assertStatus(200);
        $response->assertSee('Daftar Relawan Penyelamat Pangan');
        $response->assertSee('Wilayah Tanggung Jawab Relawan');
        $response->assertSee('Logistik & Kesiapan Operasional', false);
    }

    public function test_volunteer_can_apply_with_emsifa_and_role_details(): void
    {
        $response = $this->actingAs($this->user)->post(route('volunteer.apply.submit'), [
            'province_id' => '33',
            'province_name' => 'Jawa Tengah',
            'regency_id' => '3374',
            'regency_name' => 'Kota Semarang',
            'district_ids' => ['3374010', '3374020'],
            'district_names' => [
                '3374010' => 'Tembalang',
                '3374020' => 'Banyumanik',
            ],
            'phone' => '081234567890',
            'transportation' => 'Sepeda Motor (Box/Tas Kurir)',
            'availability' => 'Fleksibel (Kapan Saja)',
            'experience' => 'Pernah aktif di dapur umum PMI',
            'target_beneficiaries' => ['Panti Asuhan', 'Pondok Pesantren'],
            'has_food_safety_commitment' => '1',
            'motivation' => 'Saya berkomitmen aktif membantu menyalurkan makanan berlebih ke panti asuhan sekitar.',
        ]);

        $response->assertRedirect(route('volunteer.areas'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('volunteer_applications', [
            'user_id' => $this->user->id,
            'regency_id' => $this->kotaSmg->id,
            'transportation' => 'Sepeda Motor (Box/Tas Kurir)',
            'availability' => 'Fleksibel (Kapan Saja)',
            'experience' => 'Pernah aktif di dapur umum PMI',
            'target_beneficiaries' => 'Panti Asuhan, Pondok Pesantren',
            'has_food_safety_commitment' => true,
        ]);

        $this->assertDatabaseHas('volunteer_areas', [
            'user_id' => $this->user->id,
            'district_id' => $this->tembalang->id,
            'status' => 'pending',
        ]);

        $this->assertDatabaseHas('volunteer_areas', [
            'user_id' => $this->user->id,
            'district_id' => $this->banyumanik->id,
            'status' => 'pending',
        ]);
    }
}
