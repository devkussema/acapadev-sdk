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
        $response->assertJson(['error' => 'Headers de segurança ausentes.']);
    }

    public function test_it_rejects_requests_with_invalid_signature()
    {
        $response = $this->withHeaders([
            'X-Acapadev-Signature' => 'fake-signature',
            'X-Acapadev-Timestamp' => now()->timestamp,
            'X-Acapadev-Delivery' => 'test-delivery-id',
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
            'user' => [
                'id' => 1,
                'email' => 'test@example.com',
            ],
            'timestamp' => now()->toIso8601String(),
        ];

        $secret = config('acapadev.webhooks.secret');
        $timestamp = now()->timestamp;
        $signaturePayload = $timestamp . '.' . json_encode($payload);
        $signature = hash_hmac('sha256', $signaturePayload, $secret);

        $response = $this->withHeaders([
            'X-Acapadev-Signature' => $signature,
            'X-Acapadev-Timestamp' => $timestamp,
            'X-Acapadev-Delivery' => 'test-delivery-id-123',
        ])->postJson('/acapadev/webhook', $payload);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'Webhook processado com sucesso.']);

        Event::assertDispatched(WebhookReceived::class, function ($event) use ($payload) {
            return $event->event === 'user.logout' && $event->payload['user']['id'] === $payload['user']['id'];
        });
    }
}
