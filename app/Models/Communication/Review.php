<?php

namespace App\Models\Communication;

use App\Models\BaseModel;
use App\Models\Transaction\Transaction;
use App\Models\User;
use App\Models\Vehicle\Vehicle;
use App\Models\Vendor\Vendor;
use App\Traits\AuditableTrait;
use Database\Factories\ReviewFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Review extends BaseModel
{
    use AuditableTrait, HasFactory, SoftDeletes;

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return ReviewFactory::new();
    }

    protected $fillable = [
        'reviewer_id',
        'reviewee_id',
        'transaction_id',
        'vehicle_id',
        'vendor_id',
        'rating',
        'title',
        'content',
        'pros',
        'cons',
        'would_recommend',
        'verified_purchase',
        'status',
        'flagged_inappropriate',
        'flagged_reason',
        'flagged_by',
        'flagged_at',
        'approved_by',
        'approved_at',
        'helpful_count',
        'not_helpful_count',
        'response_count',
        'photos',
        'metadata',
    ];

    protected $casts = [
        'rating' => 'integer',
        'pros' => 'array',
        'cons' => 'array',
        'would_recommend' => 'boolean',
        'verified_purchase' => 'boolean',
        'flagged_inappropriate' => 'boolean',
        'flagged_at' => 'timestamp',
        'approved_at' => 'timestamp',
        'helpful_count' => 'integer',
        'not_helpful_count' => 'integer',
        'response_count' => 'integer',
        'photos' => 'array',
        'metadata' => 'array',
    ];

    protected $translatable = [
        'title',
        'content',
        'pros',
        'cons',
    ];

    // Status constants
    const STATUS_PENDING = 'pending';

    const STATUS_APPROVED = 'approved';

    const STATUS_REJECTED = 'rejected';

    const STATUS_HIDDEN = 'hidden';

    // RELATIONSHIPS

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function reviewee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewee_id');
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function flaggedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'flagged_by');
    }

    public function responses(): HasMany
    {
        return $this->hasMany(ReviewResponse::class);
    }

    // HELPER METHODS

    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isFlagged(): bool
    {
        return $this->flagged_inappropriate;
    }

    public function isVerifiedPurchase(): bool
    {
        return $this->verified_purchase;
    }

    public function getHelpfulnessRatioAttribute(): float
    {
        $total = $this->helpful_count + $this->not_helpful_count;

        if ($total === 0) {
            return 0;
        }

        return round(($this->helpful_count / $total) * 100, 1);
    }

    public function approve(): bool
    {
        return $this->update([
            'status' => self::STATUS_APPROVED,
            'approved_at' => now(),
        ]);
    }

    public function reject(): bool
    {
        return $this->update(['status' => self::STATUS_REJECTED]);
    }

    public function markAsHelpful(): void
    {
        $this->increment('helpful_count');
    }

    public function markAsNotHelpful(): void
    {
        $this->increment('not_helpful_count');
    }

    // SCOPES

    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeByRating($query, int $rating)
    {
        return $query->where('rating', $rating);
    }

    public function scopeVerifiedPurchases($query)
    {
        return $query->where('verified_purchase', true);
    }

    public function scopeByVehicle($query, int $vehicleId)
    {
        return $query->where('vehicle_id', $vehicleId);
    }

    public function scopeByVendor($query, int $vendorId)
    {
        return $query->where('vendor_id', $vendorId);
    }
}
