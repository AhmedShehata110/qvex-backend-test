<?php

namespace App\Models\Communication;

use App\Models\BaseModel;
use App\Traits\AuditableTrait;
use Database\Factories\ContactUsFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContactUs extends BaseModel
{
    use AuditableTrait, HasFactory, SoftDeletes;

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return ContactUsFactory::new();
    }

    protected $fillable = [
        'name',
        'email',
        'phone',
        'subject',
        'message',
        'category',
        'priority',
        'status',
        'assigned_to',
        'response',
        'responded_at',
        'responded_by',
        'metadata',
    ];

    protected $casts = [
        'responded_at' => 'datetime',
        'metadata' => 'array',
        'priority' => 'integer',
    ];
}
