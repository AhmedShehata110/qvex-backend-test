<?php

namespace App\Models\Content;

use App\Models\BaseModel;
use Database\Factories\TestimonialFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\AuditableTrait;

class Testimonial extends BaseModel
{
    use HasFactory, SoftDeletes, AuditableTrait;

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return TestimonialFactory::new();
    }

    protected $fillable = [
        'name',
        'email',
        'company',
        'position',
        'content',
        'rating',
        'is_featured',
        'is_approved',
        'approved_at',
        'approved_by',
        'metadata',
    ];

    protected $casts = [
        'rating' => 'integer',
        'is_featured' => 'boolean',
        'is_approved' => 'boolean',
        'approved_at' => 'datetime',
        'metadata' => 'array',
    ];
}