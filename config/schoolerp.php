<?php

return [

    /*
    |--------------------------------------------------------------------------
    | School ERP Configuration
    |--------------------------------------------------------------------------
    */

    'academic_year' => [
        'current' => env('ACADEMIC_YEAR', '2025-26'),
        'format' => 'Y-y', // 2025-26 format
        'start_month' => env('ACADEMIC_YEAR_START_MONTH', 4), // April
    ],

    'roll_number' => [
        'format' => '{academic_year}/{program_code}/{division}/{number}',
        'padding' => 3, // 001, 002, etc.
    ],

    'fee' => [
        'currency' => 'INR',
        'decimal_places' => 2,
        'late_fee_grace_days' => 7,
        'receipt_prefix' => env('FEE_RECEIPT_PREFIX', 'REC'),
    ],

    'attendance' => [
        'minimum_percentage' => 75,
        'grace_percentage' => 5,
    ],

    'results' => [
        'pass_percentage' => 40,
        'grace_marks' => 5,
    ],

    'pagination' => [
        'per_page' => 25,
        'max_per_page' => 100,
    ],

    'security' => [
        'default_password' => env('DEFAULT_USER_PASSWORD', 'password'),
        'password_min_length' => 8,
        'token_expiry_days' => 7,
    ],

    'upload' => [
        'max_file_size' => 2048, // 2MB
        'allowed_extensions' => ['jpeg', 'png', 'jpg', 'pdf'],
        'storage_disk' => 'private',
    ],

];
