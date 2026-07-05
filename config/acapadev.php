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
