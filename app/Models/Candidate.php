<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Candidate extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'company_id',
        'job_posting_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'resume_path',
        'cover_letter',
        'experience_years',
        'current_salary',
        'expected_salary',
        'notice_period',
        'status', // applied, screening, interviewed, offered, hired, rejected
        'notes',
        'is_active',
    ];

    protected $casts = [
        'experience_years' => 'float',
        'current_salary' => 'decimal:2',
        'expected_salary' => 'decimal:2',
        'notice_period' => 'integer',
        'notes' => 'array',
        'is_active' => 'boolean',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function jobPosting(): BelongsTo
    {
        return $this->belongsTo(JobPosting::class);
    }

    public function interviews(): HasMany
    {
        return $this->hasMany(Interview::class);
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }
} 