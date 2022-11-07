<?php

namespace Ojenra\Sproc\Tests;

use Ojenra\Sproc\SprocServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app)
    {
        return [
            SprocServiceProvider::class,
        ];
    }
}
