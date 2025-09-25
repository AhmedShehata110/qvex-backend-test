<?php

namespace App\Models\Communication;

use App\Models\BaseModel;
use App\Traits\AuditableTrait;
use Database\Factories\SupportTicketFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupportTicket extends BaseModel
{
    use AuditableTrait, HasFactory, SoftDeletes;

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return SupportTicketFactory::new();
    }

    protected $fillable = [
        'user_id',
        'subject',
        'description',
        'category',
        'priority',
        'status',
        'assigned_to',
        'last_reply_at',
        'resolved_at',
        'resolution_notes',
        'tags',
        'metadata',
    ];

    protected $casts = [
        'last_reply_at' => 'datetime',
        'resolved_at' => 'datetime',
        'tags' => 'array',
        'metadata' => 'array',
        'priority' => 'integer',
    ];
}
