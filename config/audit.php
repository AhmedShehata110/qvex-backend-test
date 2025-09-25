<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Audit Logging
    |--------------------------------------------------------------------------
    |
    | This option controls whether audit logging is enabled or disabled.
    | When disabled, no audit logs will be created.
    |
    */
    'enabled' => env('AUDIT_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Audit Events
    |--------------------------------------------------------------------------
    |
    | Specify which model events should be audited globally.
    | Individual models can override this configuration.
    |
    */
    'events' => [
        'created',
        'updated',
        'deleted',
        'restored',
    ],

    /*
    |--------------------------------------------------------------------------
    | Exclude Fields
    |--------------------------------------------------------------------------
    |
    | Global list of fields to exclude from audit logging.
    | These fields will never be logged in old_values or new_values.
    |
    */
    'exclude_fields' => [
        'password',
        'remember_token',
        'email_verification_token',
        'password_reset_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'created_at',
        'updated_at',
        'deleted_at',
    ],

    /*
    |--------------------------------------------------------------------------
    | Include Request Data Events
    |--------------------------------------------------------------------------
    |
    | Specify which events should include request data in the audit log.
    | This can be useful for debugging and forensic analysis.
    |
    */
    'include_request_data_events' => [
        'created',
        'deleted',
    ],

    /*
    |--------------------------------------------------------------------------
    | Model Specific Configuration
    |--------------------------------------------------------------------------
    |
    | Configure audit settings for specific models.
    | This allows fine-grained control over what gets audited.
    |
    */
    'models' => [
        'App\Models\User' => [
            'enabled' => true,
            'events' => ['created', 'updated', 'deleted', 'restored'],
            'exclude_fields' => [
                'password',
                'remember_token',
                'email_verification_token',
                'password_reset_token',
                'two_factor_secret',
                'two_factor_recovery_codes',
                'email_verified_at', // Sensitive timestamp
            ],
            'include_request_data' => true,
            'tags' => ['user_management', 'authentication'],
        ],

        'App\Models\Vehicle\Vehicle' => [
            'enabled' => true,
            'events' => ['created', 'updated', 'deleted', 'restored'],
            'exclude_fields' => [
                'admin_notes',
                'internal_reference',
            ],
            'include_request_data' => false,
            'tags' => ['vehicle_management', 'listings'],
        ],

        'App\Models\Transaction\Transaction' => [
            'enabled' => true,
            'events' => ['created', 'updated', 'deleted'],
            'exclude_fields' => [
                'payment_token',
                'card_last_four',
                'payment_gateway_response',
            ],
            'include_request_data' => true,
            'tags' => ['financial', 'transactions'],
        ],

        'App\Models\Communication\Review' => [
            'enabled' => true,
            'events' => ['created', 'updated', 'deleted', 'restored'],
            'exclude_fields' => [],
            'include_request_data' => false,
            'tags' => ['reviews', 'content_moderation'],
        ],

        'App\Models\Vendor\Vendor' => [
            'enabled' => true,
            'events' => ['created', 'updated', 'deleted', 'restored'],
            'exclude_fields' => [
                'api_key',
                'webhook_secret',
            ],
            'include_request_data' => true,
            'tags' => ['vendor_management'],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Audit Log Retention
    |--------------------------------------------------------------------------
    |
    | Configure how long audit logs should be retained.
    | Use null to keep logs indefinitely.
    |
    */
    'retention' => [
        'days' => env('AUDIT_RETENTION_DAYS', 365), // Keep for 1 year by default
        'cleanup_enabled' => env('AUDIT_CLEANUP_ENABLED', true),
        'cleanup_schedule' => '0 2 * * *', // Daily at 2 AM
    ],

    /*
    |--------------------------------------------------------------------------
    | Performance Settings
    |--------------------------------------------------------------------------
    |
    | Configure performance-related settings for audit logging.
    |
    */
    'performance' => [
        'queue_enabled' => env('AUDIT_QUEUE_ENABLED', false),
        'queue_connection' => env('AUDIT_QUEUE_CONNECTION', 'default'),
        'queue_name' => env('AUDIT_QUEUE_NAME', 'audit'),
        'batch_size' => env('AUDIT_BATCH_SIZE', 100),
    ],

    /*
    |--------------------------------------------------------------------------
    | Security Settings
    |--------------------------------------------------------------------------
    |
    | Configure security-related audit settings.
    |
    */
    'security' => [
        // Automatically flag suspicious events
        'flag_suspicious_events' => true,

        // Events that should trigger security alerts
        'security_events' => [
            'login_failed',
            'password_reset_requested',
            'role_changed',
            'user_activated',
            'user_deactivated',
            'transaction_disputed',
        ],

        // IP address tracking
        'track_ip_addresses' => true,

        // User agent tracking
        'track_user_agents' => true,

        // Anonymize IP addresses for privacy compliance
        'anonymize_ip' => env('AUDIT_ANONYMIZE_IP', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Reporting Settings
    |--------------------------------------------------------------------------
    |
    | Configure audit reporting and analytics settings.
    |
    */
    'reporting' => [
        'enabled' => true,
        'daily_summary' => true,
        'weekly_summary' => true,
        'security_alerts' => true,

        // Email settings for reports
        'email' => [
            'enabled' => env('AUDIT_EMAIL_ENABLED', false),
            'recipients' => explode(',', env('AUDIT_EMAIL_RECIPIENTS', '')),
            'security_recipients' => explode(',', env('AUDIT_SECURITY_EMAIL_RECIPIENTS', '')),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Database Settings
    |--------------------------------------------------------------------------
    |
    | Configure database-specific audit settings.
    |
    */
    'database' => [
        'connection' => env('AUDIT_DB_CONNECTION', null), // Use default if null
        'table' => 'audit_logs',

        // Index optimization
        'optimize_indexes' => true,
        'partition_by_date' => false, // Enable for high-volume applications

        // Compression settings
        'compress_old_logs' => false, // Compress logs older than X days
        'compression_days' => 90,
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Event Handlers
    |--------------------------------------------------------------------------
    |
    | Register custom event handlers for specific audit events.
    |
    */
    'event_handlers' => [
        'login_failed' => [
            // Example: \App\Handlers\FailedLoginHandler::class,
        ],
        'high_value_transaction' => [
            // Example: \App\Handlers\HighValueTransactionHandler::class,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Integration Settings
    |--------------------------------------------------------------------------
    |
    | Configure integrations with external systems.
    |
    */
    'integrations' => [
        'elasticsearch' => [
            'enabled' => env('AUDIT_ELASTICSEARCH_ENABLED', false),
            'host' => env('AUDIT_ELASTICSEARCH_HOST', 'localhost:9200'),
            'index' => env('AUDIT_ELASTICSEARCH_INDEX', 'qvex_audit_logs'),
        ],

        'slack' => [
            'enabled' => env('AUDIT_SLACK_ENABLED', false),
            'webhook_url' => env('AUDIT_SLACK_WEBHOOK_URL'),
            'channel' => env('AUDIT_SLACK_CHANNEL', '#audit-logs'),
            'security_channel' => env('AUDIT_SLACK_SECURITY_CHANNEL', '#security-alerts'),
        ],
    ],
];
