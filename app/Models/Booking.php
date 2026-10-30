<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

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

    // value => label; "none" means a one-time visit
    public const RECURRENCES = [
        'none' => 'One time',
        'weekly' => 'Every week',
        'biweekly' => 'Every 2 weeks',
        'monthly' => 'Every month',
    ];

    protected $fillable = [
        'name', 'email', 'phone', 'address', 'lawn_size', 'estimated_price', 'message', 'photos',
        'preferred_date', 'time_slot', 'recurrence', 'parent_booking_id',
        'service_id', 'user_id', 'team_member_id', 'status',
        'payment_status', 'payment_method', 'amount_paid', 'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'photos' => 'array',
            'paid_at' => 'datetime',
            'estimated_price' => 'decimal:2',
            'amount_paid' => 'decimal:2',
        ];
    }

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

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Time slot capacity
    |--------------------------------------------------------------------------
    | Each slot can take as many bookings as there are active gardeners.
    */

    public static function slotCapacity(): int
    {
        $members = Team::where('isActive', 1)->count();

        return max($members, config('gardener.default_slot_capacity', 1), 1);
    }

    /** How many active bookings are in this date + slot. */
    public static function bookedCount(string $date, string $slot, ?int $ignoreId = null): int
    {
        return static::whereDate('preferred_date', $date)
            ->where('time_slot', $slot)
            ->whereIn('status', self::ACTIVE_STATUSES)
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->count();
    }

    /** Is this date + slot already full? */
    public static function slotIsTaken(?string $date, ?string $slot, ?int $ignoreId = null): bool
    {
        if (! $date || ! $slot) {
            return false;
        }

        return static::bookedCount($date, $slot, $ignoreId) >= static::slotCapacity();
    }

    /** List of full slots on a date (used by the booking forms). */
    public static function fullSlots(string $date): array
    {
        $capacity = static::slotCapacity();

        return static::whereDate('preferred_date', $date)
            ->whereIn('status', self::ACTIVE_STATUSES)
            ->get(['time_slot'])
            ->countBy('time_slot')
            ->filter(fn ($count) => $count >= $capacity)
            ->keys()
            ->filter()
            ->values()
            ->all();
    }

    /** Position of a time slot in the day (09:00 AM = 0 …), for sorting. */
    public static function slotOrder(?string $slot): int
    {
        $i = array_search($slot, self::TIME_SLOTS, true);

        return $i === false ? 99 : $i;
    }

    /** Team members already working another booking at this date + slot. */
    public function busyTeamMemberIds(): array
    {
        if (! $this->preferred_date || ! $this->time_slot) {
            return [];
        }

        return static::whereDate('preferred_date', $this->preferred_date)
            ->where('time_slot', $this->time_slot)
            ->whereIn('status', self::ACTIVE_STATUSES)
            ->where('id', '!=', $this->id)
            ->whereNotNull('team_member_id')
            ->pluck('team_member_id')
            ->all();
    }

    /*
    |--------------------------------------------------------------------------
    | Money
    |--------------------------------------------------------------------------
    */

    /** Price to pay: the estimate, or the service base price. */
    public function amountDue(): float
    {
        return (float) ($this->estimated_price ?? $this->service?->price ?? 0);
    }

    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }

    public function invoiceNumber(): string
    {
        return 'INV-'.str_pad((string) $this->id, 5, '0', STR_PAD_LEFT);
    }

    /** Date of the next visit for a recurring booking. */
    public function nextRecurrenceDate(): ?Carbon
    {
        if (! $this->preferred_date || $this->recurrence === 'none' || ! $this->recurrence) {
            return null;
        }

        $date = Carbon::parse($this->preferred_date);

        return match ($this->recurrence) {
            'weekly' => $date->addWeek(),
            'biweekly' => $date->addWeeks(2),
            'monthly' => $date->addMonthNoOverflow(),
            default => null,
        };
    }
}
