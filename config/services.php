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
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'idp' => [
        'enabled' => env('IDP_ENABLED', true),
        'base_url' => env('IDP_BASE_URL'),
        'auth_url' => env('IDP_AUTH_URL'),
        'auth_request_mode' => env('IDP_AUTH_REQUEST_MODE', 'authorize'),
        'login_path' => env('IDP_LOGIN_PATH', '/login'),
        'callback_url' => env('IDP_CALLBACK_URL'),
        'client_id' => env('IDP_CLIENT_ID'),
        'client_secret' => env('IDP_CLIENT_SECRET'),
        'token_url' => env('IDP_TOKEN_URL'),
        'me_url' => env('IDP_ME_URL'),
        'token_auth_mode' => env('IDP_TOKEN_AUTH_MODE', 'auto'),
        'verify_tls' => env('IDP_VERIFY_TLS', true),
        'enforce_state' => env('IDP_ENFORCE_STATE', false),
        'jwt_secret' => env('IDP_JWT_SECRET'),
        'jwt_algo' => env('IDP_JWT_ALGO', 'HS256'),
        'jwt_issuer' => env('IDP_JWT_ISSUER'),
        'jwt_audience' => env('IDP_JWT_AUDIENCE'),
        'jwt_azp' => env('IDP_JWT_AZP'),
        // 'fallback_email' => env('IDP_FALLBACK_EMAIL'),
    ],

];
