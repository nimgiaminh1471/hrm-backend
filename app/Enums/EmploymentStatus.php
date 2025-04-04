<?php

namespace App\Enums;

enum EmploymentStatus: string
{
    case ACTIVE = 'active';
    case ON_LEAVE = 'on_leave';
    case TERMINATED = 'terminated';
    case RESIGNED = 'resigned';
    case SUSPENDED = 'suspended';

    public static function values(): array
    {
        return array_map(fn (EmploymentStatus $status) => $status->value, self::cases());
    }

    public function label($status): string
    {
        return match ($status) {
            self::ACTIVE => 'Active',
            self::ON_LEAVE => 'On Leave',
            self::TERMINATED => 'Terminated',
            self::RESIGNED => 'Resigned',
            self::SUSPENDED => 'Suspended',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())->map(function ($status) {
            return ['id' => $status->value, 'name' => $status->label($status)];
        })->toArray();
    }
} 