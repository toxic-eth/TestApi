<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HealthCheckRequest extends Model
{
    protected $fillable = [
        'owner_uuid',
        'db_ok',
        'cache_ok',
        'response_code',
    ];

    protected function casts(): array
    {
        return [
            'db_ok' => 'boolean',
            'cache_ok' => 'boolean',
            'response_code' => 'integer',
        ];
    }
}