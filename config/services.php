<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, SparkPost and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
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

    'sparkpost' => [
        'secret' => env('SPARKPOST_SECRET'),
    ],

    'stripe' => [
        'model' => App\Models\User::class,
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
        'webhook' => [
            'secret' => env('STRIPE_WEBHOOK_SECRET'),
            'tolerance' => env('STRIPE_WEBHOOK_TOLERANCE', 300),
        ],
    ],

    'medianaSMS' => [
        'api_key' => env('REST_MEDIANA_MESSAGE_API_KEY'),
        'normal' => [
            'from' => env('SMS_PROVIDER_DEFAULT_NUMBER'),
            'from1000' => env('SMS_PROVIDER_DEFAULT_NUMBER_1000'),
        ],
        'pattern' => [
            'from' => env('SMS_PROVIDER_DEFAULT_NUMBER'),
            'from1000' => env('SMS_PROVIDER_DEFAULT_NUMBER_1000'),
        ],

        'MEDIANA_RECHECK_SEND_MESSAGE_STATUS_INTERVAL' => 10,   // per minute
        'MEDIANA_RESEND_UNSUCCESSFUL_MESSAGE_INTERVAL' => 60,   // per minute
        'UNSUCCESSFUL_MESSAGE_NOTIFICATION_INTERVAL' => 30,     // per minute
        'RECHECK_SENT_MESSAGE_STATUS_PERIOD' => 30,             // per minute
        'NUMBER_OF_ITEM_IN_EACH_PAGE' => 10,

    ],
    'bonyad' => [
        'server' => env('BONYAD_SERVICE_SERVER'),
        'name' => env('BONYAD_SERVICE_NAME'),
        'password' => env('BONYAD_SERVICE_PASSWORD'),
        'token_time' => env('BONYAD_SERVICE_TOKEN_TIME', 3600),
    ],

    'accounting' => [
        'server' => env('ACCOUNTING_SERVER'),
    ],

    'forrest' => [
        'server' => env('FORREST_SERVER')
    ]
];
