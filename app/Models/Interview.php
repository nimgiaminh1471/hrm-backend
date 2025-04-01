<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Interview extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'company_id',
        'candidate_id',
        'interviewer_id',
        'type', // phone, video, in-person
        'status', // scheduled, completed, cancelled
        'scheduled_at',
        'duration_minutes',
        'location',
        'notes',
        'feedback',
        'rating',
        'is_active',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'duration_minutes' => 'integer',
        'notes' => 'array',
        'feedback' => 'array',
        'rating' => 'integer',
        'is_active' => 'boolean',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }

    public function interviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'interviewer_id');
    }
} 