<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\PermissionGeneratorService;

class PermissionGeneratorServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(PermissionGeneratorService::class, function ($app) {
            return new PermissionGeneratorService();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Any additional bootstrap logic
    }
}