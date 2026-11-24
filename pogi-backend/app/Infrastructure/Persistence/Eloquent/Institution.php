<?php

namespace App\Infrastructure\Persistence\Eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Institution extends Model
{
    protected $table = 'institutions';
    public $incrementing = false;
    protected $keyType = 'string';

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
    ];

    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class, 'province_id', 'id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(PogiBranch::class, 'branch_id', 'id');
    }

    public function doctors(): HasMany
    {
        return $this->hasMany(Doctor::class, 'primary_institution_id', 'id');
    }
}
