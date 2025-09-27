<?php

namespace App\Models;

use App\Enums\User\UserTypeEnum;
use App\Models\Communication\Message;
use App\Models\Communication\Review;
use App\Models\Customer\SavedSearch;
use App\Models\Customer\UserAddress;
use App\Models\Customer\UserDocument;
use App\Models\Customer\UserFavorite;
use App\Models\Marketing\CouponUse;
use App\Models\Transaction\Transaction;
use App\Models\Vehicle\Vehicle;
use App\Models\Vehicle\VehicleInquiry;
use App\Models\Vendor\Vendor;
use App\Models\Vendor\VendorStaff;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements HasMedia
{
    use HasApiTokens,
        HasFactory,
        HasRoles,
        InteractsWithMedia,
        Notifiable,
        SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'locale',
        'timezone',
        'avatar',
        'birth_date',
        'gender',
        'user_type',
        'two_factor_enabled',
        'is_active',
        'added_by_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'phone_verified_at' => 'datetime',
        'password' => 'hashed',
        'birth_date' => 'date',
        'last_login_at' => 'datetime',
        'two_factor_enabled' => 'boolean',
        'two_factor_recovery_codes' => 'array',
        'is_active' => 'boolean',
        'user_type' => UserTypeEnum::class,
    ];

    // Gender constants
    const GENDER_MALE = 'male';

    const GENDER_FEMALE = 'female';

    const GENDER_OTHER = 'other';

    // Status constants
    const STATUS_PENDING = 'pending';

    const STATUS_ACTIVE = 'active';

    const STATUS_SUSPENDED = 'suspended';

    const STATUS_BANNED = 'banned';

    // RELATIONSHIPS

    /**
     * User addresses
     */
    public function addresses(): HasMany
    {
        return $this->hasMany(UserAddress::class);
    }

    /**
     * User documents
     */
    public function documents(): HasMany
    {
        return $this->hasMany(UserDocument::class);
    }

    /**
     * User favorite vehicles
     */
    public function favorites(): HasMany
    {
        return $this->hasMany(UserFavorite::class);
    }

    /**
     * Favorite vehicles (direct relationship)
     */
    public function favoriteVehicles(): BelongsToMany
    {
        return $this->belongsToMany(Vehicle::class, 'user_favorites')
            ->withTimestamps();
    }

    /**
     * User saved searches
     */
    public function savedSearches(): HasMany
    {
        return $this->hasMany(SavedSearch::class);
    }

    /**
     * Messages sent by user
     */
    public function sentMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    /**
     * Messages received by user
     */
    public function receivedMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'recipient_id');
    }

    /**
     * Vehicle inquiries made by user
     */
    public function vehicleInquiries(): HasMany
    {
        return $this->hasMany(VehicleInquiry::class, 'inquirer_id');
    }

    /**
     * Reviews written by user
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'reviewer_id');
    }

    /**
     * Transactions as buyer
     */
    public function purchaseTransactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'buyer_id');
    }

    /**
     * Transactions as seller
     */
    public function saleTransactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'seller_id');
    }

    /**
     * All transactions (buyer or seller)
     */
    public function allTransactions()
    {
        return Transaction::where('buyer_id', $this->id)
            ->orWhere('seller_id', $this->id);
    }

    /**
     * Coupon uses by user
     */
    public function couponUses(): HasMany
    {
        return $this->hasMany(CouponUse::class);
    }

    /**
     * Vendor profile (if user is a vendor)
     */
    public function vendor(): HasOne
    {
        return $this->hasOne(Vendor::class);
    }

    /**
     * Vendor staff profile (if user is vendor staff)
     */
    public function vendorStaff(): HasOne
    {
        return $this->hasOne(VendorStaff::class);
    }

    /**
     * Vehicles owned/listed by user
     */
    public function vehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class);
    }

    /**
     * Users added by this user
     */
    public function addedUsers(): HasMany
    {
        return $this->hasMany(static::class, 'added_by_id');
    }

    /**
     * User who added this user
     */
    public function addedBy()
    {
        return $this->belongsTo(static::class, 'added_by_id');
    }

    // HELPER METHODS

    /**
     * Get genders with labels
     */
    public static function getGenders(): array
    {
        return [
            self::GENDER_MALE => 'Male',
            self::GENDER_FEMALE => 'Female',
            self::GENDER_OTHER => 'Other',
        ];
    }

    /**
     * Get gender label
     */
    public function getGenderLabelAttribute(): ?string
    {
        return static::getGenders()[$this->gender] ?? null;
    }

    /**
     * Get user's full name
     */
    public function getFullNameAttribute(): string
    {
        return trim($this->name);
    }

    /**
     * Get user's initials
     */
    public function getInitialsAttribute(): string
    {
        $names = explode(' ', trim($this->name));
        $initials = '';

        foreach ($names as $name) {
            if (! empty($name)) {
                $initials .= strtoupper(substr($name, 0, 1));
            }
        }

        return substr($initials, 0, 2);
    }

    /**
     * Get user age
     */
    public function getAgeAttribute(): ?int
    {
        return $this->birth_date ? $this->birth_date->diffInYears(now()) : null;
    }

    /**
     * Get avatar URL
     */
    public function getAvatarUrlAttribute(): ?string
    {
        if ($this->avatar) {
            return $this->avatar;
        }

        // Return media library avatar if exists
        return $this->getFirstMediaUrl('avatars') ?: null;
    }

    /**
     * Get default avatar (initials-based)
     */
    public function getDefaultAvatarAttribute(): string
    {
        return 'https://ui-avatars.com/api/?name='.urlencode($this->initials).
               '&size=200&background=3B82F6&color=ffffff';
    }

    /**
     * Check if user is admin (by user_type)
     */
    public function isAdmin(): bool
    {
        return $this->user_type?->isAdmin() ?? false;
    }

    /**
     * Check if user is employee (by user_type) - deprecated, employees are now ADMIN
     */
    public function isEmployee(): bool
    {
        return false; // Employees are now considered ADMIN
    }

    /**
     * Check if user is staff (admin, employee)
     */
    public function isStaff(): bool
    {
        return $this->user_type?->isStaff() ?? false;
    }

    // USER TYPE METHODS

    /**
     * Check if user is admin
     */
    public function isAdminUser(): bool
    {
        return $this->user_type === UserTypeEnum::ADMIN;
    }

    /**
     * Check if user is regular user
     */
    public function isRegularUser(): bool
    {
        return $this->user_type === UserTypeEnum::USER;
    }

    /**
     * Check if user can access dashboard
     */
    public function canAccessDashboard(): bool
    {
        return $this->user_type?->canAccessDashboard() ?? false;
    }

    /**
     * Check if user has dashboard access permission
     */
    public function hasDashboardPermission(): bool
    {
        // For simple permission checking - admin users have dashboard access
        return $this->isAdmin();
    }

    /**
     * Get user type label
     */
    public function getUserTypeLabel(): string
    {
        return $this->user_type?->label() ?? 'Unknown';
    }

    /**
     * Get user type description
     */
    public function getUserTypeDescription(): string
    {
        return $this->user_type?->description() ?? '';
    }

    /**
     * Get user type color
     */
    public function getUserTypeColor(): string
    {
        return $this->user_type?->color() ?? 'gray';
    }

    /**
     * Get user type icon
     */
    public function getUserTypeIcon(): string
    {
        return $this->user_type?->icon() ?? 'user';
    }

    /**
     * Check if user is vendor
     */
    public function isVendor(): bool
    {
        return $this->vendor()->exists();
    }

    /**
     * Check if user is vendor staff
     */
    public function isVendorStaff(): bool
    {
        return $this->vendorStaff()->exists();
    }

    /**
     * Check if email is verified
     */
    public function hasVerifiedEmail(): bool
    {
        return ! is_null($this->email_verified_at);
    }

    /**
     * Check if phone is verified
     */
    public function hasVerifiedPhone(): bool
    {
        return ! is_null($this->phone_verified_at);
    }

    /**
     * Check if user is fully verified
     */
    public function isFullyVerified(): bool
    {
        return $this->hasVerifiedEmail() && ($this->phone ? $this->hasVerifiedPhone() : true);
    }

    /**
     * Check if two-factor is enabled
     */
    public function hasTwoFactorEnabled(): bool
    {
        return $this->two_factor_enabled;
    }

    /**
     * Check if user is active
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * Get primary address
     */
    public function getPrimaryAddressAttribute(): ?UserAddress
    {
        return $this->addresses()->where('is_primary', true)->first()
            ?? $this->addresses()->first();
    }

    /**
     * Get time since last login
     */
    public function getLastLoginAttribute(): ?string
    {
        return $this->last_login_at ? $this->last_login_at->diffForHumans() : null;
    }

    /**
     * Get user statistics
     */
    public function getStatistics(): array
    {
        return [
            'vehicles_listed' => $this->vehicles()->count(),
            'purchases_made' => $this->purchaseTransactions()->completed()->count(),
            'sales_made' => $this->saleTransactions()->completed()->count(),
            'favorites_count' => $this->favorites()->count(),
            'reviews_count' => $this->reviews()->count(),
            'inquiries_sent' => $this->vehicleInquiries()->count(),
            'messages_sent' => $this->sentMessages()->count(),
            'saved_searches' => $this->savedSearches()->active()->count(),
            'total_spent' => $this->purchaseTransactions()->completed()->sum('total_amount'),
            'total_earned' => $this->saleTransactions()->completed()->sum('total_amount'),
        ];
    }

    // ACTION METHODS

    /**
     * Update last login information
     */
    public function updateLastLogin(?string $ipAddress = null): bool
    {
        return $this->update([
            'last_login_at' => now(),
            'last_login_ip' => $ipAddress ?? request()->ip(),
        ]);
    }

    /**
     * Verify phone number
     */
    public function markPhoneAsVerified(): bool
    {
        return $this->update(['phone_verified_at' => now()]);
    }

    /**
     * Enable two-factor authentication
     */
    public function enableTwoFactor(string $secret, array $recoveryCodes): bool
    {
        return $this->update([
            'two_factor_enabled' => true,
            'two_factor_secret' => $secret,
            'two_factor_recovery_codes' => $recoveryCodes,
        ]);
    }

    /**
     * Disable two-factor authentication
     */
    public function disableTwoFactor(): bool
    {
        return $this->update([
            'two_factor_enabled' => false,
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
        ]);
    }

    /**
     * Activate user
     */
    public function activate(): bool
    {
        return $this->update(['is_active' => true]);
    }

    /**
     * Deactivate user
     */
    public function deactivate(): bool
    {
        return $this->update(['is_active' => false]);
    }

    /**
     * Change user type
     */
    public function changeUserType(UserTypeEnum $userType): bool
    {
        return $this->update(['user_type' => $userType]);
    }

    /**
     * Add vehicle to favorites
     */
    public function addToFavorites(int $vehicleId, ?string $notes = null): bool
    {
        if ($this->favorites()->where('vehicle_id', $vehicleId)->exists()) {
            return false; // Already in favorites
        }

        $this->favorites()->create([
            'vehicle_id' => $vehicleId,
            'notes' => $notes,
        ]);

        return true;
    }

    /**
     * Remove vehicle from favorites
     */
    public function removeFromFavorites(int $vehicleId): bool
    {
        return $this->favorites()->where('vehicle_id', $vehicleId)->delete() > 0;
    }

    /**
     * Check if vehicle is in favorites
     */
    public function hasFavoriteVehicle(int $vehicleId): bool
    {
        return $this->favorites()->where('vehicle_id', $vehicleId)->exists();
    }

    // SCOPES

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    public function scopeVerified($query)
    {
        return $query->whereNotNull('email_verified_at');
    }

    public function scopeUnverified($query)
    {
        return $query->whereNull('email_verified_at');
    }

    public function scopeWithTwoFactor($query)
    {
        return $query->where('two_factor_enabled', true);
    }

    public function scopeAdmins($query)
    {
        return $query->where('user_type', UserTypeEnum::ADMIN->value);
    }

    public function scopeEmployees($query)
    {
        return $query->where('user_type', UserTypeEnum::ADMIN->value);
    }

    public function scopeVendors($query)
    {
        return $query->whereHas('vendor');
    }

    public function scopeVendorStaff($query)
    {
        return $query->whereHas('vendorStaff');
    }

    public function scopeRecentlyRegistered($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    public function scopeRecentlyActive($query, int $days = 30)
    {
        return $query->where('last_login_at', '>=', now()->subDays($days));
    }

    public function scopeInactiveUsers($query, int $days = 90)
    {
        return $query->where('last_login_at', '<', now()->subDays($days))
            ->orWhereNull('last_login_at');
    }

    public function scopeByGender($query, string $gender)
    {
        return $query->where('gender', $gender);
    }

    public function scopeByAge($query, int $minAge, ?int $maxAge = null)
    {
        $query->whereNotNull('birth_date')
            ->where('birth_date', '<=', now()->subYears($minAge));

        if ($maxAge) {
            $query->where('birth_date', '>=', now()->subYears($maxAge));
        }

        return $query;
    }

    // USER TYPE SCOPES

    public function scopeByUserType($query, UserTypeEnum $userType)
    {
        return $query->where('user_type', $userType->value);
    }

    public function scopeAdminUsers($query)
    {
        return $query->where('user_type', UserTypeEnum::ADMIN->value);
    }

    public function scopeRegularUsers($query)
    {
        return $query->where('user_type', UserTypeEnum::USER->value);
    }

    public function scopeCanAccessDashboard($query)
    {
        return $query->where('user_type', UserTypeEnum::ADMIN->value);
    }

    public function scopeCannotAccessDashboard($query)
    {
        return $query->where('user_type', UserTypeEnum::USER->value);
    }

    public function scopeSearchByName($query, string $search)
    {
        return $query->where('name', 'LIKE', "%{$search}%");
    }

    public function scopeSearchByEmail($query, string $search)
    {
        return $query->where('email', 'LIKE', "%{$search}%");
    }

    public function scopeWithStatistics($query)
    {
        return $query->withCount([
            'vehicles',
            'favorites',
            'reviews',
            'purchaseTransactions',
            'saleTransactions',
        ]);
    }

    // MEDIA COLLECTIONS

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('avatars')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp']);

        $this->addMediaCollection('documents')
            ->acceptsMimeTypes([
                'image/jpeg', 'image/png', 'image/gif', 'image/webp',
                'application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            ]);
    }
}
