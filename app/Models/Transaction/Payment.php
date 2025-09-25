<?php

namespace App\Models\Transaction;

use App\Models\BaseModel;
use App\Models\User;
use App\Traits\AuditableTrait;
use Database\Factories\PaymentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Payment extends BaseModel
{
    use AuditableTrait, HasFactory;

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return PaymentFactory::new();
    }

    protected $fillable = [
        'transaction_id',
        'user_id',
        'payment_method',
        'payment_gateway',
        'gateway_transaction_id',
        'gateway_payment_id',
        'amount',
        'currency',
        'status',
        'gateway_response',
        'failure_reason',
        'processed_at',
        'refunded_amount',
        'refunded_at',
        'metadata',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'refunded_amount' => 'decimal:2',
        'processed_at' => 'timestamp',
        'refunded_at' => 'timestamp',
        'gateway_response' => 'array',
        'metadata' => 'array',
    ];

    protected $translatable = [];

    // Status constants
    const STATUS_PENDING = 'pending';

    const STATUS_PROCESSING = 'processing';

    const STATUS_COMPLETED = 'completed';

    const STATUS_FAILED = 'failed';

    const STATUS_CANCELLED = 'cancelled';

    const STATUS_REFUNDED = 'refunded';

    const STATUS_PARTIALLY_REFUNDED = 'partially_refunded';

    // Payment method constants
    const METHOD_CREDIT_CARD = 'credit_card';

    const METHOD_DEBIT_CARD = 'debit_card';

    const METHOD_BANK_TRANSFER = 'bank_transfer';

    const METHOD_DIGITAL_WALLET = 'digital_wallet';

    const METHOD_CASH = 'cash';

    // Gateway constants
    const GATEWAY_STRIPE = 'stripe';

    const GATEWAY_PAYPAL = 'paypal';

    const GATEWAY_RAZORPAY = 'razorpay';

    const GATEWAY_FLUTTERWAVE = 'flutterwave';

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // public function refunds(): HasMany
    // {
    //     return $this->hasMany(PaymentRefund::class);
    // }

    /**
     * Get payment statuses with labels
     */
    public static function getStatuses(): array
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_PROCESSING => 'Processing',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_FAILED => 'Failed',
            self::STATUS_CANCELLED => 'Cancelled',
            self::STATUS_REFUNDED => 'Refunded',
            self::STATUS_PARTIALLY_REFUNDED => 'Partially Refunded',
        ];
    }

    /**
     * Get payment methods with labels
     */
    public static function getMethods(): array
    {
        return [
            self::METHOD_CREDIT_CARD => 'Credit Card',
            self::METHOD_DEBIT_CARD => 'Debit Card',
            self::METHOD_BANK_TRANSFER => 'Bank Transfer',
            self::METHOD_DIGITAL_WALLET => 'Digital Wallet',
            self::METHOD_CASH => 'Cash',
        ];
    }

    /**
     * Get payment gateways with labels
     */
    public static function getGateways(): array
    {
        return [
            self::GATEWAY_STRIPE => 'Stripe',
            self::GATEWAY_PAYPAL => 'PayPal',
            self::GATEWAY_RAZORPAY => 'Razorpay',
            self::GATEWAY_FLUTTERWAVE => 'Flutterwave',
        ];
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute(): string
    {
        return static::getStatuses()[$this->status] ?? $this->status;
    }

    /**
     * Get method label
     */
    public function getMethodLabelAttribute(): string
    {
        return static::getMethods()[$this->payment_method] ?? $this->payment_method;
    }

    /**
     * Get gateway label
     */
    public function getGatewayLabelAttribute(): string
    {
        return static::getGateways()[$this->payment_gateway] ?? $this->payment_gateway;
    }

    /**
     * Check if payment is pending
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if payment is processing
     */
    public function isProcessing(): bool
    {
        return $this->status === self::STATUS_PROCESSING;
    }

    /**
     * Check if payment is completed
     */
    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * Check if payment failed
     */
    public function hasFailed(): bool
    {
        return $this->status === self::STATUS_FAILED;
    }

    /**
     * Check if payment is refunded
     */
    public function isRefunded(): bool
    {
        return in_array($this->status, [self::STATUS_REFUNDED, self::STATUS_PARTIALLY_REFUNDED]);
    }

    /**
     * Check if payment can be refunded
     */
    public function canBeRefunded(): bool
    {
        return $this->isCompleted() && $this->refunded_amount < $this->amount;
    }

    /**
     * Get remaining refundable amount
     */
    public function getRemainingRefundableAmountAttribute(): float
    {
        return max(0, $this->amount - ($this->refunded_amount ?? 0));
    }

    /**
     * Mark payment as processing
     */
    public function markAsProcessing(?string $gatewayTransactionId = null): bool
    {
        $updates = ['status' => self::STATUS_PROCESSING];

        if ($gatewayTransactionId) {
            $updates['gateway_transaction_id'] = $gatewayTransactionId;
        }

        return $this->update($updates);
    }

    /**
     * Mark payment as completed
     */
    public function markAsCompleted(array $gatewayResponse = []): bool
    {
        return $this->update([
            'status' => self::STATUS_COMPLETED,
            'processed_at' => now(),
            'gateway_response' => $gatewayResponse,
        ]);
    }

    /**
     * Mark payment as failed
     */
    public function markAsFailed(?string $reason = null, array $gatewayResponse = []): bool
    {
        return $this->update([
            'status' => self::STATUS_FAILED,
            'failure_reason' => $reason,
            'gateway_response' => $gatewayResponse,
        ]);
    }

    /**
     * Process refund
     */
    public function processRefund(float $amount, ?string $reason = null): bool
    {
        if (! $this->canBeRefunded() || $amount > $this->remaining_refundable_amount) {
            return false;
        }

        $newRefundedAmount = ($this->refunded_amount ?? 0) + $amount;
        $status = $newRefundedAmount >= $this->amount ? self::STATUS_REFUNDED : self::STATUS_PARTIALLY_REFUNDED;

        return $this->update([
            'refunded_amount' => $newRefundedAmount,
            'refunded_at' => now(),
            'status' => $status,
        ]);
    }

    /**
     * Get formatted amount with currency
     */
    public function getFormattedAmountAttribute(): string
    {
        return $this->currency.' '.number_format($this->amount, 2);
    }

    /**
     * Get formatted refunded amount with currency
     */
    public function getFormattedRefundedAmountAttribute(): string
    {
        if (! $this->refunded_amount) {
            return $this->currency.' 0.00';
        }

        return $this->currency.' '.number_format($this->refunded_amount, 2);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeProcessing($query)
    {
        return $query->where('status', self::STATUS_PROCESSING);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    public function scopeFailed($query)
    {
        return $query->where('status', self::STATUS_FAILED);
    }

    public function scopeRefunded($query)
    {
        return $query->whereIn('status', [self::STATUS_REFUNDED, self::STATUS_PARTIALLY_REFUNDED]);
    }

    public function scopeByGateway($query, string $gateway)
    {
        return $query->where('payment_gateway', $gateway);
    }

    public function scopeByMethod($query, string $method)
    {
        return $query->where('payment_method', $method);
    }

    public function scopeProcessedBetween($query, $startDate, $endDate)
    {
        return $query->whereBetween('processed_at', [$startDate, $endDate]);
    }
}
