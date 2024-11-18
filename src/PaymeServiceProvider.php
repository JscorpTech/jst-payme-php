<?php

namespace JscorpTech\Payme;

use Illuminate\Support\ServiceProvider;

class PaymeServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->publishes([
            __DIR__ . "/../config/payme.php" => config_path("payme.php")
        ], "config");
    }
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . "/../migrations");
        $this->loadRoutesFrom(__DIR__ . "/../routes/api.php");
        $this->mergeConfigFrom(__DIR__ . "/../config/payme.php", "payme");
    }
}
