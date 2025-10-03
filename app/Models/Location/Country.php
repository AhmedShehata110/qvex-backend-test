<?php

namespace App\Models\Location;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class Country extends BaseModel
{
    use HasTranslations;

    protected $fillable = [
        'name',
        'code',
        'currency_code',
        'phone_code',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected $translatable = ['name'];

    /**
     * Media collections configuration
     */
    protected array $customMediaCollections = [
        'flags' => [
            'mimes' => ['image/*'],
        ],
    ];

    public function cities(): HasMany
    {
        return $this->hasMany(City::class);
    }

    public function branches(): HasMany
    {
        return $this->hasMany(Branch::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
