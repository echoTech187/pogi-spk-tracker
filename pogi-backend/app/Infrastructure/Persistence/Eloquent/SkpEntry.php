<?php

namespace App\Infrastructure\Persistence\Eloquent;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SkpEntry extends Model
{
    protected $table = 'skp_entries';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'doctor_id',
        'period_id',
        'event_id',
        'category_id',
        'domain',
        'title',
        'activity_date',
        'credit_amount',
        'status',
        'verified_by_user_id',
        'verified_at',
        'rejection_reason',
        'kemenkes_log_reference',
    ];

    protected $casts = [
        'activity_date' => 'date',
        'credit_amount' => 'float',
        'verified_at' => 'datetime',
    ];

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class, 'doctor_id', 'id');
    }

    public function period(): BelongsTo
    {
        return $this->belongsTo(DoctorSkpPeriod::class, 'period_id', 'id');
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by_user_id', 'id');
    }
}
