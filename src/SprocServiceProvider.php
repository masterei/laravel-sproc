<?php

namespace Masterei\Sproc;

use Illuminate\Support\ServiceProvider;

class SprocServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/sproc.php', 'sproc');
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([__DIR__.'/../config/sproc.php' => config_path('sproc.php')], 'sproc-config');
    }
}
