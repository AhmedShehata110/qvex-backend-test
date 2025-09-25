<?php

namespace App\Models\Marketing;

use App\Models\BaseModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Coupon extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'description',
        'type',
        'discount_type',
        'discount_value',
        'minimum_amount',
        'maximum_discount',
        'usage_limit',
        'usage_limit_per_user',
        'used_count',
        'applicable_to',
        'applicable_items',
        'exclude_sale_items',
        'free_shipping',
        'starts_at',
        'expires_at',
        'status',
        'created_by',
        'terms_conditions',
        'metadata',
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'minimum_amount' => 'decimal:2',
        'maximum_discount' => 'decimal:2',
        'usage_limit' => 'integer',
        'usage_limit_per_user' => 'integer',
        'used_count' => 'integer',
        'applicable_items' => 'array',
        'exclude_sale_items' => 'boolean',
        'free_shipping' => 'boolean',
        'starts_at' => 'timestamp',
        'expires_at' => 'timestamp',
        'terms_conditions' => 'array',
        'metadata' => 'array',
    ];

    protected $translatable = [
        'name',
        'description',
        'terms_conditions',
    ];

    // Type constants
    const TYPE_GENERAL = 'general';

    const TYPE_FIRST_TIME = 'first_time';

    const TYPE_REFERRAL = 'referral';

    const TYPE_LOYALTY = 'loyalty';

    const TYPE_SEASONAL = 'seasonal';

    const TYPE_FLASH_SALE = 'flash_sale';

    const TYPE_VIP = 'vip';

    // Discount type constants
    const DISCOUNT_TYPE_PERCENTAGE = 'percentage';

    const DISCOUNT_TYPE_FIXED_AMOUNT = 'fixed_amount';

    const DISCOUNT_TYPE_FREE_SHIPPING = 'free_shipping';

    const DISCOUNT_TYPE_BUY_X_GET_Y = 'buy_x_get_y';

    // Applicable to constants
    const APPLICABLE_ALL = 'all';

    const APPLICABLE_SPECIFIC_VEHICLES = 'specific_vehicles';

    const APPLICABLE_VEHICLE_CATEGORY = 'vehicle_category';

    const APPLICABLE_VENDOR = 'vendor';

    const APPLICABLE_MINIMUM_ORDER = 'minimum_order';

    // Status constants
    const STATUS_ACTIVE = 'active';

    const STATUS_INACTIVE = 'inactive';

    const STATUS_EXPIRED = 'expired';

    const STATUS_USED_UP = 'used_up';

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function uses(): HasMany
    {
        return $this->hasMany(CouponUse::class);
    }

    /**
     * Get coupon types with labels
     */
    public static function getTypes(): array
    {
        return [
            self::TYPE_GENERAL => 'General Discount',
            self::TYPE_FIRST_TIME => 'First Time Customer',
            self::TYPE_REFERRAL => 'Referral Reward',
            self::TYPE_LOYALTY => 'Loyalty Reward',
            self::TYPE_SEASONAL => 'Seasonal Offer',
            self::TYPE_FLASH_SALE => 'Flash Sale',
            self::TYPE_VIP => 'VIP Member',
        ];
    }

    /**
     * Get discount types with labels
     */
    public static function getDiscountTypes(): array
    {
        return [
            self::DISCOUNT_TYPE_PERCENTAGE => 'Percentage',
            self::DISCOUNT_TYPE_FIXED_AMOUNT => 'Fixed Amount',
            self::DISCOUNT_TYPE_FREE_SHIPPING => 'Free Shipping',
            self::DISCOUNT_TYPE_BUY_X_GET_Y => 'Buy X Get Y',
        ];
    }

    /**
     * Get applicable to options with labels
     */
    public static function getApplicableToOptions(): array
    {
        return [
            self::APPLICABLE_ALL => 'All Items',
            self::APPLICABLE_SPECIFIC_VEHICLES => 'Specific Vehicles',
            self::APPLICABLE_VEHICLE_CATEGORY => 'Vehicle Category',
            self::APPLICABLE_VENDOR => 'Specific Vendor',
            self::APPLICABLE_MINIMUM_ORDER => 'Minimum Order Amount',
        ];
    }

    /**
     * Get statuses with labels
     */
    public static function getStatuses(): array
    {
        return [
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_INACTIVE => 'Inactive',
            self::STATUS_EXPIRED => 'Expired',
            self::STATUS_USED_UP => 'Used Up',
        ];
    }

    /**
     * Get type label
     */
    public function getTypeLabelAttribute(): string
    {
        return static::getTypes()[$this->type] ?? $this->type;
    }

    /**
     * Get discount type label
     */
    public function getDiscountTypeLabelAttribute(): string
    {
        return static::getDiscountTypes()[$this->discount_type] ?? $this->discount_type;
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute(): string
    {
        return static::getStatuses()[$this->status] ?? $this->status;
    }

    /**
     * Check if coupon is active
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE
            && (! $this->starts_at || $this->starts_at->isPast())
            && (! $this->expires_at || $this->expires_at->isFuture())
            && ($this->usage_limit === 0 || $this->used_count < $this->usage_limit);
    }

    /**
     * Check if coupon is expired
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Check if coupon usage limit is reached
     */
    public function isUsedUp(): bool
    {
        return $this->usage_limit > 0 && $this->used_count >= $this->usage_limit;
    }

    /**
     * Check if coupon has started
     */
    public function hasStarted(): bool
    {
        return ! $this->starts_at || $this->starts_at->isPast();
    }

    /**
     * Check if user can use this coupon
     */
    public function canBeUsedByUser(int $userId): bool
    {
        if (! $this->isActive()) {
            return false;
        }

        if ($this->usage_limit_per_user > 0) {
            $userUsageCount = $this->uses()->where('user_id', $userId)->count();

            return $userUsageCount < $this->usage_limit_per_user;
        }

        return true;
    }

    /**
     * Calculate discount amount for given total
     */
    public function calculateDiscount(float $total): float
    {
        if (! $this->isValidForAmount($total)) {
            return 0;
        }

        switch ($this->discount_type) {
            case self::DISCOUNT_TYPE_PERCENTAGE:
                $discount = ($total * $this->discount_value) / 100;

                return $this->maximum_discount > 0
                    ? min($discount, $this->maximum_discount)
                    : $discount;

            case self::DISCOUNT_TYPE_FIXED_AMOUNT:
                return min($this->discount_value, $total);

            default:
                return 0;
        }
    }

    /**
     * Check if coupon is valid for given amount
     */
    public function isValidForAmount(float $amount): bool
    {
        return $this->minimum_amount === 0 || $amount >= $this->minimum_amount;
    }

    /**
     * Get formatted discount value
     */
    public function getFormattedDiscountAttribute(): string
    {
        switch ($this->discount_type) {
            case self::DISCOUNT_TYPE_PERCENTAGE:
                return $this->discount_value.'%';
            case self::DISCOUNT_TYPE_FIXED_AMOUNT:
                return '$'.number_format($this->discount_value, 2);
            case self::DISCOUNT_TYPE_FREE_SHIPPING:
                return 'Free Shipping';
            default:
                return $this->discount_value;
        }
    }

    /**
     * Get remaining uses
     */
    public function getRemainingUsesAttribute(): ?int
    {
        if ($this->usage_limit === 0) {
            return null; // Unlimited
        }

        return max(0, $this->usage_limit - $this->used_count);
    }

    /**
     * Get usage percentage
     */
    public function getUsagePercentageAttribute(): float
    {
        if ($this->usage_limit === 0) {
            return 0;
        }

        return min(100, ($this->used_count / $this->usage_limit) * 100);
    }

    /**
     * Get days until expiry
     */
    public function getDaysUntilExpiryAttribute(): ?int
    {
        if (! $this->expires_at) {
            return null;
        }

        return $this->expires_at->diffInDays(now(), false);
    }

    /**
     * Use the coupon
     */
    public function use(int $userId, float $orderAmount, array $metadata = []): bool
    {
        if (! $this->canBeUsedByUser($userId)) {
            return false;
        }

        // Create usage record
        $this->uses()->create([
            'user_id' => $userId,
            'order_amount' => $orderAmount,
            'discount_amount' => $this->calculateDiscount($orderAmount),
            'used_at' => now(),
            'metadata' => $metadata,
        ]);

        // Increment usage count
        $this->increment('used_count');

        // Update status if used up
        if ($this->isUsedUp()) {
            $this->update(['status' => self::STATUS_USED_UP]);
        }

        return true;
    }

    /**
     * Activate the coupon
     */
    public function activate(): bool
    {
        return $this->update(['status' => self::STATUS_ACTIVE]);
    }

    /**
     * Deactivate the coupon
     */
    public function deactivate(): bool
    {
        return $this->update(['status' => self::STATUS_INACTIVE]);
    }

    /**
     * Generate unique coupon code
     */
    public static function generateUniqueCode(int $length = 8): string
    {
        do {
            $code = strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, $length));
        } while (static::where('code', $code)->exists());

        return $code;
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE)
            ->where(function ($query) {
                $query->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            })
            ->where(function ($query) {
                $query->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->where(function ($query) {
                $query->where('usage_limit', 0)->orWhereColumn('used_count', '<', 'usage_limit');
            });
    }

    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<', now());
    }

    public function scopeUsedUp($query)
    {
        return $query->where('usage_limit', '>', 0)
            ->whereColumn('used_count', '>=', 'usage_limit');
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByDiscountType($query, string $discountType)
    {
        return $query->where('discount_type', $discountType);
    }

    public function scopeExpiringWithin($query, int $days)
    {
        return $query->whereNotNull('expires_at')
            ->whereBetween('expires_at', [now(), now()->addDays($days)]);
    }

    public function scopeUnlimitedUsage($query)
    {
        return $query->where('usage_limit', 0);
    }

    public function scopeWithFreeShipping($query)
    {
        return $query->where('free_shipping', true);
    }

    public function scopeMinimumAmount($query, float $amount)
    {
        return $query->where(function ($query) use ($amount) {
            $query->where('minimum_amount', 0)->orWhere('minimum_amount', '<=', $amount);
        });
    }

    public function scopeCreatedBy($query, int $userId)
    {
        return $query->where('created_by', $userId);
    }

    public function scopePopular($query, int $minUsage = 10)
    {
        return $query->where('used_count', '>=', $minUsage);
    }
}
