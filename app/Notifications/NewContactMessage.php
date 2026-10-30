<?php

namespace App\Notifications;

use App\Models\ContactMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/** Sent to the site owner when someone uses the Contact form. */
class NewContactMessage extends Notification
{
    public function __construct(public ContactMessage $contact) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $c = $this->contact;

        return (new MailMessage)
            ->subject('New contact message: '.($c->subject ?: 'No subject'))
            ->replyTo($c->email, $c->name)
            ->line('From: '.$c->name.' ('.$c->email.')')
            ->line($c->message)
            ->action('Open messages', route('admin.messages.list'));
    }
}
