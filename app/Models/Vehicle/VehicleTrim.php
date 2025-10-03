<?php

namespace App\Models\Vehicle;

use App\Models\BaseModel;
use Database\Factories\VehicleTrimFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VehicleTrim extends BaseModel
{
    use HasFactory;

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return VehicleTrimFactory::new();
    }

    protected $fillable = [
        'model_id',
        'name',
        'year',
        'engine_size',
        'fuel_type',
        'transmission',
        'drivetrain',
        'horsepower',
        'fuel_consumption_city',
        'fuel_consumption_highway',
        'seating_capacity',
    ];

    protected $casts = [
        'year' => 'integer',
        'horsepower' => 'integer',
        'fuel_consumption_city' => 'decimal:2',
        'fuel_consumption_highway' => 'decimal:2',
        'seating_capacity' => 'integer',
    ];

    protected $translatable = ['name'];

    // Fuel type constants
    const FUEL_TYPE_GASOLINE = 'gasoline';

    const FUEL_TYPE_DIESEL = 'diesel';

    const FUEL_TYPE_HYBRID = 'hybrid';

    const FUEL_TYPE_ELECTRIC = 'electric';

    const FUEL_TYPE_CNG = 'cng';

    const FUEL_TYPE_LPG = 'lpg';

    // Transmission constants
    const TRANSMISSION_MANUAL = 'manual';

    const TRANSMISSION_AUTOMATIC = 'automatic';

    const TRANSMISSION_CVT = 'cvt';

    const TRANSMISSION_DUAL_CLUTCH = 'dual_clutch';

    public function model(): BelongsTo
    {
        return $this->belongsTo(VehicleModel::class);
    }

    public function vehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class, 'trim_id');
    }

    /**
     * Get active vehicles
     */
    public function activeVehicles(): HasMany
    {
        return $this->vehicles()->where('is_active', true);
    }

    /**
     * Get fuel types with labels
     */
    public static function getFuelTypes(): array
    {
        return [
            self::FUEL_TYPE_GASOLINE => 'Gasoline',
            self::FUEL_TYPE_DIESEL => 'Diesel',
            self::FUEL_TYPE_HYBRID => 'Hybrid',
            self::FUEL_TYPE_ELECTRIC => 'Electric',
            self::FUEL_TYPE_CNG => 'CNG',
            self::FUEL_TYPE_LPG => 'LPG',
        ];
    }

    /**
     * Get transmission types with labels
     */
    public static function getTransmissionTypes(): array
    {
        return [
            self::TRANSMISSION_MANUAL => 'Manual',
            self::TRANSMISSION_AUTOMATIC => 'Automatic',
            self::TRANSMISSION_CVT => 'CVT',
            self::TRANSMISSION_DUAL_CLUTCH => 'Dual Clutch',
        ];
    }

    /**
     * Get fuel type label
     */
    public function getFuelTypeLabelAttribute(): string
    {
        return static::getFuelTypes()[$this->fuel_type] ?? $this->fuel_type;
    }

    /**
     * Get transmission label
     */
    public function getTransmissionLabelAttribute(): string
    {
        return static::getTransmissionTypes()[$this->transmission] ?? $this->transmission;
    }

    /**
     * Get full name (Make + Model + Trim)
     */
    public function getFullNameAttribute(): string
    {
        return $this->model->make->name.' '.$this->model->name.' '.$this->name;
    }

    /**
     * Get combined fuel consumption (average of city and highway)
     */
    public function getCombinedFuelConsumptionAttribute(): ?float
    {
        if ($this->fuel_consumption_city && $this->fuel_consumption_highway) {
            return ($this->fuel_consumption_city + $this->fuel_consumption_highway) / 2;
        }

        return null;
    }

    /**
     * Check if it's an electric vehicle
     */
    public function isElectric(): bool
    {
        return $this->fuel_type === self::FUEL_TYPE_ELECTRIC;
    }

    /**
     * Check if it's a hybrid vehicle
     */
    public function isHybrid(): bool
    {
        return $this->fuel_type === self::FUEL_TYPE_HYBRID;
    }

    /**
     * Check if it's an eco-friendly vehicle (electric or hybrid)
     */
    public function isEcoFriendly(): bool
    {
        return in_array($this->fuel_type, [self::FUEL_TYPE_ELECTRIC, self::FUEL_TYPE_HYBRID]);
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
     * Scope to get trims by fuel type
     */
    public function scopeByFuelType($query, string $fuelType)
    {
        return $query->where('fuel_type', $fuelType);
    }

    /**
     * Scope to get trims by transmission
     */
    public function scopeByTransmission($query, string $transmission)
    {
        return $query->where('transmission', $transmission);
    }

    /**
     * Scope to get electric vehicles
     */
    public function scopeElectric($query)
    {
        return $query->where('fuel_type', self::FUEL_TYPE_ELECTRIC);
    }

    /**
     * Scope to get hybrid vehicles
     */
    public function scopeHybrid($query)
    {
        return $query->where('fuel_type', self::FUEL_TYPE_HYBRID);
    }

    /**
     * Scope to get eco-friendly vehicles
     */
    public function scopeEcoFriendly($query)
    {
        return $query->whereIn('fuel_type', [self::FUEL_TYPE_ELECTRIC, self::FUEL_TYPE_HYBRID]);
    }

    /**
     * Scope to get trims by year
     */
    public function scopeByYear($query, int $year)
    {
        return $query->where('year', $year);
    }

    /**
     * Scope to get trims with horsepower range
     */
    public function scopeByHorsepowerRange($query, int $min, int $max)
    {
        return $query->whereBetween('horsepower', [$min, $max]);
    }
}
