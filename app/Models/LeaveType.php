<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeaveType extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'company_id',
        'name',
        'description',
        'default_days',
        'is_paid',
        'is_active',
        'settings',
        'notes',
    ];

    protected $casts = [
        'default_days' => 'integer',
        'is_paid' => 'boolean',
        'is_active' => 'boolean',
        'settings' => 'array',
        'notes' => 'array',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function leaveRequests(): HasMany
    {
        return $this->hasMany(LeaveRequest::class);
    }
} 