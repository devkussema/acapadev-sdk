<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Acapadev ID URL
    |--------------------------------------------------------------------------
    |
    | O URL base do Acapadev ID, usado para comunicação via API e Webhooks.
    |
    */
    'url' => env('ACAPADEV_URL', 'https://id.acapadev.com'),

    /*
    |--------------------------------------------------------------------------
    | Acapadev Client Credentials
    |--------------------------------------------------------------------------
    |
    | Estas chaves são usadas para o Socialite (SSO) e chamadas de API nativas.
    |
    */
    'client_id' => env('ACAPADEV_CLIENT_ID'),
    'client_secret' => env('ACAPADEV_CLIENT_SECRET'),
    'redirect' => env('ACAPADEV_REDIRECT_URI', '/auth/acapadev/callback'),

    /*
    |--------------------------------------------------------------------------
    | Webhook Configuration
    |--------------------------------------------------------------------------
    |
    | Configurações para recepção de webhooks vindos do Acapadev ID.
    | O "secret" é usado para validar a assinatura criptográfica do payload,
    | garantindo que a requisição veio mesmo do servidor Acapadev.
    |
    */
    'webhooks' => [
        'secret' => env('ACAPADEV_WEBHOOK_SECRET'),
        
        // Define o path onde a tua aplicação vai ouvir os webhooks.
        'path' => env('ACAPADEV_WEBHOOK_PATH', 'acapadev/webhook'),

        // Mapeamento de eventos: disparar eventos Laravel nativos com base na action do webhook
        'events' => [
            // 'user.logout' => \App\Events\AcapadevUserLoggedOut::class,
            // 'user.role_updated' => \App\Events\AcapadevRoleUpdated::class,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Settings
    |--------------------------------------------------------------------------
    |
    | Tempos de expiração de cache (em segundos) para mitigar excesso de chamadas
    | à API Central. 
    |
    */
    'cache' => [
        'token_ttl' => env('ACAPADEV_CACHE_TOKEN_TTL', 3000),
        'roles_ttl' => env('ACAPADEV_CACHE_ROLES_TTL', 300),
        'session_ttl' => env('ACAPADEV_CACHE_SESSION_TTL', 300),
    ],
];
