<?php

namespace App\Enums;

enum InterviewType: string
{
    case PHONE = 'phone';
    case VIDEO = 'video';
    case IN_PERSON = 'in_person';
    case TECHNICAL = 'technical';
    case BEHAVIORAL = 'behavioral';
    case GROUP = 'group';
    case FINAL = 'final';

    public function label(): string
    {
        return match($this) {
            self::PHONE => 'Phone Interview',
            self::VIDEO => 'Video Interview',
            self::IN_PERSON => 'In-Person Interview',
            self::TECHNICAL => 'Technical Interview',
            self::BEHAVIORAL => 'Behavioral Interview',
            self::GROUP => 'Group Interview',
            self::FINAL => 'Final Interview',
        };
    }
} 