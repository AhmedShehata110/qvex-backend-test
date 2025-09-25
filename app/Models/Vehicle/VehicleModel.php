<?php

namespace App\Models\Vehicle;

use App\Models\BaseModel;
use Database\Factories\VehicleModelFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class VehicleModel extends BaseModel
{
    use HasFactory;

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return VehicleModelFactory::new();
    }

    protected $fillable = [
        'make_id',
        'name',
        'name_ar',
        'slug',
        'year_start',
        'year_end',
        'body_type',
        'sort_order',
    ];

    protected $casts = [
        'year_start' => 'integer',
        'year_end' => 'integer',
        'sort_order' => 'integer',
    ];

    protected $translatable = ['name'];

    // Body type constants
    const BODY_TYPE_SEDAN = 'sedan';

    const BODY_TYPE_SUV = 'suv';

    const BODY_TYPE_HATCHBACK = 'hatchback';

    const BODY_TYPE_COUPE = 'coupe';

    const BODY_TYPE_CONVERTIBLE = 'convertible';

    const BODY_TYPE_WAGON = 'wagon';

    const BODY_TYPE_PICKUP = 'pickup';

    const BODY_TYPE_VAN = 'van';

    const BODY_TYPE_TRUCK = 'truck';

    const BODY_TYPE_MOTORCYCLE = 'motorcycle';

    const BODY_TYPE_OTHER = 'other';

    public function make(): BelongsTo
    {
        return $this->belongsTo(VehicleMake::class);
    }

    public function trims(): HasMany
    {
        return $this->hasMany(VehicleTrim::class, 'model_id');
    }

    public function vehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class, 'model_id');
    }

    /**
     * Get active trims
     */
    public function activeTrims(): HasMany
    {
        return $this->trims()->where('is_active', true);
    }

    /**
     * Get active vehicles
     */
    public function activeVehicles(): HasMany
    {
        return $this->vehicles()->where('is_active', true);
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
     * Get body types with labels
     */
    public static function getBodyTypes(): array
    {
        return [
            self::BODY_TYPE_SEDAN => 'Sedan',
            self::BODY_TYPE_SUV => 'SUV',
            self::BODY_TYPE_HATCHBACK => 'Hatchback',
            self::BODY_TYPE_COUPE => 'Coupe',
            self::BODY_TYPE_CONVERTIBLE => 'Convertible',
            self::BODY_TYPE_WAGON => 'Wagon',
            self::BODY_TYPE_PICKUP => 'Pickup',
            self::BODY_TYPE_VAN => 'Van',
            self::BODY_TYPE_TRUCK => 'Truck',
            self::BODY_TYPE_MOTORCYCLE => 'Motorcycle',
            self::BODY_TYPE_OTHER => 'Other',
        ];
    }

    /**
     * Get body type label
     */
    public function getBodyTypeLabelAttribute(): string
    {
        return static::getBodyTypes()[$this->body_type] ?? $this->body_type;
    }

    /**
     * Get full name (Make + Model)
     */
    public function getFullNameAttribute(): string
    {
        return $this->make->name.' '.$this->name;
    }

    /**
     * Get year range
     */
    public function getYearRangeAttribute(): string
    {
        if ($this->year_end) {
            return $this->year_start.' - '.$this->year_end;
        }

        return $this->year_start.' - Present';
    }

    /**
     * Check if model is current (no end year or end year is in future)
     */
    public function isCurrent(): bool
    {
        return ! $this->year_end || $this->year_end >= date('Y');
    }

    /**
     * Get vehicle count
     */
    public function getVehicleCountAttribute(): int
    {
        return $this->vehicles()->count();
    }

    /**
     * Get active vehicle count
     */
    public function getActiveVehicleCountAttribute(): int
    {
        return $this->activeVehicles()->count();
    }

    /**
     * Scope to order by make and model name
     */
    public function scopeOrdered($query)
    {
        return $query->join('vehicle_makes', 'vehicle_models.make_id', '=', 'vehicle_makes.id')
            ->orderBy('vehicle_makes.name')
            ->orderBy('vehicle_models.sort_order')
            ->orderBy('vehicle_models.name')
            ->select('vehicle_models.*');
    }

    /**
     * Scope to get models by body type
     */
    public function scopeByBodyType($query, string $bodyType)
    {
        return $query->where('body_type', $bodyType);
    }

    /**
     * Scope to get current models
     */
    public function scopeCurrent($query)
    {
        return $query->where(function ($query) {
            $query->whereNull('year_end')
                ->orWhere('year_end', '>=', date('Y'));
        });
    }

    /**
     * Scope to get models by year range
     */
    public function scopeByYearRange($query, int $startYear, ?int $endYear = null)
    {
        return $query->where('year_start', '<=', $endYear ?? date('Y'))
            ->where(function ($query) use ($startYear) {
                $query->whereNull('year_end')
                    ->orWhere('year_end', '>=', $startYear);
            });
    }
}
