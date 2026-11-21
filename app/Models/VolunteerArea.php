<?php

namespace App\Models;

use App\Enums\AreaStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VolunteerArea extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id',
        'user_id',
        'regency_id',
        'district_id',
        'status',
        'decided_by',
        'decided_at',
        'decision_note',
        'cooldown_until',
    ];

    protected function casts(): array
    {
        return [
            'decided_at' => 'datetime',
            'cooldown_until' => 'datetime',
        ];
    }

    public function application(): BelongsTo
    {
        return $this->belongsTo(VolunteerApplication::class, 'application_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function regency(): BelongsTo
    {
        return $this->belongsTo(Regency::class);
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    public function decidedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'decided_by');
    }
}
