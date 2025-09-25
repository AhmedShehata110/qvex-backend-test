<?php

namespace App\Models\Vehicle;

use App\Models\BaseModel;
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
}