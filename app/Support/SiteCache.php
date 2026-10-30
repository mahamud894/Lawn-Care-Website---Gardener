<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;

/**
 * Caches the lists every visitor sees (services, team, projects, reviews,
 * FAQ, blog). The cache is cleared automatically whenever an admin saves
 * or deletes one of those records (see FlushesSiteCache), so changes show
 * up right away while normal visitors don't hit the database.
 */
class SiteCache
{
    private const TTL = 3600; // seconds

    public static function remember(string $key, callable $callback)
    {
        try {
            return Cache::remember(self::key($key), self::TTL, $callback);
        } catch (\Throwable $e) {
            // If the cache store isn't available, just run the query
            return $callback();
        }
    }

    /** Throw away every cached list at once. */
    public static function flush(): void
    {
        try {
            Cache::forever('site_cache_version', self::version() + 1);
        } catch (\Throwable $e) {
            report($e);
        }
    }

    private static function version(): int
    {
        return (int) Cache::get('site_cache_version', 1);
    }

    private static function key(string $key): string
    {
        return 'site.v'.self::version().'.'.$key;
    }
}
