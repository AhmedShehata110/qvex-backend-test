<?php

namespace App\Models\Marketing;

use App\Models\BaseModel;
use App\Models\Transaction\Transaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CouponUse extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'coupon_id',
        'user_id',
        'transaction_id',
        'order_amount',
        'discount_amount',
        'used_at',
        'metadata',
    ];

    protected $casts = [
        'order_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'used_at' => 'timestamp',
        'metadata' => 'array',
    ];

    protected $translatable = [];

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    /**
     * Get discount percentage
     */
    public function getDiscountPercentageAttribute(): float
    {
        if ($this->order_amount == 0) {
            return 0;
        }

        return ($this->discount_amount / $this->order_amount) * 100;
    }

    /**
     * Get savings amount formatted
     */
    public function getFormattedSavingsAttribute(): string
    {
        return '$'.number_format($this->discount_amount, 2);
    }

    /**
     * Get time since used
     */
    public function getTimeSinceUsedAttribute(): string
    {
        return $this->used_at->diffForHumans();
    }

    // Scopes
    public function scopeByCoupon($query, int $couponId)
    {
        return $query->where('coupon_id', $couponId);
    }

    public function scopeByUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeUsedToday($query)
    {
        return $query->whereDate('used_at', today());
    }

    public function scopeUsedThisWeek($query)
    {
        return $query->whereBetween('used_at', [now()->startOfWeek(), now()->endOfWeek()]);
    }

    public function scopeUsedThisMonth($query)
    {
        return $query->whereBetween('used_at', [now()->startOfMonth(), now()->endOfMonth()]);
    }

    public function scopeHighValue($query, float $minDiscount = 50)
    {
        return $query->where('discount_amount', '>=', $minDiscount);
    }
}
