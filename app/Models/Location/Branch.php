<?php

namespace App\Models\Location;

use App\Models\BaseModel;
use App\Models\Vehicle\Vehicle;
use App\Models\Vendor\Vendor;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class Branch extends BaseModel
{
    use HasTranslations;

    protected $fillable = [
        'name',
        'description',
        'vendor_id',
        'country_id',
        'city_id',
        'address',
        'phone',
        'email',
        'manager_name',
        'latitude',
        'longitude',
        'working_hours',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'working_hours' => 'array',
    ];

    protected $translatable = ['name', 'description', 'address'];

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function vehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByVendor($query, $vendorId)
    {
        return $query->where('vendor_id', $vendorId);
    }

    public function scopeByCity($query, $cityId)
    {
        return $query->where('city_id', $cityId);
    }

    public function scopeByCountry($query, $countryId)
    {
        return $query->where('country_id', $countryId);
    }
}
