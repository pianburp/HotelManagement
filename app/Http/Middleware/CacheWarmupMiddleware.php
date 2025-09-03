<?php

namespace App\Http\Middleware;

use App\Services\RoomAvailabilityCacheService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CacheWarmupMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Only warm cache on room-related requests
        if ($this->shouldWarmCache($request)) {
            $this->warmCacheInBackground();
        }

        return $response;
    }

    /**
     * Determine if cache should be warmed for this request
     */
    private function shouldWarmCache(Request $request): bool
    {
        // Only warm cache for room-related routes
        $roomRoutes = [
            'user.rooms.index',
            'user.rooms.search',
            'user.rooms.show',
            'admin.rooms.index',
            'api.rooms.availability'
        ];

        $routeName = $request->route()?->getName();
        
        return in_array($routeName, $roomRoutes) && 
               !Cache::has('cache_warmed_recently');
    }

    /**
     * Warm cache in the background
     */
    private function warmCacheInBackground(): void
    {
        try {
            // Prevent multiple cache warming operations
            if (Cache::add('cache_warmed_recently', true, 600)) { // 10 minutes
                
                // Use Laravel's queue if available, otherwise run synchronously
                if (config('queue.default') !== 'sync') {
                    dispatch(function () {
                        app(RoomAvailabilityCacheService::class)->warmUpCache();
                    })->onQueue('cache');
                } else {
                    // Run in background without blocking response
                    fastcgi_finish_request();
                    app(RoomAvailabilityCacheService::class)->warmUpCache();
                }
                
                Log::info('Cache warming initiated');
            }
        } catch (\Exception $e) {
            Log::warning('Failed to initiate cache warming', ['error' => $e->getMessage()]);
        }
    }
}
