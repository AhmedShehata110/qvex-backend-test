<?php

namespace App\Models\Vendor;

use App\Models\BaseModel;
use App\Models\User;
use App\Models\Vehicle\Vehicle;
use Database\Factories\VendorFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vendor extends BaseModel
{
    use HasFactory, SoftDeletes;

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return VendorFactory::new();
    }

    protected $fillable = [
        'user_id',
        'business_name',
        'business_name_ar',
        'slug',
        'description',
        'description_ar',
        'registration_number',
        'tax_id',
        'trade_license',
        'logo',
        'cover_image',
        'vendor_type',
        'status',
        'business_hours',
        'services_offered',
        'website',
        'commission_rate',
        'total_sales',
        'total_revenue',
        'rating_average',
        'rating_count',
        'is_featured',
        'is_verified',
        'verified_at',
        'verified_by',
        'subscription_expires_at',
    ];

    protected $casts = [
        'business_hours' => 'array',
        'services_offered' => 'array',
        'commission_rate' => 'decimal:2',
        'total_revenue' => 'decimal:2',
        'rating_average' => 'decimal:2',
        'is_featured' => 'boolean',
        'is_verified' => 'boolean',
        'verified_at' => 'datetime',
        'subscription_expires_at' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function vehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class);
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
