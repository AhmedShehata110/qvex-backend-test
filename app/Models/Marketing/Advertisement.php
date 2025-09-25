<?php

namespace App\Models\Marketing;

use App\Models\BaseModel;
use App\Traits\AuditableTrait;
use Database\Factories\AdvertisementFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Advertisement extends BaseModel
{
    use AuditableTrait, HasFactory, SoftDeletes;

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return AdvertisementFactory::new();
    }

    protected $fillable = [
        'title',
        'description',
        'type',
        'position',
        'target_url',
        'image_url',
        'start_date',
        'end_date',
        'is_active',
        'click_count',
        'view_count',
        'budget',
        'spent',
        'target_audience',
        'priority',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_active' => 'boolean',
        'click_count' => 'integer',
        'view_count' => 'integer',
        'budget' => 'decimal:2',
        'spent' => 'decimal:2',
        'target_audience' => 'array',
        'priority' => 'integer',
    ];
}
