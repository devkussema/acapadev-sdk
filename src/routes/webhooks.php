<?php

use Illuminate\Support\Facades\Route;
use Acapadev\Sdk\Http\Controllers\WebhookController;
use Acapadev\Sdk\Http\Middleware\VerifyWebhookSignature;

Route::post(config('acapadev.webhooks.path', 'acapadev/webhook'), [WebhookController::class, 'handle'])
    ->middleware([
        VerifyWebhookSignature::class,
    ])
    ->name('acapadev.webhook');
