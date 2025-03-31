<?php

namespace App\Models;

use App\Enums\InterviewStatus;
use App\Enums\InterviewType;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Interview extends ScopedModel
{
    protected $fillable = [
        'candidate_id',
        'interviewer_id',
        'type',
        'status',
        'scheduled_at',
        'duration_minutes',
        'location',
        'notes',
        'feedback',
        'rating',
        'is_active',
    ];

    protected $casts = [
        'type' => InterviewType::class,
        'status' => InterviewStatus::class,
        'scheduled_at' => 'datetime',
        'duration_minutes' => 'integer',
        'rating' => 'integer',
        'is_active' => 'boolean',
        'notes' => 'array',
        'feedback' => 'array',
    ];

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }

    public function interviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'interviewer_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeStatus($query, InterviewStatus $status)
    {
        return $query->where('status', $status);
    }

    public function scopeType($query, InterviewType $type)
    {
        return $query->where('type', $type);
    }

    public function scopeCandidate($query, int $candidateId)
    {
        return $query->where('candidate_id', $candidateId);
    }

    public function scopeInterviewer($query, int $interviewerId)
    {
        return $query->where('interviewer_id', $interviewerId);
    }

    public function scopeScheduledBetween($query, string $start, string $end)
    {
        return $query->whereBetween('scheduled_at', [$start, $end]);
    }

    public function scopeSearch($query, string $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->whereHas('candidate', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })
            ->orWhereHas('interviewer', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        });
    }
} 