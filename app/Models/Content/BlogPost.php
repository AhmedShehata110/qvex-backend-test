<?php

namespace App\Models\Content;

use App\Models\BaseModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class BlogPost extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'featured_image',
        'meta_title',
        'meta_description',
        'tags',
        'view_count',
        'is_featured',
        'status',
        'published_at',
        'author_id',
        'reading_time',
        'category',
        'seo_keywords',
    ];

    protected $casts = [
        'tags' => 'array',
        'view_count' => 'integer',
        'is_featured' => 'boolean',
        'published_at' => 'timestamp',
        'reading_time' => 'integer',
        'seo_keywords' => 'array',
    ];

    protected $translatable = [
        'title',
        'excerpt',
        'content',
        'meta_title',
        'meta_description',
        'category',
    ];

    // Status constants
    const STATUS_DRAFT = 'draft';

    const STATUS_PUBLISHED = 'published';

    const STATUS_SCHEDULED = 'scheduled';

    // Category constants
    const CATEGORY_NEWS = 'news';

    const CATEGORY_TIPS = 'tips';

    const CATEGORY_REVIEWS = 'reviews';

    const CATEGORY_GUIDES = 'guides';

    const CATEGORY_INDUSTRY = 'industry';

    const CATEGORY_COMPANY = 'company';

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * Get statuses with labels
     */
    public static function getStatuses(): array
    {
        return [
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_PUBLISHED => 'Published',
            self::STATUS_SCHEDULED => 'Scheduled',
        ];
    }

    /**
     * Get categories with labels
     */
    public static function getCategories(): array
    {
        return [
            self::CATEGORY_NEWS => 'News',
            self::CATEGORY_TIPS => 'Tips & Advice',
            self::CATEGORY_REVIEWS => 'Vehicle Reviews',
            self::CATEGORY_GUIDES => 'Buying Guides',
            self::CATEGORY_INDUSTRY => 'Industry Insights',
            self::CATEGORY_COMPANY => 'Company Updates',
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
     * Check if blog post is published
     */
    public function isPublished(): bool
    {
        return $this->status === self::STATUS_PUBLISHED
            && (! $this->published_at || $this->published_at->isPast());
    }

    /**
     * Check if blog post is scheduled
     */
    public function isScheduled(): bool
    {
        return $this->status === self::STATUS_SCHEDULED
            && $this->published_at
            && $this->published_at->isFuture();
    }

    /**
     * Check if blog post is featured
     */
    public function isFeatured(): bool
    {
        return $this->is_featured;
    }

    /**
     * Get formatted published date
     */
    public function getFormattedPublishedDateAttribute(): ?string
    {
        return $this->published_at ? $this->published_at->format('M d, Y') : null;
    }

    /**
     * Get time since published
     */
    public function getTimeSincePublishedAttribute(): ?string
    {
        return $this->published_at ? $this->published_at->diffForHumans() : null;
    }

    /**
     * Get estimated reading time
     */
    public function getEstimatedReadingTimeAttribute(): int
    {
        if ($this->reading_time) {
            return $this->reading_time;
        }

        // Calculate based on content (average 200 words per minute)
        $wordCount = str_word_count(strip_tags($this->content ?? ''));

        return max(1, ceil($wordCount / 200));
    }

    /**
     * Get excerpt or generate from content
     */
    public function getExcerptAttribute($value): string
    {
        if ($value) {
            return $value;
        }

        // Generate excerpt from content if not provided
        $content = strip_tags($this->content ?? '');

        return Str::limit($content, 160);
    }

    /**
     * Get URL for blog post
     */
    public function getUrlAttribute(): string
    {
        return route('blog.show', $this->slug);
    }

    /**
     * Get tags as string
     */
    public function getTagsStringAttribute(): string
    {
        return ! empty($this->tags) ? implode(', ', $this->tags) : '';
    }

    /**
     * Get related posts by tags
     */
    public function getRelatedPosts(int $limit = 5)
    {
        if (empty($this->tags)) {
            return collect();
        }

        return static::published()
            ->where('id', '!=', $this->id)
            ->where(function ($query) {
                foreach ($this->tags as $tag) {
                    $query->orWhereJsonContains('tags', $tag);
                }
            })
            ->orderByDesc('published_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Publish the blog post
     */
    public function publish(): bool
    {
        return $this->update([
            'status' => self::STATUS_PUBLISHED,
            'published_at' => now(),
        ]);
    }

    /**
     * Schedule the blog post
     */
    public function schedule(\DateTime $publishDate): bool
    {
        return $this->update([
            'status' => self::STATUS_SCHEDULED,
            'published_at' => $publishDate,
        ]);
    }

    /**
     * Mark as featured
     */
    public function markAsFeatured(): bool
    {
        return $this->update(['is_featured' => true]);
    }

    /**
     * Unmark as featured
     */
    public function unmarkAsFeatured(): bool
    {
        return $this->update(['is_featured' => false]);
    }

    /**
     * Increment view count
     */
    public function incrementViews(): void
    {
        $this->increment('view_count');
    }

    /**
     * Generate slug from title
     */
    public function generateSlug(): string
    {
        $baseSlug = Str::slug($this->title);
        $slug = $baseSlug;
        $counter = 1;

        while (static::where('slug', $slug)->where('id', '!=', $this->id ?? 0)->exists()) {
            $slug = $baseSlug.'-'.$counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Boot model events
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($post) {
            if (empty($post->slug)) {
                $post->slug = $post->generateSlug();
            }
        });

        static::updating(function ($post) {
            if ($post->isDirty('title') && empty($post->slug)) {
                $post->slug = $post->generateSlug();
            }
        });
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('status', self::STATUS_PUBLISHED)
            ->where(function ($query) {
                $query->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            });
    }

    public function scopeDraft($query)
    {
        return $query->where('status', self::STATUS_DRAFT);
    }

    public function scopeScheduled($query)
    {
        return $query->where('status', self::STATUS_SCHEDULED)
            ->where('published_at', '>', now());
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByAuthor($query, int $authorId)
    {
        return $query->where('author_id', $authorId);
    }

    public function scopeByTag($query, string $tag)
    {
        return $query->whereJsonContains('tags', $tag);
    }

    public function scopePopular($query, int $minViews = 100)
    {
        return $query->where('view_count', '>=', $minViews)
            ->orderByDesc('view_count');
    }

    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('published_at', '>=', now()->subDays($days))
            ->orderByDesc('published_at');
    }

    public function scopeSearchByTitle($query, string $search)
    {
        return $query->where('title', 'LIKE', "%{$search}%");
    }

    public function scopeSearchByContent($query, string $search)
    {
        return $query->where(function ($query) use ($search) {
            $query->where('title', 'LIKE', "%{$search}%")
                ->orWhere('excerpt', 'LIKE', "%{$search}%")
                ->orWhere('content', 'LIKE', "%{$search}%");
        });
    }

    public function scopeReadyToPublish($query)
    {
        return $query->where('status', self::STATUS_SCHEDULED)
            ->where('published_at', '<=', now());
    }
}
