<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Performance Monitoring
    |--------------------------------------------------------------------------
    |
    | Configuration for performance monitoring and optimization
    | settings for the QVEX application.
    |
    */

    'monitoring' => [
        'enabled' => env('PERFORMANCE_MONITORING', true),
        'slow_query_threshold' => env('SLOW_QUERY_THRESHOLD', 1000), // milliseconds
        'memory_limit_warning' => env('MEMORY_LIMIT_WARNING', 128), // MB
    ],

    /*
    |--------------------------------------------------------------------------
    | Caching Strategy
    |--------------------------------------------------------------------------
    |
    | Configure caching strategy for different types of data
    |
    */

    'cache' => [
        'models' => [
            'ttl' => env('MODEL_CACHE_TTL', 3600), // 1 hour
            'enabled' => env('MODEL_CACHE_ENABLED', true),
        ],
        'api_responses' => [
            'ttl' => env('API_CACHE_TTL', 300), // 5 minutes
            'enabled' => env('API_CACHE_ENABLED', true),
        ],
        'media' => [
            'ttl' => env('MEDIA_CACHE_TTL', 86400), // 24 hours
            'enabled' => env('MEDIA_CACHE_ENABLED', true),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Database Optimization
    |--------------------------------------------------------------------------
    |
    | Database optimization settings
    |
    */

    'database' => [
        'connection_pool_size' => env('DB_POOL_SIZE', 10),
        'query_timeout' => env('DB_QUERY_TIMEOUT', 30),
        'lazy_loading' => env('DB_LAZY_LOADING', true),
        'chunk_size' => env('DB_CHUNK_SIZE', 1000),
    ],

    /*
    |--------------------------------------------------------------------------
    | Queue Configuration
    |--------------------------------------------------------------------------
    |
    | Optimize queue processing for better performance
    |
    */

    'queue' => [
        'workers' => env('QUEUE_WORKERS', 4),
        'timeout' => env('QUEUE_TIMEOUT', 60),
        'memory_limit' => env('QUEUE_MEMORY_LIMIT', 128),
        'sleep' => env('QUEUE_SLEEP', 3),
        'tries' => env('QUEUE_TRIES', 3),
    ],

    /*
    |--------------------------------------------------------------------------
    | Image Optimization
    |--------------------------------------------------------------------------
    |
    | Configuration for image processing and optimization
    |
    */

    'images' => [
        'quality' => env('IMAGE_QUALITY', 85),
        'max_width' => env('IMAGE_MAX_WIDTH', 1920),
        'max_height' => env('IMAGE_MAX_HEIGHT', 1080),
        'webp_conversion' => env('IMAGE_WEBP_CONVERSION', true),
        'lazy_loading' => env('IMAGE_LAZY_LOADING', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | API Rate Limiting
    |--------------------------------------------------------------------------
    |
    | Rate limiting configuration for API endpoints
    |
    */

    'rate_limiting' => [
        'api' => env('RATE_LIMIT_API', 60), // requests per minute
        'auth' => env('RATE_LIMIT_AUTH', 5), // login attempts per minute
        'upload' => env('RATE_LIMIT_UPLOAD', 10), // file uploads per minute
    ],

    /*
    |--------------------------------------------------------------------------
    | Asset Optimization
    |--------------------------------------------------------------------------
    |
    | Frontend asset optimization settings
    |
    */

    'assets' => [
        'minification' => env('ASSET_MINIFICATION', true),
        'compression' => env('ASSET_COMPRESSION', true),
        'cdn_enabled' => env('CDN_ENABLED', false),
        'cdn_url' => env('CDN_URL'),
    ],

];
