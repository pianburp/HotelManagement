<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Hotel Amenities
    |--------------------------------------------------------------------------
    |
    | List of available amenities that can be assigned to room types.
    | These amenities will be used in search filters and room displays.
    |
    */
    'amenities' => [
        'wifi',
        'tv',
        'ac',
        'minibar',
        'balcony',
        'kitchen',
        'jacuzzi',
    ],

    /*
    |--------------------------------------------------------------------------
    | Hotel Settings
    |--------------------------------------------------------------------------
    |
    | General hotel configuration settings
    |
    */
    'tax_rate' => 0.10, // 10% tax rate
    'currency' => 'MYR',
    'currency_symbol' => 'RM',

    /*
    |--------------------------------------------------------------------------
    | Booking Settings
    |--------------------------------------------------------------------------
    |
    | Configuration for booking-related features
    |
    */
    'max_guests_per_room' => 6,
    'advance_booking_days' => 365, // Maximum days in advance for booking
    'cancellation_hours' => 24, // Hours before check-in to allow cancellation
];
