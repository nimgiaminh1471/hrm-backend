<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

abstract class ScopedModel extends Model
{
    use BelongsToTenant;

    protected $guarded = [];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'is_active' => 'boolean',
        'settings' => 'array',
        'json' => 'array',
    ];

    public function getTable()
    {
        return $this->table ?? strtolower(class_basename($this));
    }
} 