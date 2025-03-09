<?php

declare(strict_types=1);

return [
    'clickatell' => [
        'api_key' => env('CLICKATELL_API_KEY'),
        'api_id' => env('CLICKATELL_API_ID'),
    ],
    'plivo' => [
        'auth_id' => env('PLIVO_AUTH_ID'),
        'auth_token' => env('PLIVO_AUTH_TOKEN'),
        'from_number' => env('PLIVO_FROM_NUMBER'),
    ],
    'twilio' => [
        'username' => env('TWILIO_USERNAME'),
        'password' => env('TWILIO_PASSWORD'),
        'auth_token' => env('TWILIO_AUTH_TOKEN'),
        'account_sid' => env('TWILIO_ACCOUNT_SID'),
        'from' => env('TWILIO_FROM'),
    ],
    'vonage' => [
        'api_key' => env('VONAGE_API_KEY'),
        'api_secret' => env('VONAGE_API_SECRET'),
        'send_from' => '',
    ],
    'aws' => [
        'key' => env('AWS_KEY'),
        'secret' => env('AWS_SECRET'),
    ],
];
