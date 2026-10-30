<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Support\Facades\Auth;

/** Printable invoice — the browser's "Save as PDF" turns it into a PDF. */
class InvoiceController extends Controller
{
    public function show(Booking $booking)
    {
        $user = Auth::user();
        abort_unless($user->isAdmin || $booking->user_id === $user->id, 403);

        $booking->load(['service', 'teamMember', 'payments' => fn ($q) => $q->where('status', 'paid')]);

        return view('pages.invoice', compact('booking'));
    }
}
