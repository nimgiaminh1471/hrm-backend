<?php

namespace App\Enums;

enum ContractStatus: string
{
    case ACTIVE = 'active';
    case TERMINATED = 'terminated';
    case EXPIRED = 'expired';

    public static function values(): array
    {
        return array_map(fn (ContractStatus $status) => $status->value, self::cases());
    }

    public function label($status): string
    {
        return match ($status) {
            self::ACTIVE => 'Active',
            self::TERMINATED => 'Terminated',
            self::EXPIRED => 'Expired',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())->map(function ($status) {
            return ['id' => $status->value, 'name' => $status->label($status)];
        })->toArray();
    }
} 