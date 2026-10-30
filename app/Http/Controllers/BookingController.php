<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Service;
use App\Models\Team;
use App\Notifications\BookingReceived;
use App\Notifications\BookingStatusChanged;
use App\Notifications\NewBookingForAdmin;
use App\Support\SafeNotify;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use RealRashid\SweetAlert\Facades\Alert;

class BookingController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Public
    |--------------------------------------------------------------------------
    */

    public function viewQuotePage(Request $request)
    {
        $services = Schema::hasTable('services') ? Service::where('isActive', 1)->get() : collect();

        // e.g. /quote?service_id=2 pre-selects a service
        $selectedServiceId = $request->query('service_id');

        return view('pages.quote', compact('services', 'selectedServiceId'));
    }

    public function createBooking(Request $req)
    {
        if (! Auth::check()) {
            Alert::info('Login required', 'Please log in to book a service.');

            return redirect()->route('login');
        }

        $validated = $req->validate([
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            'service' => ['required', Rule::exists('services', 'id')->where('isActive', 1)],
            'preferred_date' => ['required', 'date', 'after:today'],
            'time_slot' => ['required', Rule::in(Booking::TIME_SLOTS)],
            'message' => ['nullable', 'string', 'max:2000'],
        ], [
            'preferred_date.after' => 'Please choose a date from tomorrow onwards.',
            'time_slot.in' => 'Please choose a valid time slot.',
        ]);

        // Check the slot again on the server and lock while saving,
        // so two people can never get the same date + slot.
        $booking = DB::transaction(function () use ($validated) {
            $taken = Booking::whereDate('preferred_date', $validated['preferred_date'])
                ->where('time_slot', $validated['time_slot'])
                ->whereIn('status', Booking::ACTIVE_STATUSES)
                ->lockForUpdate()
                ->exists();

            if ($taken) {
                return null;
            }

            return Booking::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'address' => $validated['address'],
                'message' => $validated['message'] ?? null,
                'preferred_date' => $validated['preferred_date'],
                'time_slot' => $validated['time_slot'],
                'service_id' => $validated['service'],
                'user_id' => Auth::id(),
                'status' => 'pending',
            ]);
        });

        if (! $booking) {
            return back()
                ->withInput()
                ->withErrors(['time_slot' => 'Sorry, this time slot was just booked by someone else. Please pick another one.']);
        }

        $booking->load('service');
        SafeNotify::toEmail($booking->email, new BookingReceived($booking));
        SafeNotify::toAdmin(new NewBookingForAdmin($booking));

        Alert::success(
            'Thank You! 🌿',
            'Your quote request has been received. Our team will contact you shortly and assign a member for your service.'
        );

        return redirect()->back();
    }

    /** Returns the slots already taken on a date (JSON list). */
    public function checkSlots(Request $request)
    {
        $date = $request->query('date');

        if (! $date || strtotime($date) === false) {
            return response()->json([]);
        }

        $bookedSlots = Booking::whereDate('preferred_date', $date)
            ->whereIn('status', Booking::ACTIVE_STATUSES)
            ->pluck('time_slot')
            ->filter()
            ->values();

        return response()->json($bookedSlots);
    }

    /*
    |--------------------------------------------------------------------------
    | Customer dashboard
    |--------------------------------------------------------------------------
    */

    public function viewMyBookings()
    {
        $bookings = Booking::where('user_id', Auth::id())
            ->with(['service', 'teamMember', 'review'])
            ->latest()
            ->get();

        return view('pages.user-dashboard', compact('bookings'));
    }

    public function cancelMyBooking(Booking $booking)
    {
        $this->authorizeOwner($booking);

        if (! in_array($booking->status, ['pending', 'approved'])) {
            Alert::error('Not allowed', 'Only pending or approved bookings can be cancelled.');

            return back();
        }

        $booking->update(['status' => 'cancelled']);

        Alert::success('Cancelled', 'Your booking has been cancelled.');

        return back();
    }

    public function rescheduleMyBooking(Request $req, Booking $booking)
    {
        $this->authorizeOwner($booking);

        if ($booking->status !== 'pending') {
            Alert::error('Not allowed', 'Only pending bookings can be rescheduled. Please contact us for approved bookings.');

            return back();
        }

        $data = $req->validate([
            'preferred_date' => ['required', 'date', 'after:today'],
            'time_slot' => ['required', Rule::in(Booking::TIME_SLOTS)],
        ]);

        if (Booking::slotIsTaken($data['preferred_date'], $data['time_slot'], $booking->id)) {
            Alert::error('Slot taken', 'That time slot is already booked. Please choose another one.');

            return back();
        }

        $booking->update($data);
        Alert::success('Rescheduled', 'Your booking date has been updated.');

        return back();
    }

    private function authorizeOwner(Booking $booking): void
    {
        abort_if($booking->user_id !== Auth::id(), 403);
    }

    /*
    |--------------------------------------------------------------------------
    | Admin
    |--------------------------------------------------------------------------
    */

    public function viewAllBookings(Request $request)
    {
        $bookings = Booking::with(['user', 'service', 'teamMember'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('date'), fn ($q) => $q->whereDate('preferred_date', $request->date))
            ->when($request->filled('search'), function ($q) use ($request) {
                $term = '%'.$request->search.'%';
                $q->where(function ($q) use ($term) {
                    $q->where('name', 'like', $term)
                        ->orWhere('email', 'like', $term)
                        ->orWhere('address', 'like', $term);
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $teamMembers = Team::where('isActive', 1)->get();
        $statuses = Booking::STATUSES;

        return view('pages.admin-panel.booking.list', compact('bookings', 'teamMembers', 'statuses'));
    }

    public function updateStatus(Request $req, $id)
    {
        $req->validate([
            'status' => ['required', Rule::in(['pending', 'approved', 'done', 'rejected'])],
        ]);

        $booking = Booking::findOrFail($id);

        if ($booking->status !== $req->status) {
            $booking->update(['status' => $req->status]);
            SafeNotify::toEmail($booking->email, new BookingStatusChanged($booking->load(['service', 'teamMember'])));
        }

        Alert::success('Success', 'Booking status updated successfully');

        return redirect()->back();
    }
}
