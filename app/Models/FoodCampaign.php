<?php

namespace App\Models;

use App\Enums\CampaignStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FoodCampaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'district_id',
        'title',
        'slug',
        'body',
        'cover_path',
        'beneficiary_type',
        'beneficiary_name',
        'address',
        'latitude',
        'longitude',
        'public_latitude',
        'public_longitude',
        'location_precision',
        'daily_need',
        'target_portions',
        'collected_portions',
        'starts_at',
        'ends_at',
        'status',
        'paused_reason',
        'reviewed_by',
        'review_note',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'float',
            'longitude' => 'float',
            'public_latitude' => 'float',
            'public_longitude' => 'float',
            'target_portions' => 'integer',
            'collected_portions' => 'integer',
            'starts_at' => 'date',
            'ends_at' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function listings(): HasMany
    {
        return $this->hasMany(FoodListing::class, 'campaign_id');
    }

    public function updates(): HasMany
    {
        return $this->hasMany(CampaignUpdate::class, 'campaign_id');
    }

    public function claims(): HasMany
    {
        return $this->hasMany(Claim::class, 'campaign_id');
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class, 'campaign_id');
    }

    public function progressPercent(): int
    {
        if ($this->target_portions <= 0) {
            return 0;
        }
        return (int) min(100, round(($this->collected_portions / $this->target_portions) * 100));
    }
}
