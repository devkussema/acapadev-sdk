<?php

namespace Acapadev\Sdk\Tests\Feature;

use Acapadev\Sdk\Tests\TestCase;
use Acapadev\Sdk\Events\WebhookReceived;
use Illuminate\Support\Facades\Event;

class WebhookTest extends TestCase
{
    public function test_it_rejects_requests_without_signature()
    {
        $response = $this->postJson('/acapadev/webhook', [
            'event' => 'user.logout',
        ]);

        $response->assertStatus(401);
        $response->assertJson(['error' => 'Assinatura ausente.']);
    }

    public function test_it_rejects_requests_with_invalid_signature()
    {
        $response = $this->withHeaders([
            'X-Acapadev-Signature' => 'fake-signature',
        ])->postJson('/acapadev/webhook', [
            'event' => 'user.logout',
        ]);

        $response->assertStatus(403);
        $response->assertJson(['error' => 'Assinatura inválida.']);
    }

    public function test_it_accepts_valid_webhooks_and_dispatches_event()
    {
        Event::fake();

        $payload = [
            'event' => 'user.logout',
            'user_id' => 1,
            'timestamp' => now()->toIso8601String(),
        ];

        $secret = config('acapadev.webhooks.secret');
        $signature = hash_hmac('sha256', json_encode($payload), $secret);

        $response = $this->withHeaders([
            'X-Acapadev-Signature' => $signature,
        ])->postJson('/acapadev/webhook', $payload);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'Webhook recebido com sucesso.']);

        Event::assertDispatched(WebhookReceived::class, function ($event) use ($payload) {
            return $event->event === 'user.logout' && $event->payload['user_id'] === $payload['user_id'];
        });
    }
}
