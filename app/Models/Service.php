<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Service extends Model
{
    protected $guarded = [];

    protected static function booted(): void
    {
        // Every service gets a unique URL slug from its title
        static::saving(function (Service $service) {
            if (! $service->slug || $service->isDirty('title')) {
                $service->slug = static::uniqueSlug($service->title, $service->id);
            }
        });
    }

    public static function uniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title) ?: 'service';
        $slug = $base;
        $i = 2;

        while (static::where('slug', $slug)->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }

    /** Price estimate for a lawn of $sqft square feet. */
    public function estimateFor(?int $sqft): ?float
    {
        if ($this->price === null && $this->price_per_100_sqft === null) {
            return null;
        }

        $extra = ($sqft && $this->price_per_100_sqft) ? ($sqft / 100) * (float) $this->price_per_100_sqft : 0;

        return round((float) $this->price + $extra, 2);
    }
}
