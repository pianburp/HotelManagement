<?php

namespace App\Services;

use App\Models\Room;
use App\Models\RoomType;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RoomAvailabilityCacheService
{
    protected $cachePrefix = 'room_availability:';
    protected $searchPrefix = 'room_search:';
    protected $statsPrefix = 'room_stats:';
    protected $defaultTtl = 3600; // 1 hour
    protected $searchTtl = 1800; // 30 minutes
    protected $statsTtl = 600; // 10 minutes

    /**
     * Get cached room availability for specific dates
     */
    public function getRoomAvailability(int $roomId, string $checkIn, string $checkOut): ?bool
    {
        $cacheKey = $this->cachePrefix . "room:{$roomId}:from:{$checkIn}:to:{$checkOut}";
        
        try {
            if ($this->supportsTagging()) {
                return Cache::tags(['room_availability', "room:{$roomId}"])->get($cacheKey);
            } else {
                return Cache::get($cacheKey);
            }
        } catch (\Exception $e) {
            Log::warning("Failed to get room availability from cache", [
                'room_id' => $roomId,
                'check_in' => $checkIn,
                'check_out' => $checkOut,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Cache room availability for specific dates
     */
    public function cacheRoomAvailability(int $roomId, string $checkIn, string $checkOut, bool $isAvailable): void
    {
        $cacheKey = $this->cachePrefix . "room:{$roomId}:from:{$checkIn}:to:{$checkOut}";
        
        try {
            if ($this->supportsTagging()) {
                Cache::tags(['room_availability', "room:{$roomId}"])
                    ->put($cacheKey, $isAvailable, $this->defaultTtl);
            } else {
                Cache::put($cacheKey, $isAvailable, $this->defaultTtl);
            }
        } catch (\Exception $e) {
            Log::warning("Failed to cache room availability", [
                'room_id' => $roomId,
                'check_in' => $checkIn,
                'check_out' => $checkOut,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get cached search results
     */
    public function getSearchResults(array $searchParams): ?array
    {
        $cacheKey = $this->searchPrefix . md5(serialize($searchParams));
        
        try {
            if ($this->supportsTagging()) {
                return Cache::tags(['room_search'])->get($cacheKey);
            } else {
                return Cache::get($cacheKey);
            }
        } catch (\Exception $e) {
            Log::warning("Failed to get search results from cache", [
                'search_params' => $searchParams,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Cache search results
     */
    public function cacheSearchResults(array $searchParams, array $results): void
    {
        $cacheKey = $this->searchPrefix . md5(serialize($searchParams));
        
        try {
            if ($this->supportsTagging()) {
                Cache::tags(['room_search'])->put($cacheKey, $results, $this->searchTtl);
            } else {
                Cache::put($cacheKey, $results, $this->searchTtl);
            }
        } catch (\Exception $e) {
            Log::warning("Failed to cache search results", [
                'search_params' => $searchParams,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get cached room statistics
     */
    public function getRoomStats(): ?array
    {
        try {
            if ($this->supportsTagging()) {
                return Cache::tags(['room_stats'])->get($this->statsPrefix . 'overview');
            } else {
                return Cache::get($this->statsPrefix . 'overview');
            }
        } catch (\Exception $e) {
            Log::warning("Failed to get room stats from cache", ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Cache room statistics
     */
    public function cacheRoomStats(array $stats): void
    {
        try {
            if ($this->supportsTagging()) {
                Cache::tags(['room_stats'])->put($this->statsPrefix . 'overview', $stats, $this->statsTtl);
            } else {
                Cache::put($this->statsPrefix . 'overview', $stats, $this->statsTtl);
            }
        } catch (\Exception $e) {
            Log::warning("Failed to cache room stats", ['error' => $e->getMessage()]);
        }
    }

    /**
     * Get cached available rooms for date range
     */
    public function getAvailableRoomsForDateRange(string $checkIn, string $checkOut, array $filters = []): ?array
    {
        $filterHash = md5(serialize($filters));
        $cacheKey = $this->cachePrefix . "available:from:{$checkIn}:to:{$checkOut}:filters:{$filterHash}";
        
        try {
            if ($this->supportsTagging()) {
                return Cache::tags(['room_availability', 'available_rooms'])->get($cacheKey);
            } else {
                return Cache::get($cacheKey);
            }
        } catch (\Exception $e) {
            Log::warning("Failed to get available rooms from cache", [
                'check_in' => $checkIn,
                'check_out' => $checkOut,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Cache available rooms for date range
     */
    public function cacheAvailableRoomsForDateRange(string $checkIn, string $checkOut, array $filters, array $roomIds): void
    {
        $filterHash = md5(serialize($filters));
        $cacheKey = $this->cachePrefix . "available:from:{$checkIn}:to:{$checkOut}:filters:{$filterHash}";
        
        try {
            if ($this->supportsTagging()) {
                Cache::tags(['room_availability', 'available_rooms'])
                    ->put($cacheKey, $roomIds, $this->defaultTtl);
            } else {
                Cache::put($cacheKey, $roomIds, $this->defaultTtl);
            }
        } catch (\Exception $e) {
            Log::warning("Failed to cache available rooms", [
                'check_in' => $checkIn,
                'check_out' => $checkOut,
                'error' => $e->getMessage()
            ]);
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
     * Invalidate cache for specific room
     */
    public function invalidateRoomCache(int $roomId): void
    {
        try {
            if ($this->supportsTagging()) {
                Cache::tags(["room:{$roomId}"])->flush();
                Cache::tags(['room_availability'])->flush();
                Cache::tags(['room_search'])->flush();
                Cache::tags(['room_stats'])->flush();
                Cache::tags(['available_rooms'])->flush();
            } else {
                // For non-tagging cache stores, we need to clear specific keys
                $this->clearRoomSpecificKeys($roomId);
                // Clear all search and stats cache as a fallback
                $this->clearAllSearchAndStats();
            }
            
            Log::info("Cache invalidated for room", ['room_id' => $roomId]);
        } catch (\Exception $e) {
            Log::warning("Failed to invalidate room cache", [
                'room_id' => $roomId,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Invalidate all room-related cache
     */
    public function invalidateAllRoomCache(): void
    {
        try {
            if ($this->supportsTagging()) {
                Cache::tags(['room_availability'])->flush();
                Cache::tags(['room_search'])->flush();
                Cache::tags(['room_stats'])->flush();
                Cache::tags(['available_rooms'])->flush();
                Cache::tags(['room_types'])->flush();
            } else {
                // For non-tagging stores, clear with pattern matching
                $this->clearAllCacheByPattern();
            }
            
            Log::info("All room cache invalidated");
        } catch (\Exception $e) {
            Log::warning("Failed to invalidate all room cache", ['error' => $e->getMessage()]);
        }
    }

    /**
     * Clear room-specific cache keys for non-tagging stores
     */
    private function clearRoomSpecificKeys(int $roomId): void
    {
        // Clear availability cache for this room (we can't easily enumerate all keys)
        // So we'll clear known patterns
        $patterns = [
            $this->cachePrefix . "room:{$roomId}:*",
        ];

        foreach ($patterns as $pattern) {
            $this->clearCachePattern($pattern);
        }
    }

    /**
     * Clear all search and stats cache for non-tagging stores
     */
    private function clearAllSearchAndStats(): void
    {
        $patterns = [
            $this->searchPrefix . '*',
            $this->statsPrefix . '*',
        ];

        foreach ($patterns as $pattern) {
            $this->clearCachePattern($pattern);
        }
    }

    /**
     * Clear all cache by pattern for non-tagging stores
     */
    private function clearAllCacheByPattern(): void
    {
        $patterns = [
            $this->cachePrefix . '*',
            $this->searchPrefix . '*',
            $this->statsPrefix . '*',
            'room_types_*',
            'distinct_floors',
            'price_range',
        ];

        foreach ($patterns as $pattern) {
            $this->clearCachePattern($pattern);
        }
    }

    /**
     * Clear cache keys matching a pattern (for non-tagging stores)
     */
    private function clearCachePattern(string $pattern): void
    {
        try {
            $driver = config('cache.default');
            
            if ($driver === 'database') {
                // For database cache, we can't easily pattern match, so clear individual known keys
                $commonKeys = [
                    $this->statsPrefix . 'overview',
                    $this->statsPrefix . 'room_types',
                    'room_types_active',
                    'room_types_with_translations',
                    'distinct_floors',
                    'price_range',
                ];
                
                foreach ($commonKeys as $key) {
                    Cache::forget($key);
                }
            } elseif ($driver === 'file') {
                // For file cache, similar approach
                $this->clearFileCache();
            }
            // For other drivers, individual key deletion might be needed
        } catch (\Exception $e) {
            Log::warning("Failed to clear cache pattern", [
                'pattern' => $pattern,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Clear file cache (simplified approach)
     */
    private function clearFileCache(): void
    {
        try {
            // Just clear the entire cache for file driver
            Cache::flush();
        } catch (\Exception $e) {
            Log::warning("Failed to clear file cache", ['error' => $e->getMessage()]);
        }
    }

    /**
     * Warm up cache for popular date ranges
     */
    public function warmUpCache(): void
    {
        try {
            $startDate = Carbon::today();
            $endDate = $startDate->copy()->addMonths(3);
            
            // Get all active rooms
            $rooms = Room::where('status', 'available')->pluck('id');
            
            // Warm up cache for next 30 days in 1-3 night increments
            for ($days = 1; $days <= 30; $days++) {
                $checkIn = $startDate->copy()->addDays($days);
                
                foreach ([1, 2, 3] as $nights) {
                    $checkOut = $checkIn->copy()->addDays($nights);
                    
                    foreach ($rooms as $roomId) {
                        if (!$this->getRoomAvailability($roomId, $checkIn->format('Y-m-d'), $checkOut->format('Y-m-d'))) {
                            $room = Room::find($roomId);
                            if ($room) {
                                $isAvailable = $room->isAvailable($checkIn->format('Y-m-d'), $checkOut->format('Y-m-d'));
                                $this->cacheRoomAvailability($roomId, $checkIn->format('Y-m-d'), $checkOut->format('Y-m-d'), $isAvailable);
                            }
                        }
                    }
                }
            }
            
            Log::info("Cache warming completed");
        } catch (\Exception $e) {
            Log::error("Failed to warm up cache", ['error' => $e->getMessage()]);
        }
    }

    /**
     * Get cached room type statistics
     */
    public function getRoomTypeStats(): ?array
    {
        try {
            if ($this->supportsTagging()) {
                return Cache::tags(['room_stats'])->get($this->statsPrefix . 'room_types');
            } else {
                return Cache::get($this->statsPrefix . 'room_types');
            }
        } catch (\Exception $e) {
            Log::warning("Failed to get room type stats from cache", ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Cache room type statistics
     */
    public function cacheRoomTypeStats(): void
    {
        try {
            $stats = RoomType::with(['rooms' => function($query) {
                $query->select('room_type_id', 'status', DB::raw('count(*) as count'))
                      ->groupBy('room_type_id', 'status');
            }])->get()->map(function($roomType) {
                $statusCounts = $roomType->rooms->pluck('count', 'status');
                return [
                    'id' => $roomType->id,
                    'name' => $roomType->name,
                    'total_rooms' => $statusCounts->sum(),
                    'available' => $statusCounts->get('available', 0),
                    'reserved' => $statusCounts->get('reserved', 0),
                    'occupied' => $statusCounts->get('onboard', 0),
                    'maintenance' => $statusCounts->get('closed', 0),
                ];
            })->toArray();

            if ($this->supportsTagging()) {
                Cache::tags(['room_stats'])
                    ->put($this->statsPrefix . 'room_types', $stats, $this->statsTtl);
            } else {
                Cache::put($this->statsPrefix . 'room_types', $stats, $this->statsTtl);
            }
        } catch (\Exception $e) {
            Log::warning("Failed to cache room type stats", ['error' => $e->getMessage()]);
        }
    }

    /**
     * Get cache hit statistics
     */
    public function getCacheStats(): array
    {
        try {
            return [
                'availability_cache_size' => Cache::tags(['room_availability'])->get('cache_size', 0),
                'search_cache_size' => Cache::tags(['room_search'])->get('cache_size', 0),
                'stats_cache_size' => Cache::tags(['room_stats'])->get('cache_size', 0),
                'last_warmed' => Cache::get('cache_last_warmed'),
            ];
        } catch (\Exception $e) {
            Log::warning("Failed to get cache stats", ['error' => $e->getMessage()]);
            return [];
        }
    }
}
