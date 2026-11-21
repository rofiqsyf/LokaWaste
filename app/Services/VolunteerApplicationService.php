<?php

namespace App\Services;

use App\Enums\AreaStatus;
use App\Models\District;
use App\Models\FoodCampaign;
use App\Models\User;
use App\Models\VolunteerApplication;
use App\Models\VolunteerArea;
use Illuminate\Support\Facades\DB;

class VolunteerApplicationService
{
    /**
     * Submit volunteer application for a single regency and multiple districts.
     * SDD 8.3: Enforces single regency per submission, handles reapplication cooldown.
     */
    public function submit(User $user, int $regencyId, array $districtIds, string $motivation): VolunteerApplication
    {
        return DB::transaction(function () use ($user, $regencyId, $districtIds, $motivation) {
            $application = VolunteerApplication::create([
                'user_id' => $user->id,
                'regency_id' => $regencyId,
                'motivation' => $motivation,
            ]);

            // Only districts belonging to the selected regency are valid (TC-25)
            $validDistricts = District::where('regency_id', $regencyId)
                ->whereIn('id', $districtIds)
                ->pluck('id');

            abort_if($validDistricts->isEmpty(), 422, 'Pilih setidaknya satu kecamatan yang valid.');

            foreach ($validDistricts as $districtId) {
                $area = VolunteerArea::where([
                    'user_id' => $user->id,
                    'district_id' => $districtId,
                ])->lockForUpdate()->first();

                if (!$area) {
                    VolunteerArea::create([
                        'application_id' => $application->id,
                        'user_id' => $user->id,
                        'regency_id' => $regencyId,
                        'district_id' => $districtId,
                        'status' => AreaStatus::PENDING->value,
                    ]);
                } elseif (
                    in_array($area->status, [AreaStatus::REJECTED->value, AreaStatus::RESIGNED->value, 'rejected', 'resigned'])
                    && (!$area->cooldown_until || $area->cooldown_until->isPast())
                ) {
                    // Re-application allowed after 7-day cooldown (Audit #8, TC-24)
                    $area->update([
                        'application_id' => $application->id,
                        'status' => AreaStatus::PENDING->value,
                        'decided_by' => null,
                        'decided_at' => null,
                        'decision_note' => null,
                        'cooldown_until' => null,
                    ]);
                }
                // Pending or Approved areas are skipped; Revoked can only be restored by admin
            }

            return $application;
        });
    }

    public function approveArea(VolunteerArea $area, User $admin, ?string $note = null): void
    {
        $area->update([
            'status' => AreaStatus::APPROVED->value,
            'decided_by' => $admin->id,
            'decided_at' => now(),
            'decision_note' => $note,
            'cooldown_until' => null,
        ]);
    }

    public function rejectArea(VolunteerArea $area, User $admin, ?string $note = null): void
    {
        $area->update([
            'status' => AreaStatus::REJECTED->value,
            'decided_by' => $admin->id,
            'decided_at' => now(),
            'decision_note' => $note,
            'cooldown_until' => now()->addDays(7), // 7-day cooldown
        ]);
    }

    public function revokeArea(VolunteerArea $area, User $admin, ?string $note = null): void
    {
        DB::transaction(function () use ($area, $admin, $note) {
            $area->update([
                'status' => AreaStatus::REVOKED->value,
                'decided_by' => $admin->id,
                'decided_at' => now(),
                'decision_note' => $note,
            ]);

            // SDD 8.3: If volunteer revoked, pause active campaigns in that district
            FoodCampaign::where('user_id', $area->user_id)
                ->where('district_id', $area->district_id)
                ->where('status', 'published')
                ->update([
                    'status' => 'paused',
                    'paused_reason' => 'Pengelola tidak lagi relawan di kecamatan ini (status dicabut admin)',
                ]);
        });
    }

    public function resignArea(VolunteerArea $area, User $user): void
    {
        abort_unless($area->user_id === $user->id, 403, 'Akses ditolak.');

        DB::transaction(function () use ($area) {
            $area->update([
                'status' => AreaStatus::RESIGNED->value,
                'decided_at' => now(),
                'decision_note' => 'Mundur atas permintaan relawan',
            ]);

            // SDD 8.3: If volunteer resigns, pause active campaigns in that district
            FoodCampaign::where('user_id', $area->user_id)
                ->where('district_id', $area->district_id)
                ->where('status', 'published')
                ->update([
                    'status' => 'paused',
                    'paused_reason' => 'Pengelola telah mundur dari relawan di kecamatan ini',
                ]);
        });
    }
}
