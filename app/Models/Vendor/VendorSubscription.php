<?php

namespace App\Models\Vendor;

use App\Models\BaseModel;
use App\Traits\AuditableTrait;
use Carbon\Carbon;
use Database\Factories\VendorSubscriptionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class VendorSubscription extends BaseModel
{
    use AuditableTrait, HasFactory, SoftDeletes;

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return VendorSubscriptionFactory::new();
    }

    protected $fillable = [
        'vendor_id',
        'subscription_plan_id',
        'amount_paid',
        'currency',
        'status',
        'starts_at',
        'ends_at',
        'cancelled_at',
        'listings_used',
        'featured_listings_used',
        'auto_renewal',
        'payment_reference',
        'next_billing_date',
        'billing_cycle',
        'discount_applied',
        'discount_amount',
        'tax_amount',
        'total_amount',
        'payment_method',
        'metadata',
    ];

    protected $casts = [
        'amount_paid' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'auto_renewal' => 'boolean',
        'starts_at' => 'date',
        'ends_at' => 'date',
        'cancelled_at' => 'date',
        'next_billing_date' => 'date',
        'listings_used' => 'integer',
        'featured_listings_used' => 'integer',
        'metadata' => 'array',
    ];

    // Status constants
    const STATUS_ACTIVE = 'active';

    const STATUS_CANCELLED = 'cancelled';

    const STATUS_EXPIRED = 'expired';

    const STATUS_SUSPENDED = 'suspended';

    const STATUS_PENDING = 'pending';

    const STATUS_TRIAL = 'trial';

    // Payment method constants
    const PAYMENT_CREDIT_CARD = 'credit_card';

    const PAYMENT_BANK_TRANSFER = 'bank_transfer';

    const PAYMENT_PAYPAL = 'paypal';

    const PAYMENT_STRIPE = 'stripe';

    // RELATIONSHIPS

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function subscriptionPlan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class);
    }

    // HELPER METHODS

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    public function isExpired(): bool
    {
        return $this->status === self::STATUS_EXPIRED ||
               ($this->ends_at && $this->ends_at->isPast());
    }

    public function isSuspended(): bool
    {
        return $this->status === self::STATUS_SUSPENDED;
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isOnTrial(): bool
    {
        return $this->status === self::STATUS_TRIAL;
    }

    public function hasAutoRenewal(): bool
    {
        return $this->auto_renewal;
    }

    public function getDaysRemainingAttribute(): int
    {
        if (! $this->ends_at) {
            return 0;
        }

        $today = Carbon::today();

        return max(0, $today->diffInDays($this->ends_at, false));
    }

    public function getListingsRemainingAttribute(): int
    {
        if (! $this->subscriptionPlan) {
            return 0;
        }

        return max(0, $this->subscriptionPlan->max_listings - $this->listings_used);
    }

    public function getFeaturedListingsRemainingAttribute(): int
    {
        if (! $this->subscriptionPlan) {
            return 0;
        }

        return max(0, $this->subscriptionPlan->max_featured_listings - $this->featured_listings_used);
    }

    public function getUsagePercentageAttribute(): float
    {
        if (! $this->subscriptionPlan || $this->subscriptionPlan->max_listings === 0) {
            return 0;
        }

        return round(($this->listings_used / $this->subscriptionPlan->max_listings) * 100, 1);
    }

    public function canCreateListing(): bool
    {
        return $this->isActive() && $this->getListingsRemainingAttribute() > 0;
    }

    public function canCreateFeaturedListing(): bool
    {
        return $this->isActive() && $this->getFeaturedListingsRemainingAttribute() > 0;
    }

    public function isNearExpiry(int $days = 7): bool
    {
        return $this->getDaysRemainingAttribute() <= $days && $this->getDaysRemainingAttribute() > 0;
    }

    public function getRenewalAmountAttribute(): float
    {
        return $this->subscriptionPlan ? $this->subscriptionPlan->price : 0;
    }

    // ACTIONS

    public function cancel(?string $reason = null): bool
    {
        return $this->update([
            'status' => self::STATUS_CANCELLED,
            'cancelled_at' => now(),
            'auto_renewal' => false,
            'metadata' => array_merge($this->metadata ?? [], [
                'cancellation_reason' => $reason,
                'cancelled_by' => Auth::id(),
            ]),
        ]);
    }

    public function suspend(?string $reason = null): bool
    {
        return $this->update([
            'status' => self::STATUS_SUSPENDED,
            'metadata' => array_merge($this->metadata ?? [], [
                'suspension_reason' => $reason,
                'suspended_at' => now(),
                'suspended_by' => Auth::id(),
            ]),
        ]);
    }

    public function reactivate(): bool
    {
        if ($this->isExpired()) {
            return false;
        }

        return $this->update([
            'status' => self::STATUS_ACTIVE,
            'metadata' => array_merge($this->metadata ?? [], [
                'reactivated_at' => now(),
                'reactivated_by' => Auth::id(),
            ]),
        ]);
    }

    public function extend(int $days): bool
    {
        $newEndDate = $this->ends_at ?
            $this->ends_at->addDays($days) :
            Carbon::now()->addDays($days);

        return $this->update([
            'ends_at' => $newEndDate,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    public function renewForPeriod(int $months = 1): bool
    {
        $startDate = $this->ends_at && $this->ends_at->isFuture() ?
            $this->ends_at : Carbon::now();

        $endDate = $startDate->copy()->addMonths($months);

        return $this->update([
            'starts_at' => $startDate,
            'ends_at' => $endDate,
            'status' => self::STATUS_ACTIVE,
            'listings_used' => 0,
            'featured_listings_used' => 0,
            'next_billing_date' => $endDate,
        ]);
    }

    public function incrementListingUsage(bool $featured = false): bool
    {
        $field = $featured ? 'featured_listings_used' : 'listings_used';

        return $this->increment($field);
    }

    public function decrementListingUsage(bool $featured = false): bool
    {
        $field = $featured ? 'featured_listings_used' : 'listings_used';

        return $this->decrement($field);
    }

    public function enableAutoRenewal(): bool
    {
        return $this->update(['auto_renewal' => true]);
    }

    public function disableAutoRenewal(): bool
    {
        return $this->update(['auto_renewal' => false]);
    }

    // SCOPES

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeExpired($query)
    {
        return $query->where('status', self::STATUS_EXPIRED)
            ->orWhere('ends_at', '<', now());
    }

    public function scopeExpiringWithin($query, int $days)
    {
        return $query->where('status', self::STATUS_ACTIVE)
            ->where('ends_at', '<=', now()->addDays($days))
            ->where('ends_at', '>', now());
    }

    public function scopeAutoRenewing($query)
    {
        return $query->where('auto_renewal', true);
    }

    public function scopeByVendor($query, int $vendorId)
    {
        return $query->where('vendor_id', $vendorId);
    }

    public function scopeByCurrency($query, string $currency)
    {
        return $query->where('currency', $currency);
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByPlan($query, int $planId)
    {
        return $query->where('subscription_plan_id', $planId);
    }

    public function scopeCurrentlyActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE)
            ->where('starts_at', '<=', now())
            ->where('ends_at', '>', now());
    }

    public function scopeOverUsage($query, float $percentage = 80.0)
    {
        return $query->whereHas('subscriptionPlan', function ($query) use ($percentage) {
            $query->whereRaw(
                '(vendor_subscriptions.listings_used / subscription_plans.max_listings * 100) >= ?',
                [$percentage]
            );
        });
    }

    public function scopeNeedingRenewal($query)
    {
        return $query->where('auto_renewal', true)
            ->where('status', self::STATUS_ACTIVE)
            ->where('next_billing_date', '<=', now()->addDays(3));
    }

    public function scopeTrialEnding($query, int $days = 3)
    {
        return $query->where('status', self::STATUS_TRIAL)
            ->where('ends_at', '<=', now()->addDays($days))
            ->where('ends_at', '>', now());
    }
}
