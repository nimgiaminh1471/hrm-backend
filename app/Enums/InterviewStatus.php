<?php

namespace App\Enums;

enum InterviewStatus: string
{
    case SCHEDULED = 'scheduled';
    case IN_PROGRESS = 'in_progress';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';
    case NO_SHOW = 'no_show';
    case RESCHEDULED = 'rescheduled';

    public function label(): string
    {
        return match($this) {
            self::SCHEDULED => 'Scheduled',
            self::IN_PROGRESS => 'In Progress',
            self::COMPLETED => 'Completed',
            self::CANCELLED => 'Cancelled',
            self::NO_SHOW => 'No Show',
            self::RESCHEDULED => 'Rescheduled',
        };
    }
} 