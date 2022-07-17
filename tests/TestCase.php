<?php

namespace Masterei\Sproc\Tests;

use Masterei\Sproc\SprocServiceProvider;
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
