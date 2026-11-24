<?php

namespace App\Infrastructure\Persistence\Eloquent;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PogiBranch extends Model
{
    protected $table = 'pogi_branches';
    public $incrementing = false;
    protected $keyType = 'string';

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
        return $this->belongsTo(Province::class, 'province_id', 'id');
    }

    public function institutions(): HasMany
    {
        return $this->hasMany(Institution::class, 'branch_id', 'id');
    }

    public function doctors(): HasMany
    {
        return $this->hasMany(Doctor::class, 'branch_id', 'id');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'branch_id', 'id');
    }
}
