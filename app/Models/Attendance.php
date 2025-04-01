<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attendance extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'company_id',
        'employee_id',
        'date',
        'clock_in',
        'clock_out',
        'late_minutes',
        'early_leave_minutes',
        'overtime_minutes',
        'status', // present, absent, late, early_leave, half_day
        'notes',
        'is_active',
    ];

    protected $casts = [
        'date' => 'date',
        'clock_in' => 'datetime',
        'clock_out' => 'datetime',
        'late_minutes' => 'integer',
        'early_leave_minutes' => 'integer',
        'overtime_minutes' => 'integer',
        'notes' => 'array',
        'is_active' => 'boolean',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
} 