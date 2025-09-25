<?php

namespace App\Enums\User;

enum UserTypeEnum: string
{
    case SUPER_ADMIN = 'super_admin';
    case ADMIN = 'admin';
    case EMPLOYEE = 'employee';
    case USER = 'user';

    /**
     * Get all values as array
     */
    public static function values(): array
    {
        return [
            self::SUPER_ADMIN->value => 'Super Admin',
            self::ADMIN->value => 'Admin',
            self::EMPLOYEE->value => 'Employee',
            self::USER->value => 'User',
        ];
    }

    /**
     * Get label for the user type
     */
    public function label(): string
    {
        return match ($this) {
            self::SUPER_ADMIN => 'Super Admin',
            self::ADMIN => 'Admin',
            self::EMPLOYEE => 'Employee',
            self::USER => 'User',
        };
    }

    /**
     * Get description for the user type
     */
    public function description(): string
    {
        return match ($this) {
            self::SUPER_ADMIN => 'Super administrator with complete system control',
            self::ADMIN => 'Admin user with dashboard access and management privileges',
            self::EMPLOYEE => 'Employee with limited administrative access',
            self::USER => 'Regular user without dashboard access',
        };
    }

    /**
     * Check if user type is administrative
     */
    public function isAdmin(): bool
    {
        return in_array($this, [self::SUPER_ADMIN, self::ADMIN]);
    }

    /**
     * Check if user type is regular user
     */
    public function isUser(): bool
    {
        return $this === self::USER;
    }

    /**
     * Check if user type is staff (admin or employee)
     */
    public function isStaff(): bool
    {
        return in_array($this, [self::SUPER_ADMIN, self::ADMIN, self::EMPLOYEE]);
    }

    /**
     * Check if user type can access dashboard
     */
    public function canAccessDashboard(): bool
    {
        return in_array($this, [self::SUPER_ADMIN, self::ADMIN, self::EMPLOYEE]);
    }

    /**
     * Get admin types
     */
    public static function adminTypes(): array
    {
        return [self::SUPER_ADMIN, self::ADMIN];
    }

    /**
     * Get staff types
     */
    public static function staffTypes(): array
    {
        return [self::SUPER_ADMIN, self::ADMIN, self::EMPLOYEE];
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
            self::SUPER_ADMIN => [
                '*', // Complete system access
            ],
            self::ADMIN => [
                'dashboard.access', // Key permission for admin users
                'users.manage', 'vendors.manage', 'vehicles.manage',
                'transactions.manage', 'reviews.manage', 'content.manage',
            ],
            self::EMPLOYEE => [
                'dashboard.access', // Limited dashboard access
                'vehicles.view', 'users.view', 'reviews.moderate',
                'content.manage', 'messages.view',
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
            self::SUPER_ADMIN => 'danger',
            self::ADMIN => 'warning',
            self::EMPLOYEE => 'info',
            self::USER => 'success',
        };
    }

    /**
     * Get icon for user type
     */
    public function icon(): string
    {
        return match ($this) {
            self::SUPER_ADMIN => 'shield-check',
            self::ADMIN => 'user-gear',
            self::EMPLOYEE => 'user-cog',
            self::USER => 'user',
        };
    }
}
