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

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | EGI Internal API
    |--------------------------------------------------------------------------
    */
    'egi' => [
        'url' => env('EGI_API_URL', 'http://localhost:8000/api'),
        'token' => env('EGI_API_TOKEN'),
        'timeout' => 30,
        'retry' => 2,
    ],

    'egi_credential' => [
        'url' => env('EGI_CREDENTIAL_API_URL', 'http://localhost:3000/api'),
        'token' => env('EGI_CREDENTIAL_API_TOKEN'),
        'timeout' => 30,
    ],

    /*
    |--------------------------------------------------------------------------
    | OpenAI (Embeddings)
    |--------------------------------------------------------------------------
    */
    'openai' => [
        'api_key' => env('OPENAI_API_KEY'),
        'base_url' => env('OPENAI_BASE_URL', 'https://api.openai.com/v1'),
        'timeout' => 30,
    ],

    /*
    |--------------------------------------------------------------------------
    | Voyage AI (Reranker primario)
    |--------------------------------------------------------------------------
    */
    'voyage' => [
        'api_key' => env('VOYAGE_API_KEY'),
        'base_url' => env('VOYAGE_BASE_URL', 'https://api.voyageai.com/v1'),
        'timeout' => 15,
    ],

    /*
    |--------------------------------------------------------------------------
    | Cohere (Reranker fallback)
    |--------------------------------------------------------------------------
    */
    'cohere' => [
        'api_key' => env('COHERE_API_KEY'),
        'base_url' => env('COHERE_BASE_URL', 'https://api.cohere.ai/v1'),
        'timeout' => 15,
    ],

];
