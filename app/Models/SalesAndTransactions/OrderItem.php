<?php

namespace App\Models\SalesAndTransactions;

use App\Models\BaseModel;
use App\Traits\AuditableTrait;
use Database\Factories\OrderItemFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderItem extends BaseModel
{
    use AuditableTrait, HasFactory, SoftDeletes;

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return OrderItemFactory::new();
    }

    protected $fillable = [
        'order_id',
        'product_id',
        'product_type',
        'product_name',
        'quantity',
        'unit_price',
        'total_price',
        'options',
        'status',
        'metadata',
    ];

    protected $casts = [
        'order_id' => 'integer',
        'product_id' => 'integer',
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'options' => 'array',
        'metadata' => 'array',
    ];

    // RELATIONSHIPS

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
