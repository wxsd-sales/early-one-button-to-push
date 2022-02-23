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

    'microsoft' => [
        'graph_api_url' => env('GRAPH_API_URL', 'https://graph.microsoft.com/v1.0')
    ],

    'azure' => [
        'client_id' => env('AZURE_CLIENT_ID'),
        'client_secret' => env('AZURE_CLIENT_SECRET'),
        'redirect' => env('AZURE_REDIRECT_URI', '/auth/azure/callback'),
        'tenant' => env('AZURE_TENANT_ID'),
        'logout_url' => 'https://login.microsoftonline.com/' . env('AZURE_TENANT_ID') . '/oauth2/v2.0/logout?post_logout_redirect_uri=',
        'proxy' => env('PROXY')  // optionally
    ],

    'webex' => [
        'api_url' => env('WEBEX_API_URL', 'https://webexapis.com/v1'),
        'client_id' => env('WEBEX_CLIENT_ID'),
        'client_secret' => env('WEBEX_CLIENT_SECRET'),
        'redirect' => env('WEBEX_REDIRECT_URI', '/auth/webex/callback'),
        'bot_id' => env('WEBEX_BOT_ID'),
        'bot_token' => env('WEBEX_BOT_TOKEN'),
        'notification_channel_url' => env('WEBEX_NOTIFICATION_CHANNEL_URL', 'https://webexapis.com/v1/messages'),
        'notification_channel_id' => env('WEBEX_NOTIFICATION_CHANNEL_ID'),
        'notification_channel_token' => env('WEBEX_NOTIFICATION_CHANNEL_TOKEN')
    ],

];
