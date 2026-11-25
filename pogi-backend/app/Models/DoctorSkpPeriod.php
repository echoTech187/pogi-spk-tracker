<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DoctorSkpPeriod extends Model
{
    use HasUuids;

    protected $table = 'doctor_skp_periods';
    protected $keyType = 'string';
    public $incrementing = false;

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

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class, 'doctor_id');
    }
}
