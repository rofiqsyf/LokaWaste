<?php

namespace App\Services;

use App\Enums\CampaignStatus;
use App\Models\District;
use App\Models\FoodCampaign;
use App\Models\User;
use Illuminate\Support\Str;

class CampaignService
{
    public function __construct(
        protected LocationPrivacyService $privacyService
    ) {}

    public function create(User $user, array $data): FoodCampaign
    {
        abort_unless($user->isVolunteerIn($data['district_id']), 403, 'Anda bukan relawan terdaftar di kecamatan ini.');

        $lat = (float) $data['latitude'];
        $lng = (float) $data['longitude'];

        // If location precision is area or beneficiary is tunawisma, generate public area jitter
        [$publicLat, $publicLng] = $this->privacyService->publicPoint($lat, $lng);

        $slug = Str::slug($data['title']) . '-' . Str::lower(Str::random(6));

        return FoodCampaign::create([
            'user_id' => $user->id,
            'district_id' => $data['district_id'],
            'title' => $data['title'],
            'slug' => $slug,
            'body' => strip_tags($data['body'], '<p><br><strong><em><ul><ol><li>'),
            'cover_path' => $data['cover_path'] ?? null,
            'beneficiary_type' => $data['beneficiary_type'],
            'beneficiary_name' => $data['beneficiary_name'],
            'address' => $data['address'],
            'latitude' => $lat,
            'longitude' => $lng,
            'public_latitude' => $publicLat,
            'public_longitude' => $publicLng,
            'location_precision' => $data['location_precision'] ?? ($data['beneficiary_type'] === 'tunawisma' ? 'area' : 'exact'),
            'daily_need' => $data['daily_need'] ?? null,
            'target_portions' => $data['target_portions'],
            'collected_portions' => 0,
            'starts_at' => $data['starts_at'],
            'ends_at' => $data['ends_at'],
            'status' => CampaignStatus::DRAFT->value,
        ]);
    }

    public function checkDistrictCenterWarning(int $districtId, float $lat, float $lng): ?string
    {
        $district = District::find($districtId);
        if ($district && $district->latitude && $district->longitude) {
            $distKm = 6371 * acos(min(1.0, max(-1.0,
                cos(deg2rad($lat)) * cos(deg2rad($district->latitude)) *
                cos(deg2rad($district->longitude) - deg2rad($lng)) +
                sin(deg2rad($lat)) * sin(deg2rad($district->latitude))
            )));

            if ($distKm > 15.0) {
                return "Pin ini berjarak " . round($distKm, 1) . " km dari pusat Kec. {$district->name}. Pastikan kecamatannya benar.";
            }
        }
        return null;
    }

    public function submitForReview(FoodCampaign $campaign, User $user): void
    {
        abort_unless($campaign->user_id === $user->id, 403, 'Akses ditolak.');
        abort_unless($user->isVolunteerIn($campaign->district_id), 403, 'Status relawan Anda di kecamatan ini tidak aktif.');

        $campaign->update([
            'status' => CampaignStatus::PENDING_REVIEW->value,
            'review_note' => null,
        ]);
    }

    public function approveAndPublish(FoodCampaign $campaign, User $admin, ?string $note = null): void
    {
        $campaign->update([
            'status' => CampaignStatus::PUBLISHED->value,
            'reviewed_by' => $admin->id,
            'review_note' => $note,
            'paused_reason' => null,
        ]);
    }

    public function requestRevision(FoodCampaign $campaign, User $admin, string $note): void
    {
        $campaign->update([
            'status' => CampaignStatus::DRAFT->value,
            'reviewed_by' => $admin->id,
            'review_note' => $note,
        ]);
    }

    public function reject(FoodCampaign $campaign, User $admin, string $note): void
    {
        $campaign->update([
            'status' => CampaignStatus::REJECTED->value,
            'reviewed_by' => $admin->id,
            'review_note' => $note,
        ]);
    }

    public function close(FoodCampaign $campaign, User $user): void
    {
        abort_unless($campaign->user_id === $user->id || $user->isAdmin(), 403, 'Akses ditolak.');
        $campaign->update(['status' => CampaignStatus::CLOSED->value]);
    }

    public function reassign(FoodCampaign $campaign, User $newVolunteer, User $admin): void
    {
        abort_unless($newVolunteer->isVolunteerIn($campaign->district_id), 422, 'Relawan baru tidak disetujui di kecamatan galang ini.');

        $campaign->update([
            'user_id' => $newVolunteer->id,
            'status' => CampaignStatus::PUBLISHED->value,
            'paused_reason' => null,
            'reviewed_by' => $admin->id,
            'review_note' => "Dialihkan oleh admin ke relawan: {$newVolunteer->name}",
        ]);
    }
}
