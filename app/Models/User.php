<?php

namespace App\Models;

use App\Enums\AreaStatus;
use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     * Note: 'role', 'is_verified', 'is_active', 'no_show_count', 'claim_restricted_until' are intentionally NOT fillable!
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'district_id',
        'default_latitude',
        'default_longitude',
        'organization_name',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_verified' => 'boolean',
            'is_active' => 'boolean',
            'claim_restricted_until' => 'datetime',
            'default_latitude' => 'float',
            'default_longitude' => 'float',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === UserRole::ADMIN->value || $this->role === 'admin';
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    public function volunteerAreas(): HasMany
    {
        return $this->hasMany(VolunteerArea::class);
    }

    public function volunteerApplications(): HasMany
    {
        return $this->hasMany(VolunteerApplication::class);
    }

    public function listings(): HasMany
    {
        return $this->hasMany(FoodListing::class);
    }

    public function claims(): HasMany
    {
        return $this->hasMany(Claim::class);
    }

    public function campaigns(): HasMany
    {
        return $this->hasMany(FoodCampaign::class);
    }

    public function approvedDistrictIds(): Collection
    {
        return $this->volunteerAreas()
            ->where('status', AreaStatus::APPROVED->value)
            ->pluck('district_id');
    }

    public function isVolunteer(): bool
    {
        return $this->volunteerAreas()
            ->where('status', AreaStatus::APPROVED->value)
            ->exists();
    }

    public function isVolunteerIn(int $districtId): bool
    {
        return $this->approvedDistrictIds()->contains($districtId);
    }

    public function isRestrictedFromClaiming(): bool
    {
        return $this->claim_restricted_until && $this->claim_restricted_until->isFuture();
    }
}
