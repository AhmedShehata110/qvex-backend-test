<?php

namespace App\Models\Content;

use App\Models\BaseModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Page extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'excerpt',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'featured_image',
        'template',
        'status',
        'visibility',
        'show_in_menu',
        'menu_order',
        'parent_id',
        'author_id',
        'published_at',
        'custom_fields',
    ];

    protected $casts = [
        'show_in_menu' => 'boolean',
        'menu_order' => 'integer',
        'published_at' => 'timestamp',
        'custom_fields' => 'array',
        'meta_keywords' => 'array',
    ];

    protected $translatable = [
        'title',
        'content',
        'excerpt',
        'meta_title',
        'meta_description',
    ];

    // Status constants
    const STATUS_DRAFT = 'draft';

    const STATUS_PUBLISHED = 'published';

    const STATUS_SCHEDULED = 'scheduled';

    const STATUS_PRIVATE = 'private';

    // Visibility constants
    const VISIBILITY_PUBLIC = 'public';

    const VISIBILITY_PRIVATE = 'private';

    const VISIBILITY_PASSWORD_PROTECTED = 'password_protected';

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(static::class, 'parent_id');
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
            self::STATUS_PRIVATE => 'Private',
        ];
    }

    /**
     * Check if page is published
     */
    public function isPublished(): bool
    {
        return $this->status === self::STATUS_PUBLISHED
            && $this->visibility === self::VISIBILITY_PUBLIC
            && (! $this->published_at || $this->published_at->isPast());
    }

    /**
     * Get URL slug
     */
    public function getUrl(): string
    {
        return route('pages.show', $this->slug);
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('status', self::STATUS_PUBLISHED)
            ->where('visibility', self::VISIBILITY_PUBLIC)
            ->where(function ($query) {
                $query->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            });
    }

    public function scopeInMenu($query)
    {
        return $query->where('show_in_menu', true)->orderBy('menu_order');
    }
}
