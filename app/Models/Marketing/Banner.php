<?php

namespace App\Models\Marketing;

use App\Models\BaseModel;
use App\Models\User;
use App\Traits\AuditableTrait;
use Carbon\Carbon;
use Database\Factories\BannerFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Banner extends BaseModel
{
    use AuditableTrait, HasFactory, SoftDeletes;

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return BannerFactory::new();
    }

    protected $fillable = [
        'title',
        'description',
        'image_url',
        'mobile_image_url',
        'link_url',
        'position',
        'type',
        'status',
        'priority',
        'starts_at',
        'ends_at',
        'target_audience',
        'display_rules',
        'click_count',
        'impression_count',
        'created_by',
        'button_text',
        'button_color',
        'text_color',
        'background_color',
        'animation_type',
    ];

    protected $casts = [
        'priority' => 'integer',
        'starts_at' => 'timestamp',
        'ends_at' => 'timestamp',
        'target_audience' => 'array',
        'display_rules' => 'array',
        'click_count' => 'integer',
        'impression_count' => 'integer',
    ];

    protected $translatable = [
        'title',
        'description',
        'button_text',
        'link_text',
    ];

    protected array $customMediaCollections = [
        'banners' => [
            'mimes' => ['image/*'],
            'single' => true,
        ],
        'banners-mobile' => [
            'mimes' => ['image/*'],
            'single' => true,
        ],
    ];

    // Position constants
    const POSITION_HEADER = 'header';

    const POSITION_FOOTER = 'footer';

    const POSITION_SIDEBAR = 'sidebar';

    const POSITION_HERO = 'hero';

    const POSITION_BETWEEN_LISTINGS = 'between_listings';

    const POSITION_MODAL = 'modal';

    const POSITION_STICKY = 'sticky';

    // Type constants
    const TYPE_PROMOTIONAL = 'promotional';

    const TYPE_INFORMATIONAL = 'informational';

    const TYPE_EVENT = 'event';

    const TYPE_SEASONAL = 'seasonal';

    const TYPE_BRAND = 'brand';

    const TYPE_PARTNER = 'partner';

    // Status constants
    const STATUS_DRAFT = 'draft';

    const STATUS_ACTIVE = 'active';

    const STATUS_PAUSED = 'paused';

    const STATUS_EXPIRED = 'expired';

    // Animation type constants
    const ANIMATION_NONE = 'none';

    const ANIMATION_FADE = 'fade';

    const ANIMATION_SLIDE = 'slide';

    const ANIMATION_BOUNCE = 'bounce';

    const ANIMATION_ZOOM = 'zoom';

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get positions with labels
     */
    public static function getPositions(): array
    {
        return [
            self::POSITION_HEADER => 'Header',
            self::POSITION_FOOTER => 'Footer',
            self::POSITION_SIDEBAR => 'Sidebar',
            self::POSITION_HERO => 'Hero Section',
            self::POSITION_BETWEEN_LISTINGS => 'Between Listings',
            self::POSITION_MODAL => 'Modal/Popup',
            self::POSITION_STICKY => 'Sticky/Fixed',
        ];
    }

    /**
     * Get types with labels
     */
    public static function getTypes(): array
    {
        return [
            self::TYPE_PROMOTIONAL => 'Promotional',
            self::TYPE_INFORMATIONAL => 'Informational',
            self::TYPE_EVENT => 'Event',
            self::TYPE_SEASONAL => 'Seasonal',
            self::TYPE_BRAND => 'Brand',
            self::TYPE_PARTNER => 'Partner',
        ];
    }

    /**
     * Get statuses with labels
     */
    public static function getStatuses(): array
    {
        return [
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_PAUSED => 'Paused',
            self::STATUS_EXPIRED => 'Expired',
        ];
    }

    /**
     * Get animation types with labels
     */
    public static function getAnimationTypes(): array
    {
        return [
            self::ANIMATION_NONE => 'None',
            self::ANIMATION_FADE => 'Fade In',
            self::ANIMATION_SLIDE => 'Slide In',
            self::ANIMATION_BOUNCE => 'Bounce',
            self::ANIMATION_ZOOM => 'Zoom In',
        ];
    }

    /**
     * Get position label
     */
    public function getPositionLabelAttribute(): string
    {
        return static::getPositions()[$this->position] ?? $this->position;
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
     * Check if banner is currently active
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE
            && $this->is_active
            && (! $this->starts_at || $this->starts_at->isPast())
            && (! $this->ends_at || $this->ends_at->isFuture());
    }

    /**
     * Check if banner is scheduled
     */
    public function isScheduled(): bool
    {
        return $this->status === self::STATUS_ACTIVE
            && $this->starts_at
            && $this->starts_at->isFuture();
    }

    /**
     * Check if banner is expired
     */
    public function isExpired(): bool
    {
        return $this->ends_at && $this->ends_at->isPast();
    }

    /**
     * Get click-through rate
     */
    public function getClickThroughRateAttribute(): float
    {
        if ($this->impression_count === 0) {
            return 0;
        }

        return round(($this->click_count / $this->impression_count) * 100, 2);
    }

    /**
     * Get days until expiry
     */
    public function getDaysUntilExpiryAttribute(): ?int
    {
        if (! $this->ends_at) {
            return null;
        }

        return $this->ends_at->diffInDays(now(), false);
    }

    /**
     * Get appropriate image based on device
     */
    public function getImageForDevice(string $device = 'desktop'): string
    {
        if ($device === 'mobile' && $this->mobile_image_url) {
            return $this->mobile_image_url;
        }

        return $this->image_url;
    }

    /**
     * Check if banner should be displayed to user
     */
    public function shouldDisplayToUser(?int $userId = null, array $userAttributes = []): bool
    {
        if (! $this->isActive()) {
            return false;
        }

        // Check target audience rules
        if (! empty($this->target_audience)) {
            return $this->matchesTargetAudience($userId, $userAttributes);
        }

        return true;
    }

    /**
     * Check if user matches target audience
     */
    protected function matchesTargetAudience(?int $userId, array $userAttributes): bool
    {
        $rules = $this->target_audience;

        // Check user authentication status
        if (isset($rules['user_type'])) {
            $isLoggedIn = ! is_null($userId);

            if ($rules['user_type'] === 'logged_in' && ! $isLoggedIn) {
                return false;
            }

            if ($rules['user_type'] === 'guest' && $isLoggedIn) {
                return false;
            }
        }

        // Check user role
        if (isset($rules['user_role']) && isset($userAttributes['role'])) {
            if (! in_array($userAttributes['role'], (array) $rules['user_role'])) {
                return false;
            }
        }

        // Check location
        if (isset($rules['location']) && isset($userAttributes['location'])) {
            if (! in_array($userAttributes['location'], (array) $rules['location'])) {
                return false;
            }
        }

        return true;
    }

    /**
     * Record banner impression
     */
    public function recordImpression(): void
    {
        $this->increment('impression_count');
    }

    /**
     * Record banner click
     */
    public function recordClick(): void
    {
        $this->increment('click_count');
    }

    /**
     * Activate banner
     */
    public function activate(): bool
    {
        return $this->update(['status' => self::STATUS_ACTIVE]);
    }

    /**
     * Pause banner
     */
    public function pause(): bool
    {
        return $this->update(['status' => self::STATUS_PAUSED]);
    }

    /**
     * Mark as expired
     */
    public function markExpired(): bool
    {
        return $this->update(['status' => self::STATUS_EXPIRED]);
    }

    /**
     * Schedule banner
     */
    public function schedule(Carbon $startDate, ?Carbon $endDate = null): bool
    {
        return $this->update([
            'status' => self::STATUS_ACTIVE,
            'starts_at' => $startDate,
            'ends_at' => $endDate,
        ]);
    }

    /**
     * Duplicate banner
     */
    public function duplicate(string $suffix = ' - Copy'): static
    {
        $attributes = $this->toArray();
        unset($attributes['id'], $attributes['created_at'], $attributes['updated_at']);

        $attributes['title'] .= $suffix;
        $attributes['status'] = self::STATUS_DRAFT;
        $attributes['click_count'] = 0;
        $attributes['impression_count'] = 0;

        return static::create($attributes);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE)
            ->where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            })
            ->where(function ($query) {
                $query->whereNull('ends_at')->orWhere('ends_at', '>', now());
            });
    }

    public function scopeScheduled($query)
    {
        return $query->where('status', self::STATUS_ACTIVE)
            ->where('starts_at', '>', now());
    }

    public function scopeExpired($query)
    {
        return $query->where(function ($query) {
            $query->where('status', self::STATUS_EXPIRED)
                ->orWhere('ends_at', '<', now());
        });
    }

    public function scopeByPosition($query, string $position)
    {
        return $query->where('position', $position);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeHighPriority($query)
    {
        return $query->orderByDesc('priority');
    }

    public function scopeExpiringWithin($query, int $days)
    {
        return $query->whereNotNull('ends_at')
            ->whereBetween('ends_at', [now(), now()->addDays($days)]);
    }

    public function scopeHighPerformance($query, float $minCtr = 5.0)
    {
        return $query->whereRaw('
            CASE 
                WHEN impression_count > 0 
                THEN (click_count / impression_count) * 100 
                ELSE 0 
            END >= ?
        ', [$minCtr]);
    }

    public function scopePopular($query, int $minImpressions = 1000)
    {
        return $query->where('impression_count', '>=', $minImpressions)
            ->orderByDesc('impression_count');
    }

    public function scopeForTargetAudience($query, string $audienceType)
    {
        return $query->whereJsonContains('target_audience->user_type', $audienceType);
    }

    public function scopeNeedsAttention($query)
    {
        return $query->where(function ($query) {
            $query->where('impression_count', '>', 1000)
                ->where('click_count', 0)
                ->orWhereRaw('ends_at < DATE_ADD(NOW(), INTERVAL 7 DAY)');
        });
    }

    /**
     * Register media conversions for banner images
     */
    public function registerMediaConversions(?\Spatie\MediaLibrary\MediaCollections\Models\Media $media = null): void
    {
        parent::registerMediaConversions($media);

        $this->addMediaConversion('banner_desktop')
            ->width(1920)
            ->height(600)
            ->sharpen(5)
            ->performOnCollections($this->imagesCollection);

        $this->addMediaConversion('banner_mobile')
            ->width(768)
            ->height(400)
            ->sharpen(5)
            ->performOnCollections($this->imagesCollection);

        $this->addMediaConversion('banner_tablet')
            ->width(1024)
            ->height(500)
            ->sharpen(5)
            ->performOnCollections($this->imagesCollection);
    }
}
