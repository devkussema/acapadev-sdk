<?php

namespace Acapadev\Sdk\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Acapadev\Sdk\Exceptions\AcapadevApiException;

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
     * Force refresh ignores cache and fetches a new token.
     */
    public function getAccessToken(bool $forceRefresh = false): ?string
    {
        $cacheKey = 'acapadev_client_token';
        $ttl = config('acapadev.cache.token_ttl', 3000);

        if ($forceRefresh) {
            Cache::forget($cacheKey);
        }

        return Cache::remember($cacheKey, $ttl, function () {
            try {
                $response = Http::asForm()->post(config('acapadev.url') . '/oauth/token', [
                    'grant_type' => 'client_credentials',
                    'client_id' => config('acapadev.client_id'),
                    'client_secret' => config('acapadev.client_secret'),
                    'scope' => '*',
                ]);

                if ($response->successful()) {
                    return $response->json('access_token');
                }

                Log::error('AcapadevApiClient: Failed to fetch access token.', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
            } catch (\Exception $e) {
                Log::error('AcapadevApiClient: Exception fetching access token.', [
                    'message' => $e->getMessage()
                ]);
            }

            return null;
        });
    }

    /**
     * Build the base HTTP client with authorization.
     */
    protected function client(bool $forceRefresh = false)
    {
        $token = $this->getAccessToken($forceRefresh);

        if (!$token) {
            throw new AcapadevApiException("Acapadev API: Could not retrieve a valid access token.");
        }

        return Http::baseUrl($this->getBaseUrl())
            ->withToken($token)
            ->acceptJson()
            ->timeout(15);
    }

    /**
     * Execute an API call with automatic token refresh on 401 Unauthorized.
     */
    protected function executeRequest(string $method, string $endpoint, array $data = [])
    {
        try {
            // First attempt
            $response = $this->client()->$method($endpoint, $data);

            // If unauthorized, token might have expired. Refresh and retry once.
            if ($response->status() === 401) {
                Log::info('AcapadevApiClient: Token expired or invalid. Refreshing...');
                $response = $this->client(true)->$method($endpoint, $data);
            }

            if (!$response->successful()) {
                Log::error("AcapadevApiClient: API request failed [{$method} {$endpoint}]", [
                    'status' => $response->status(),
                    'response' => $response->body(),
                ]);
                throw AcapadevApiException::fromResponse($response);
            }

            return $response->json();
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error("AcapadevApiClient: Connection error [{$method} {$endpoint}]", [
                'message' => $e->getMessage()
            ]);
            throw new AcapadevApiException("Acapadev API Connection Error: " . $e->getMessage(), 0, null, $e);
        }
    }

    /**
     * Make a GET request to the Acapadev API.
     */
    public function get(string $endpoint, array $query = [])
    {
        return $this->executeRequest('get', $endpoint, $query);
    }

    /**
     * Make a POST request to the Acapadev API.
     */
    public function post(string $endpoint, array $data = [])
    {
        return $this->executeRequest('post', $endpoint, $data);
    }

    /**
     * Get roles for a specific user ID.
     */
    public function getUserRoles(int|string $userId): array
    {
        try {
            $response = $this->get("/users/{$userId}/roles");
            return $response['data'] ?? [];
        } catch (AcapadevApiException $e) {
            // On failure, return empty roles to default to a safe state
            return [];
        }
    }
}
