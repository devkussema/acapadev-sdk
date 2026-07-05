# Acapadev SDK para Laravel

Bem-vindo ao **Acapadev SDK**, a ferramenta oficial para integrar aplicações satélite (como o *Token.ao*) ao ecossistema central do **Acapadev ID**.

Este pacote fornece uma base sólida, rápida e segura para que a tua aplicação possa comunicar com o Acapadev ID sem que tenhas de escrever código repetitivo. Com um único comando, a tua aplicação fica configurada para receber Webhooks, validar assinaturas criptográficas e escutar eventos nativos em tempo real.

---

## 🚀 Funcionalidades

- **Configuração Automática (Plug & Play):** Comando Artisan `php artisan acapadev:install` para publicar e configurar tudo o que precisas.
- **Segurança de Webhooks Integrada:** Middleware de verificação de assinatura HMAC SHA256 (garante que os dados vieram mesmo do Acapadev ID e não de um atacante).
- **Controlador Interno de Webhooks:** Tratamento padronizado de payloads do Acapadev e conversão em Eventos Nativos do Laravel.
- **Pronto para Integração SSO:** (Em breve) Base preparada para integração direta com o Laravel Socialite.

---

## 📦 Requisitos

- **PHP:** 8.2 ou superior
- **Laravel:** 11.x ou superior

---

## 🛠️ Instalação

1. Requer o pacote através do Composer:
   ```bash
   composer require devkussema/acapadev-sdk
   ```

2. Executa o comando de instalação mágico do Acapadev:
   ```bash
   php artisan acapadev:install
   ```

3. O comando irá publicar o ficheiro `config/acapadev.php`. Em seguida, abre o teu ficheiro `.env` e configura as variáveis obrigatórias:
   ```dotenv
   ACAPADEV_URL=https://id.acapadev.com
   ACAPADEV_WEBHOOK_SECRET=o_teu_segredo_gerado_no_acapadev_id
   ```

---

## 🔗 Integração com Webhooks

O Acapadev ID notifica as aplicações satélite sempre que ocorrem ações cruciais (exemplo: utilizador fez logout central, utilizador mudou de cargo/role, permissões alteradas, etc.).

### 1. Rota Disponibilizada Automáticamente
O pacote expõe, por defeito, a rota `POST /acapadev/webhook`.
Tens de configurar este exato URL (ex: `https://teu-dominio.ao/acapadev/webhook`) no painel do programador do teu Acapadev ID.

### 2. Isenção da Proteção CSRF
Como os webhooks são pedidos externos (S2S - Server to Server), o Laravel precisa de ignorar a validação CSRF para esta rota.
Se estiveres a usar o Laravel 11, vai ao ficheiro `bootstrap/app.php` e adiciona a exclusão:

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->validateCsrfTokens(except: [
        'acapadev/webhook', // Ignorar CSRF para webhooks
    ]);
})
```

### 3. Ouvir os Eventos Nativos

Quando um webhook válido e seguro chega, o SDK não toma decisões de negócio cegas. Em vez disso, ele converte o webhook num Evento Nativo do Laravel: `Acapadev\Sdk\Events\WebhookReceived`.

Para reagires a estes eventos na tua aplicação, podes registar um Listener ou usar Closures (ex: no teu `AppServiceProvider`):

```php
use Acapadev\Sdk\Events\WebhookReceived;
use Illuminate\Support\Facades\Event;

public function boot(): void
{
    Event::listen(WebhookReceived::class, function (WebhookReceived $webhook) {
        $evento = $webhook->event; // Ex: 'user.logout'
        $dados = $webhook->payload; // O Array com todos os dados enviados
        
        if ($evento === 'user.logout') {
            // Lógica para terminar a sessão do utilizador na tua aplicação local
            // Exemplo: Session::forget('user_id');
        }
    });
}
```

---

## 🔒 Segurança Adicional (Criptografia)

Todos os webhooks incluem um cabeçalho HTTP obrigatório: `X-Acapadev-Signature`.
O nosso middleware `VerifyWebhookSignature` bloqueia silenciosamente todos os pedidos que não possuam uma assinatura HMAC válida gerada a partir do teu `ACAPADEV_WEBHOOK_SECRET`. Se vires erros HTTP 403 (Forbidden) ou HTTP 401, verifica se os segredos correspondem em ambos os lados.

---

## 👨‍💻 Desenvolvido por
**Equipa Acapadev** | Augusto Kussema - Engenharia e Arquitetura de Software
