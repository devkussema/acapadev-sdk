<?php

namespace Acapadev\Sdk\Facades;

use Illuminate\Support\Facades\Facade;
use Acapadev\Sdk\Services\AcapadevApiClient;

/**
 * @method static mixed get(string $endpoint, array $query = [])
 * @method static mixed post(string $endpoint, array $data = [])
 * @method static array getUserRoles(int|string $userId)
 *
 * @see \Acapadev\Sdk\Services\AcapadevApiClient
 */
class Acapadev extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return AcapadevApiClient::class;
    }
}
