<?php

namespace App\Models;

use App\Enums\ClaimStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Claim extends Model
{
    use HasFactory;

    protected $fillable = [
        'listing_id',
        'user_id',
        'campaign_id',
        'portions_requested',
        'portions_approved',
        'status',
        'active_key',
        'pickup_code',
        'pickup_attempts',
        'pickup_locked_until',
        'cancelled_by',
        'cancel_reason',
        'note',
        'approved_at',
        'picked_up_at',
    ];

    protected function casts(): array
    {
        return [
            'portions_requested' => 'integer',
            'portions_approved' => 'integer',
            'pickup_attempts' => 'integer',
            'pickup_locked_until' => 'datetime',
            'approved_at' => 'datetime',
            'picked_up_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Claim $claim) {
            if (in_array($claim->status, [ClaimStatus::PENDING->value, ClaimStatus::APPROVED->value, 'pending', 'approved'])) {
                $claim->active_key = "{$claim->listing_id}-{$claim->user_id}";
            } else {
                $claim->active_key = null;
            }
        });
    }

    public function listing(): BelongsTo
    {
        return $this->belongsTo(FoodListing::class, 'listing_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(FoodCampaign::class, 'campaign_id');
    }

    public function cancelledByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    public function review(): HasOne
    {
        return $this->hasOne(Review::class);
    }

    public function scopeActive(Builder $q): Builder
    {
        return $q->whereIn('status', [ClaimStatus::PENDING->value, ClaimStatus::APPROVED->value, 'pending', 'approved']);
    }
}
