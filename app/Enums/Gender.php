<?php

namespace App\Enums;

enum Gender: string
{
    case MALE = 'male';
    case FEMALE = 'female';
    case OTHER = 'other';
    case PREFER_NOT_TO_SAY = 'prefer_not_to_say';

    public static function values(): array
    {
        return array_map(fn (Gender $gender) => $gender->value, self::cases());
    }

    public function label($gender): string
    {
        return match ($gender) {
            self::MALE => 'Male',
            self::FEMALE => 'Female',
            self::PREFER_NOT_TO_SAY => 'Prefer not to say',
            self::OTHER => 'Other',
        };
    }
    public static function options(): array
    {
        return collect(self::cases())->map(function ($gender) {
            return ['id' => $gender->value, 'name' => $gender->label($gender)];
        })->toArray();
    }
    
} 