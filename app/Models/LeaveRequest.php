<?php

namespace App\Models;

use App\Enums\LeaveRequestStatus;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaveRequest extends ScopedModel
{
    protected $fillable = [
        'employee_id',
        'leave_type_id',
        'start_date',
        'end_date',
        'duration_days',
        'reason',
        'status',
        'approved_by',
        'approved_at',
        'rejection_reason',
        'rejected_at',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'duration_days' => 'float',
        'status' => LeaveRequestStatus::class,
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'is_active' => 'boolean',
        'notes' => 'array',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    public function leaveType(): BelongsTo
    {
        return $this->belongsTo(LeaveType::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeStatus($query, LeaveRequestStatus $status)
    {
        return $query->where('status', $status);
    }

    public function scopeEmployee($query, int $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    public function scopeLeaveType($query, int $leaveTypeId)
    {
        return $query->where('leave_type_id', $leaveTypeId);
    }

    public function scopeDateRange($query, string $start, string $end)
    {
        return $query->where(function ($q) use ($start, $end) {
            $q->whereBetween('start_date', [$start, $end])
              ->orWhereBetween('end_date', [$start, $end])
              ->orWhere(function ($q) use ($start, $end) {
                  $q->where('start_date', '<=', $start)
                    ->where('end_date', '>=', $end);
              });
        });
    }

    public function scopeSearch($query, string $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->whereHas('employee', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })
            ->orWhereHas('leaveType', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })
            ->orWhere('reason', 'like', "%{$search}%");
        });
    }
} 