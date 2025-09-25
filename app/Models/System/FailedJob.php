<?php

namespace App\Models\System;

use App\Models\BaseModel;
use App\Traits\AuditableTrait;
use Database\Factories\FailedJobFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class FailedJob extends BaseModel
{
    use AuditableTrait, HasFactory, SoftDeletes;

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return FailedJobFactory::new();
    }

    protected $fillable = [
        'uuid',
        'connection',
        'queue',
        'payload',
        'exception',
        'failed_at',
        'retried_at',
        'retry_count',
        'max_retries',
    ];

    protected $casts = [
        'payload' => 'array',
        'failed_at' => 'datetime',
        'retried_at' => 'datetime',
        'retry_count' => 'integer',
        'max_retries' => 'integer',
    ];
}
