<?php

namespace App\Console\Commands;

use App\Services\RoomAvailabilityCacheService;
use Illuminate\Console\Command;

class WarmUpRoomAvailabilityCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:warm-rooms {--days=30 : Number of days to warm up} {--force : Force cache warming even if recently warmed}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Warm up the Redis cache for room availability data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $cacheService = app(RoomAvailabilityCacheService::class);
        $days = $this->option('days');
        $force = $this->option('force');

        $this->info("Starting cache warm-up for room availability...");
        
        $startTime = microtime(true);
        
        try {
            if ($force) {
                $this->warn("Force option enabled - clearing existing cache first...");
                $cacheService->invalidateAllRoomCache();
            }
            
            $cacheService->warmUpCache();
            
            $endTime = microtime(true);
            $duration = round($endTime - $startTime, 2);
            
            $this->info("✅ Cache warm-up completed successfully in {$duration} seconds!");
            
            // Display cache statistics
            $stats = $cacheService->getCacheStats();
            $this->table(
                ['Metric', 'Value'],
                [
                    ['Availability Cache Size', $stats['availability_cache_size'] ?? 'N/A'],
                    ['Search Cache Size', $stats['search_cache_size'] ?? 'N/A'],
                    ['Stats Cache Size', $stats['stats_cache_size'] ?? 'N/A'],
                    ['Last Warmed', $stats['last_warmed'] ?? 'Never'],
                ]
            );
            
        } catch (\Exception $e) {
            $this->error("❌ Cache warm-up failed: " . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
