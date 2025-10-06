<?php

namespace App\Models\Vehicle;

use App\Models\BaseModel;
use Database\Factories\VehicleFeatureFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class VehicleFeature extends BaseModel
{
    use HasFactory;

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return VehicleFeatureFactory::new();
    }

    protected $fillable = [
        'name',
        'slug',
        'category',
        'icon',
        'is_premium',
        'sort_order',
    ];

    protected $casts = [
        'is_premium' => 'boolean',
        'sort_order' => 'integer',
    ];

    protected $translatable = ['name'];

    /**
     * Media collections configuration
     */
    protected array $customMediaCollections = [
        'icons' => [
            'mimes' => ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml'],
            'single' => true,
            'fallbackUrl' => '/images/default-image.png',
        ],
    ];

    // Category constants
    const CATEGORY_SAFETY = 'safety';

    const CATEGORY_TECHNOLOGY = 'technology';

    const CATEGORY_COMFORT = 'comfort';

    const CATEGORY_PERFORMANCE = 'performance';

    const CATEGORY_EXTERIOR = 'exterior';

    const CATEGORY_INTERIOR = 'interior';

    const CATEGORY_AUDIO = 'audio';

    const CATEGORY_OTHER = 'other';

    public function vehicles(): BelongsToMany
    {
        return $this->belongsToMany(Vehicle::class, 'vehicle_feature_pivot', 'feature_id', 'vehicle_id')
            ->withTimestamps();
    }

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->name);
            }
        });

        static::updating(function ($model) {
            if ($model->isDirty('name') && empty($model->slug)) {
                $model->slug = Str::slug($model->name);
            }
        });
    }

    /**
     * Get feature categories with labels
     */
    public static function getCategories(): array
    {
        return [
            self::CATEGORY_SAFETY => 'Safety',
            self::CATEGORY_TECHNOLOGY => 'Technology',
            self::CATEGORY_COMFORT => 'Comfort',
            self::CATEGORY_PERFORMANCE => 'Performance',
            self::CATEGORY_EXTERIOR => 'Exterior',
            self::CATEGORY_INTERIOR => 'Interior',
            self::CATEGORY_AUDIO => 'Audio',
            self::CATEGORY_OTHER => 'Other',
        ];
    }

    /**
     * Get category label
     */
    public function getCategoryLabelAttribute(): string
    {
        return static::getCategories()[$this->category] ?? $this->category;
    }

    /**
     * Get icon URL or return default
     */
    public function getIconUrlAttribute(): ?string
    {
        if ($this->icon) {
            // Check if it's a URL or a file path
            if (filter_var($this->icon, FILTER_VALIDATE_URL)) {
                return $this->icon;
            }

            return asset('storage/'.$this->icon);
        }

        return null;
    }

    /**
     * Get vehicle count that have this feature
     */
    public function getVehicleCountAttribute(): int
    {
        return $this->vehicles()->count();
    }

    /**
     * Get active vehicle count that have this feature
     */
    public function getActiveVehicleCountAttribute(): int
    {
        return $this->vehicles()->where('vehicles.is_active', true)->count();
    }

    /**
     * Check if this is a premium feature
     */
    public function isPremiumFeature(): bool
    {
        return $this->is_premium;
    }

    /**
     * Scope to get features by category
     */
    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope to get premium features
     */
    public function scopePremium($query)
    {
        return $query->where('is_premium', true);
    }

    /**
     * Scope to get standard features
     */
    public function scopeStandard($query)
    {
        return $query->where('is_premium', false);
    }

    /**
     * Scope to order by category and sort order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('category')->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Scope to get safety features
     */
    public function scopeSafety($query)
    {
        return $query->where('category', self::CATEGORY_SAFETY);
    }

    /**
     * Scope to get technology features
     */
    public function scopeTechnology($query)
    {
        return $query->where('category', self::CATEGORY_TECHNOLOGY);
    }

    /**
     * Scope to get comfort features
     */
    public function scopeComfort($query)
    {
        return $query->where('category', self::CATEGORY_COMFORT);
    }

    /**
     * Scope to get performance features
     */
    public function scopePerformance($query)
    {
        return $query->where('category', self::CATEGORY_PERFORMANCE);
    }

    /**
     * Get features grouped by category
     */
    // public static function getGroupedByCategory()
    // {
    //     return static::active()
    //         ->ordered()
    //         ->get()
    //         ->groupBy('category');
    // }

    /**
     * Get popular features (most used)
     */
    public function scopePopular($query, int $limit = 10)
    {
        return $query->withCount(['vehicles' => function ($query) {
            $query->where('is_active', true);
        }])->orderBy('vehicles_count', 'desc')->limit($limit);
    }
}
