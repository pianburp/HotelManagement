# Redis Caching for Hotel Management System

This document explains the Redis caching implementation for efficient room availability retrieval and enhanced system performance.

## Overview

The Redis caching system provides:
- Fast room availability lookup
- Cached search results
- Room statistics caching
- Automatic cache invalidation
- Background cache warming
- Performance monitoring

## Installation & Setup

### 1. Install Redis

**Windows (using Chocolatey):**
```bash
choco install redis-64
```

**Or download from:** https://github.com/microsoftarchive/redis/releases

### 2. Configure Environment

Copy the Redis configuration to your `.env` file:
```bash
copy .env.redis .env.additional
```

Add these lines to your `.env`:
```env
CACHE_STORE=redis
REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
REDIS_DB=0
REDIS_CACHE_DB=1
QUEUE_CONNECTION=redis
```

### 3. Install PHP Redis Extension

Install the PHP Redis extension for optimal performance:
```bash
# Using PECL
pecl install redis

# Or for Windows with XAMPP/Laragon
# Download php_redis.dll and add to php.ini:
extension=redis
```

### 4. Start Redis Service

```bash
# Windows
redis-server

# Or as a service
net start Redis
```

## Usage

### Console Commands

#### Warm Up Cache
```bash
# Warm up cache for next 30 days
php artisan cache:warm-rooms

# Warm up for specific number of days
php artisan cache:warm-rooms --days=60

# Force cache refresh
php artisan cache:warm-rooms --force
```

#### Clear Cache
```bash
# Clear all room-related cache
php artisan cache:clear-rooms

# Clear specific cache types
php artisan cache:clear-rooms --type=availability
php artisan cache:clear-rooms --type=search
php artisan cache:clear-rooms --type=stats
```

### Programmatic Usage

#### Using the Cache Service
```php
use App\Services\RoomAvailabilityCacheService;

$cacheService = app(RoomAvailabilityCacheService::class);

// Check room availability
$isAvailable = $cacheService->getRoomAvailability($roomId, $checkIn, $checkOut);

// Cache search results
$cacheService->cacheSearchResults($searchParams, $results);

// Invalidate cache for a room
$cacheService->invalidateRoomCache($roomId);
```

#### Direct Cache Usage
```php
use Illuminate\Support\Facades\Cache;

// Cache with tags
Cache::tags(['room_availability'])->put('key', $value, 3600);

// Get cached data
$data = Cache::tags(['room_availability'])->get('key');

// Clear tagged cache
Cache::tags(['room_availability'])->flush();
```

## Cache Strategies

### 1. Room Availability Cache
- **TTL:** 1 hour
- **Tags:** `room_availability`, `room:{id}`
- **Key Format:** `room_availability:room:{id}:from:{date}:to:{date}`

### 2. Search Results Cache
- **TTL:** 30 minutes
- **Tags:** `room_search`
- **Key Format:** `room_search:{hash_of_params}`

### 3. Statistics Cache
- **TTL:** 10 minutes
- **Tags:** `room_stats`
- **Key Format:** `room_stats:overview`

### 4. Room Types Cache
- **TTL:** 1 hour
- **Tags:** `room_types`
- **Key Format:** `room_types_active`

## Cache Invalidation

Cache is automatically invalidated when:

### Room Changes
- Room status updates
- Room creation/deletion
- Room type assignment changes

### Booking Changes
- New booking creation
- Booking status updates
- Booking cancellation
- Check-in/check-out operations

### Room Type Changes
- Price updates
- Amenity changes
- Availability status changes

## Performance Optimizations

### 1. Cache Warming
```php
// Automatic warming on first request
// Background warming via middleware
// Scheduled warming via cron job

// Add to scheduler in app/Console/Kernel.php
$schedule->command('cache:warm-rooms')->hourly();
```

### 2. Batch Operations
```php
// Cache multiple rooms at once
$rooms = Room::where('status', 'available')->get();
foreach ($rooms as $room) {
    $cacheService->cacheRoomAvailability($room->id, $checkIn, $checkOut, $isAvailable);
}
```

### 3. Background Processing
```php
// Queue cache warming
dispatch(function () {
    app(RoomAvailabilityCacheService::class)->warmUpCache();
})->onQueue('cache');
```

## Monitoring & Debugging

### Cache Statistics
```php
$cacheService = app(RoomAvailabilityCacheService::class);
$stats = $cacheService->getCacheStats();
```

### Logging
Enable cache logging in `config/room_cache.php`:
```php
'monitoring' => [
    'log_cache_hits' => true,
    'log_cache_misses' => true,
    'track_performance' => true,
],
```

### Redis Monitoring
```bash
# Connect to Redis CLI
redis-cli

# Monitor real-time commands
MONITOR

# Get cache info
INFO

# Check memory usage
INFO memory

# List all keys
KEYS *
```

## Configuration

### Cache TTL Settings
Edit `config/room_cache.php`:
```php
'ttl' => [
    'availability' => 3600, // 1 hour
    'search' => 1800,       // 30 minutes
    'stats' => 600,         // 10 minutes
],
```

### Performance Tuning
```php
'performance' => [
    'batch_size' => 100,
    'max_execution_time' => 300,
    'memory_limit' => '256M',
],
```

## Troubleshooting

### Common Issues

1. **Redis Connection Failed**
   ```bash
   # Check if Redis is running
   redis-cli ping
   # Should return: PONG
   ```

2. **Cache Not Working**
   ```bash
   # Clear Laravel cache
   php artisan cache:clear
   
   # Check Redis connection
   php artisan tinker
   Cache::put('test', 'value');
   Cache::get('test');
   ```

3. **Memory Issues**
   ```bash
   # Check Redis memory usage
   redis-cli info memory
   
   # Set memory limit in redis.conf
   maxmemory 256mb
   maxmemory-policy allkeys-lru
   ```

4. **Performance Issues**
   ```bash
   # Check slow queries
   redis-cli slowlog get 10
   
   # Monitor operations
   redis-cli monitor
   ```

### Debug Commands
```bash
# Test room availability caching
php artisan tinker
$room = App\Models\Room::first();
$room->isAvailable('2025-08-30', '2025-09-01');

# Check cache contents
Cache::tags(['room_availability'])->get('room_availability:room:1:from:2025-08-30:to:2025-09-01');

# Test cache invalidation
$room->update(['status' => 'maintenance']);
```

## Best Practices

1. **Use appropriate TTL values** based on data volatility
2. **Tag cache entries** for efficient invalidation
3. **Monitor cache hit rates** and adjust strategies
4. **Use background warming** for better user experience
5. **Implement fallback mechanisms** when cache fails
6. **Regular cache cleanup** to prevent memory bloat

## Security Considerations

1. **Secure Redis instance** with password and firewall
2. **Use SSL/TLS** for Redis connections in production
3. **Limit access** to Redis CLI in production
4. **Monitor unauthorized access** attempts
5. **Regular security updates** for Redis server

## Production Deployment

### Redis Configuration for Production
```bash
# redis.conf settings
bind 127.0.0.1
protected-mode yes
port 6379
timeout 0
keepalive 300
maxmemory 2gb
maxmemory-policy allkeys-lru
save 900 1
save 300 10
save 60 10000
```

### Environment Variables
```env
CACHE_STORE=redis
REDIS_CLIENT=phpredis
REDIS_PASSWORD=your_secure_password
REDIS_PERSISTENT=true
CACHE_PREFIX=hms_prod
```

### Monitoring Setup
```bash
# Add to crontab for regular monitoring
*/5 * * * * /usr/local/bin/redis-cli ping > /dev/null || systemctl restart redis
```
