<?php

namespace App\Models\Content;

use App\Models\BaseModel;
use Database\Factories\NewsletterFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\AuditableTrait;

class Newsletter extends BaseModel
{
    use HasFactory, SoftDeletes, AuditableTrait;

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return NewsletterFactory::new();
    }

    protected $fillable = [
        'title',
        'slug',
        'subject',
        'content',
        'excerpt',
        'status',
        'scheduled_at',
        'sent_at',
        'recipient_count',
        'open_rate',
        'click_rate',
        'template_id',
        'tags',
        'metadata',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
        'open_rate' => 'decimal:2',
        'click_rate' => 'decimal:2',
        'recipient_count' => 'integer',
        'tags' => 'array',
        'metadata' => 'array',
    ];
}