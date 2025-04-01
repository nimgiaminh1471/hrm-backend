<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobPosting extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'company_id',
        'department_id',
        'position_id',
        'title',
        'description',
        'requirements',
        'responsibilities',
        'qualifications',
        'experience_years',
        'salary_min',
        'salary_max',
        'salary_type', // hourly, monthly, yearly
        'job_type', // full-time, part-time, contract, internship
        'location',
        'remote_type', // remote, hybrid, on-site
        'status', // draft, published, closed
        'published_at',
        'closing_date',
        'settings',
        'is_active',
    ];

    protected $casts = [
        'requirements' => 'array',
        'responsibilities' => 'array',
        'qualifications' => 'array',
        'experience_years' => 'float',
        'salary_min' => 'decimal:2',
        'salary_max' => 'decimal:2',
        'published_at' => 'datetime',
        'closing_date' => 'datetime',
        'settings' => 'array',
        'is_active' => 'boolean',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    public function candidates(): HasMany
    {
        return $this->hasMany(Candidate::class);
    }
} 