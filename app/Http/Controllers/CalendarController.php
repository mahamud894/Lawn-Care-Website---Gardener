<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

/** Month calendar of bookings for the admin panel. */
class CalendarController extends Controller
{
    public function index(Request $request)
    {
        try {
            $month = Carbon::createFromFormat('Y-m', (string) $request->query('month', now()->format('Y-m')))->startOfMonth();
        } catch (\Throwable $e) {
            $month = now()->startOfMonth();
        }

        // Grid starts on Saturday (common week start in Bangladesh)
        $start = $month->copy()->startOfWeek(Carbon::SATURDAY);
        $end = $month->copy()->endOfMonth()->endOfWeek(Carbon::FRIDAY);

        $bookings = Booking::with(['service', 'teamMember'])
            ->whereBetween('preferred_date', [$start->toDateString(), $end->toDateString().' 23:59:59'])
            ->whereNotIn('status', ['cancelled', 'rejected'])
            ->get()
            ->sortBy(fn ($b) => Booking::slotOrder($b->time_slot))
            ->groupBy(fn ($b) => substr((string) $b->preferred_date, 0, 10));

        $weeks = [];
        for ($day = $start->copy(); $day->lte($end); $day->addDay()) {
            $weeks[intdiv($start->diffInDays($day), 7)][] = $day->copy();
        }

        return view('pages.admin-panel.calendar', [
            'month' => $month,
            'weeks' => $weeks,
            'bookings' => $bookings,
            'capacity' => Booking::slotCapacity(),
        ]);
    }
}
