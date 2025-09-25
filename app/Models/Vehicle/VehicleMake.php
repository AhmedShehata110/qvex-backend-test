<?php

namespace App\Models\Vehicle;

use App\Models\BaseModel;
use Database\Factories\VehicleMakeFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class VehicleMake extends BaseModel
{
    use HasFactory;

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return VehicleMakeFactory::new();
    }

    protected $fillable = [
        'name',
        'name_ar',
        'slug',
        'logo',
        'country_origin',
        'sort_order',
    ];

    protected $translatable = ['name'];

    public function vehicleModels(): HasMany
    {
        return $this->hasMany(VehicleModel::class, 'make_id');
    }

    public function vehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class, 'make_id');
    }

    /**
     * Get active vehicle models
     */
    public function activeVehicleModels(): HasMany
    {
        return $this->vehicleModels()->where('is_active', true);
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
     * Get logo URL attribute
     */
    public function getLogoUrlAttribute(): ?string
    {
        return $this->logo ? asset('storage/'.$this->logo) : null;
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
     * Scope to order by sort order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Scope to get makes by country
     */
    public function scopeFromCountry($query, string $country)
    {
        return $query->where('country_origin', $country);
    }

    /**
     * Get popular makes (with most vehicles)
     */
    public function scopePopular($query, int $limit = 10)
    {
        return $query->withCount(['vehicles' => function ($query) {
            $query->where('is_active', true);
        }])->orderBy('vehicles_count', 'desc')->limit($limit);
    }
}
