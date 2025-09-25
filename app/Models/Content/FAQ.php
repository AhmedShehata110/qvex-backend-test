<?php

namespace App\Models\Content;

use App\Models\BaseModel;
use App\Models\User;
use App\Traits\AuditableTrait;
use Database\Factories\FAQFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class FAQ extends BaseModel
{
    use AuditableTrait, HasFactory, SoftDeletes;

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return FAQFactory::new();
    }

    protected $table = 'faqs';

    protected $fillable = [
        'question',
        'question_ar',
        'answer',
        'answer_ar',
        'category',
        'sort_order',
        'view_count',
        'helpful_count',
        'not_helpful_count',
        'tags',
        'status',
        'is_active',
        'added_by_id',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'view_count' => 'integer',
        'helpful_count' => 'integer',
        'not_helpful_count' => 'integer',
        'tags' => 'array',
        'is_active' => 'boolean',
    ];

    protected $translatable = [
        'question',
        'answer',
        'category',
    ];

    // RELATIONSHIPS

    public function addedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'added_by_id');
    }

    // Status constants
    const STATUS_DRAFT = 'draft';

    const STATUS_PUBLISHED = 'published';

    const STATUS_ARCHIVED = 'archived';

    // Category constants
    const CATEGORY_GENERAL = 'general';

    const CATEGORY_BUYING = 'buying';

    const CATEGORY_SELLING = 'selling';

    const CATEGORY_RENTAL = 'rental';

    const CATEGORY_PAYMENTS = 'payments';

    const CATEGORY_ACCOUNT = 'account';

    const CATEGORY_TECHNICAL = 'technical';

    /**
     * Get statuses with labels
     */
    public static function getStatuses(): array
    {
        return [
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_PUBLISHED => 'Published',
            self::STATUS_ARCHIVED => 'Archived',
        ];
    }

    /**
     * Get categories with labels
     */
    public static function getCategories(): array
    {
        return [
            self::CATEGORY_GENERAL => 'General Questions',
            self::CATEGORY_BUYING => 'Buying Vehicles',
            self::CATEGORY_SELLING => 'Selling Vehicles',
            self::CATEGORY_RENTAL => 'Vehicle Rental',
            self::CATEGORY_PAYMENTS => 'Payments & Pricing',
            self::CATEGORY_ACCOUNT => 'Account Management',
            self::CATEGORY_TECHNICAL => 'Technical Support',
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
     * Get category label
     */
    public function getCategoryLabelAttribute(): string
    {
        return static::getCategories()[$this->category] ?? $this->category;
    }

    /**
     * Check if FAQ is published
     */
    public function isPublished(): bool
    {
        return $this->status === self::STATUS_PUBLISHED && $this->is_active;
    }

    /**
     * Get helpfulness ratio
     */
    public function getHelpfulnessRatioAttribute(): float
    {
        $total = $this->helpful_count + $this->not_helpful_count;

        if ($total === 0) {
            return 0;
        }

        return round(($this->helpful_count / $total) * 100, 1);
    }

    /**
     * Get total feedback count
     */
    public function getTotalFeedbackAttribute(): int
    {
        return $this->helpful_count + $this->not_helpful_count;
    }

    /**
     * Get tags as string
     */
    public function getTagsStringAttribute(): string
    {
        return ! empty($this->tags) ? implode(', ', $this->tags) : '';
    }

    /**
     * Get related FAQs by category and tags
     */
    public function getRelatedFAQs(int $limit = 5)
    {
        $query = static::published()
            ->where('id', '!=', $this->id)
            ->where('category', $this->category);

        // Add tag-based matching if tags exist
        if (! empty($this->tags)) {
            $query->where(function ($q) {
                foreach ($this->tags as $tag) {
                    $q->orWhereJsonContains('tags', $tag);
                }
            });
        }

        return $query->orderByDesc('helpful_count')
            ->limit($limit)
            ->get();
    }

    /**
     * Publish the FAQ
     */
    public function publish(): bool
    {
        return $this->update(['status' => self::STATUS_PUBLISHED]);
    }

    /**
     * Archive the FAQ
     */
    public function archive(): bool
    {
        return $this->update(['status' => self::STATUS_ARCHIVED]);
    }

    /**
     * Increment view count
     */
    public function incrementViews(): void
    {
        $this->increment('view_count');
    }

    /**
     * Mark as helpful
     */
    public function markAsHelpful(): void
    {
        $this->increment('helpful_count');
    }

    /**
     * Mark as not helpful
     */
    public function markAsNotHelpful(): void
    {
        $this->increment('not_helpful_count');
    }

    /**
     * Update sort order for category
     */
    public function updateSortOrder(int $newOrder): bool
    {
        // Adjust other FAQs in the same category
        static::where('category', $this->category)
            ->where('sort_order', '>=', $newOrder)
            ->where('id', '!=', $this->id)
            ->increment('sort_order');

        return $this->update(['sort_order' => $newOrder]);
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('status', self::STATUS_PUBLISHED)
            ->where('is_active', true);
    }

    public function scopeDraft($query)
    {
        return $query->where('status', self::STATUS_DRAFT);
    }

    public function scopeArchived($query)
    {
        return $query->where('status', self::STATUS_ARCHIVED);
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByTag($query, string $tag)
    {
        return $query->whereJsonContains('tags', $tag);
    }

    public function scopePopular($query, int $minViews = 10)
    {
        return $query->where('view_count', '>=', $minViews)
            ->orderByDesc('view_count');
    }

    public function scopeHelpful($query, float $minRatio = 70.0)
    {
        return $query->whereRaw('
            CASE 
                WHEN (helpful_count + not_helpful_count) > 0 
                THEN (helpful_count / (helpful_count + not_helpful_count)) * 100 
                ELSE 0 
            END >= ?
        ', [$minRatio]);
    }

    public function scopeOrderedByCategory($query)
    {
        return $query->orderBy('category')->orderBy('sort_order');
    }

    public function scopeSearchByQuestion($query, string $search)
    {
        return $query->where('question', 'LIKE', "%{$search}%");
    }

    public function scopeSearchByContent($query, string $search)
    {
        return $query->where(function ($query) use ($search) {
            $query->where('question', 'LIKE', "%{$search}%")
                ->orWhere('answer', 'LIKE', "%{$search}%");
        });
    }

    public function scopeNeedingAttention($query)
    {
        return $query->where(function ($query) {
            $query->whereRaw('not_helpful_count > helpful_count')
                ->orWhere('view_count', '>', 100)
                ->where('helpful_count', 0);
        });
    }

    public function scopeRecentlyViewed($query, int $days = 30)
    {
        return $query->where('updated_at', '>=', now()->subDays($days))
            ->where('view_count', '>', 0)
            ->orderByDesc('view_count');
    }
}
