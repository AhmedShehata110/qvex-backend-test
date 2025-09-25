<?php

namespace App\Models\Transaction;

use App\Models\BaseModel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RentalAgreement extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'transaction_id',
        'start_date',
        'end_date',
        'rental_days',
        'daily_rate',
        'weekly_rate',
        'monthly_rate',
        'security_deposit',
        'mileage_limit_per_day',
        'extra_mileage_rate',
        'pickup_mileage',
        'return_mileage',
        'pickup_location',
        'return_location',
        'pickup_time',
        'return_time',
        'fuel_policy',
        'terms_conditions',
        'special_instructions',
        'damage_report_pickup',
        'damage_report_return',
        'damage_charges',
        'late_return_charges',
        'status',
        'pickup_confirmed_at',
        'return_confirmed_at',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'rental_days' => 'integer',
        'daily_rate' => 'decimal:2',
        'weekly_rate' => 'decimal:2',
        'monthly_rate' => 'decimal:2',
        'security_deposit' => 'decimal:2',
        'mileage_limit_per_day' => 'decimal:2',
        'extra_mileage_rate' => 'decimal:2',
        'pickup_mileage' => 'integer',
        'return_mileage' => 'integer',
        'pickup_time' => 'time',
        'return_time' => 'time',
        'damage_report_pickup' => 'array',
        'damage_report_return' => 'array',
        'damage_charges' => 'decimal:2',
        'late_return_charges' => 'decimal:2',
        'pickup_confirmed_at' => 'timestamp',
        'return_confirmed_at' => 'timestamp',
    ];

    protected $translatable = [];

    // Status constants
    const STATUS_ACTIVE = 'active';

    const STATUS_COMPLETED = 'completed';

    const STATUS_CANCELLED = 'cancelled';

    const STATUS_EXTENDED = 'extended';

    // Fuel policy constants
    const FUEL_POLICY_FULL_TO_FULL = 'full_to_full';

    const FUEL_POLICY_FULL_TO_EMPTY = 'full_to_empty';

    const FUEL_POLICY_SAME_TO_SAME = 'same_to_same';

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    /**
     * Get rental statuses with labels
     */
    public static function getStatuses(): array
    {
        return [
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_CANCELLED => 'Cancelled',
            self::STATUS_EXTENDED => 'Extended',
        ];
    }

    /**
     * Get fuel policies with labels
     */
    public static function getFuelPolicies(): array
    {
        return [
            self::FUEL_POLICY_FULL_TO_FULL => 'Full to Full',
            self::FUEL_POLICY_FULL_TO_EMPTY => 'Full to Empty',
            self::FUEL_POLICY_SAME_TO_SAME => 'Same to Same',
        ];
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute(): string
    {
        return static::getStatuses()[$this->status] ?? $this->status;
    }

    /**
     * Get fuel policy label
     */
    public function getFuelPolicyLabelAttribute(): string
    {
        return static::getFuelPolicies()[$this->fuel_policy] ?? $this->fuel_policy;
    }

    /**
     * Check if rental is active
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    /**
     * Check if rental is completed
     */
    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * Check if rental is overdue
     */
    public function isOverdue(): bool
    {
        return $this->isActive() && $this->end_date->isPast();
    }

    /**
     * Calculate total rental cost
     */
    public function getTotalRentalCostAttribute(): float
    {
        $cost = $this->rental_days * $this->daily_rate;
        $cost += $this->damage_charges;
        $cost += $this->late_return_charges;

        return $cost;
    }

    /**
     * Calculate extra mileage charges
     */
    public function getExtraMileageChargesAttribute(): float
    {
        if (! $this->pickup_mileage || ! $this->return_mileage || ! $this->mileage_limit_per_day) {
            return 0;
        }

        $totalMileage = $this->return_mileage - $this->pickup_mileage;
        $allowedMileage = $this->rental_days * $this->mileage_limit_per_day;
        $extraMileage = max(0, $totalMileage - $allowedMileage);

        return $extraMileage * ($this->extra_mileage_rate ?? 0);
    }

    /**
     * Calculate late return charges
     */
    public function calculateLateReturnCharges(): float
    {
        if (! $this->return_confirmed_at || ! $this->isOverdue()) {
            return 0;
        }

        $lateDays = Carbon::parse($this->return_confirmed_at)->diffInDays($this->end_date);

        return $lateDays * $this->daily_rate * 1.5; // 1.5x daily rate for late returns
    }

    /**
     * Confirm pickup
     */
    public function confirmPickup(): bool
    {
        return $this->update([
            'pickup_confirmed_at' => now(),
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Confirm return
     */
    public function confirmReturn(?int $returnMileage = null, array $damageReport = []): bool
    {
        $updates = [
            'return_confirmed_at' => now(),
            'status' => self::STATUS_COMPLETED,
        ];

        if ($returnMileage) {
            $updates['return_mileage'] = $returnMileage;
        }

        if (! empty($damageReport)) {
            $updates['damage_report_return'] = $damageReport;
        }

        // Calculate late charges if overdue
        if ($this->isOverdue()) {
            $updates['late_return_charges'] = $this->calculateLateReturnCharges();
        }

        return $this->update($updates);
    }

    /**
     * Extend rental period
     */
    public function extend(Carbon $newEndDate): bool
    {
        if ($newEndDate->lte($this->end_date)) {
            return false;
        }

        $additionalDays = $this->end_date->diffInDays($newEndDate);

        return $this->update([
            'end_date' => $newEndDate,
            'rental_days' => $this->rental_days + $additionalDays,
            'status' => self::STATUS_EXTENDED,
        ]);
    }

    /**
     * Cancel rental agreement
     */
    public function cancel(): bool
    {
        return $this->update(['status' => self::STATUS_CANCELLED]);
    }

    /**
     * Get rental duration in human readable format
     */
    public function getDurationAttribute(): string
    {
        if ($this->rental_days >= 30) {
            $months = floor($this->rental_days / 30);
            $remainingDays = $this->rental_days % 30;

            $duration = $months.' month'.($months > 1 ? 's' : '');

            if ($remainingDays > 0) {
                $duration .= ' '.$remainingDays.' day'.($remainingDays > 1 ? 's' : '');
            }

            return $duration;
        }

        if ($this->rental_days >= 7) {
            $weeks = floor($this->rental_days / 7);
            $remainingDays = $this->rental_days % 7;

            $duration = $weeks.' week'.($weeks > 1 ? 's' : '');

            if ($remainingDays > 0) {
                $duration .= ' '.$remainingDays.' day'.($remainingDays > 1 ? 's' : '');
            }

            return $duration;
        }

        return $this->rental_days.' day'.($this->rental_days > 1 ? 's' : '');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', self::STATUS_ACTIVE)
            ->where('end_date', '<', now());
    }

    public function scopeEndingToday($query)
    {
        return $query->where('status', self::STATUS_ACTIVE)
            ->whereDate('end_date', today());
    }

    public function scopeEndingSoon($query, int $days = 3)
    {
        return $query->where('status', self::STATUS_ACTIVE)
            ->whereBetween('end_date', [now(), now()->addDays($days)]);
    }
}
