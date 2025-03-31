<?php

namespace App\Enums;

enum EmploymentType: string
{
    case FULL_TIME = 'full_time';
    case PART_TIME = 'part_time';
    case CONTRACT = 'contract';
    case INTERNSHIP = 'internship';
    case TEMPORARY = 'temporary';
    case FREELANCE = 'freelance';

    public function label(): string
    {
        return match($this) {
            self::FULL_TIME => 'Full Time',
            self::PART_TIME => 'Part Time',
            self::CONTRACT => 'Contract',
            self::INTERNSHIP => 'Internship',
            self::TEMPORARY => 'Temporary',
            self::FREELANCE => 'Freelance',
        };
    }
} 