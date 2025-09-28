<?php

namespace App\Models\Vehicle;

use App\Models\BaseModel;
use App\Models\Vehicle\Vehicle;
use Database\Factories\ColorFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\AuditableTrait;

class Color extends BaseModel
{
    use HasFactory, SoftDeletes, AuditableTrait;

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return ColorFactory::new();
    }

    protected $fillable = [
        'name',
        'hex_code',
        'rgb_value',
        'type',
        'is_metallic',
        'is_popular',
        'sort_order',
    ];

    protected $casts = [
        'is_metallic' => 'boolean',
        'is_popular' => 'boolean',
        'sort_order' => 'integer',
        'rgb_value' => 'array',
    ];

    /**
     * Get vehicles with this color (exterior or interior)
     */
    public function vehicles()
    {
        return Vehicle::where('exterior_color', $this->name)
            ->orWhere('interior_color', $this->name);
    }

    /**
     * Get active vehicles with this color
     */
    public function activeVehicles()
    {
        return $this->vehicles()->where('is_active', true);
    }
}