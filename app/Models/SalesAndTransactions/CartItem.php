<?php

namespace App\Models\SalesAndTransactions;

use App\Models\BaseModel;
use App\Traits\AuditableTrait;
use Database\Factories\CartItemFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CartItem extends BaseModel
{
    use AuditableTrait, HasFactory, SoftDeletes;

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return CartItemFactory::new();
    }

    protected $fillable = [
        'cart_id',
        'product_id',
        'product_type',
        'quantity',
        'unit_price',
        'total_price',
        'options',
        'metadata',
    ];

    protected $casts = [
        'cart_id' => 'integer',
        'product_id' => 'integer',
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'options' => 'array',
        'metadata' => 'array',
    ];

    // RELATIONSHIPS

    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }
}
