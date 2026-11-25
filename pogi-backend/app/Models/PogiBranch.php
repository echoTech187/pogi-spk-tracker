<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PogiBranch extends Model
{
    use HasUuids;

    protected $table = 'pogi_branches';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'province_id',
        'branch_code',
        'branch_name',
        'secretariat_address',
        'contact_email',
        'contact_phone',
    ];

    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class, 'province_id');
    }

    public function doctors(): HasMany
    {
        return $this->hasMany(Doctor::class, 'branch_id');
    }
}
