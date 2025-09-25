<?php

namespace App\Models\Communication;

use App\Models\BaseModel;
use App\Traits\AuditableTrait;
use Database\Factories\NewsletterSubscriberFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class NewsletterSubscriber extends BaseModel
{
    use AuditableTrait, HasFactory, SoftDeletes;

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return NewsletterSubscriberFactory::new();
    }

    protected $fillable = [
        'email',
        'name',
        'is_subscribed',
        'subscribed_at',
        'unsubscribed_at',
        'subscription_source',
        'preferences',
        'verification_token',
        'is_verified',
        'verified_at',
    ];

    protected $casts = [
        'is_subscribed' => 'boolean',
        'is_verified' => 'boolean',
        'subscribed_at' => 'datetime',
        'unsubscribed_at' => 'datetime',
        'verified_at' => 'datetime',
        'preferences' => 'array',
    ];
}
