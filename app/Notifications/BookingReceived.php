<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/** Sent to the customer right after they submit a booking. */
class BookingReceived extends Notification
{
    public function __construct(public Booking $booking) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $b = $this->booking;

        return (new MailMessage)
            ->subject('We received your booking request')
            ->greeting('Hi '.$b->name.',')
            ->line('Thank you for choosing '.config('app.name').'! We have received your request.')
            ->line('Service: '.($b->service->title ?? 'Not specified'))
            ->line('Date: '.($b->preferred_date ?? 'Not specified').' — '.($b->time_slot ?? ''))
            ->line('Address: '.$b->address)
            ->line('Our team will review it and confirm shortly.')
            ->action('View my bookings', route('user.dashboard'));
    }
}
