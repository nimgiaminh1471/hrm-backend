<?php

namespace App\Enums;

enum PositionLevel: string
{
    case ENTRY = 'entry';
    case MID = 'mid';
    case SENIOR = 'senior';
    case LEAD = 'lead';
    case MANAGER = 'manager';
    case DIRECTOR = 'director';
    case VP = 'vp';
    case C_LEVEL = 'c_level';

    public static function values(): array
    {
        return array_map(fn (PositionLevel $level) => $level->value, PositionLevel::cases());
    }

    public static function label(PositionLevel $level): string
    {
        return match ($level) {
            PositionLevel::ENTRY => 'Entry',
            PositionLevel::MID => 'Mid',
            PositionLevel::SENIOR => 'Senior',
            PositionLevel::LEAD => 'Lead',
            PositionLevel::MANAGER => 'Manager',
            PositionLevel::DIRECTOR => 'Director',
            PositionLevel::VP => 'VP',
            PositionLevel::C_LEVEL => 'C-Level',
        };
    }

    public static function options(): array
    {
        return array_map(fn (PositionLevel $level) => [
            'id' => $level->value,
            'name' => $level->label($level),
        ], PositionLevel::cases());
    }
} 