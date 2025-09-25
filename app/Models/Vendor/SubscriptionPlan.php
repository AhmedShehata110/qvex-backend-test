<?php

namespace App\Models\Vendor;

use App\Models\BaseModel;
use Database\Factories\SubscriptionPlanFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubscriptionPlan extends BaseModel
{
    use HasFactory, SoftDeletes;

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return SubscriptionPlanFactory::new();
    }

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price_monthly',
        'price_yearly',
        'setup_fee',
        'trial_days',
        'billing_cycle',
        'features',
        'limits',
        'vehicle_listing_limit',
        'photo_limit_per_vehicle',
        'featured_listing_limit',
        'staff_account_limit',
        'analytics_access',
        'priority_support',
        'custom_branding',
        'api_access',
        'commission_rate',
        'is_popular',
        'is_active',
        'sort_order',
        'created_by',
    ];

    protected $casts = [
        'price_monthly' => 'decimal:2',
        'price_yearly' => 'decimal:2',
        'setup_fee' => 'decimal:2',
        'trial_days' => 'integer',
        'features' => 'array',
        'limits' => 'array',
        'vehicle_listing_limit' => 'integer',
        'photo_limit_per_vehicle' => 'integer',
        'featured_listing_limit' => 'integer',
        'staff_account_limit' => 'integer',
        'analytics_access' => 'boolean',
        'priority_support' => 'boolean',
        'custom_branding' => 'boolean',
        'api_access' => 'boolean',
        'commission_rate' => 'decimal:2',
        'is_popular' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    protected $translatable = [
        'name',
        'description',
        'features',
    ];

    // Billing cycle constants
    const BILLING_CYCLE_MONTHLY = 'monthly';

    const BILLING_CYCLE_YEARLY = 'yearly';

    const BILLING_CYCLE_LIFETIME = 'lifetime';

    // Feature constants
    const FEATURE_UNLIMITED_LISTINGS = 'unlimited_listings';

    const FEATURE_PREMIUM_SUPPORT = 'premium_support';

    const FEATURE_ADVANCED_ANALYTICS = 'advanced_analytics';

    const FEATURE_CUSTOM_DOMAIN = 'custom_domain';

    const FEATURE_WHITE_LABEL = 'white_label';

    const FEATURE_API_ACCESS = 'api_access';

    const FEATURE_BULK_UPLOAD = 'bulk_upload';

    const FEATURE_AUTO_POSTING = 'auto_posting';

    const FEATURE_LEAD_MANAGEMENT = 'lead_management';

    const FEATURE_INVENTORY_SYNC = 'inventory_sync';

    public function vendors(): HasMany
    {
        return $this->hasMany(Vendor::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(VendorSubscription::class);
    }

    /**
     * Get billing cycles with labels
     */
    public static function getBillingCycles(): array
    {
        return [
            self::BILLING_CYCLE_MONTHLY => 'Monthly',
            self::BILLING_CYCLE_YEARLY => 'Yearly',
            self::BILLING_CYCLE_LIFETIME => 'Lifetime',
        ];
    }

    /**
     * Get available features with labels
     */
    public static function getAvailableFeatures(): array
    {
        return [
            self::FEATURE_UNLIMITED_LISTINGS => 'Unlimited Vehicle Listings',
            self::FEATURE_PREMIUM_SUPPORT => '24/7 Premium Support',
            self::FEATURE_ADVANCED_ANALYTICS => 'Advanced Analytics & Reports',
            self::FEATURE_CUSTOM_DOMAIN => 'Custom Domain',
            self::FEATURE_WHITE_LABEL => 'White Label Solution',
            self::FEATURE_API_ACCESS => 'API Access',
            self::FEATURE_BULK_UPLOAD => 'Bulk Vehicle Upload',
            self::FEATURE_AUTO_POSTING => 'Auto Posting to Social Media',
            self::FEATURE_LEAD_MANAGEMENT => 'Advanced Lead Management',
            self::FEATURE_INVENTORY_SYNC => 'Inventory Synchronization',
        ];
    }

    /**
     * Get billing cycle label
     */
    public function getBillingCycleLabelAttribute(): string
    {
        return static::getBillingCycles()[$this->billing_cycle] ?? $this->billing_cycle;
    }

    /**
     * Check if plan is active
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * Check if plan is popular
     */
    public function isPopular(): bool
    {
        return $this->is_popular;
    }

    /**
     * Check if plan has trial period
     */
    public function hasTrial(): bool
    {
        return $this->trial_days > 0;
    }

    /**
     * Check if plan has feature
     */
    public function hasFeature(string $feature): bool
    {
        return in_array($feature, $this->features ?? []);
    }

    /**
     * Get formatted monthly price
     */
    public function getFormattedMonthlyPriceAttribute(): string
    {
        return '$'.number_format($this->price_monthly, 2);
    }

    /**
     * Get formatted yearly price
     */
    public function getFormattedYearlyPriceAttribute(): string
    {
        return '$'.number_format($this->price_yearly, 2);
    }

    /**
     * Get formatted setup fee
     */
    public function getFormattedSetupFeeAttribute(): ?string
    {
        return $this->setup_fee ? '$'.number_format($this->setup_fee, 2) : null;
    }

    /**
     * Get effective price based on billing cycle
     */
    public function getEffectivePrice(?string $billingCycle = null): float
    {
        $cycle = $billingCycle ?? $this->billing_cycle;

        switch ($cycle) {
            case self::BILLING_CYCLE_YEARLY:
                return $this->price_yearly;
            case self::BILLING_CYCLE_LIFETIME:
                return 0; // Lifetime plans are one-time payments
            default:
                return $this->price_monthly;
        }
    }

    /**
     * Get yearly savings amount
     */
    public function getYearlySavingsAttribute(): float
    {
        $monthlyTotal = $this->price_monthly * 12;

        return max(0, $monthlyTotal - $this->price_yearly);
    }

    /**
     * Get yearly savings percentage
     */
    public function getYearlySavingsPercentageAttribute(): float
    {
        if ($this->price_monthly == 0) {
            return 0;
        }

        $monthlyTotal = $this->price_monthly * 12;
        $savings = $monthlyTotal - $this->price_yearly;

        return round(($savings / $monthlyTotal) * 100, 1);
    }

    /**
     * Check if plan allows unlimited listings
     */
    public function hasUnlimitedListings(): bool
    {
        return $this->vehicle_listing_limit === 0 || $this->vehicle_listing_limit === null;
    }

    /**
     * Check if plan allows unlimited staff accounts
     */
    public function hasUnlimitedStaff(): bool
    {
        return $this->staff_account_limit === 0 || $this->staff_account_limit === null;
    }

    /**
     * Check if vendor can create more listings
     */
    public function canCreateMoreListings(int $currentListingsCount): bool
    {
        return $this->hasUnlimitedListings() || $currentListingsCount < $this->vehicle_listing_limit;
    }

    /**
     * Check if vendor can add more staff
     */
    public function canAddMoreStaff(int $currentStaffCount): bool
    {
        return $this->hasUnlimitedStaff() || $currentStaffCount < $this->staff_account_limit;
    }

    /**
     * Get remaining listings quota
     */
    public function getRemainingListings(int $currentListingsCount): ?int
    {
        if ($this->hasUnlimitedListings()) {
            return null; // Unlimited
        }

        return max(0, $this->vehicle_listing_limit - $currentListingsCount);
    }

    /**
     * Get remaining staff quota
     */
    public function getRemainingStaff(int $currentStaffCount): ?int
    {
        if ($this->hasUnlimitedStaff()) {
            return null; // Unlimited
        }

        return max(0, $this->staff_account_limit - $currentStaffCount);
    }

    /**
     * Get feature list as formatted string
     */
    public function getFeatureListAttribute(): array
    {
        $availableFeatures = static::getAvailableFeatures();
        $planFeatures = [];

        foreach ($this->features ?? [] as $feature) {
            $planFeatures[] = $availableFeatures[$feature] ?? $feature;
        }

        // Add limit-based features
        if ($this->hasUnlimitedListings()) {
            $planFeatures[] = 'Unlimited Vehicle Listings';
        } elseif ($this->vehicle_listing_limit > 0) {
            $planFeatures[] = "Up to {$this->vehicle_listing_limit} Vehicle Listings";
        }

        if ($this->photo_limit_per_vehicle > 0) {
            $planFeatures[] = "Up to {$this->photo_limit_per_vehicle} Photos per Vehicle";
        }

        if ($this->featured_listing_limit > 0) {
            $planFeatures[] = "Up to {$this->featured_listing_limit} Featured Listings";
        }

        if ($this->hasUnlimitedStaff()) {
            $planFeatures[] = 'Unlimited Staff Accounts';
        } elseif ($this->staff_account_limit > 0) {
            $planFeatures[] = "Up to {$this->staff_account_limit} Staff Accounts";
        }

        return $planFeatures;
    }

    /**
     * Activate the plan
     */
    public function activate(): bool
    {
        return $this->update(['is_active' => true]);
    }

    /**
     * Deactivate the plan
     */
    public function deactivate(): bool
    {
        return $this->update(['is_active' => false]);
    }

    /**
     * Mark as popular
     */
    public function markAsPopular(): bool
    {
        // Unmark other popular plans first
        static::where('is_popular', true)->update(['is_popular' => false]);

        return $this->update(['is_popular' => true]);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopePopular($query)
    {
        return $query->where('is_popular', true);
    }

    public function scopeByBillingCycle($query, string $cycle)
    {
        return $query->where('billing_cycle', $cycle);
    }

    public function scopeMonthly($query)
    {
        return $query->where('billing_cycle', self::BILLING_CYCLE_MONTHLY);
    }

    public function scopeYearly($query)
    {
        return $query->where('billing_cycle', self::BILLING_CYCLE_YEARLY);
    }

    public function scopeWithFeature($query, string $feature)
    {
        return $query->whereJsonContains('features', $feature);
    }

    public function scopeByPriceRange($query, float $minPrice, float $maxPrice)
    {
        return $query->where(function ($query) use ($minPrice, $maxPrice) {
            $query->whereBetween('price_monthly', [$minPrice, $maxPrice])
                ->orWhereBetween('price_yearly', [$minPrice, $maxPrice]);
        });
    }

    public function scopeOrderByPrice($query, string $direction = 'asc')
    {
        return $query->orderBy('price_monthly', $direction);
    }

    public function scopeOrderByPopularity($query)
    {
        return $query->orderByDesc('is_popular')->orderBy('sort_order');
    }

    public function scopeWithTrial($query)
    {
        return $query->where('trial_days', '>', 0);
    }

    public function scopeWithoutSetupFee($query)
    {
        return $query->where(function ($query) {
            $query->whereNull('setup_fee')->orWhere('setup_fee', 0);
        });
    }
}
