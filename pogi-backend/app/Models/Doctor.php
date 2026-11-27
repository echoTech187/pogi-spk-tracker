<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Crypt;

class Doctor extends Model
{
    use HasUuids, SoftDeletes;

    protected $table = 'doctors';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'user_id',
        'npa_pogi',
        'full_name',
        'specialization_title',
        'is_active',
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
        'plataran_sehat_username',
        'plataran_sehat_pass_encrypted',
        'status',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Enkripsi 2-arah (reversible AES-256 via Crypt) untuk Password LMS Plataran Sehat.
     */
    protected function plataranSehatPassEncrypted(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if (empty($value)) {
                    return null;
                }
                try {
                    return Crypt::decryptString($value);
                } catch (\Throwable) {
                    return (string) $value; // Fallback untuk data legacy plain-text
                }
            },
            set: function ($value) {
                if (empty($value)) {
                    return null;
                }
                $trimmed = trim((string) $value);
                try {
                    Crypt::decryptString($trimmed);
                    return $trimmed; // Sudah dalam bentuk ciphertext valid
                } catch (\Throwable) {
                    return Crypt::encryptString($trimmed);
                }
            }
        );
    }

    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class, 'province_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(PogiBranch::class, 'branch_id');
    }

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class, 'primary_institution_id');
    }

    public function skpPeriods(): HasMany
    {
        return $this->hasMany(DoctorSkpPeriod::class, 'doctor_id');
    }

    public function activeSkpPeriod(): HasOne
    {
        return $this->hasOne(DoctorSkpPeriod::class, 'doctor_id')->where('is_active', true);
    }

    public function assignedPic(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_pic_id');
    }
}
