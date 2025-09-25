<?php

namespace App\Models\SalesAndTransactions;

use App\Models\BaseModel;
use App\Traits\AuditableTrait;
use Database\Factories\CartFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cart extends BaseModel
{
    use AuditableTrait, HasFactory, SoftDeletes;

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return CartFactory::new();
    }

    protected $fillable = [
        'user_id',
        'session_id',
        'total_items',
        'total_amount',
        'currency',
        'status',
        'expires_at',
        'metadata',
    ];

    protected $casts = [
        'total_items' => 'integer',
        'total_amount' => 'decimal:2',
        'expires_at' => 'datetime',
        'metadata' => 'array',
    ];
}
