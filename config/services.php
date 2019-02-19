<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, Mandrill, and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
    */

    'authy' => [
        'secret' => env('AUTHY_SECRET'),
    ],

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN', 'sandbox8fae34caa5e74d3ba9dbece796b6a618.mailgun.org'),
        'secret' => env('MAILGUN_SECRET', 'ab2a9e01c470b1292f8e664f4168e040-9ce9335e-af2ce521'),
    ],

    'ses' => [
        'key'    => env('SES_KEY'),
        'secret' => env('SES_SECRET'),
        'region' => 'us-east-1',
    ],

    'stripe' => [
        'model'  => App\User::class,
        'key'    => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
    ],
    'bugsnag' => [
        'api_key' => env('NOVA_BUGSNAG_API_KEY'),
        'project_id' => env('NOVA_BUGSNAG_PROJECT_ID'),
        'account_slug' => env('NOVA_BUGSNAG_ACCOUNT_SLUG'),
    ]
];
