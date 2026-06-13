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

    'local_ai' => [
        'endpoint' => env('LOCAL_AI_ENDPOINT', ''),
        'model' => env('LOCAL_AI_MODEL', 'mistral'),
    ],

    'ai' => [
        'provider' => env('AI_PROVIDER', 'gemini'),
        'model' => env('AI_MODEL', 'gemini-3.5-flash'),
        'gemini_endpoint' => env('GEMINI_API_ENDPOINT', ''),
        'gemini_api_key' => env('GEMINI_API_KEY', ''),
        'openai_endpoint' => env('OPENAI_API_ENDPOINT', 'https://api.openai.com/v1/responses'),
        'openai_api_key' => env('OPENAI_API_KEY', ''),
        'openai_model' => env('OPENAI_MODEL', env('AI_MODEL', 'gpt-4.1-mini')),
        'cache_ttl' => (int) env('AI_CACHE_TTL', 300),
        'auto_insights' => filter_var(env('AI_AUTO_INSIGHTS', false), FILTER_VALIDATE_BOOL),
    ],


];
