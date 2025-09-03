<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Redis Cache Configuration for Room Availability
    |--------------------------------------------------------------------------
    |
    | This configuration controls the Redis caching behavior for room
    | availability data and search functionality.
    |
    */

    'enabled' => env('ROOM_CACHE_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Cache TTL (Time To Live) Settings
    |--------------------------------------------------------------------------
    */
    'ttl' => [
        'availability' => env('ROOM_AVAILABILITY_TTL', 3600), // 1 hour
        'search' => env('ROOM_SEARCH_TTL', 1800), // 30 minutes
        'stats' => env('ROOM_STATS_TTL', 600), // 10 minutes
        'room_types' => env('ROOM_TYPES_TTL', 3600), // 1 hour
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Prefixes
    |--------------------------------------------------------------------------
    */
    'prefixes' => [
        'availability' => 'room_availability:',
        'search' => 'room_search:',
        'stats' => 'room_stats:',
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Tags
    |--------------------------------------------------------------------------
    */
    'tags' => [
        'availability' => 'room_availability',
        'search' => 'room_search',
        'stats' => 'room_stats',
        'room_types' => 'room_types',
        'rooms' => 'rooms',
    ],

    /*
    |--------------------------------------------------------------------------
    | Warm-up Configuration
    |--------------------------------------------------------------------------
    */
    'warmup' => [
        'enabled' => env('CACHE_WARMUP_ENABLED', true),
        'days_ahead' => env('CACHE_WARMUP_DAYS', 30),
        'max_nights' => env('CACHE_WARMUP_MAX_NIGHTS', 7),
        'interval' => env('CACHE_WARMUP_INTERVAL', 600), // 10 minutes
    ],

    /*
    |--------------------------------------------------------------------------
    | Performance Settings
    |--------------------------------------------------------------------------
    */
    'performance' => [
        'batch_size' => env('CACHE_BATCH_SIZE', 100),
        'max_execution_time' => env('CACHE_MAX_EXECUTION_TIME', 300), // 5 minutes
        'memory_limit' => env('CACHE_MEMORY_LIMIT', '256M'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Monitoring & Debugging
    |--------------------------------------------------------------------------
    */
    'monitoring' => [
        'log_cache_hits' => env('LOG_CACHE_HITS', false),
        'log_cache_misses' => env('LOG_CACHE_MISSES', false),
        'track_performance' => env('TRACK_CACHE_PERFORMANCE', true),
    ],
];
