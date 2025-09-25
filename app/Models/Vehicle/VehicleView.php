<?php

namespace App\Models\Vehicle;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VehicleView extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'vehicle_id',
        'user_id',
        'ip_address',
        'user_agent',
        'referrer',
    ];

    protected $translatable = [];

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to get unique views (by user or IP)
     */
    public function scopeUnique($query)
    {
        return $query->distinct(['user_id', 'ip_address', 'vehicle_id']);
    }

    /**
     * Scope to get views by date range
     */
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Scope to get authenticated user views
     */
    public function scopeAuthenticated($query)
    {
        return $query->whereNotNull('user_id');
    }

    /**
     * Scope to get guest views
     */
    public function scopeGuest($query)
    {
        return $query->whereNull('user_id');
    }
}
