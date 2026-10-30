<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    // Categories used by the filter buttons on the Projects page
    public const CATEGORIES = [
        'complete' => 'Complete Projects',
        'ongoing' => 'Ongoing Projects',
    ];

    protected $fillable = ['title', 'category', 'image_url', 'description', 'isActive'];

    protected function casts(): array
    {
        return ['isActive' => 'boolean'];
    }
}
