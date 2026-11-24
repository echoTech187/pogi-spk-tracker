<?php

namespace App\Infrastructure\Persistence\Eloquent;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Doctor extends Model
{
    protected $table = 'doctors';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'user_id',
        'npa_pogi',
        'full_name',
        'specialization_title',
        'nik_encrypted',
        'nik_hash',
        'str_number',
        'sip_number',
        'sip_expiry_date',
        'email',
        'whatsapp_number',
        'branch_id',
        'province_id',
        'primary_institution_id',
        'assigned_pic_id',
        'status',
        'dashboard_updated_at',
        'kemenkes_updated_at',
    ];

    protected $casts = [
        'sip_expiry_date' => 'date',
        'dashboard_updated_at' => 'datetime',
        'kemenkes_updated_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(PogiBranch::class, 'branch_id', 'id');
    }

    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class, 'province_id', 'id');
    }

    public function primaryInstitution(): BelongsTo
    {
        return $this->belongsTo(Institution::class, 'primary_institution_id', 'id');
    }

    public function pic(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_pic_id', 'id');
    }

    public function periods(): HasMany
    {
        return $this->hasMany(DoctorSkpPeriod::class, 'doctor_id', 'id');
    }

    public function activePeriod(): HasOne
    {
        return $this->hasOne(DoctorSkpPeriod::class, 'doctor_id', 'id')->where('is_active', true);
    }

    public function skpEntries(): HasMany
    {
        return $this->hasMany(SkpEntry::class, 'doctor_id', 'id');
    }
}
