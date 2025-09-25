<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AuditLog extends Model
{
    protected $fillable = [
        'model_type',
        'model_id',
        'event',
        'old_values',
        'new_values',
        'user_id',
        'user_type',
        'ip_address',
        'user_agent',
        'url',
        'method',
        'request_data',
        'batch_uuid',
        'tags',
        'occurred_at',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'request_data' => 'array',
        'occurred_at' => 'timestamp',
    ];

    protected $dates = [
        'occurred_at',
        'created_at',
        'updated_at',
    ];

    // Event constants
    const EVENT_CREATED = 'created';

    const EVENT_UPDATED = 'updated';

    const EVENT_DELETED = 'deleted';

    const EVENT_RESTORED = 'restored';

    const EVENT_FORCE_DELETED = 'force_deleted';

    const EVENT_LOGIN = 'login';

    const EVENT_LOGOUT = 'logout';

    const EVENT_PASSWORD_CHANGED = 'password_changed';

    const EVENT_EMAIL_VERIFIED = 'email_verified';

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->occurred_at)) {
                $model->occurred_at = now();
            }

            if (empty($model->batch_uuid)) {
                $model->batch_uuid = Str::uuid();
            }
        });
    }

    // RELATIONSHIPS

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function auditable(): MorphTo
    {
        return $this->morphTo('model', 'model_type', 'model_id');
    }

    // HELPER METHODS

    /**
     * Get changes made to the model
     */
    public function getChanges(): array
    {
        $changes = [];

        if ($this->event === self::EVENT_CREATED) {
            return $this->new_values ?? [];
        }

        if ($this->event === self::EVENT_UPDATED) {
            $old = $this->old_values ?? [];
            $new = $this->new_values ?? [];

            foreach ($new as $key => $value) {
                if (! array_key_exists($key, $old) || $old[$key] !== $value) {
                    $changes[$key] = [
                        'old' => $old[$key] ?? null,
                        'new' => $value,
                    ];
                }
            }
        }

        return $changes;
    }

    /**
     * Get the model that was audited
     */
    public function getAuditedModel()
    {
        if (! $this->model_type || ! $this->model_id) {
            return null;
        }

        $modelClass = $this->model_type;

        if (! class_exists($modelClass)) {
            return null;
        }

        try {
            return $modelClass::find($this->model_id);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get human-readable description of the audit event
     */
    public function getDescriptionAttribute(): string
    {
        $modelName = class_basename($this->model_type);
        $userName = $this->user ? $this->user->name : 'System';

        switch ($this->event) {
            case self::EVENT_CREATED:
                return "{$userName} created {$modelName} #{$this->model_id}";
            case self::EVENT_UPDATED:
                return "{$userName} updated {$modelName} #{$this->model_id}";
            case self::EVENT_DELETED:
                return "{$userName} deleted {$modelName} #{$this->model_id}";
            case self::EVENT_RESTORED:
                return "{$userName} restored {$modelName} #{$this->model_id}";
            case self::EVENT_FORCE_DELETED:
                return "{$userName} permanently deleted {$modelName} #{$this->model_id}";
            default:
                return "{$userName} performed '{$this->event}' on {$modelName} #{$this->model_id}";
        }
    }

    /**
     * Get the model name without namespace
     */
    public function getModelNameAttribute(): string
    {
        return class_basename($this->model_type);
    }

    /**
     * Get tags as array
     */
    public function getTagsArrayAttribute(): array
    {
        if (empty($this->tags)) {
            return [];
        }

        return array_filter(array_map('trim', explode(',', $this->tags)));
    }

    /**
     * Check if the audit log has a specific tag
     */
    public function hasTag(string $tag): bool
    {
        return in_array($tag, $this->tags_array);
    }

    /**
     * Get the time elapsed since the event occurred
     */
    public function getTimeElapsedAttribute(): string
    {
        return $this->occurred_at->diffForHumans();
    }

    /**
     * Create an audit log entry
     */
    public static function createEntry(
        $model,
        string $event,
        ?array $oldValues = null,
        ?array $newValues = null,
        array $options = []
    ): self {
        $user = Auth::user();
        $request = request();

        return self::create([
            'model_type' => get_class($model),
            'model_id' => $model->getKey(),
            'event' => $event,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'user_id' => $user ? $user->id : null,
            'user_type' => $user ? get_class($user) : null,
            'ip_address' => $request ? $request->ip() : null,
            'user_agent' => $request ? $request->userAgent() : null,
            'url' => $request ? $request->fullUrl() : null,
            'method' => $request ? $request->method() : null,
            'request_data' => $options['include_request_data'] ?? false ? $request?->all() : null,
            'batch_uuid' => $options['batch_uuid'] ?? Str::uuid(),
            'tags' => $options['tags'] ?? null,
            'occurred_at' => $options['occurred_at'] ?? now(),
        ]);
    }

    // SCOPES

    public static function forModel($model)
    {
        return static::where('model_type', get_class($model))
            ->where('model_id', $model->getKey());
    }

    public function scopeForModel($query, $model)
    {
        return $query->where('model_type', get_class($model))
            ->where('model_id', $model->getKey());
    }

    public function scopeByEvent($query, string $event)
    {
        return $query->where('event', $event);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByModelType($query, string $modelType)
    {
        return $query->where('model_type', $modelType);
    }

    public function scopeByBatch($query, string $batchUuid)
    {
        return $query->where('batch_uuid', $batchUuid);
    }

    public function scopeWithTag($query, string $tag)
    {
        return $query->where('tags', 'like', '%'.$tag.'%');
    }

    public function scopeInDateRange($query, $startDate, $endDate = null)
    {
        $query->where('occurred_at', '>=', $startDate);

        if ($endDate) {
            $query->where('occurred_at', '<=', $endDate);
        }

        return $query;
    }

    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('occurred_at', '>=', now()->subDays($days));
    }

    public function scopeByIpAddress($query, string $ipAddress)
    {
        return $query->where('ip_address', $ipAddress);
    }

    public function scopeOrderByOccurredAt($query, string $direction = 'desc')
    {
        return $query->orderBy('occurred_at', $direction);
    }

    /**
     * Get audit statistics for a model
     */
    public static function getStatsForModel($model, int $days = 30): array
    {
        $query = self::forModel($model)->recent($days);

        return [
            'total_events' => $query->count(),
            'created_events' => (clone $query)->byEvent(self::EVENT_CREATED)->count(),
            'updated_events' => (clone $query)->byEvent(self::EVENT_UPDATED)->count(),
            'deleted_events' => (clone $query)->byEvent(self::EVENT_DELETED)->count(),
            'unique_users' => (clone $query)->distinct('user_id')->count('user_id'),
            'events_by_day' => (clone $query)
                ->selectRaw('DATE(occurred_at) as date, COUNT(*) as count')
                ->groupBy('date')
                ->orderBy('date')
                ->pluck('count', 'date')
                ->toArray(),
        ];
    }
}
