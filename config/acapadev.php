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
    ],
];
