# Acapadev API Client (Facade)

O SDK inclui uma ponte direta para a API central do Acapadev ID. Isto permite que a tua aplicação faça pedidos à API central autenticando-se automaticamente como uma máquina (Client Credentials).

## Exemplo de Uso

Para usares a API, basta chamares a Facade `Acapadev`:

```php
use Acapadev\Sdk\Facades\Acapadev;

// Fazer um pedido GET
$utilizadores = Acapadev::get('/users/active');

// Fazer um pedido POST
$resposta = Acapadev::post('/logs/register', [
    'action' => 'login',
    'app' => 'token.ao'
]);

// Buscar cargos de um utilizador específico
$cargos = Acapadev::getUserRoles($userId);
```

A Facade trata automaticamente de gerar e renovar o `access_token` usando o `ACAPADEV_CLIENT_ID` e `ACAPADEV_CLIENT_SECRET` configurados no teu `.env`. Todos os pedidos são feitos em JSON.
