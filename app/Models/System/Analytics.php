<?php

namespace App\Models\System;

use App\Models\BaseModel;
use Database\Factories\AnalyticsFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\AuditableTrait;

class Analytics extends BaseModel
{
    use HasFactory, SoftDeletes, AuditableTrait;

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return AnalyticsFactory::new();
    }

    protected $fillable = [
        'type',
        'entity_type',
        'entity_id',
        'metric',
        'value',
        'date',
        'metadata',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'date' => 'date',
        'metadata' => 'array',
    ];
}