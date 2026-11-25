<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EventCategory extends Model
{
    use HasUuids;

    protected $table = 'event_categories';
    protected $keyType = 'string';
    public $incrementing = false;
    public const UPDATED_AT = null;

    protected $fillable = [
        'id',
        'name',
        'domain',
        'default_credit_value',
        'description',
    ];

    public function events(): HasMany
    {
        return $this->hasMany(SkpEvent::class, 'category_id');
    }
}
