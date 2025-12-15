<?php

namespace Carloeusebi\RybbitTunnel;

use Illuminate\Support\ServiceProvider;

class Provider extends ServiceProvider
{
    public function boot(): void
    {
        $this->registerRoutes();
        $this->registerPublishing();
    }

    private function registerRoutes(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
    }

    private function registerPublishing(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/rybbit-tunnel.php' => config_path('rybbit-tunnel.php'),
            ], 'rybbit-tunnel-config');
        }
    }

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/rybbit-tunnel.php', 'rybbit-tunnel');
    }
}
