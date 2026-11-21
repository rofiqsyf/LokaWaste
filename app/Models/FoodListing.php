<?php

namespace App\Models;

use App\Enums\ListingStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class FoodListing extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'category_id',
        'campaign_id',
        'district_id',
        'title',
        'description',
        'photo_path',
        'food_type',
        'prepared_at',
        'allergens',
        'storage_note',
        'halal_claim',
        'is_vegetarian',
        'portions_total',
        'portions_left',
        'max_portions_per_claim',
        'pickup_address',
        'latitude',
        'longitude',
        'public_latitude',
        'public_longitude',
        'show_exact_location',
        'available_from',
        'expires_at',
        'campaign_priority_until',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'allergens' => 'array',
            'halal_claim' => 'boolean',
            'is_vegetarian' => 'boolean',
            'show_exact_location' => 'boolean',
            'prepared_at' => 'datetime',
            'available_from' => 'datetime',
            'expires_at' => 'datetime',
            'campaign_priority_until' => 'datetime',
            'latitude' => 'float',
            'longitude' => 'float',
            'public_latitude' => 'float',
            'public_longitude' => 'float',
            'portions_total' => 'integer',
            'portions_left' => 'integer',
            'max_portions_per_claim' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(FoodCampaign::class, 'campaign_id');
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    public function claims(): HasMany
    {
        return $this->hasMany(Claim::class, 'listing_id');
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class, 'listing_id');
    }

    public function scopeVisibleToMap(Builder $q): Builder
    {
        return $q->where('status', ListingStatus::AVAILABLE->value)
            ->where('available_from', '<=', now())
            ->where('expires_at', '>', now())
            ->where('portions_left', '>', 0);
    }

    public function isClaimableBy(User $user): bool
    {
        if ($this->user_id === $user->id) {
            return false;
        }

        if ($user->isRestrictedFromClaiming()) {
            return false;
        }

        if ($this->status !== ListingStatus::AVAILABLE->value) {
            return false;
        }

        if ($this->expires_at->isPast() || $this->available_from->isFuture() || $this->portions_left <= 0) {
            return false;
        }

        // Campaign priority check: during priority window, only eligible volunteer of that campaign can claim
        if ($this->campaign_priority_until && $this->campaign_priority_until->isFuture() && $this->campaign_id) {
            $campaign = $this->campaign;
            if (!$campaign || $campaign->status !== 'published') {
                return true; // if campaign is not published, open to everyone
            }
            if (!$user->isVolunteerIn($campaign->district_id)) {
                return false;
            }
        }

        return true;
    }

    public function timezone(): string
    {
        return $this->district?->regency?->province?->timezone ?? 'Asia/Jakarta';
    }

    public function tzLabel(): string
    {
        return match ($this->timezone()) {
            'Asia/Makassar' => 'WITA',
            'Asia/Jayapura' => 'WIT',
            default => 'WIB',
        };
    }

    public function windowMinutes(): int
    {
        if (!$this->available_from || !$this->expires_at) {
            return 120;
        }
        return max(1, $this->available_from->diffInMinutes($this->expires_at));
    }
}
