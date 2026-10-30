<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class ActivityLog extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = ['user_id', 'action', 'description', 'subject_type', 'subject_id', 'ip_address'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Write one line to the admin activity log.
     * Example: ActivityLog::record('booking.status', 'Booking #5 marked done', $booking);
     */
    public static function record(string $action, string $description, ?Model $subject = null): void
    {
        try {
            static::create([
                'user_id' => Auth::id(),
                'action' => $action,
                'description' => mb_substr($description, 0, 255),
                'subject_type' => $subject ? class_basename($subject) : null,
                'subject_id' => $subject?->getKey(),
                'ip_address' => request()?->ip(),
            ]);
        } catch (\Throwable $e) {
            // Logging must never break the real action
            report($e);
        }
    }
}
