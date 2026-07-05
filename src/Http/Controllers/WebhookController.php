<?php

namespace Acapadev\Sdk\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Acapadev\Sdk\Events\WebhookReceived;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    /**
     * Handle incoming webhooks from Acapadev ID.
     */
    public function handle(Request $request)
    {
        $payload = $request->all();

        if (!isset($payload['event'])) {
            return response()->json(['error' => 'Formato de webhook inválido.'], 400);
        }

        // Emitir o evento nativo do Laravel para que a aplicação satélite possa ouvir
        event(new WebhookReceived($payload['event'], $payload));

        return response()->json(['status' => 'Webhook recebido com sucesso.']);
    }
}
