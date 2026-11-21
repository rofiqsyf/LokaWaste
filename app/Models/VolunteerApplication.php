<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VolunteerApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'regency_id',
        'motivation',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function regency(): BelongsTo
    {
        return $this->belongsTo(Regency::class);
    }

    public function areas(): HasMany
    {
        return $this->hasMany(VolunteerArea::class, 'application_id');
    }

    /**
     * Derived application status (SDD 3.6 / 5.2):
     * Does not have a stored status column; status is derived from its areas.
     */
    public function derivedStatus(): string
    {
        $statuses = $this->areas()->pluck('status');
        if ($statuses->isEmpty()) {
            return 'pending';
        }

        if ($statuses->every(fn($s) => $s === 'approved')) {
            return 'approved';
        }

        if ($statuses->every(fn($s) => $s === 'rejected')) {
            return 'rejected';
        }

        if ($statuses->contains('approved')) {
            return 'partially_approved';
        }

        return 'pending';
    }
}
