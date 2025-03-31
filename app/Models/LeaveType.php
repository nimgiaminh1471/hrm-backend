<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class LeaveType extends ScopedModel
{
    protected $fillable = [
        'name',
        'description',
        'default_days',
        'is_paid',
        'is_active',
        'settings',
        'notes',
    ];

    protected $casts = [
        'default_days' => 'float',
        'is_paid' => 'boolean',
        'is_active' => 'boolean',
        'settings' => 'array',
        'notes' => 'array',
    ];

    public function leaveRequests(): HasMany
    {
        return $this->hasMany(LeaveRequest::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopePaid($query, bool $isPaid = true)
    {
        return $query->where('is_paid', $isPaid);
    }

    public function scopeSearch($query, string $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%");
        });
    }
} 