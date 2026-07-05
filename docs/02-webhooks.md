# Webhooks e Eventos

A comunicação principal entre o **Acapadev ID** e a tua aplicação faz-se através de Webhooks silenciosos.

Sempre que acontece algo crucial na rede (ex: um administrador revoga o acesso do Utilizador X, ou o Utilizador Y mudou de departamento), o Acapadev ID envia um pedido `POST` invisível para a tua aplicação.

## A Rota Automática
A rota é criada magicamente pelo pacote no endereço estático: `/acapadev/webhook`.
Tens de configurar este URL no painel principal da tua aplicação lá no Acapadev ID.

## O Evento `WebhookReceived`
Tu não precisas de validar as chaves, não precisas de descodificar o JSON e não precisas de gerir respostas HTTP. O pacote trata de tudo isto. A tua única responsabilidade é "Ouvir" o evento que o pacote grita dentro do teu código.

### Exemplo de Listener
```php
use Acapadev\Sdk\Events\WebhookReceived;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;

public function boot(): void
{
    Event::listen(WebhookReceived::class, function (WebhookReceived $webhook) {
        if ($webhook->event === 'user.logout') {
            Log::info("Utilizador {$webhook->payload['user_id']} fez logout na central!");
            // Destruir as sessões do utilizador aqui
        }
    });
}
```
