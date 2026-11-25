<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SkpEvent extends Model
{
    use HasUuids;

    protected $table = 'skp_events';
    protected $keyType = 'string';
    public $incrementing = false;
    public const UPDATED_AT = null;

    protected $fillable = [
        'id',
        'category_id',
        'title',
        'organizer',
        'event_date',
        'credit_value',
        'kemenkes_accreditation_number',
        'plataran_sehat_course_id',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(EventCategory::class, 'category_id');
    }
}
