<?php

namespace App\Models\Communication;

use App\Models\BaseModel;
use App\Models\User;
use App\Models\Vendor\Vendor;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReviewResponse extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'review_id',
        'vendor_id',
        'responder_id',
        'response_text',
        'response_type',
        'status',
        'is_public',
        'internal_notes',
        'sentiment',
        'flagged_inappropriate',
        'flagged_reason',
        'flagged_by',
        'flagged_at',
        'approved_by',
        'approved_at',
        'published_at',
        'template_used',
        'metadata',
    ];

    protected $casts = [
        'is_public' => 'boolean',
        'flagged_inappropriate' => 'boolean',
        'flagged_at' => 'timestamp',
        'approved_at' => 'timestamp',
        'published_at' => 'timestamp',
        'metadata' => 'array',
    ];

    protected $translatable = [
        'response_text',
        'internal_notes',
    ];

    // Response type constants
    const TYPE_OFFICIAL = 'official';

    const TYPE_PERSONAL = 'personal';

    const TYPE_AUTOMATED = 'automated';

    const TYPE_TEMPLATE = 'template';

    // Status constants
    const STATUS_DRAFT = 'draft';

    const STATUS_PENDING_APPROVAL = 'pending_approval';

    const STATUS_APPROVED = 'approved';

    const STATUS_PUBLISHED = 'published';

    const STATUS_REJECTED = 'rejected';

    const STATUS_HIDDEN = 'hidden';

    // Sentiment constants
    const SENTIMENT_POSITIVE = 'positive';

    const SENTIMENT_NEUTRAL = 'neutral';

    const SENTIMENT_NEGATIVE = 'negative';

    const SENTIMENT_PROFESSIONAL = 'professional';

    const SENTIMENT_APOLOGETIC = 'apologetic';

    const SENTIMENT_DEFENSIVE = 'defensive';

    public function review(): BelongsTo
    {
        return $this->belongsTo(Review::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function responder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responder_id');
    }

    public function flaggedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'flagged_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get response types with labels
     */
    public static function getResponseTypes(): array
    {
        return [
            self::TYPE_OFFICIAL => 'Official Response',
            self::TYPE_PERSONAL => 'Personal Response',
            self::TYPE_AUTOMATED => 'Automated Response',
            self::TYPE_TEMPLATE => 'Template Response',
        ];
    }

    /**
     * Get status options with labels
     */
    public static function getStatuses(): array
    {
        return [
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_PENDING_APPROVAL => 'Pending Approval',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_PUBLISHED => 'Published',
            self::STATUS_REJECTED => 'Rejected',
            self::STATUS_HIDDEN => 'Hidden',
        ];
    }

    /**
     * Get sentiment options with labels
     */
    public static function getSentiments(): array
    {
        return [
            self::SENTIMENT_POSITIVE => 'Positive',
            self::SENTIMENT_NEUTRAL => 'Neutral',
            self::SENTIMENT_NEGATIVE => 'Negative',
            self::SENTIMENT_PROFESSIONAL => 'Professional',
            self::SENTIMENT_APOLOGETIC => 'Apologetic',
            self::SENTIMENT_DEFENSIVE => 'Defensive',
        ];
    }

    /**
     * Get response type label
     */
    public function getResponseTypeLabelAttribute(): string
    {
        return static::getResponseTypes()[$this->response_type] ?? $this->response_type;
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute(): string
    {
        return static::getStatuses()[$this->status] ?? $this->status;
    }

    /**
     * Get sentiment label
     */
    public function getSentimentLabelAttribute(): string
    {
        return static::getSentiments()[$this->sentiment] ?? $this->sentiment;
    }

    /**
     * Check if response is published
     */
    public function isPublished(): bool
    {
        return $this->status === self::STATUS_PUBLISHED;
    }

    /**
     * Check if response is public
     */
    public function isPublic(): bool
    {
        return $this->is_public && $this->isPublished();
    }

    /**
     * Check if response is pending approval
     */
    public function isPendingApproval(): bool
    {
        return $this->status === self::STATUS_PENDING_APPROVAL;
    }

    /**
     * Check if response is approved
     */
    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    /**
     * Check if response is flagged
     */
    public function isFlagged(): bool
    {
        return $this->flagged_inappropriate;
    }

    /**
     * Check if response is automated
     */
    public function isAutomated(): bool
    {
        return $this->response_type === self::TYPE_AUTOMATED;
    }

    /**
     * Check if response uses template
     */
    public function usesTemplate(): bool
    {
        return ! empty($this->template_used);
    }

    /**
     * Get response length
     */
    public function getResponseLengthAttribute(): int
    {
        return strlen(strip_tags($this->response_text ?? ''));
    }

    /**
     * Get time since response was created
     */
    public function getTimeSinceCreatedAttribute(): string
    {
        return $this->created_at->diffForHumans();
    }

    /**
     * Get time since published
     */
    public function getTimeSincePublishedAttribute(): ?string
    {
        return $this->published_at ? $this->published_at->diffForHumans() : null;
    }

    /**
     * Get response preview (first 100 characters)
     */
    public function getResponsePreviewAttribute(): string
    {
        $text = strip_tags($this->response_text ?? '');

        return strlen($text) > 100 ? substr($text, 0, 100).'...' : $text;
    }

    /**
     * Approve the response
     */
    public function approve(?int $approvedBy = null): bool
    {
        return $this->update([
            'status' => self::STATUS_APPROVED,
            'approved_by' => $approvedBy,
            'approved_at' => now(),
        ]);
    }

    /**
     * Reject the response
     */
    public function reject(): bool
    {
        return $this->update(['status' => self::STATUS_REJECTED]);
    }

    /**
     * Publish the response
     */
    public function publish(): bool
    {
        if (! $this->isApproved()) {
            return false;
        }

        return $this->update([
            'status' => self::STATUS_PUBLISHED,
            'published_at' => now(),
        ]);
    }

    /**
     * Hide the response
     */
    public function hide(): bool
    {
        return $this->update(['status' => self::STATUS_HIDDEN]);
    }

    /**
     * Make response public
     */
    public function makePublic(): bool
    {
        return $this->update(['is_public' => true]);
    }

    /**
     * Make response private
     */
    public function makePrivate(): bool
    {
        return $this->update(['is_public' => false]);
    }

    /**
     * Flag as inappropriate
     */
    public function flagAsInappropriate(string $reason, ?int $flaggedBy = null): bool
    {
        return $this->update([
            'flagged_inappropriate' => true,
            'flagged_reason' => $reason,
            'flagged_by' => $flaggedBy,
            'flagged_at' => now(),
        ]);
    }

    /**
     * Unflag response
     */
    public function unflag(): bool
    {
        return $this->update([
            'flagged_inappropriate' => false,
            'flagged_reason' => null,
            'flagged_by' => null,
            'flagged_at' => null,
        ]);
    }

    /**
     * Set sentiment
     */
    public function setSentiment(string $sentiment): bool
    {
        return $this->update(['sentiment' => $sentiment]);
    }

    /**
     * Submit for approval
     */
    public function submitForApproval(): bool
    {
        return $this->update(['status' => self::STATUS_PENDING_APPROVAL]);
    }

    /**
     * Save as draft
     */
    public function saveAsDraft(): bool
    {
        return $this->update(['status' => self::STATUS_DRAFT]);
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('status', self::STATUS_PUBLISHED);
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true)
            ->where('status', self::STATUS_PUBLISHED);
    }

    public function scopePendingApproval($query)
    {
        return $query->where('status', self::STATUS_PENDING_APPROVAL);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopeDrafts($query)
    {
        return $query->where('status', self::STATUS_DRAFT);
    }

    public function scopeFlagged($query)
    {
        return $query->where('flagged_inappropriate', true);
    }

    public function scopeNotFlagged($query)
    {
        return $query->where('flagged_inappropriate', false);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('response_type', $type);
    }

    public function scopeOfficial($query)
    {
        return $query->where('response_type', self::TYPE_OFFICIAL);
    }

    public function scopeAutomated($query)
    {
        return $query->where('response_type', self::TYPE_AUTOMATED);
    }

    public function scopeBySentiment($query, string $sentiment)
    {
        return $query->where('sentiment', $sentiment);
    }

    public function scopePositive($query)
    {
        return $query->where('sentiment', self::SENTIMENT_POSITIVE);
    }

    public function scopeNegative($query)
    {
        return $query->where('sentiment', self::SENTIMENT_NEGATIVE);
    }

    public function scopeByVendor($query, int $vendorId)
    {
        return $query->where('vendor_id', $vendorId);
    }

    public function scopeByResponder($query, int $responderId)
    {
        return $query->where('responder_id', $responderId);
    }

    public function scopeRecentlyPublished($query, int $days = 7)
    {
        return $query->where('status', self::STATUS_PUBLISHED)
            ->where('published_at', '>=', now()->subDays($days));
    }

    public function scopeNeedsApproval($query)
    {
        return $query->where('status', self::STATUS_PENDING_APPROVAL)
            ->orderBy('created_at');
    }

    public function scopeByReviewRating($query, int $rating)
    {
        return $query->whereHas('review', function ($query) use ($rating) {
            $query->where('rating', $rating);
        });
    }

    public function scopeForNegativeReviews($query)
    {
        return $query->whereHas('review', function ($query) {
            $query->where('rating', '<=', 2);
        });
    }

    public function scopeForPositiveReviews($query)
    {
        return $query->whereHas('review', function ($query) {
            $query->where('rating', '>=', 4);
        });
    }

    public function scopeUsingTemplate($query, ?string $template = null)
    {
        if ($template) {
            return $query->where('template_used', $template);
        }

        return $query->whereNotNull('template_used');
    }

    public function scopeByWordCount($query, ?int $minWords = null, ?int $maxWords = null)
    {
        if ($minWords) {
            $query->whereRaw('CHAR_LENGTH(TRIM(response_text)) - CHAR_LENGTH(REPLACE(TRIM(response_text), \' \', \'\')) + 1 >= ?', [$minWords]);
        }

        if ($maxWords) {
            $query->whereRaw('CHAR_LENGTH(TRIM(response_text)) - CHAR_LENGTH(REPLACE(TRIM(response_text), \' \', \'\')) + 1 <= ?', [$maxWords]);
        }

        return $query;
    }
}
