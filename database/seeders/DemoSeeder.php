<?php

namespace Database\Seeders;

use App\Enums\AreaStatus;
use App\Enums\CampaignStatus;
use App\Enums\ClaimStatus;
use App\Enums\FoodType;
use App\Enums\ListingStatus;
use App\Models\Category;
use App\Models\Claim;
use App\Models\District;
use App\Models\FoodCampaign;
use App\Models\FoodListing;
use App\Models\Regency;
use App\Models\User;
use App\Models\VolunteerApplication;
use App\Models\VolunteerArea;
use App\Services\LocationPrivacyService;
use App\Services\PickupCode;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $privacy = new LocationPrivacyService();
        $tembalang = District::where('code', '337401')->firstOrFail();
        $banyumanik = District::where('code', '337402')->firstOrFail();
        $kotaSmg = Regency::where('code', '3374')->firstOrFail();

        $catNasi = Category::where('slug', 'makanan-berat')->first();
        $catRoti = Category::where('slug', 'roti-kue')->first();

        // 1. Admin
        $admin = User::whereIn('email', ['admin@lokawaste.test', 'admin@locabite.test'])->first();
        if ($admin) {
            $admin->update(['email' => 'admin@lokawaste.test', 'name' => 'Admin LokaWaste']);
        } else {
            $admin = User::create([
                'name' => 'Admin LokaWaste',
                'email' => 'admin@lokawaste.test',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'phone' => '081200000001',
                'email_verified_at' => now(),
                'district_id' => $tembalang->id,
                'is_verified' => true,
            ]);
        }
        $admin->role = 'admin';
        $admin->save();

        // 2. Verified Business User (Warung Bu Sari)
        $warung = User::whereIn('email', ['warung@lokawaste.test', 'warung@locabite.test'])->first();
        if ($warung) {
            $warung->update(['email' => 'warung@lokawaste.test']);
        } else {
            $warung = User::create([
                'name' => 'Warung Bu Sari',
                'email' => 'warung@lokawaste.test',
                'password' => Hash::make('password'),
                'role' => 'user',
                'phone' => '081234567890',
                'organization_name' => 'Warung Bu Sari & Katering',
                'district_id' => $tembalang->id,
                'default_latitude' => -7.0520,
                'default_longitude' => 110.4380,
                'is_verified' => true,
                'email_verified_at' => now(),
            ]);
        }
        $warung->is_verified = true;
        $warung->save();

        // 3. Volunteer User (Rina)
        $relawan = User::whereIn('email', ['relawan@lokawaste.test', 'relawan@locabite.test'])->first();
        if ($relawan) {
            $relawan->update(['email' => 'relawan@lokawaste.test']);
        } else {
            $relawan = User::create([
                'name' => 'Rina Relawan',
                'email' => 'relawan@lokawaste.test',
                'password' => Hash::make('password'),
                'role' => 'user',
                'phone' => '081298765432',
                'district_id' => $tembalang->id,
                'email_verified_at' => now(),
            ]);
        }

        // Volunteer application & approved areas for Rina
        $app = VolunteerApplication::firstOrCreate(['user_id' => $relawan->id], [
            'regency_id' => $kotaSmg->id,
            'motivation' => 'Aktif di komunitas peduli pangan Tembalang dan ingin membantu menyalurkan makanan ke panti asuhan sekitar.',
        ]);

        VolunteerArea::firstOrCreate([
            'user_id' => $relawan->id,
            'district_id' => $tembalang->id,
        ], [
            'application_id' => $app->id,
            'regency_id' => $kotaSmg->id,
            'status' => AreaStatus::APPROVED->value,
            'decided_by' => $admin->id,
            'decided_at' => now(),
            'decision_note' => 'Disetujui untuk wilayah Tembalang.',
        ]);

        VolunteerArea::firstOrCreate([
            'user_id' => $relawan->id,
            'district_id' => $banyumanik->id,
        ], [
            'application_id' => $app->id,
            'regency_id' => $kotaSmg->id,
            'status' => AreaStatus::APPROVED->value,
            'decided_by' => $admin->id,
            'decided_at' => now(),
            'decision_note' => 'Disetujui untuk wilayah Banyumanik.',
        ]);

        // 4. Regular Citizen (Budi)
        $warga = User::whereIn('email', ['warga@lokawaste.test', 'warga@locabite.test'])->first();
        if ($warga) {
            $warga->update(['email' => 'warga@lokawaste.test']);
        } else {
            $warga = User::create([
                'name' => 'Budi Santoso',
                'email' => 'warga@lokawaste.test',
                'password' => Hash::make('password'),
                'role' => 'user',
                'phone' => '081311223344',
                'district_id' => $tembalang->id,
                'default_latitude' => -7.0540,
                'default_longitude' => 110.4350,
                'email_verified_at' => now(),
            ]);
        }

        // 5. Food Campaign by Rina
        $campaignLat = -7.05350;
        $campaignLng = 110.43900;
        [$pubCampLat, $pubCampLng] = $privacy->publicPoint($campaignLat, $campaignLng);

        $campaign = FoodCampaign::firstOrCreate([
            'slug' => 'makan-malam-anak-panti-nurul-huda',
        ], [
            'user_id' => $relawan->id,
            'district_id' => $tembalang->id,
            'title' => 'Makan Malam untuk 40 Anak Panti Asuhan Nurul Huda',
            'body' => '<p>Panti Asuhan Nurul Huda menaungi 40 anak asuh. Kebutuhan makan malam setiap hari membutuhkan 40 porsi makanan layak. Relawan wilayah Tembalang siap menjemput dan menyalurkan makanan berlebih dari katering atau warung sekitar.</p>',
            'beneficiary_type' => 'panti_asuhan',
            'beneficiary_name' => 'Panti Asuhan Nurul Huda',
            'address' => 'Jl. Banjarsari No. 12, Tembalang, Kota Semarang',
            'latitude' => $campaignLat,
            'longitude' => $campaignLng,
            'public_latitude' => $pubCampLat,
            'public_longitude' => $pubCampLng,
            'location_precision' => 'exact',
            'daily_need' => '40 porsi makan malam siap santap',
            'target_portions' => 600,
            'collected_portions' => 340,
            'starts_at' => now()->subDays(5),
            'ends_at' => now()->addDays(25),
            'status' => CampaignStatus::PUBLISHED->value,
            'reviewed_by' => $admin->id,
            'review_note' => 'Artikel terverifikasi dan memenuhi kriteria kelayakan publikasi.',
        ]);

        // 6. Listings
        // Listing 1: 15 Porsi Nasi Kotak (Sisa 4 jam - Hijau Segar)
        $l1Lat = -7.0525;
        $l1Lng = 110.4375;
        [$l1PubLat, $l1PubLng] = $privacy->publicPoint($l1Lat, $l1Lng);
        $listing1 = FoodListing::create([
            'user_id' => $warung->id,
            'category_id' => $catNasi->id,
            'district_id' => $tembalang->id,
            'title' => 'Nasi Kotak Ayam Bakar Bumbu Rujak',
            'description' => 'Sisa pesanan katering siang tadi, dalam keadaan bersih, higienis, dan terbungkus rapi dalam box nasi komplit sayur dan sambal.',
            'food_type' => FoodType::COOKED->value,
            'prepared_at' => now()->subHours(2),
            'allergens' => ['Kacang'],
            'storage_note' => 'Disimpan dalam ruangan sejuk tertutup rapat',
            'halal_claim' => true,
            'is_vegetarian' => false,
            'portions_total' => 15,
            'portions_left' => 15,
            'max_portions_per_claim' => 3,
            'pickup_address' => 'Warung Bu Sari, Jl. Prof. Soedarto No. 45, Tembalang',
            'latitude' => $l1Lat,
            'longitude' => $l1Lng,
            'public_latitude' => $l1PubLat,
            'public_longitude' => $l1PubLng,
            'show_exact_location' => true,
            'available_from' => now()->subMinutes(30),
            'expires_at' => now()->addHours(4),
            'status' => ListingStatus::AVAILABLE->value,
        ]);

        // Listing 2: Roti Manis (Sisa 2 jam - Kunyit Tua)
        $l2Lat = -7.0560;
        $l2Lng = 110.4360;
        [$l2PubLat, $l2PubLng] = $privacy->publicPoint($l2Lat, $l2Lng);
        $listing2 = FoodListing::create([
            'user_id' => $warung->id,
            'category_id' => $catRoti->id,
            'district_id' => $tembalang->id,
            'title' => 'Roti Manis Cokelat & Keju Hari Ini',
            'description' => 'Produksi pagi hari, roti lembut dalam kemasan plastik individual masih sangat renyah dan bersih.',
            'food_type' => FoodType::PACKAGED->value,
            'prepared_at' => now()->subHours(5),
            'allergens' => ['Gluten', 'Susu'],
            'storage_note' => 'Suhu ruang',
            'halal_claim' => true,
            'is_vegetarian' => true,
            'portions_total' => 8,
            'portions_left' => 8,
            'max_portions_per_claim' => 2,
            'pickup_address' => 'Warung Bu Sari Cabang 2, Tembalang',
            'latitude' => $l2Lat,
            'longitude' => $l2Lng,
            'public_latitude' => $l2PubLat,
            'public_longitude' => $l2PubLng,
            'show_exact_location' => false,
            'available_from' => now()->subHours(1),
            'expires_at' => now()->addHours(2),
            'status' => ListingStatus::AVAILABLE->value,
        ]);

        // Listing 3: Sup Jagung & Ayam (Sisa 40 menit - Cabai Merah)
        $l3Lat = -7.0510;
        $l3Lng = 110.4410;
        [$l3PubLat, $l3PubLng] = $privacy->publicPoint($l3Lat, $l3Lng);
        $listing3 = FoodListing::create([
            'user_id' => $warung->id,
            'category_id' => $catNasi->id,
            'district_id' => $tembalang->id,
            'title' => 'Sup Jagung Manis Hangat',
            'description' => 'Sup jagung manis hangat dalam cup siap konsumsi.',
            'food_type' => FoodType::COOKED->value,
            'prepared_at' => now()->subHours(3),
            'allergens' => ['Telur'],
            'storage_note' => 'Hangat',
            'halal_claim' => true,
            'is_vegetarian' => false,
            'portions_total' => 6,
            'portions_left' => 6,
            'max_portions_per_claim' => 2,
            'pickup_address' => 'Jl. Tirto Agung No. 8, Tembalang',
            'latitude' => $l3Lat,
            'longitude' => $l3Lng,
            'public_latitude' => $l3PubLat,
            'public_longitude' => $l3PubLng,
            'show_exact_location' => false,
            'available_from' => now()->subHours(2),
            'expires_at' => now()->addMinutes(40),
            'status' => ListingStatus::AVAILABLE->value,
        ]);

        // Listing 4: Ditujukan ke Galang (dengan masa prioritas relawan)
        $l4Lat = -7.0550;
        $l4Lng = 110.4395;
        [$l4PubLat, $l4PubLng] = $privacy->publicPoint($l4Lat, $l4Lng);
        $listing4 = FoodListing::create([
            'user_id' => $warung->id,
            'category_id' => $catNasi->id,
            'campaign_id' => $campaign->id,
            'district_id' => $tembalang->id,
            'title' => '20 Porsi Nasi Kuning Lengkap untuk Galang Panti',
            'description' => 'Didedikasikan untuk disalurkan ke anak panti asuhan melalui relawan galang makanan.',
            'food_type' => FoodType::COOKED->value,
            'prepared_at' => now()->subHours(1),
            'allergens' => ['Telur'],
            'storage_note' => 'Disimpan higienis',
            'halal_claim' => true,
            'is_vegetarian' => false,
            'portions_total' => 20,
            'portions_left' => 20,
            'max_portions_per_claim' => 10,
            'pickup_address' => 'Dapur Katering Berkah, Tembalang',
            'latitude' => $l4Lat,
            'longitude' => $l4Lng,
            'public_latitude' => $l4PubLat,
            'public_longitude' => $l4PubLng,
            'show_exact_location' => false,
            'available_from' => now(),
            'expires_at' => now()->addHours(6),
            'campaign_priority_until' => now()->addHours(2),
            'status' => ListingStatus::AVAILABLE->value,
        ]);
    }
}
