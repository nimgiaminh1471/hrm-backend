<?php

namespace App\Enums;

enum ContractType: string
{
    case FULL_TIME = 'full_time';
    case PART_TIME = 'part_time';
    case CONTRACT = 'contract';
    case INTERNSHIP = 'internship';

    public static function values(): array
    {
        return array_map(fn (ContractType $type) => $type->value, self::cases());
    }

    public function label($type): string
    {
        return match ($type) {
            self::FULL_TIME => 'Full Time',
            self::PART_TIME => 'Part Time',
            self::CONTRACT => 'Contract',
            self::INTERNSHIP => 'Internship',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())->map(function ($type) {
            return ['id' => $type->value, 'name' => $type->label($type)];
        })->toArray();
    }
} 