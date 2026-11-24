<?php

namespace App\Infrastructure\Persistence\Eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Province extends Model
{
    protected $table = 'provinces';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'name',
        'region_island',
        'latitude',
        'longitude',
        'created_at',
    ];

    public function branches(): HasMany
    {
        return $this->hasMany(PogiBranch::class, 'province_id', 'id');
    }

    public function institutions(): HasMany
    {
        return $this->hasMany(Institution::class, 'province_id', 'id');
    }

    public function doctors(): HasMany
    {
        return $this->hasMany(Doctor::class, 'province_id', 'id');
    }
}
