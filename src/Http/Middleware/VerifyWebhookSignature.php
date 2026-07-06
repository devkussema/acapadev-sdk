<?php

namespace Acapadev\Sdk\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class VerifyWebhookSignature
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $signature = $request->header('X-Acapadev-Signature');
        $timestamp = $request->header('X-Acapadev-Timestamp');
        $deliveryId = $request->header('X-Acapadev-Delivery');
        
        if (!$signature || !$timestamp || !$deliveryId) {
            return response()->json(['error' => 'Headers de segurança ausentes.'], 401);
        }

        // Proteção contra Replay Attack (Tolerância de 5 minutos)
        if (now()->timestamp - (int) $timestamp > 300) {
            Log::warning('Acapadev SDK: Webhook rejeitado por timeout (Replay Attack).', ['ip' => $request->ip()]);
            return response()->json(['error' => 'Timestamp expirado.'], 403);
        }

        // Deduplicação (Idempotency)
        $cacheKey = "acapadev_webhook_{$deliveryId}";
        if (Cache::has($cacheKey)) {
            Log::info("Acapadev SDK: Webhook duplicado ignorado ({$deliveryId}).");
            return response()->json(['message' => 'Webhook já processado.'], 200);
        }

        $secret = config('acapadev.webhooks.secret');
        
        if (empty($secret)) {
            Log::warning('Acapadev SDK: Tentativa de recepção de webhook, mas o ACAPADEV_WEBHOOK_SECRET não está configurado.');
            return response()->json(['error' => 'Configuração do servidor inválida.'], 500);
        }

        $payload = $request->getContent();
        // A assinatura HMAC é feita concatenando o Timestamp ao JSON (timestamp.json)
        $expectedSignature = hash_hmac('sha256', $timestamp . '.' . $payload, $secret);

        if (!hash_equals($expectedSignature, $signature)) {
            Log::warning('Acapadev SDK: Assinatura de webhook inválida.', ['ip' => $request->ip()]);
            return response()->json(['error' => 'Assinatura inválida.'], 403);
        }

        // Marcar este webhook como processado (expira em 24h para libertar espaço no Cache)
        Cache::put($cacheKey, true, now()->addDay());

        return $next($request);
    }
}
