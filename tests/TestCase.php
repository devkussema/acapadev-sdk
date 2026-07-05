<?php

namespace Acapadev\Sdk\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Acapadev\Sdk\AcapadevServiceProvider;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            AcapadevServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        // Add any necessary configuration overrides here
    }
}
