<?php

namespace App\Enums;

enum MaritalStatus: string
{
    case SINGLE = 'single';
    case MARRIED = 'married';
    case DIVORCED = 'divorced';
    case WIDOWED = 'widowed';
    case OTHER = 'other';

    public static function values(): array
    {
        return array_map(fn (MaritalStatus $maritalStatus) => $maritalStatus->value, self::cases());
    }

    public function label($maritalStatus): string
    {
        return match ($maritalStatus) {
            self::SINGLE => 'Single',
            self::MARRIED => 'Married',
            self::DIVORCED => 'Divorced',
            self::WIDOWED => 'Widowed',
            self::OTHER => 'Other',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())->map(function ($maritalStatus) {
            return ['id' => $maritalStatus->value, 'name' => $maritalStatus->label($maritalStatus)];
        })->toArray();
    }
} 