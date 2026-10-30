<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/** Sent to the customer when an admin changes the status or assigns a team member. */
class BookingStatusChanged extends Notification
{
    public function __construct(public Booking $booking) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $b = $this->booking;

        $text = match ($b->status) {
            'approved' => 'Good news — your booking has been approved.',
            'done' => 'Your service has been completed. We would love to hear your feedback!',
            'rejected' => 'Sorry, we are unable to take this booking. Please try another date or contact us.',
            'cancelled' => 'Your booking has been cancelled.',
            default => 'Your booking status is now: '.ucfirst($b->status).'.',
        };

        $mail = (new MailMessage)
            ->subject('Booking #'.$b->id.' update: '.ucfirst($b->status))
            ->greeting('Hi '.$b->name.',')
            ->line($text)
            ->line('Service: '.($b->service->title ?? 'Not specified'))
            ->line('Date: '.($b->preferred_date ?? '-').' — '.($b->time_slot ?? '-'));

        if ($b->teamMember) {
            $mail->line('Assigned gardener: '.$b->teamMember->title);
        }

        // After a finished job, ask for a review
        if ($b->status === 'done' && $b->user_id) {
            return $mail->line('How did we do? Your review helps other customers choose us.')
                ->action('Leave a review', route('user.dashboard'));
        }

        return $mail->action('View my bookings', route('user.dashboard'));
    }
}
