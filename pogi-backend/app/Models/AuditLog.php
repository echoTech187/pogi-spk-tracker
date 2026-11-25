<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use HasUuids;

    protected $table = 'audit_logs';
    protected $keyType = 'string';
    public $incrementing = false;
    public const UPDATED_AT = null;

    protected $fillable = [
        'id',
        'user_id',
        'action',
        'entity_name',
        'entity_id',
        'new_values',
    ];
}
