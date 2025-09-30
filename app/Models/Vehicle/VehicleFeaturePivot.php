<?php

namespace App\Models\Vehicle;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VehicleFeaturePivot extends BaseModel
{
    use HasFactory;

    protected $table = 'vehicle_feature_pivot';

    protected $fillable = [
        'vehicle_id',
        'feature_id',
        'value',
        'notes',
    ];

    protected $casts = [];

    protected $translatable = ['notes'];

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function feature(): BelongsTo
    {
        return $this->belongsTo(VehicleFeature::class, 'feature_id');
    }

    // Scopes
    public function scopeByVehicle($query, int $vehicleId)
    {
        return $query->where('vehicle_id', $vehicleId);
    }

    public function scopeByFeature($query, int $featureId)
    {
        return $query->where('feature_id', $featureId);
    }
}
