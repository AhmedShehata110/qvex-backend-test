<?php

namespace App\Models\Vehicle;

use App\Models\BaseModel;
use App\Models\Communication\Message;
use App\Models\Communication\Review;
use App\Models\Customer\UserFavorite;
use App\Models\Transaction\Transaction;
use App\Models\User;
use App\Models\Vendor\Vendor;
use App\Models\Vehicle\Brand;
use Database\Factories\VehicleFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Vehicle extends BaseModel
{
    use HasFactory;

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return VehicleFactory::new();
    }

    protected $fillable = [
        'vendor_id',
        'brand_id',
        'model_id',
        'trim_id',
        'vin',
        'year',
        'title',
        'description',
        'condition',
        'availability_type',
        'status',
        'price',
        'original_price',
        'is_negotiable',
        'rental_daily_rate',
        'rental_weekly_rate',
        'rental_monthly_rate',
        'security_deposit',
        'mileage',
        'mileage_unit',
        'exterior_color',
        'interior_color',
        'doors',
        'cylinders',
        'license_plate',
        'additional_specs',
        'city',
        'state',
        'country',
        'latitude',
        'longitude',
        'has_warranty',
        'warranty_details',
        'warranty_expires_at',
        'last_service_date',
        'service_interval_km',
        'service_history',
        'slug',
        'seo_keywords',
        'is_featured',
        'is_urgent',
        'featured_until',
        'view_count',
        'inquiry_count',
        'favorite_count',
        'approved_at',
        'approved_by',
        'rejection_reason',
    ];

    protected $casts = [
        'year' => 'integer',
        'price' => 'decimal:2',
        'original_price' => 'decimal:2',
        'is_negotiable' => 'boolean',
        'rental_daily_rate' => 'decimal:2',
        'rental_weekly_rate' => 'decimal:2',
        'rental_monthly_rate' => 'decimal:2',
        'security_deposit' => 'decimal:2',
        'mileage' => 'integer',
        'doors' => 'integer',
        'cylinders' => 'integer',
        'additional_specs' => 'array',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'has_warranty' => 'boolean',
        'warranty_expires_at' => 'date',
        'last_service_date' => 'date',
        'service_interval_km' => 'integer',
        'seo_keywords' => 'array',
        'is_featured' => 'boolean',
        'is_urgent' => 'boolean',
        'featured_until' => 'timestamp',
        'view_count' => 'integer',
        'inquiry_count' => 'integer',
        'favorite_count' => 'integer',
        'approved_at' => 'timestamp',
    ];

    protected $translatable = ['title', 'description'];

    /**
     * Media collections configuration
     */
    protected array $customMediaCollections = [
        'exterior' => [
            'mimes' => ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml'],
            'fallbackUrl' => '/images/default-image.png',
        ],
        'interior' => [
            'mimes' => ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml'],
            'fallbackUrl' => '/images/default-image.png',
        ],
        'engine' => [
            'mimes' => ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml'],
            'fallbackUrl' => '/images/default-image.png',
        ],
        'documents' => [
            'mimes' => ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'application/pdf'],
            'fallbackUrl' => '/images/default-image.png',
        ],
        'featured-images' => [
            'mimes' => ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml'],
            'fallbackUrl' => '/images/default-image.png',
        ],
    ];

    // Status constants
    const STATUS_DRAFT = 'draft';

    const STATUS_ACTIVE = 'active';

    const STATUS_SOLD = 'sold';

    const STATUS_RENTED = 'rented';

    const STATUS_INACTIVE = 'inactive';

    const STATUS_PENDING_APPROVAL = 'pending_approval';

    // Condition constants
    const CONDITION_NEW = 'new';

    const CONDITION_USED = 'used';

    const CONDITION_CERTIFIED_PREOWNED = 'certified_preowned';

    const CONDITION_SALVAGE = 'salvage';

    // Availability type constants
    const AVAILABILITY_SALE = 'sale';

    const AVAILABILITY_RENT = 'rent';

    const AVAILABILITY_BOTH = 'both';

    // Relationships
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    public function model(): BelongsTo
    {
        return $this->belongsTo(VehicleModel::class);
    }

    public function trim(): BelongsTo
    {
        return $this->belongsTo(VehicleTrim::class);
    }

    public function features(): BelongsToMany
    {
        return $this->belongsToMany(VehicleFeature::class, 'vehicle_feature_pivot', 'vehicle_id', 'feature_id')
            ->withTimestamps();
    }

    public function documents(): HasMany
    {
        return $this->hasMany(VehicleDocument::class);
    }

    public function inquiries(): HasMany
    {
        return $this->hasMany(VehicleInquiry::class);
    }

    public function views(): HasMany
    {
        return $this->hasMany(VehicleView::class);
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(UserFavorite::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->title);
            }
        });

        static::updating(function ($model) {
            if ($model->isDirty('title') && empty($model->slug)) {
                $model->slug = Str::slug($model->title);
            }
        });
    }

    /**
     * Get status options with labels
     */
    public static function getStatuses(): array
    {
        return [
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_SOLD => 'Sold',
            self::STATUS_RENTED => 'Rented',
            self::STATUS_INACTIVE => 'Inactive',
            self::STATUS_PENDING_APPROVAL => 'Pending Approval',
        ];
    }

    /**
     * Get condition options with labels
     */
    public static function getConditions(): array
    {
        return [
            self::CONDITION_NEW => 'New',
            self::CONDITION_USED => 'Used',
            self::CONDITION_CERTIFIED_PREOWNED => 'Certified Pre-owned',
            self::CONDITION_SALVAGE => 'Salvage',
        ];
    }

    /**
     * Get availability type options with labels
     */
    public static function getAvailabilityTypes(): array
    {
        return [
            self::AVAILABILITY_SALE => 'For Sale',
            self::AVAILABILITY_RENT => 'For Rent',
            self::AVAILABILITY_BOTH => 'Sale & Rent',
        ];
    }

    /**
     * Get full name (Make Model Year)
     */
    public function getFullNameAttribute(): string
    {
        $name = $this->brand->name.' '.$this->model->name.' '.$this->year;

        if ($this->trim) {
            $name .= ' '.$this->trim->name;
        }

        return $name;
    }

    /**
     * Check if vehicle is available for sale
     */
    public function isForSale(): bool
    {
        return in_array($this->availability_type, [self::AVAILABILITY_SALE, self::AVAILABILITY_BOTH]);
    }

    /**
     * Check if vehicle is available for rent
     */
    public function isForRent(): bool
    {
        return in_array($this->availability_type, [self::AVAILABILITY_RENT, self::AVAILABILITY_BOTH]);
    }

    /**
     * Check if vehicle is active
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    /**
     * Check if vehicle is sold
     */
    public function isSold(): bool
    {
        return $this->status === self::STATUS_SOLD;
    }

    /**
     * Check if vehicle is rented
     */
    public function isRented(): bool
    {
        return $this->status === self::STATUS_RENTED;
    }

    /**
     * Check if vehicle is pending approval
     */
    public function isPendingApproval(): bool
    {
        return $this->status === self::STATUS_PENDING_APPROVAL;
    }

    /**
     * Check if vehicle is featured
     */
    public function isFeatured(): bool
    {
        return $this->is_featured && (! $this->featured_until || $this->featured_until->isFuture());
    }

    /**
     * Approve the vehicle
     */
    public function approve(User $approver): bool
    {
        return $this->update([
            'status' => self::STATUS_ACTIVE,
            'approved_at' => now(),
            'approved_by' => $approver->id,
            'rejection_reason' => null,
        ]);
    }

    /**
     * Reject the vehicle
     */
    public function reject(string $reason, User $approver): bool
    {
        return $this->update([
            'status' => self::STATUS_INACTIVE,
            'rejection_reason' => $reason,
            'approved_by' => $approver->id,
            'approved_at' => null,
        ]);
    }

    /**
     * Mark as sold
     */
    public function markAsSold(): bool
    {
        return $this->update(['status' => self::STATUS_SOLD]);
    }

    /**
     * Mark as rented
     */
    public function markAsRented(): bool
    {
        return $this->update(['status' => self::STATUS_RENTED]);
    }

    /**
     * Increment view count
     */
    public function incrementViewCount(): bool
    {
        return $this->increment('view_count');
    }

    /**
     * Increment inquiry count
     */
    public function incrementInquiryCount(): bool
    {
        return $this->increment('inquiry_count');
    }

    /**
     * Increment favorite count
     */
    public function incrementFavoriteCount(): bool
    {
        return $this->increment('favorite_count');
    }

    /**
     * Decrement favorite count
     */
    public function decrementFavoriteCount(): bool
    {
        return $this->decrement('favorite_count');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeForSale($query)
    {
        return $query->whereIn('availability_type', [self::AVAILABILITY_SALE, self::AVAILABILITY_BOTH]);
    }

    public function scopeForRent($query)
    {
        return $query->whereIn('availability_type', [self::AVAILABILITY_RENT, self::AVAILABILITY_BOTH]);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true)
            ->where(function ($query) {
                $query->whereNull('featured_until')
                    ->orWhere('featured_until', '>', now());
            });
    }

    public function scopePendingApproval($query)
    {
        return $query->where('status', self::STATUS_PENDING_APPROVAL);
    }

    public function scopeByBrand($query, $brandId)
    {
        return $query->where('brand_id', $brandId);
    }

    public function scopeByModel($query, $modelId)
    {
        return $query->where('model_id', $modelId);
    }

    public function scopeByYear($query, $year)
    {
        return $query->where('year', $year);
    }

    public function scopeByCondition($query, $condition)
    {
        return $query->where('condition', $condition);
    }

    public function scopeByPriceRange($query, $min, $max)
    {
        return $query->whereBetween('price', [$min, $max]);
    }

    public function scopeByLocation($query, $city, $state = null, $country = null)
    {
        $query->where('city', $city);

        if ($state) {
            $query->where('state', $state);
        }

        if ($country) {
            $query->where('country', $country);
        }

        return $query;
    }
}
