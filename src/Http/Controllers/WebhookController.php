<?php

namespace Acapadev\Sdk\Http\Controllers;

use Illuminate\Routing\Controller;
use Acapadev\Sdk\Events\WebhookReceived;
use Acapadev\Sdk\Http\Requests\AcapadevWebhookRequest;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    /**
     * Handle incoming webhooks from Acapadev ID.
     */
    public function handle(AcapadevWebhookRequest $request)
    {
        $payload = $request->validated();
        $eventName = $payload['event'];

        Log::info("Acapadev SDK: Processando webhook [{$eventName}]", ['user_id' => $payload['user']['id'] ?? null]);

        // Disparar o evento nativo geral (já existente)
        event(new WebhookReceived($eventName, $payload));

        // Mapeamento dinâmico (Custom Events) - Ponto 10
        $customEvents = config('acapadev.webhooks.events', []);
        
        if (isset($customEvents[$eventName]) && class_exists($customEvents[$eventName])) {
            $eventClass = $customEvents[$eventName];
            event(new $eventClass($payload));
        }

        return response()->json(['status' => 'Webhook processado com sucesso.']);
    }
}
