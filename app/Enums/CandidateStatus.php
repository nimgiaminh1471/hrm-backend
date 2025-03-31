<?php

namespace App\Enums;

enum CandidateStatus: string
{
    case NEW = 'new';
    case REVIEWING = 'reviewing';
    case SHORTLISTED = 'shortlisted';
    case INTERVIEW_SCHEDULED = 'interview_scheduled';
    case INTERVIEWED = 'interviewed';
    case OFFERED = 'offered';
    case HIRED = 'hired';
    case REJECTED = 'rejected';
    case WITHDRAWN = 'withdrawn';

    public function label(): string
    {
        return match($this) {
            self::NEW => 'New Application',
            self::REVIEWING => 'Under Review',
            self::SHORTLISTED => 'Shortlisted',
            self::INTERVIEW_SCHEDULED => 'Interview Scheduled',
            self::INTERVIEWED => 'Interviewed',
            self::OFFERED => 'Offer Extended',
            self::HIRED => 'Hired',
            self::REJECTED => 'Rejected',
            self::WITHDRAWN => 'Withdrawn',
        };
    }
} 