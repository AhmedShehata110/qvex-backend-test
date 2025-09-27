<?php

namespace App\Enums\User;

enum UserTypeEnum: string
{
    case ADMIN = 'admin';
    case USER = 'user';

    /**
     * Get all values as array
     */
    public static function values(): array
    {
        return [
            self::ADMIN->value => 'Admin',
            self::USER->value => 'User',
        ];
    }

    /**
     * Get label for the user type
     */
    public function label(): string
    {
        return match ($this) {
            self::ADMIN => 'Admin',
            self::USER => 'User',
        };
    }

    /**
     * Get description for the user type
     */
    public function description(): string
    {
        return match ($this) {
            self::ADMIN => 'Administrator with complete system access and management privileges',
            self::USER => 'Regular user/customer using the system',
        };
    }

    /**
     * Check if user type is administrative
     */
    public function isAdmin(): bool
    {
        return $this === self::ADMIN;
    }

    /**
     * Check if user type is regular user
     */
    public function isUser(): bool
    {
        return $this === self::USER;
    }

    /**
     * Check if user type can access dashboard
     */
    public function canAccessDashboard(): bool
    {
        return $this === self::ADMIN;
    }

    /**
     * Get admin types
     */
    public static function adminTypes(): array
    {
        return [self::ADMIN];
    }

    /**
     * Get user types
     */
    public static function userTypes(): array
    {
        return [self::USER];
    }

    /**
     * Get all types as string array
     */
    public static function toArray(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get default permissions for user type
     */
    public function defaultPermissions(): array
    {
        return match ($this) {
            self::ADMIN => [
                '*', // Complete system access for admin users
            ],
            self::USER => [
                'profile.view', 'profile.update',
                'vehicles.view', 'messages.create', 'reviews.create',
                'transactions.create', 'transactions.view',
                // Note: NO 'dashboard.access' permission
            ],
        };
    }

    /**
     * Get CSS color class for user type
     */
    public function color(): string
    {
        return match ($this) {
            self::ADMIN => 'danger',
            self::USER => 'success',
        };
    }

    /**
     * Get icon for user type
     */
    public function icon(): string
    {
        return match ($this) {
            self::ADMIN => 'shield-check',
            self::USER => 'user',
        };
    }
}
