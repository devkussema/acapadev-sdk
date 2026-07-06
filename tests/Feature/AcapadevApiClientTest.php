<?php

namespace Acapadev\Sdk\Tests\Feature;

use Acapadev\Sdk\Tests\TestCase;
use Acapadev\Sdk\Services\AcapadevApiClient;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class AcapadevApiClientTest extends TestCase
{
    public function test_it_can_fetch_access_token()
    {
        Http::fake([
            '*/oauth/token' => Http::response(['access_token' => 'mocked-token-123'], 200),
        ]);

        $client = new AcapadevApiClient();
        $token = $client->getAccessToken();

        $this->assertEquals('mocked-token-123', $token);
        $this->assertEquals('mocked-token-123', Cache::get('acapadev_client_token'));
    }

    public function test_it_handles_failed_token_fetch()
    {
        Http::fake([
            '*/oauth/token' => Http::response(['error' => 'invalid_client'], 401),
        ]);

        $client = new AcapadevApiClient();
        $token = $client->getAccessToken(true);

        $this->assertNull($token);
    }
}
