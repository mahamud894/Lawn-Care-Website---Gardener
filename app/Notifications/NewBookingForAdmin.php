<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/** Sent to the site owner when a new booking comes in. */
class NewBookingForAdmin extends Notification
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
            ->subject('New booking #'.$b->id.' from '.$b->name)
            ->line('Customer: '.$b->name.' ('.$b->email.')')
            ->line('Service: '.($b->service->title ?? 'Not specified'))
            ->line('Date: '.($b->preferred_date ?? '-').' — '.($b->time_slot ?? '-'))
            ->line('Address: '.$b->address)
            ->line('Message: '.($b->message ?: '-'))
            ->action('Open booking list', route('viewAllBookings'));
    }
}
