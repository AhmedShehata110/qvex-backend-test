<?php

namespace App\Models\Vendor;

use App\Models\BaseModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class VendorStaff extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'vendor_id',
        'user_id',
        'position',
        'department',
        'role',
        'permissions',
        'hire_date',
        'employment_status',
        'salary',
        'commission_rate',
        'phone',
        'email',
        'emergency_contact_name',
        'emergency_contact_phone',
        'address',
        'employee_id',
        'notes',
        'is_active',
        'last_active_at',
        'added_by',
    ];

    protected $casts = [
        'permissions' => 'array',
        'hire_date' => 'date',
        'salary' => 'decimal:2',
        'commission_rate' => 'decimal:2',
        'is_active' => 'boolean',
        'last_active_at' => 'timestamp',
    ];

    protected $translatable = [
        'position',
        'department',
        'notes',
    ];

    // Role constants
    const ROLE_MANAGER = 'manager';

    const ROLE_SALES = 'sales';

    const ROLE_FINANCE = 'finance';

    const ROLE_SERVICE = 'service';

    const ROLE_SUPPORT = 'support';

    const ROLE_ADMIN = 'admin';

    // Employment status constants
    const EMPLOYMENT_STATUS_FULL_TIME = 'full_time';

    const EMPLOYMENT_STATUS_PART_TIME = 'part_time';

    const EMPLOYMENT_STATUS_CONTRACT = 'contract';

    const EMPLOYMENT_STATUS_INTERN = 'intern';

    const EMPLOYMENT_STATUS_TERMINATED = 'terminated';

    // Permission constants
    const PERMISSION_MANAGE_VEHICLES = 'manage_vehicles';

    const PERMISSION_MANAGE_INQUIRIES = 'manage_inquiries';

    const PERMISSION_MANAGE_TRANSACTIONS = 'manage_transactions';

    const PERMISSION_MANAGE_CUSTOMERS = 'manage_customers';

    const PERMISSION_MANAGE_REPORTS = 'manage_reports';

    const PERMISSION_MANAGE_SETTINGS = 'manage_settings';

    const PERMISSION_MANAGE_STAFF = 'manage_staff';

    const PERMISSION_MANAGE_FINANCES = 'manage_finances';

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function addedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    /**
     * Get roles with labels
     */
    public static function getRoles(): array
    {
        return [
            self::ROLE_MANAGER => 'Manager',
            self::ROLE_SALES => 'Sales Representative',
            self::ROLE_FINANCE => 'Finance Officer',
            self::ROLE_SERVICE => 'Service Advisor',
            self::ROLE_SUPPORT => 'Support Staff',
            self::ROLE_ADMIN => 'Administrator',
        ];
    }

    /**
     * Get employment statuses with labels
     */
    public static function getEmploymentStatuses(): array
    {
        return [
            self::EMPLOYMENT_STATUS_FULL_TIME => 'Full Time',
            self::EMPLOYMENT_STATUS_PART_TIME => 'Part Time',
            self::EMPLOYMENT_STATUS_CONTRACT => 'Contract',
            self::EMPLOYMENT_STATUS_INTERN => 'Intern',
            self::EMPLOYMENT_STATUS_TERMINATED => 'Terminated',
        ];
    }

    /**
     * Get available permissions with labels
     */
    public static function getAvailablePermissions(): array
    {
        return [
            self::PERMISSION_MANAGE_VEHICLES => 'Manage Vehicles',
            self::PERMISSION_MANAGE_INQUIRIES => 'Manage Inquiries',
            self::PERMISSION_MANAGE_TRANSACTIONS => 'Manage Transactions',
            self::PERMISSION_MANAGE_CUSTOMERS => 'Manage Customers',
            self::PERMISSION_MANAGE_REPORTS => 'View Reports',
            self::PERMISSION_MANAGE_SETTINGS => 'Manage Settings',
            self::PERMISSION_MANAGE_STAFF => 'Manage Staff',
            self::PERMISSION_MANAGE_FINANCES => 'Manage Finances',
        ];
    }

    /**
     * Get role label
     */
    public function getRoleLabelAttribute(): string
    {
        return static::getRoles()[$this->role] ?? $this->role;
    }

    /**
     * Get employment status label
     */
    public function getEmploymentStatusLabelAttribute(): string
    {
        return static::getEmploymentStatuses()[$this->employment_status] ?? $this->employment_status;
    }

    /**
     * Get full name
     */
    public function getFullNameAttribute(): string
    {
        return $this->user ? $this->user->name : 'Unknown';
    }

    /**
     * Get years of service
     */
    public function getYearsOfServiceAttribute(): float
    {
        if (! $this->hire_date) {
            return 0;
        }

        return $this->hire_date->diffInYears(now(), true);
    }

    /**
     * Get formatted salary
     */
    public function getFormattedSalaryAttribute(): ?string
    {
        return $this->salary ? '$'.number_format($this->salary, 2) : null;
    }

    /**
     * Check if staff member is active
     */
    public function isActive(): bool
    {
        return $this->is_active && $this->employment_status !== self::EMPLOYMENT_STATUS_TERMINATED;
    }

    /**
     * Check if staff member is a manager
     */
    public function isManager(): bool
    {
        return $this->role === self::ROLE_MANAGER || $this->role === self::ROLE_ADMIN;
    }

    /**
     * Check if staff has permission
     */
    public function hasPermission(string $permission): bool
    {
        if ($this->role === self::ROLE_ADMIN) {
            return true;
        }

        return in_array($permission, $this->permissions ?? []);
    }

    /**
     * Grant permission
     */
    public function grantPermission(string $permission): bool
    {
        $permissions = $this->permissions ?? [];

        if (! in_array($permission, $permissions)) {
            $permissions[] = $permission;

            return $this->update(['permissions' => $permissions]);
        }

        return true;
    }

    /**
     * Revoke permission
     */
    public function revokePermission(string $permission): bool
    {
        $permissions = $this->permissions ?? [];
        $key = array_search($permission, $permissions);

        if ($key !== false) {
            unset($permissions[$key]);

            return $this->update(['permissions' => array_values($permissions)]);
        }

        return true;
    }

    /**
     * Set permissions
     */
    public function setPermissions(array $permissions): bool
    {
        return $this->update(['permissions' => $permissions]);
    }

    /**
     * Activate staff member
     */
    public function activate(): bool
    {
        return $this->update(['is_active' => true]);
    }

    /**
     * Deactivate staff member
     */
    public function deactivate(): bool
    {
        return $this->update(['is_active' => false]);
    }

    /**
     * Terminate employment
     */
    public function terminate(): bool
    {
        return $this->update([
            'employment_status' => self::EMPLOYMENT_STATUS_TERMINATED,
            'is_active' => false,
        ]);
    }

    /**
     * Update last active timestamp
     */
    public function updateLastActive(): bool
    {
        return $this->update(['last_active_at' => now()]);
    }

    /**
     * Get role-based default permissions
     */
    public function getDefaultPermissionsForRole(string $role): array
    {
        $defaultPermissions = [
            self::ROLE_ADMIN => [
                self::PERMISSION_MANAGE_VEHICLES,
                self::PERMISSION_MANAGE_INQUIRIES,
                self::PERMISSION_MANAGE_TRANSACTIONS,
                self::PERMISSION_MANAGE_CUSTOMERS,
                self::PERMISSION_MANAGE_REPORTS,
                self::PERMISSION_MANAGE_SETTINGS,
                self::PERMISSION_MANAGE_STAFF,
                self::PERMISSION_MANAGE_FINANCES,
            ],
            self::ROLE_MANAGER => [
                self::PERMISSION_MANAGE_VEHICLES,
                self::PERMISSION_MANAGE_INQUIRIES,
                self::PERMISSION_MANAGE_TRANSACTIONS,
                self::PERMISSION_MANAGE_CUSTOMERS,
                self::PERMISSION_MANAGE_REPORTS,
                self::PERMISSION_MANAGE_STAFF,
            ],
            self::ROLE_SALES => [
                self::PERMISSION_MANAGE_VEHICLES,
                self::PERMISSION_MANAGE_INQUIRIES,
                self::PERMISSION_MANAGE_CUSTOMERS,
            ],
            self::ROLE_FINANCE => [
                self::PERMISSION_MANAGE_TRANSACTIONS,
                self::PERMISSION_MANAGE_FINANCES,
                self::PERMISSION_MANAGE_REPORTS,
            ],
            self::ROLE_SERVICE => [
                self::PERMISSION_MANAGE_VEHICLES,
                self::PERMISSION_MANAGE_CUSTOMERS,
            ],
            self::ROLE_SUPPORT => [
                self::PERMISSION_MANAGE_INQUIRIES,
                self::PERMISSION_MANAGE_CUSTOMERS,
            ],
        ];

        return $defaultPermissions[$role] ?? [];
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where('employment_status', '!=', self::EMPLOYMENT_STATUS_TERMINATED);
    }

    public function scopeInactive($query)
    {
        return $query->where('is_active', false)
            ->orWhere('employment_status', self::EMPLOYMENT_STATUS_TERMINATED);
    }

    public function scopeByRole($query, string $role)
    {
        return $query->where('role', $role);
    }

    public function scopeManagers($query)
    {
        return $query->whereIn('role', [self::ROLE_MANAGER, self::ROLE_ADMIN]);
    }

    public function scopeSalesStaff($query)
    {
        return $query->where('role', self::ROLE_SALES);
    }

    public function scopeByEmploymentStatus($query, string $status)
    {
        return $query->where('employment_status', $status);
    }

    public function scopeFullTime($query)
    {
        return $query->where('employment_status', self::EMPLOYMENT_STATUS_FULL_TIME);
    }

    public function scopePartTime($query)
    {
        return $query->where('employment_status', self::EMPLOYMENT_STATUS_PART_TIME);
    }

    public function scopeWithPermission($query, string $permission)
    {
        return $query->where(function ($query) use ($permission) {
            $query->where('role', self::ROLE_ADMIN)
                ->orWhereJsonContains('permissions', $permission);
        });
    }

    public function scopeHiredBetween($query, $startDate, $endDate)
    {
        return $query->whereBetween('hire_date', [$startDate, $endDate]);
    }

    public function scopeRecentlyActive($query, int $days = 30)
    {
        return $query->where('last_active_at', '>=', now()->subDays($days));
    }

    public function scopeByDepartment($query, string $department)
    {
        return $query->where('department', $department);
    }
}
