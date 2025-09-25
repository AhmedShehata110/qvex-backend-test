<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Config;

trait AuditableTrait
{
    /**
     * Fields to exclude from audit logging
     */
    protected $auditExclude = [
        'created_at',
        'updated_at',
        'deleted_at',
        'password',
        'remember_token',
        'email_verified_at',
    ];

    /**
     * Events to audit (if not specified, all events will be audited)
     */
    protected $auditEvents = [
        'created',
        'updated',
        'deleted',
        'restored',
    ];

    /**
     * Tags to apply to audit logs for this model
     */
    protected $auditTags = [];

    /**
     * Whether to include request data in audit logs
     */
    protected $auditIncludeRequestData = false;

    /**
     * Create an audit log entry for the model
     */
    public function audit(string $event, array $options = []): ?AuditLog
    {
        // Check if auditing is enabled
        if (! Config::get('audit.enabled', true)) {
            return null;
        }

        // Check if this event should be audited
        if (! $this->shouldAuditEvent($event)) {
            return null;
        }

        // Get old and new values
        $oldValues = $this->getAuditOldValues($event);
        $newValues = $this->getAuditNewValues($event);

        // Filter out excluded fields
        $oldValues = $this->filterAuditData($oldValues);
        $newValues = $this->filterAuditData($newValues);

        // Skip if no meaningful changes occurred
        if ($event === 'updated' && empty(array_diff_assoc($newValues, $oldValues))) {
            return null;
        }

        // Create audit log entry
        return AuditLog::createEntry(
            $this,
            $event,
            $oldValues,
            $newValues,
            array_merge([
                'tags' => $this->getAuditTags(),
                'include_request_data' => $this->auditIncludeRequestData,
            ], $options)
        );
    }

    /**
     * Get old values for auditing
     */
    protected function getAuditOldValues(string $event): array
    {
        switch ($event) {
            case 'created':
                return [];
            case 'updated':
                return $this->getOriginal();
            case 'deleted':
            case 'restored':
                return $this->getAttributes();
            default:
                return [];
        }
    }

    /**
     * Get new values for auditing
     */
    protected function getAuditNewValues(string $event): array
    {
        switch ($event) {
            case 'created':
            case 'updated':
            case 'restored':
                return $this->getAttributes();
            case 'deleted':
                return [];
            default:
                return [];
        }
    }

    /**
     * Filter audit data to exclude sensitive/unwanted fields
     */
    protected function filterAuditData(array $data): array
    {
        $excludeFields = array_merge(
            $this->auditExclude,
            Config::get('audit.exclude_fields', [])
        );

        return array_diff_key($data, array_flip($excludeFields));
    }

    /**
     * Check if an event should be audited
     */
    protected function shouldAuditEvent(string $event): bool
    {
        // Check global config
        $enabledEvents = Config::get('audit.events', ['created', 'updated', 'deleted', 'restored']);

        if (! in_array($event, $enabledEvents)) {
            return false;
        }

        // Check model-specific config
        if (! empty($this->auditEvents)) {
            return in_array($event, $this->auditEvents);
        }

        return true;
    }

    /**
     * Get audit tags for this model
     */
    protected function getAuditTags(): ?string
    {
        $tags = array_merge(
            $this->auditTags,
            [class_basename(static::class)]
        );

        // Add custom tags based on model state
        $customTags = $this->getCustomAuditTags();
        if (! empty($customTags)) {
            $tags = array_merge($tags, $customTags);
        }

        return ! empty($tags) ? implode(',', array_unique($tags)) : null;
    }

    /**
     * Override this method to add custom audit tags based on model state
     */
    protected function getCustomAuditTags(): array
    {
        return [];
    }

    /**
     * Get the model's audit logs
     */
    public function auditLogs()
    {
        return AuditLog::forModel($this);
    }

    /**
     * Get recent audit logs for this model
     */
    public function recentAuditLogs(int $days = 30)
    {
        return $this->auditLogs()->recent($days)->orderByOccurredAt();
    }

    /**
     * Get audit statistics for this model
     */
    public function getAuditStats(int $days = 30): array
    {
        return AuditLog::getStatsForModel($this, $days);
    }

    /**
     * Boot the auditable trait
     */
    protected static function bootAuditableTrait()
    {
        // Only set up automatic auditing if observers are not manually defined
        if (! static::hasObserver()) {
            static::created(function ($model) {
                $model->audit('created');
            });

            static::updated(function ($model) {
                $model->audit('updated');
            });

            static::deleted(function ($model) {
                $model->audit('deleted');
            });

            if (method_exists(static::class, 'restored')) {
                static::restored(function ($model) {
                    $model->audit('restored');
                });
            }
        }
    }

    /**
     * Check if model has observers registered
     */
    protected static function hasObserver(): bool
    {
        $className = static::class;
        $observerClass = str_replace('\\Models\\', '\\Observers\\', $className).'Observer';

        return class_exists($observerClass);
    }

    /**
     * Audit a custom event
     */
    public function auditCustomEvent(string $event, array $data = [], array $options = []): ?AuditLog
    {
        return AuditLog::createEntry(
            $this,
            $event,
            null,
            $data,
            array_merge([
                'tags' => $this->getAuditTags().',custom_event',
                'include_request_data' => $this->auditIncludeRequestData,
            ], $options)
        );
    }
}
