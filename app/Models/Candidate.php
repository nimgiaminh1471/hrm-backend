<?php

namespace App\Models;

use App\Enums\CandidateStatus;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Candidate extends ScopedModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'job_posting_id',
        'resume_path',
        'cover_letter',
        'experience_years',
        'current_salary',
        'expected_salary',
        'notice_period',
        'status',
        'notes',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'status' => CandidateStatus::class,
        'experience_years' => 'float',
        'current_salary' => 'decimal:2',
        'expected_salary' => 'decimal:2',
        'notice_period' => 'integer',
        'is_active' => 'boolean',
        'notes' => 'array',
    ];

    /**
     * Get the job posting that owns the candidate.
     */
    public function jobPosting(): BelongsTo
    {
        return $this->belongsTo(JobPosting::class);
    }

    /**
     * Get the interviews for the candidate.
     */
    public function interviews(): HasMany
    {
        return $this->hasMany(Interview::class);
    }

    /**
     * Get the candidate's full name.
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Scope a query to only include active candidates.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to filter by status.
     */
    public function scopeStatus($query, CandidateStatus $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to filter by job posting.
     */
    public function scopeJobPosting($query, int $jobPostingId)
    {
        return $query->where('job_posting_id', $jobPostingId);
    }

    /**
     * Scope a query to search candidates.
     */
    public function scopeSearch($query, string $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('first_name', 'like', "%{$search}%")
              ->orWhere('last_name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%");
        });
    }
} 