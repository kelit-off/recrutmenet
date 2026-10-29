<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Resend, Postmark, AWS, and more. This file provides the de facto
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

    'discord' => [
        'client_id' => env('DISCORD_CLIENT_ID'),
        'client_secret' => env('DISCORD_CLIENT_SECRET'),
        'redirect' => env('DISCORD_REDIRECT_URI', '/auth/discord/callback'),
        // Comptes qui reçoivent les rôles Admin + DRH à leur première connexion.
        'admin_ids' => array_values(array_filter(array_map('trim', explode(',', (string) env('ADMIN_DISCORD_IDS', ''))))),
        'webhooks' => [
            'direction' => env('DISCORD_WEBHOOK_DIRECTION'),
            'hr' => env('DISCORD_WEBHOOK_DRH'),
            'sectors' => env('DISCORD_WEBHOOK_SECTORS'),
            // Salon où les candidats peuvent être mentionnés (<@id>). Par défaut : aucun.
            'candidates' => env('DISCORD_WEBHOOK_CANDIDATES'),
        ],
    ],
];
