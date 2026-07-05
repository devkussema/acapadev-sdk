<?php

namespace Acapadev\Sdk\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
        
        if (!$signature) {
            return response()->json(['error' => 'Assinatura ausente.'], 401);
        }

        $secret = config('acapadev.webhooks.secret');
        
        if (empty($secret)) {
            Log::warning('Acapadev SDK: Tentativa de recepção de webhook, mas o ACAPADEV_WEBHOOK_SECRET não está configurado.');
            return response()->json(['error' => 'Configuração do servidor inválida.'], 500);
        }

        $payload = $request->getContent();
        $expectedSignature = hash_hmac('sha256', $payload, $secret);

        if (!hash_equals($expectedSignature, $signature)) {
            Log::warning('Acapadev SDK: Assinatura de webhook inválida.', ['ip' => $request->ip()]);
            return response()->json(['error' => 'Assinatura inválida.'], 403);
        }

        return $next($request);
    }
}
