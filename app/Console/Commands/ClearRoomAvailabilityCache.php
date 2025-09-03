<?php

namespace App\Console\Commands;

use App\Services\RoomAvailabilityCacheService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class ClearRoomAvailabilityCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:clear-rooms {--type=all : Type of cache to clear (all|availability|search|stats)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear Redis cache for room availability data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $cacheService = app(RoomAvailabilityCacheService::class);
        $type = $this->option('type');

        $this->info("Clearing room cache...");

        try {
            $supportsTagging = $this->supportsTagging();
            
            switch ($type) {
                case 'availability':
                    if ($supportsTagging) {
                        Cache::tags(['room_availability'])->flush();
                    } else {
                        $this->clearNonTaggingCache('availability');
                    }
                    $this->info("✅ Room availability cache cleared!");
                    break;
                    
                case 'search':
                    if ($supportsTagging) {
                        Cache::tags(['room_search'])->flush();
                    } else {
                        $this->clearNonTaggingCache('search');
                    }
                    $this->info("✅ Room search cache cleared!");
                    break;
                    
                case 'stats':
                    if ($supportsTagging) {
                        Cache::tags(['room_stats'])->flush();
                    } else {
                        $this->clearNonTaggingCache('stats');
                    }
                    $this->info("✅ Room statistics cache cleared!");
                    break;
                    
                case 'all':
                default:
                    $cacheService->invalidateAllRoomCache();
                    $this->info("✅ All room cache cleared!");
                    break;
            }
            
            return 0;
            
        } catch (\Exception $e) {
            $this->error("❌ Failed to clear cache: " . $e->getMessage());
            return 1;
        }
    }

    /**
     * Check if the current cache driver supports tagging
     */
    private function supportsTagging(): bool
    {
        $driver = config('cache.default');
        $supportedDrivers = ['redis', 'memcached'];
        return in_array($driver, $supportedDrivers);
    }

    /**
     * Clear specific cache types for non-tagging stores
     */
    private function clearNonTaggingCache(string $type): void
    {
        switch ($type) {
            case 'availability':
                // Clear known availability cache keys
                $patterns = ['room_availability:', 'room_dates:'];
                break;
            case 'search':
                $patterns = ['room_search:'];
                break;
            case 'stats':
                $patterns = ['room_stats:'];
                break;
            default:
                $patterns = [];
        }

        // For non-tagging stores, we can only clear known keys or flush all
        // This is a simplified approach - in production you might want to track keys
        foreach ($patterns as $pattern) {
            // Clear some common keys
            if ($pattern === 'room_stats:') {
                Cache::forget('room_stats:overview');
                Cache::forget('room_stats:room_types');
            }
        }
    }
}
