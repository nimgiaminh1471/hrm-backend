<?php

namespace App\Models;

use App\Enums\EmploymentType;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JobPosting extends ScopedModel
{
    protected $fillable = [
        'title',
        'description',
        'requirements',
        'responsibilities',
        'employment_type',
        'department_id',
        'location',
        'salary_range_min',
        'salary_range_max',
        'experience_years',
        'education_level',
        'skills_required',
        'benefits',
        'deadline',
        'is_active',
        'status',
        'notes',
    ];

    protected $casts = [
        'employment_type' => EmploymentType::class,
        'salary_range_min' => 'decimal:2',
        'salary_range_max' => 'decimal:2',
        'experience_years' => 'float',
        'deadline' => 'datetime',
        'is_active' => 'boolean',
        'requirements' => 'array',
        'responsibilities' => 'array',
        'skills_required' => 'array',
        'benefits' => 'array',
        'notes' => 'array',
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function candidates(): HasMany
    {
        return $this->hasMany(Candidate::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeDepartment($query, int $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }

    public function scopeEmploymentType($query, EmploymentType $type)
    {
        return $query->where('employment_type', $type);
    }

    public function scopeSearch($query, string $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%")
              ->orWhere('location', 'like', "%{$search}%");
        });
    }
} 