<?php

namespace App\Providers;

use App\Services\RoomAvailabilityCacheService;
use Illuminate\Support\ServiceProvider;

class RoomCacheServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(RoomAvailabilityCacheService::class, function ($app) {
            return new RoomAvailabilityCacheService();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Register console commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                \App\Console\Commands\WarmUpRoomAvailabilityCache::class,
                \App\Console\Commands\ClearRoomAvailabilityCache::class,
            ]);
        }
    }
}
