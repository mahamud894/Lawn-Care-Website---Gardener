<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Booking;
use App\Notifications\BookingStatusChanged;
use App\Support\SafeNotify;
use App\Support\Sms;
use Illuminate\Support\Carbon;

/**
 * Everything that should happen when a booking changes,
 * no matter who changes it (admin, gardener or customer).
 */
class BookingWorkflow
{
    public static function changeStatus(Booking $booking, string $status, bool $notify = true): Booking
    {
        if ($booking->status === $status) {
            return $booking;
        }

        $old = $booking->status;
        $booking->update(['status' => $status]);
        $booking->load(['service', 'teamMember']);

        ActivityLog::record('booking.status', "Booking #{$booking->id}: {$old} → {$status}", $booking);

        if ($notify) {
            self::notifyCustomer($booking);
        }

        if ($status === 'done') {
            self::scheduleNextVisit($booking);
        }

        return $booking;
    }

    public static function notifyCustomer(Booking $booking): void
    {
        SafeNotify::toEmail($booking->email, new BookingStatusChanged($booking));
        Sms::send($booking->phone, self::smsText($booking));
    }

    public static function smsText(Booking $booking): string
    {
        $name = config('gardener.business_name');
        $when = $booking->preferred_date
            ? Carbon::parse($booking->preferred_date)->format('d M').' '.$booking->time_slot
            : '';

        return match ($booking->status) {
            'pending' => "{$name}: We received your booking #{$booking->id} for {$when}. We will confirm soon.",
            'approved' => "{$name}: Booking #{$booking->id} confirmed for {$when}."
                .($booking->teamMember ? " Gardener: {$booking->teamMember->title}." : ''),
            'done' => "{$name}: Job #{$booking->id} is done. Thank you! Please leave a review: ".route('user.dashboard'),
            'rejected' => "{$name}: Sorry, we can't take booking #{$booking->id}. Please choose another date.",
            'cancelled' => "{$name}: Booking #{$booking->id} has been cancelled.",
            default => "{$name}: Booking #{$booking->id} status: {$booking->status}.",
        };
    }

    /**
     * For weekly / bi-weekly / monthly bookings, create the next visit
     * when the current one is completed. Uses the same slot if free,
     * otherwise the first free slot that day.
     */
    public static function scheduleNextVisit(Booking $booking): ?Booking
    {
        $nextDate = $booking->nextRecurrenceDate();

        if (! $nextDate) {
            return null;
        }

        // Already created (e.g. status toggled done → approved → done)
        $exists = Booking::where('parent_booking_id', $booking->id)->exists();
        if ($exists) {
            return null;
        }

        // Same slot if free; otherwise the first free slot that day;
        // if the whole day is full, try the following days (up to 2 weeks).
        $date = null;
        $slot = null;
        for ($i = 0; $i < 14 && ! $slot; $i++) {
            $date = $nextDate->copy()->addDays($i)->toDateString();
            $slot = ! Booking::slotIsTaken($date, $booking->time_slot)
                ? $booking->time_slot
                : collect(Booking::TIME_SLOTS)->first(fn ($s) => ! Booking::slotIsTaken($date, $s));
        }

        if (! $slot) {
            ActivityLog::record('booking.recurring', "No free slot found for the next visit of #{$booking->id}", $booking);

            return null;
        }

        $next = Booking::create([
            'name' => $booking->name,
            'email' => $booking->email,
            'phone' => $booking->phone,
            'address' => $booking->address,
            'lawn_size' => $booking->lawn_size,
            'estimated_price' => $booking->estimated_price,
            'message' => $booking->message,
            'service_id' => $booking->service_id,
            'user_id' => $booking->user_id,
            // Keep the same gardener if they are free
            'team_member_id' => $slot === $booking->time_slot && ! in_array($booking->team_member_id, Booking::query()->whereDate('preferred_date', $date)->where('time_slot', $slot)->whereIn('status', Booking::ACTIVE_STATUSES)->pluck('team_member_id')->all())
                ? $booking->team_member_id
                : null,
            'preferred_date' => $date,
            'time_slot' => $slot,
            'recurrence' => $booking->recurrence,
            'parent_booking_id' => $booking->id,
            'status' => 'pending',
        ]);

        ActivityLog::record('booking.recurring', "Next visit #{$next->id} created from #{$booking->id} for {$date}", $next);

        return $next;
    }

    /** Mark a booking as paid (online or cash). */
    public static function markPaid(Booking $booking, string $method, ?float $amount = null): Booking
    {
        $booking->update([
            'payment_status' => 'paid',
            'payment_method' => $method,
            'amount_paid' => $amount ?? $booking->amountDue(),
            'paid_at' => now(),
        ]);

        ActivityLog::record('payment.paid', "Booking #{$booking->id} paid by {$method}", $booking);

        return $booking;
    }
}
