<?php

namespace App\Models\Location;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class Area extends BaseModel
{
    use HasTranslations;

    protected $fillable = [
        'name',
        'city_id',
        'description',
        'postal_code',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected $translatable = ['name', 'description'];

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(\App\Models\Customer\UserAddress::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCity($query, $cityId)
    {
        return $query->where('city_id', $cityId);
    }
}
