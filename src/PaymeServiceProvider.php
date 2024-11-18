<?php

namespace Jscorptech\Payme;

use Illuminate\Support\ServiceProvider;

class PaymeServiceProvider extends ServiceProvider
{
    function register() {}
    function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . "/migrations");
    }
}
