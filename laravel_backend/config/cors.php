<?php

/**
 * @package App\Config
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI — La Bottega)
 * @date 2026-04-12
 * @purpose CORS config per SSO cross-subdomain .florenceegi.com
 */

return [

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        'https://la-bottega.florenceegi.com',
        'https://art.florenceegi.com',
        'https://hub.florenceegi.com',
        'https://florenceegi.com',
    ],

    'allowed_origins_patterns' => [
        '#^https://.*\.florenceegi\.com$#',
    ],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,

];
