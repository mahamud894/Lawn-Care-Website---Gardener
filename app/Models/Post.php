<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = ['title', 'slug', 'excerpt', 'body', 'image_url', 'isPublished', 'published_at'];

    protected function casts(): array
    {
        return ['isPublished' => 'boolean', 'published_at' => 'datetime'];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('isPublished', true)
            ->where(fn ($q) => $q->whereNull('published_at')->orWhere('published_at', '<=', now()));
    }

    /** Short summary for cards: the excerpt, or the start of the body. */
    public function summary(int $length = 160): string
    {
        return $this->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($this->body), $length);
    }
}
