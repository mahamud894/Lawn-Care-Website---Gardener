<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Booking extends Model
{
    public const STATUSES = ['pending', 'approved', 'done', 'rejected', 'cancelled'];

    // Statuses that keep a time slot occupied
    public const ACTIVE_STATUSES = ['pending', 'approved', 'done'];

    public const TIME_SLOTS = [
        '09:00 AM - 11:00 AM',
        '11:00 AM - 01:00 PM',
        '02:00 PM - 04:00 PM',
        '04:00 PM - 06:00 PM',
    ];

    protected $fillable = [
        'name', 'email', 'address', 'message', 'preferred_date', 'time_slot',
        'service_id', 'user_id', 'team_member_id', 'status',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function teamMember(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'team_member_id');
    }

    public function review(): HasOne
    {
        return $this->hasOne(Review::class);
    }

    /**
     * Is this date + slot already taken by another active booking?
     */
    public static function slotIsTaken(?string $date, ?string $slot, ?int $ignoreId = null): bool
    {
        if (! $date || ! $slot) {
            return false;
        }

        return static::whereDate('preferred_date', $date)
            ->where('time_slot', $slot)
            ->whereIn('status', self::ACTIVE_STATUSES)
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->exists();
    }
}
