<?php

namespace App\Models\Customer;

use App\Models\BaseModel;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SavedSearch extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'search_criteria',
        'email_alerts',
        'alert_frequency',
        'last_alert_sent',
        'results_count',
        'new_results_count',
    ];

    protected $casts = [
        'search_criteria' => 'array',
        'email_alerts' => 'boolean',
        'alert_frequency' => 'integer',
        'last_alert_sent' => 'timestamp',
        'results_count' => 'integer',
        'new_results_count' => 'integer',
    ];

    protected $translatable = [
        'name',
    ];

    // Alert frequency constants (in hours)
    const FREQUENCY_IMMEDIATE = 1;

    const FREQUENCY_DAILY = 24;

    const FREQUENCY_WEEKLY = 168;

    const FREQUENCY_MONTHLY = 720;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get alert frequencies with labels
     */
    public static function getAlertFrequencies(): array
    {
        return [
            self::FREQUENCY_IMMEDIATE => 'Immediate',
            self::FREQUENCY_DAILY => 'Daily',
            self::FREQUENCY_WEEKLY => 'Weekly',
            self::FREQUENCY_MONTHLY => 'Monthly',
        ];
    }

    /**
     * Get alert frequency label
     */
    public function getAlertFrequencyLabelAttribute(): string
    {
        return static::getAlertFrequencies()[$this->alert_frequency] ?? 'Custom';
    }

    /**
     * Check if alerts are enabled
     */
    public function hasAlertsEnabled(): bool
    {
        return $this->email_alerts && $this->is_active;
    }

    /**
     * Check if it's time to send alert
     */
    public function shouldSendAlert(): bool
    {
        if (! $this->hasAlertsEnabled()) {
            return false;
        }

        if (! $this->last_alert_sent) {
            return true;
        }

        $nextAlertTime = $this->last_alert_sent->addHours($this->alert_frequency);

        return now()->gte($nextAlertTime);
    }

    /**
     * Check if search has new results
     */
    public function hasNewResults(): bool
    {
        return $this->new_results_count > 0;
    }

    /**
     * Get search criteria as readable text
     */
    public function getSearchCriteriaTextAttribute(): string
    {
        $criteria = $this->search_criteria ?? [];
        $text = [];

        // Vehicle make/model
        if (! empty($criteria['make'])) {
            $text[] = 'Make: '.$criteria['make'];
        }
        if (! empty($criteria['model'])) {
            $text[] = 'Model: '.$criteria['model'];
        }

        // Price range
        if (! empty($criteria['price_min']) && ! empty($criteria['price_max'])) {
            $text[] = 'Price: $'.number_format($criteria['price_min']).' - $'.number_format($criteria['price_max']);
        } elseif (! empty($criteria['price_min'])) {
            $text[] = 'Price: From $'.number_format($criteria['price_min']);
        } elseif (! empty($criteria['price_max'])) {
            $text[] = 'Price: Up to $'.number_format($criteria['price_max']);
        }

        // Year range
        if (! empty($criteria['year_min']) && ! empty($criteria['year_max'])) {
            $text[] = 'Year: '.$criteria['year_min'].' - '.$criteria['year_max'];
        } elseif (! empty($criteria['year_min'])) {
            $text[] = 'Year: From '.$criteria['year_min'];
        }

        // Location
        if (! empty($criteria['location'])) {
            $text[] = 'Location: '.$criteria['location'];
        }

        // Fuel type
        if (! empty($criteria['fuel_type'])) {
            $text[] = 'Fuel: '.ucfirst($criteria['fuel_type']);
        }

        // Transmission
        if (! empty($criteria['transmission'])) {
            $text[] = 'Transmission: '.ucfirst($criteria['transmission']);
        }

        return ! empty($text) ? implode(', ', $text) : 'All vehicles';
    }

    /**
     * Get time since last alert
     */
    public function getTimeSinceLastAlertAttribute(): ?string
    {
        return $this->last_alert_sent ? $this->last_alert_sent->diffForHumans() : null;
    }

    /**
     * Get next alert time
     */
    public function getNextAlertTimeAttribute(): ?Carbon
    {
        if (! $this->hasAlertsEnabled() || ! $this->last_alert_sent) {
            return null;
        }

        return $this->last_alert_sent->addHours($this->alert_frequency);
    }

    /**
     * Update search results count
     */
    public function updateResultsCount(int $newCount): bool
    {
        $oldCount = $this->results_count ?? 0;
        $newResultsCount = max(0, $newCount - $oldCount);

        return $this->update([
            'results_count' => $newCount,
            'new_results_count' => $newResultsCount,
        ]);
    }

    /**
     * Mark alert as sent
     */
    public function markAlertSent(): bool
    {
        return $this->update([
            'last_alert_sent' => now(),
            'new_results_count' => 0,
        ]);
    }

    /**
     * Enable alerts
     */
    public function enableAlerts(): bool
    {
        return $this->update(['email_alerts' => true]);
    }

    /**
     * Disable alerts
     */
    public function disableAlerts(): bool
    {
        return $this->update(['email_alerts' => false]);
    }

    /**
     * Update search criteria
     */
    public function updateCriteria(array $criteria): bool
    {
        return $this->update([
            'search_criteria' => $criteria,
            'new_results_count' => 0, // Reset new results count
        ]);
    }

    /**
     * Clone search with new name
     */
    public function duplicate(string $newName): static
    {
        return static::create([
            'user_id' => $this->user_id,
            'name' => $newName,
            'search_criteria' => $this->search_criteria,
            'email_alerts' => $this->email_alerts,
            'alert_frequency' => $this->alert_frequency,
        ]);
    }

    // Scopes
    public function scopeByUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeWithAlertsEnabled($query)
    {
        return $query->where('email_alerts', true)->where('is_active', true);
    }

    public function scopeNeedingAlerts($query)
    {
        return $query->withAlertsEnabled()
            ->where(function ($query) {
                $query->whereNull('last_alert_sent')
                    ->orWhereRaw('last_alert_sent + INTERVAL alert_frequency HOUR <= NOW()');
            });
    }

    public function scopeWithNewResults($query)
    {
        return $query->where('new_results_count', '>', 0);
    }

    public function scopeByFrequency($query, int $frequency)
    {
        return $query->where('alert_frequency', $frequency);
    }

    public function scopeRecentlyCreated($query, int $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    public function scopeInactive($query, int $days = 30)
    {
        return $query->where('last_alert_sent', '<', now()->subDays($days))
            ->orWhereNull('last_alert_sent');
    }

    public function scopePopular($query)
    {
        return $query->orderByDesc('results_count');
    }
}
