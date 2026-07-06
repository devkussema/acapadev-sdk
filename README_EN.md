# Acapadev SDK

![Acapadev SDK](https://img.shields.io/badge/Acapadev-SDK-blue)
![Laravel](https://img.shields.io/badge/Laravel-10.x|11.x-red)
![PHP](https://img.shields.io/badge/PHP-8.2+-purple)

The **Acapadev SDK** is the official package for integrating your Laravel applications (Satellite Apps) with the Acapadev ID (Central Identity Server). 

It provides seamless integration for Single Sign-On (OAuth2), Machine-to-Machine (M2M) API communication, and Webhook synchronization.

## Features
- 🔐 **SSO Integration**: Pre-configured Laravel Socialite driver (`acapadev`).
- 🤖 **M2M API Client**: Background communication with token auto-refresh to fetch user roles and status.
- 📡 **Webhooks**: Built-in webhook receiver with replay protection and idempotency checks.
- 🚀 **Interactive Installer**: Easy to install via `php artisan acapadev:install`.

## Installation

1. Install the package via Composer:
```bash
composer require devkussema/acapadev-sdk
```

2. Run the interactive installer:
```bash
php artisan acapadev:install
```

You will be prompted to enter your **Client ID**, **Client Secret**, and **Webhook Secret**.

## Usage

### 1. Single Sign-On (Login)
To authenticate users via Acapadev ID, simply use the `acapadev` Socialite driver:

```php
use Laravel\Socialite\Facades\Socialite;

Route::get('/login/acapadev', function () {
    return Socialite::driver('acapadev')->redirect();
});

Route::get('/auth/acapadev/callback', function () {
    $user = Socialite::driver('acapadev')->user();
    // Log the user in...
});
```

### 2. User Roles (M2M)
Use the `HasAcapadevRoles` trait on your `User` model to seamlessly fetch and cache the user's roles from the central server.

```php
use Acapadev\Sdk\Traits\HasAcapadevRoles;

class User extends Authenticatable {
    use HasAcapadevRoles;
}

// Check if user has an admin role
if ($user->hasAcapadevRole('admin')) { ... }
```

### 3. Session Security
Protect your routes using the `acapadev.session` middleware. This middleware periodically verifies with the central server if the user's account is still active or has been suspended.

```php
Route::middleware(['auth', 'acapadev.session'])->group(function () {
    Route::get('/dashboard', ...);
});
```

## Documentation
For full documentation, run:
```bash
php artisan acapadev:docs
```
This will publish the complete documentation inside the `docs/acapadev` directory of your project.
