<?php

namespace App\Models\Content;

use App\Models\BaseModel;
use Database\Factories\StaticPageFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\AuditableTrait;

class StaticPage extends BaseModel
{
    use HasFactory, SoftDeletes, AuditableTrait;

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return StaticPageFactory::new();
    }

    protected $fillable = [
        'title',
        'slug',
        'content',
        'excerpt',
        'meta_title',
        'meta_description',
        'is_published',
        'published_at',
        'template',
        'order',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'published_at' => 'datetime',
        'order' => 'integer',
    ];
}