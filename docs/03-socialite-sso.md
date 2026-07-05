# Integração com Socialite (SSO)

O Acapadev SDK inclui um provedor customizado para o Laravel Socialite, permitindo que os utilizadores iniciem sessão na tua aplicação usando a conta central do Acapadev ID.

## Pré-requisitos
Certifica-te de que executaste `php artisan acapadev:install` e configuraste o teu `ACAPADEV_CLIENT_ID` e `ACAPADEV_CLIENT_SECRET` no ficheiro `.env`.

## Como usar

No teu controlador de autenticação, redireciona o utilizador para a central:

```php
use Laravel\Socialite\Facades\Socialite;

public function redirect()
{
    return Socialite::driver('acapadev')->redirect();
}
```

Na rota de callback (que deves ter configurado no painel do Acapadev ID), recebe o utilizador:

```php
public function callback()
{
    $acapadevUser = Socialite::driver('acapadev')->user();

    // Encontrar ou criar o utilizador na base de dados local
    $user = User::updateOrCreate(
        ['email' => $acapadevUser->getEmail()],
        [
            'name' => $acapadevUser->getName(),
            'acapadev_id' => $acapadevUser->getId(),
        ]
    );

    Auth::login($user);

    return redirect('/dashboard');
}
```
