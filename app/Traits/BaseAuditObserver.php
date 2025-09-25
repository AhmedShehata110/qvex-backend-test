<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

trait BaseAuditObserver
{
    /**
     * Handle the model created event
     */
    public function created($model)
    {
        $this->logAuditEvent($model, AuditLog::EVENT_CREATED);
    }

    /**
     * Handle the model updated event
     */
    public function updated($model)
    {
        $this->logAuditEvent($model, AuditLog::EVENT_UPDATED);
    }

    /**
     * Handle the model deleted event
     */
    public function deleted($model)
    {
        $this->logAuditEvent($model, AuditLog::EVENT_DELETED);
    }

    /**
     * Handle the model restored event
     */
    public function restored($model)
    {
        $this->logAuditEvent($model, AuditLog::EVENT_RESTORED);
    }

    /**
     * Handle the model force deleted event
     */
    public function forceDeleted($model)
    {
        $this->logAuditEvent($model, AuditLog::EVENT_FORCE_DELETED);
    }

    /**
     * Log an audit event
     */
    protected function logAuditEvent($model, string $event, array $options = [])
    {
        // Check if auditing is enabled
        if (! Config::get('audit.enabled', true)) {
            return;
        }

        // Check if this event should be audited
        if (! $this->shouldAuditEvent($model, $event)) {
            return;
        }

        // Get old and new values
        $oldValues = $this->getOldValues($model, $event);
        $newValues = $this->getNewValues($model, $event);

        // Filter out excluded fields
        $oldValues = $this->filterAuditData($model, $oldValues);
        $newValues = $this->filterAuditData($model, $newValues);

        // Skip if no meaningful changes occurred
        if ($event === AuditLog::EVENT_UPDATED &&
            $this->arraysAreEqual($newValues, $oldValues)) {
            return;
        }

        // Create audit log entry
        AuditLog::createEntry(
            $model,
            $event,
            $oldValues,
            $newValues,
            array_merge([
                'tags' => $this->getAuditTags($model, $event),
                'include_request_data' => $this->shouldIncludeRequestData($model, $event),
            ], $options)
        );

        // Call custom event handler if it exists
        $eventMethod = 'audit'.Str::studly($event);
        if (method_exists($this, $eventMethod)) {
            $this->$eventMethod($model, $oldValues, $newValues);
        }
    }

    /**
     * Get old values for the audit log
     */
    protected function getOldValues($model, string $event): ?array
    {
        switch ($event) {
            case AuditLog::EVENT_CREATED:
                return null;
            case AuditLog::EVENT_UPDATED:
                return $model->getOriginal();
            case AuditLog::EVENT_DELETED:
            case AuditLog::EVENT_RESTORED:
            case AuditLog::EVENT_FORCE_DELETED:
                return $model->getAttributes();
            default:
                return null;
        }
    }

    /**
     * Get new values for the audit log
     */
    protected function getNewValues($model, string $event): ?array
    {
        switch ($event) {
            case AuditLog::EVENT_CREATED:
            case AuditLog::EVENT_UPDATED:
            case AuditLog::EVENT_RESTORED:
                return $model->getAttributes();
            case AuditLog::EVENT_DELETED:
            case AuditLog::EVENT_FORCE_DELETED:
                return null;
            default:
                return null;
        }
    }

    /**
     * Filter audit data to exclude sensitive fields
     */
    protected function filterAuditData($model, ?array $data): ?array
    {
        if (! $data) {
            return null;
        }

        $excludeFields = array_merge(
            $this->getDefaultExcludeFields(),
            $this->getModelExcludeFields($model),
            Config::get('audit.exclude_fields', [])
        );

        return array_diff_key($data, array_flip($excludeFields));
    }

    /**
     * Get default fields to exclude from auditing
     */
    protected function getDefaultExcludeFields(): array
    {
        return [
            'created_at',
            'updated_at',
            'deleted_at',
            'password',
            'remember_token',
            'email_verified_at',
        ];
    }

    /**
     * Get model-specific fields to exclude from auditing
     */
    protected function getModelExcludeFields($model): array
    {
        // Check if model has auditExclude property
        if (property_exists($model, 'auditExclude')) {
            return $model->auditExclude;
        }

        return [];
    }

