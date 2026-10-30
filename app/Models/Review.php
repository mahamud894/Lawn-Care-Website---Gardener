<?php

namespace App\Models;

use App\Models\Concerns\FlushesSiteCache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    use FlushesSiteCache;

    protected $fillable = ['user_id', 'booking_id', 'rating', 'comment', 'isApproved'];

    protected function casts(): array
    {
        return ['isApproved' => 'boolean', 'rating' => 'integer'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }
}
