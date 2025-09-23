<?php

if (! function_exists('convertToArray')) {
    /**
     * Convert file inputs or single values to array uniformly.
     * Accepts comma-separated strings, arrays, Collections, or single objects.
     *
     * @param  mixed  $value
     */
    function convertToArray($value): array
    {
        if (is_null($value)) {
            return [];
        }

        if (is_string($value)) {
            // handle comma separated file paths or ids
            return array_filter(array_map('trim', explode(',', $value)));
        }

        if (is_array($value)) {
            return $value;
        }

        if ($value instanceof \Illuminate\Support\Collection) {
            return $value->all();
        }

        // Single object/primitive
        return [$value];
    }
}

if (! function_exists('uuid')) {
    /**
     * Generate a string UUID.
     */
    function uuid(): string
    {
        return (string) \Illuminate\Support\Str::uuid();
    }
}
