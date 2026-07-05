<?php

namespace Acapadev\Sdk\Socialite;

use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\ProviderInterface;
use Laravel\Socialite\Two\User;

class AcapadevProvider extends AbstractProvider implements ProviderInterface
{
    /**
     * Get the authentication URL for the provider.
     *
     * @param  string  $state
     * @return string
     */
    protected function getAuthUrl($state)
    {
        return $this->buildAuthUrlFromBase(config('acapadev.url') . '/oauth/authorize', $state);
    }

    /**
     * Get the token URL for the provider.
     *
     * @return string
     */
    protected function getTokenUrl()
    {
        return config('acapadev.url') . '/oauth/token';
    }

    /**
     * Get the raw user for the given access token.
     *
     * @param  string  $token
     * @return array
     */
    protected function getUserByToken($token)
    {
        $response = $this->getHttpClient()->get(config('acapadev.url') . '/api/user', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ],
        ]);

        return json_decode($response->getBody(), true);
    }

    /**
     * Map the raw user array to a Socialite User instance.
     *
     * @param  array  $user
     * @return \Laravel\Socialite\Two\User
     */
    protected function mapUser(array $user)
    {
        return (new User)->setRaw($user)->map([
            'id'       => $user['id'] ?? null,
            'nickname' => $user['username'] ?? null,
            'name'     => $user['name'] ?? null,
            'email'    => $user['email'] ?? null,
            'avatar'   => $user['avatar'] ?? null,
        ]);
    }
}
