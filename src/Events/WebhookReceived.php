<?php

namespace Acapadev\Sdk\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WebhookReceived
{
    use Dispatchable, SerializesModels;

    /**
     * O tipo de evento recebido (ex: 'user.logout', 'role.updated').
     */
    public string $event;

    /**
     * O payload completo recebido.
     */
    public array $payload;

    /**
     * Create a new event instance.
     *
     * @param string $event
     * @param array $payload
     */
    public function __construct(string $event, array $payload)
    {
        $this->event = $event;
        $this->payload = $payload;
    }
}
