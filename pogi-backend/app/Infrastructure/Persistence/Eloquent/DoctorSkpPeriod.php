<?php

namespace App\Infrastructure\Persistence\Eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DoctorSkpPeriod extends Model
{
    protected $table = 'doctor_skp_periods';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'doctor_id',
        'cycle_number',
        'start_date',
        'end_date',
        'target_total_credits',
        'target_pembelajaran_credits',
        'target_pelayanan_credits',
        'target_pengabdian_credits',
        'target_flexibility_credits',
        'achieved_pembelajaran_credits',
        'achieved_pelayanan_credits',
        'achieved_pengabdian_credits',
        'achieved_total_credits',
        'is_active',
        'is_completed',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'target_total_credits' => 'float',
        'target_pembelajaran_credits' => 'float',
        'target_pelayanan_credits' => 'float',
        'target_pengabdian_credits' => 'float',
        'target_flexibility_credits' => 'float',
        'achieved_pembelajaran_credits' => 'float',
        'achieved_pelayanan_credits' => 'float',
        'achieved_pengabdian_credits' => 'float',
        'achieved_total_credits' => 'float',
        'is_active' => 'boolean',
        'is_completed' => 'boolean',
    ];

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class, 'doctor_id', 'id');
    }

    public function entries(): HasMany
    {
        return $this->hasMany(SkpEntry::class, 'period_id', 'id');
    }
}