    /**
     * Check if an event should be audited
     */
    protected function shouldAuditEvent($model, string $event): bool
    {
        // Check global config
        $enabledEvents = Config::get('audit.events', [
            AuditLog::EVENT_CREATED,
            AuditLog::EVENT_UPDATED,
            AuditLog::EVENT_DELETED,
            AuditLog::EVENT_RESTORED,
        ]);

        if (! in_array($event, $enabledEvents)) {
            return false;
        }

        // Check model-specific config
        if (property_exists($model, 'auditEvents') && ! empty($model->auditEvents)) {
            return in_array($event, $model->auditEvents);
        }

        // Check if observer has custom logic
        $shouldAuditMethod = 'shouldAudit'.Str::studly($event);
        if (method_exists($this, $shouldAuditMethod)) {
            return $this->$shouldAuditMethod($model);
        }

        return true;
    }

    /**
     * Get audit tags for the model and event
     */
    protected function getAuditTags($model, string $event): ?string
    {
        $tags = [class_basename(get_class($model))];

        // Add model-specific tags
        if (property_exists($model, 'auditTags') && ! empty($model->auditTags)) {
            $tags = array_merge($tags, $model->auditTags);
        }

        // Add event-specific tags
        $tags[] = $event;

        // Add custom tags
        $customTags = $this->getCustomAuditTags($model, $event);
        if (! empty($customTags)) {
            $tags = array_merge($tags, $customTags);
        }

        return implode(',', array_unique($tags));
    }

    /**
     * Get custom audit tags - override in specific observers
     */
    protected function getCustomAuditTags($model, string $event): array
    {
        return [];
    }

    /**
     * Check if request data should be included
     */
    protected function shouldIncludeRequestData($model, string $event): bool
    {
        // Check model property
        if (property_exists($model, 'auditIncludeRequestData')) {
            return $model->auditIncludeRequestData;
        }

        // Check config for specific events
        $includeRequestDataEvents = Config::get('audit.include_request_data_events', []);

        return in_array($event, $includeRequestDataEvents);
    }

    /**
     * Log a custom audit event
     */
    protected function logCustomEvent($model, string $event, array $data = [], array $options = [])
    {
        AuditLog::createEntry(
            $model,
            $event,
            null,
            $data,
            array_merge([
                'tags' => $this->getAuditTags($model, $event).',custom_event',
                'include_request_data' => $this->shouldIncludeRequestData($model, $event),
            ], $options)
        );
    }

    /**
     * Log authentication events
     */
    protected function logAuthEvent($user, string $event, array $data = [])
    {
        AuditLog::createEntry(
            $user,
            $event,
            null,
            $data,
            [
                'tags' => 'authentication,'.$event,
                'include_request_data' => true,
            ]
        );
    }

    /**
     * Compare arrays handling enum values properly
     */
    protected function arraysAreEqual(?array $array1, ?array $array2): bool
    {
        if ($array1 === null && $array2 === null) {
            return true;
        }

        if ($array1 === null || $array2 === null) {
            return false;
        }

        // Convert enum values to strings for comparison
        $array1Converted = $this->convertEnumsToStrings($array1);
        $array2Converted = $this->convertEnumsToStrings($array2);

        // Perform a deep comparison on the converted arrays
        return $this->deepArrayCompare($array1Converted, $array2Converted);
    }

    /**
     * Recursively compare two arrays for equality.
     */
    protected function deepArrayCompare(array $array1, array $array2): bool
    {
        // Check if the number of elements is different
        if (count($array1) !== count($array2)) {
            return false;
        }

        // Check all keys from array1 exist in array2 and their values are equal
        foreach ($array1 as $key => $value) {
            if (! array_key_exists($key, $array2)) {
                return false;
            }

            // If both values are arrays, recurse
            if (is_array($value) && is_array($array2[$key])) {
                if (! $this->deepArrayCompare($value, $array2[$key])) {
                    return false;
                }
            } elseif ($value !== $array2[$key]) { // Otherwise, compare values directly
                return false;
            }
        }

        return true;
    }

    /**
     * Convert enum values in array to strings
     */
    protected function convertEnumsToStrings(array $data): array
    {
        $converted = [];
        foreach ($data as $key => $value) {
            if ($value instanceof \BackedEnum) {
                $converted[$key] = $value->value;
            } elseif (is_array($value)) {
                $converted[$key] = $this->convertEnumsToStrings($value); // Recursively handle nested arrays
            } elseif (is_object($value)) {
                // Convert other objects to string, typically JSON representation
                // This assumes that the JSON representation is stable and comparable.
                $converted[$key] = json_encode($value);
            } else {
                $converted[$key] = $value;
            }
        }

        return $converted;
    }
}
