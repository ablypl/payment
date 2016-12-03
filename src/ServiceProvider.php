<?php

namespace Lari\Payments;

use Illuminate\Support\ServiceProvider as Base;

class ServiceProvider extends Base
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__.'/../database');
    }
}
