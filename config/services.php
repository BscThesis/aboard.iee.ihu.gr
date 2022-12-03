<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'passport' => [
        'login_endpoint' => env('PASSPORT_LOGIN_ENDPOINT'),
        'client_id' => env('PASSPORT_CLIENT_ID'),
        'client_secret' => env('PASSPORT_CLIENT_SECRET'),
    ],

    'no_auth_access' => [
        'single_ips' => env('DEV_IP'),
        'ip_ranges' => env('LOCAL_IP_RANGES'),
    ],

    'only_admin_access' => [
        'admin_ip' => env('ADMIN_IP')
    ],
  
    // Add our own Driver
    'iee' => [
        'client_id' => env('CLIENT_ID'),
        'redirect' => env('REDIRECT_URI'),
        'response_type' => env('RESPONSE_TYPE'),
        'client_secret' => env('CLIENT_SECRET'),
    ],
    'iee_api' => [
        'client_id' => env('V2_CLIENT_ID'),
        'redirect' => env('V2_REDIRECT_URI'),
        'response_type' => env('V2_RESPONSE_TYPE'),
        'client_secret' => env('V2_CLIENT_SECRET'),
    ],
];
