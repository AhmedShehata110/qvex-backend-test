<?php

namespace App\Enums\User;

enum ActiveEnum: int
{
    case Active = 1;
    case Inactive = 0;

    public static function values(): array
    {
        return [
            self::Active->value => __('keys.active'),
            self::Inactive->value => __('keys.inactive'),
        ];
    }

    public static function getValue(int $key): ?string
    {
        $values = self::values();

        return $values[$key] ?? null;
    }
}
