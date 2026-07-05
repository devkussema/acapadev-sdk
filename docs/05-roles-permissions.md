# Cargos e Permissões (Roles)

Para garantires que os utilizadores da tua aplicação satélite herdam os cargos estipulados centralmente no Acapadev ID, o SDK oferece uma Trait muito conveniente.

## Como configurar

Abre o modelo `User` da tua aplicação (geralmente `app/Models/User.php`) e adiciona a Trait `HasAcapadevRoles`:

```php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Acapadev\Sdk\Traits\HasAcapadevRoles;

class User extends Authenticatable
{
    use HasAcapadevRoles;
    
    // ...
}
```

## Como usar

A Trait adiciona métodos poderosos ao teu utilizador. Ela vai comunicar com o Acapadev ID de forma invisível e guardar o resultado em Cache durante 5 minutos para otimizar o desempenho.

```php
// Obter um array com todos os cargos do utilizador na central
$cargos = $user->getAcapadevRoles();

// Verificar se o utilizador tem um cargo específico
if ($user->hasAcapadevRole('admin_financeiro')) {
    // Permitir acesso
}

// Verificar se tem pelo menos um de vários cargos
if ($user->hasAnyAcapadevRole(['admin', 'gestor'])) {
    // Permitir acesso
}

// Limpar a cache manualmente (por exemplo, quando recebes um Webhook de atualização)
$user->clearAcapadevRolesCache();
```
