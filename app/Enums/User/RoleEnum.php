<?php

namespace App\Enums\User;

enum RoleEnum: int
{
    case Admin = 1;
    case User = 2;
    case Employee = 3;

    public static function values(): array
    {
        return [
            self::Admin->value => 'Admin',
            self::User->value => 'User',
            self::Employee->value => 'Employee',
        ];
    }

    public static function getValue(int $key): ?string
    {
        $values = self::values();

        return $values[$key] ?? null;
    }
}
