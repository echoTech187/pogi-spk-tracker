<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Institution extends Model
{
    use HasUuids;

    protected $table = 'institutions';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'province_id',
        'branch_id',
        'code',
        'name',
        'institution_type',
        'hospital_class',
        'address',
        'city',
        'is_active',
        'status',
    ];

    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class, 'province_id');
    }

    public function doctors(): HasMany
    {
        return $this->hasMany(Doctor::class, 'primary_institution_id');
    }
}
