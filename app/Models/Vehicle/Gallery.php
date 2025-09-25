<?php

namespace App\Models\Vehicle;

use App\Models\BaseModel;
use Database\Factories\GalleryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\AuditableTrait;

class Gallery extends BaseModel
{
    use HasFactory, SoftDeletes, AuditableTrait;

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return GalleryFactory::new();
    }

    protected $fillable = [
        'name',
        'description',
        'vehicle_id',
        'type',
        'is_featured',
        'sort_order',
        'metadata',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'sort_order' => 'integer',
        'metadata' => 'array',
    ];
}