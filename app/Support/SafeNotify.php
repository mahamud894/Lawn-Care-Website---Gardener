<?php

namespace App\Support;

use Illuminate\Notifications\Notification as BaseNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

/**
 * Sends e-mail notifications without ever breaking the page.
 * If mail is not configured (or the SMTP server is down) the error is
 * written to the log and the user still gets a normal response.
 */
class SafeNotify
{
    /** Send to any e-mail address (customer or guest). */
    public static function toEmail(?string $email, BaseNotification $notification): void
    {
        if (! $email) {
            return;
        }

        try {
            Notification::route('mail', $email)->notify($notification);
        } catch (\Throwable $e) {
            Log::warning('Mail could not be sent: '.$e->getMessage());
        }
    }

    /** Send to the site owner (ADMIN_EMAIL in .env). */
    public static function toAdmin(BaseNotification $notification): void
    {
        self::toEmail(config('app.admin_email'), $notification);
    }
}
