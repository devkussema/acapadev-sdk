<?php

namespace Acapadev\Sdk\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class AcapadevApiClient
{
    /**
     * Get the base URL for the API.
     */
    protected function getBaseUrl(): string
    {
        return config('acapadev.url') . '/api/v1';
    }

    /**
     * Get an access token via Client Credentials Grant.
     */
    public function getAccessToken(): ?string
    {
        return Cache::remember('acapadev_client_token', 3000, function () {
            $response = Http::asForm()->post(config('acapadev.url') . '/oauth/token', [
                'grant_type' => 'client_credentials',
                'client_id' => config('acapadev.client_id'),
                'client_secret' => config('acapadev.client_secret'),
                'scope' => '*',
            ]);

            if ($response->successful()) {
                return $response->json('access_token');
            }

            return null;
        });
    }

    /**
     * Build the base HTTP client with authorization.
     */
    protected function client()
    {
        $token = $this->getAccessToken();

        return Http::baseUrl($this->getBaseUrl())
            ->withToken($token)
            ->acceptJson();
    }

    /**
     * Make a GET request to the Acapadev API.
     */
    public function get(string $endpoint, array $query = [])
    {
        return $this->client()->get($endpoint, $query)->json();
    }

    /**
     * Make a POST request to the Acapadev API.
     */
    public function post(string $endpoint, array $data = [])
    {
        return $this->client()->post($endpoint, $data)->json();
    }

    /**
     * Get roles for a specific user ID.
     */
    public function getUserRoles(int|string $userId): array
    {
        $response = $this->get("/users/{$userId}/roles");

        return $response['data'] ?? [];
    }
}
