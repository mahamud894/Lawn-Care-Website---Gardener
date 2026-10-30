<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = ['booking_id', 'tran_id', 'amount', 'currency', 'status', 'method', 'val_id', 'gateway_response'];

    protected function casts(): array
    {
        return ['gateway_response' => 'array', 'amount' => 'decimal:2'];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }
}
