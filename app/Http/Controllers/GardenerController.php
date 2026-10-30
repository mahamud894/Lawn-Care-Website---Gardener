<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Services\BookingWorkflow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

/**
 * Portal where a gardener sees the jobs assigned to them
 * and marks them done from their phone.
 */
class GardenerController extends Controller
{
    public function dashboard()
    {
        $member = Auth::user()->teamMember;
        $today = now()->toDateString();

        $jobs = Booking::with('service')
            ->where('team_member_id', $member->id)
            ->whereIn('status', ['approved', 'pending'])
            ->get()
            // Sort by date, then by real time of day (not alphabetically: "02:00 PM" < "09:00 AM")
            ->sortBy(fn ($b) => substr((string) $b->preferred_date, 0, 10).'-'.str_pad(Booking::slotOrder($b->time_slot), 2, '0', STR_PAD_LEFT))
            ->values();

        $todayJobs = $jobs->filter(fn ($b) => substr((string) $b->preferred_date, 0, 10) === $today);
        $upcomingJobs = $jobs->filter(fn ($b) => substr((string) $b->preferred_date, 0, 10) > $today);
        $overdueJobs = $jobs->filter(fn ($b) => $b->preferred_date && substr((string) $b->preferred_date, 0, 10) < $today);

        $doneJobs = Booking::with('service')
            ->where('team_member_id', $member->id)
            ->where('status', 'done')
            ->latest('updated_at')
            ->take(10)
            ->get();

        return view('pages.gardener-dashboard', compact('member', 'todayJobs', 'upcomingJobs', 'overdueJobs', 'doneJobs'));
    }

    public function markDone(Request $request, Booking $booking)
    {
        abort_if($booking->team_member_id !== Auth::user()->teamMember->id, 403);

        if ($booking->status !== 'approved') {
            Alert::error(__('Not allowed'), __('Only approved jobs can be marked done.'));

            return back();
        }

        BookingWorkflow::changeStatus($booking, 'done');

        // Gardener collected cash on site
        if ($request->boolean('cash_collected') && ! $booking->isPaid()) {
            BookingWorkflow::markPaid($booking, 'Cash');
        }

        Alert::success(__('Great job! 🌿'), __('The job is marked as done and the customer has been notified.'));

        return back();
    }
}
