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

    'ses' => [
        'key' => env('SES_KEY'),
        'secret' => env('SES_SECRET'),
        'region' => env('SES_REGION', 'us-east-1'),
    ],

    'sparkpost' => [
        'secret' => env('SPARKPOST_SECRET'),
    ],

    'stripe' => [
        'model' => App\User::class,
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
        'webhook' => [
            'secret' => env('STRIPE_WEBHOOK_SECRET'),
            'tolerance' => env('STRIPE_WEBHOOK_TOLERANCE', 300),
        ],
    ],
    'passport' => [
        'client_id' => env('PASSPORT_CLIENT_ID', 2),
        'client_secret' => env('PASSPORT_SECRET')
    ],

    'webhook_listeners' => [
        [
            'id' => 1,
            'url' => env('AGENTCIS_WEBHOOK_URL'),
            'name' => 'AgentcisApp',
            'secret' => env('AGENTCIS_WEBHOOK_SECRET')
        ],
//        [
//            'id' => 1,
//            'url' => env('GLOBALY_WEBHOOK_URL'),
//            'name' => 'AgentcisApp',
//            'secret' => env('GLOBALY_WEBHOOK_SECRET')
//        ]
    ],
    'agentcisapp' => [
        'domain' => env('AGENTCISAPP_DOMAIN', 'agentciapp.com')
    ],
    'agentcis' => [
        'domain' => env('AGENTCIS_DOMAIN', 'agentcis.com')
    ],
    'registration' => [
        'origin' => env('REGISTRATION_ORIGIN', 'https://join.agentcis.com')
    ]
];
