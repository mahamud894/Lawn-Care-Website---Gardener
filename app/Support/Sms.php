<?php

namespace App\Support;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Sends SMS through the driver set in config/gardener.php.
 * Never throws — if sending fails it is only logged.
 */
class Sms
{
    public static function send(?string $phone, string $message): bool
    {
        $number = self::normalize($phone);

        if (! $number) {
            return false;
        }

        $config = config('gardener.sms');

        try {
            if (($config['driver'] ?? 'log') === 'bulksmsbd' && ! empty($config['api_key'])) {
                $response = Http::timeout(10)->get($config['url'], [
                    'api_key' => $config['api_key'],
                    'type' => 'text',
                    'number' => $number,
                    'senderid' => $config['sender_id'],
                    'message' => $message,
                ]);

                return $response->successful();
            }

            Log::info("[SMS to {$number}] {$message}");

            return true;
        } catch (\Throwable $e) {
            Log::warning('SMS could not be sent: '.$e->getMessage());

            return false;
        }
    }

    /** 01712345678 / +8801712345678 / 8801712345678 → 8801712345678 */
    public static function normalize(?string $phone): ?string
    {
        $digits = preg_replace('/\D+/', '', (string) $phone);

        if (strlen($digits) === 11 && str_starts_with($digits, '01')) {
            return '88'.$digits;
        }

        if (strlen($digits) === 13 && str_starts_with($digits, '8801')) {
            return $digits;
        }

        // Non-Bangladeshi numbers are passed through if they look valid
        return strlen($digits) >= 10 ? $digits : null;
    }
}
