<?php

namespace Masterei\Sproc;

use Illuminate\Support\ServiceProvider;

class SprocServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/sproc.php', 'sproc'
        );
    }

    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/sproc.php' => config_path('sproc.php'),
        ], 'sproc-config');
    }
}
