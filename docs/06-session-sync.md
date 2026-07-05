# Sincronização de Sessão (Single Logout)

Apesar do Acapadev SDK disponibilizar Webhooks que enviam notificações em tempo real quando um utilizador encerra a sessão centralmente, os Webhooks podem falhar (falhas de rede, servidor em baixo, etc.).

Para uma segurança absoluta ("Zero Trust"), o SDK fornece o middleware `EnsureAcapadevSession`.

## O que faz?
Sempre que um utilizador autenticado tentar abrir uma página protegida da tua aplicação, o middleware vai contactar invisivelmente o Acapadev ID para garantir que a conta dele continua ativa e a sessão não expirou. 
Para não deixar a tua aplicação lenta, a resposta desta validação é mantida em **Cache durante 5 minutos**.

## Como utilizar

Regista o middleware no ficheiro `bootstrap/app.php` (no Laravel 11+) criando um alias:

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'acapadev.session' => \Acapadev\Sdk\Http\Middleware\EnsureAcapadevSession::class,
    ]);
})
```

Depois, protege as tuas rotas principais aplicando este middleware, geralmente logo a seguir ao middleware `auth`:

```php
Route::middleware(['auth', 'acapadev.session'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);
});
```

Se o middleware detetar que o utilizador foi banido ou fez logout na central, ele vai forçar o encerramento da sessão local (`Auth::logout()`) e redirecionar o utilizador para a página de Login.
