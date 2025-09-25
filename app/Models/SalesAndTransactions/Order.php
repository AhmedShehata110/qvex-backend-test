<?php

namespace App\Models\SalesAndTransactions;

use App\Models\BaseModel;
use App\Traits\AuditableTrait;
use Database\Factories\OrderFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends BaseModel
{
    use AuditableTrait, HasFactory, SoftDeletes;

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return OrderFactory::new();
    }

    protected $fillable = [
        'user_id',
        'order_number',
        'total_amount',
        'currency',
        'status',
        'payment_status',
        'shipping_address',
        'billing_address',
        'order_date',
        'shipped_at',
        'delivered_at',
        'notes',
        'metadata',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'order_date' => 'datetime',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
        'shipping_address' => 'array',
        'billing_address' => 'array',
        'metadata' => 'array',
    ];
}
