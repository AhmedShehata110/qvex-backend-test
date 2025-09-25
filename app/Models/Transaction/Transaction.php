<?php

namespace App\Models\Transaction;

use App\Models\BaseModel;
use App\Models\User;
use App\Models\Vehicle\Vehicle;
use Database\Factories\TransactionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Transaction extends BaseModel
{
    use HasFactory;

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return TransactionFactory::new();
    }

    protected $fillable = [
        'transaction_number',
        'buyer_id',
        'seller_id',
        'vehicle_id',
        'type',
        'status',
        'subtotal',
        'tax_amount',
        'commission_amount',
        'total_amount',
        'paid_amount',
        'refunded_amount',
        'currency',
        'transaction_data',
        'notes',
        'cancellation_reason',
        'confirmed_at',
        'completed_at',
        'cancelled_at',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'refunded_amount' => 'decimal:2',
        'transaction_data' => 'array',
        'confirmed_at' => 'timestamp',
        'completed_at' => 'timestamp',
        'cancelled_at' => 'timestamp',
    ];

    protected $translatable = [];

    // Type constants
    const TYPE_SALE = 'sale';

    const TYPE_RENTAL = 'rental';

    const TYPE_LEASE = 'lease';

    // Status constants
    const STATUS_PENDING = 'pending';

    const STATUS_CONFIRMED = 'confirmed';

    const STATUS_PAYMENT_PENDING = 'payment_pending';

    const STATUS_PAID = 'paid';

    const STATUS_IN_PROGRESS = 'in_progress';

    const STATUS_COMPLETED = 'completed';

    const STATUS_CANCELLED = 'cancelled';

    const STATUS_REFUNDED = 'refunded';

    const STATUS_DISPUTED = 'disputed';

    // Relationships
    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function rentalAgreement(): HasOne
    {
        return $this->hasOne(RentalAgreement::class);
    }

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->transaction_number)) {
                $model->transaction_number = $model->generateTransactionNumber();
            }
        });
    }

    /**
     * Generate unique transaction number
     */
    protected function generateTransactionNumber(): string
    {
        do {
            $number = 'TXN-'.strtoupper(Str::random(8));
        } while (static::where('transaction_number', $number)->exists());

        return $number;
    }

    /**
     * Get transaction types with labels
     */
    public static function getTypes(): array
    {
        return [
            self::TYPE_SALE => 'Sale',
            self::TYPE_RENTAL => 'Rental',
            self::TYPE_LEASE => 'Lease',
        ];
    }

    /**
     * Get transaction statuses with labels
     */
    public static function getStatuses(): array
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_CONFIRMED => 'Confirmed',
            self::STATUS_PAYMENT_PENDING => 'Payment Pending',
            self::STATUS_PAID => 'Paid',
            self::STATUS_IN_PROGRESS => 'In Progress',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_CANCELLED => 'Cancelled',
            self::STATUS_REFUNDED => 'Refunded',
            self::STATUS_DISPUTED => 'Disputed',
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
     * Get status label
     */
    public function getStatusLabelAttribute(): string
    {
        return static::getStatuses()[$this->status] ?? $this->status;
    }

    /**
     * Check if transaction is a sale
     */
    public function isSale(): bool
    {
        return $this->type === self::TYPE_SALE;
    }

    /**
     * Check if transaction is a rental
     */
    public function isRental(): bool
    {
        return $this->type === self::TYPE_RENTAL;
    }

    /**
     * Check if transaction is a lease
     */
    public function isLease(): bool
    {
        return $this->type === self::TYPE_LEASE;
    }

    /**
     * Check if transaction is pending
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if transaction is completed
     */
    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * Check if transaction is cancelled
     */
    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    /**
     * Get remaining amount to be paid
     */
    public function getRemainingAmountAttribute(): float
    {
        return max(0, $this->total_amount - $this->paid_amount);
    }

    /**
     * Check if transaction is fully paid
     */
    public function isFullyPaid(): bool
    {
        return $this->paid_amount >= $this->total_amount;
    }

    /**
     * Confirm the transaction
     */
    public function confirm(): bool
    {
        return $this->update([
            'status' => self::STATUS_CONFIRMED,
            'confirmed_at' => now(),
        ]);
    }

    /**
     * Mark as payment pending
     */
    public function markPaymentPending(): bool
    {
        return $this->update(['status' => self::STATUS_PAYMENT_PENDING]);
    }

    /**
     * Mark as paid
     */
    public function markAsPaid(): bool
    {
        return $this->update(['status' => self::STATUS_PAID]);
    }

    /**
     * Complete the transaction
     */
    public function complete(): bool
    {
        return $this->update([
            'status' => self::STATUS_COMPLETED,
            'completed_at' => now(),
        ]);
    }

    /**
     * Cancel the transaction
     */
    public function cancel(?string $reason = null): bool
    {
        return $this->update([
            'status' => self::STATUS_CANCELLED,
            'cancelled_at' => now(),
            'cancellation_reason' => $reason,
        ]);
    }

    /**
     * Add payment amount
     */
    public function addPayment(float $amount): bool
    {
        return $this->update([
            'paid_amount' => $this->paid_amount + $amount,
        ]);
    }

    /**
     * Calculate commission
     */
    public function calculateCommission(float $rate = 5.0): float
    {
        return round($this->subtotal * ($rate / 100), 2);
    }

    // Scopes
    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeSales($query)
    {
        return $query->where('type', self::TYPE_SALE);
    }

    public function scopeRentals($query)
    {
        return $query->where('type', self::TYPE_RENTAL);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    public function scopeByBuyer($query, int $buyerId)
    {
        return $query->where('buyer_id', $buyerId);
    }

    public function scopeBySeller($query, int $sellerId)
    {
        return $query->where('seller_id', $sellerId);
    }

    public function scopeRecentFirst($query)
    {
        return $query->orderBy('created_at', 'desc');
    }
}
