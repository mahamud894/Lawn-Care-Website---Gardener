<?php

namespace App\Models\Concerns;

use App\Support\SiteCache;

/** Clears the public page cache whenever this record changes. */
trait FlushesSiteCache
{
    protected static function bootFlushesSiteCache(): void
    {
        static::saved(fn () => SiteCache::flush());
        static::deleted(fn () => SiteCache::flush());
    }
}
