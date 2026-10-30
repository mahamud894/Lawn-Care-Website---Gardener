<?php

namespace App\Models;

use App\Models\Concerns\FlushesSiteCache;
use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    use FlushesSiteCache;

    protected $fillable = ['question', 'answer', 'sort_order', 'isActive'];

    protected function casts(): array
    {
        return ['isActive' => 'boolean'];
    }
}
