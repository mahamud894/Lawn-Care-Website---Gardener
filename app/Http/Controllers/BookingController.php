<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Booking;
use App\Models\Service;
use App\Models\Team;
use App\Notifications\BookingReceived;
use App\Notifications\NewBookingForAdmin;
use App\Services\BookingWorkflow;
use App\Support\ImageUpload;
use App\Support\SafeNotify;
use App\Support\Sms;
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
            Alert::info(__('Login required'), __('Please log in to book a service.'));

            return redirect()->route('login');
        }

        $validated = $req->validate([
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'min:10', 'max:20'],
            'address' => ['required', 'string', 'max:255'],
            'service' => ['required', Rule::exists('services', 'id')->where('isActive', 1)],
            'lawn_size' => ['nullable', 'integer', 'min:50', 'max:1000000'],
            'preferred_date' => ['required', 'date', 'after:today'],
            'time_slot' => ['required', Rule::in(Booking::TIME_SLOTS)],
            'recurrence' => ['nullable', Rule::in(array_keys(Booking::RECURRENCES))],
            'message' => ['nullable', 'string', 'max:2000'],
            'photos' => ['nullable', 'array', 'max:3'],
            'photos.*' => ['image', 'mimes:jpeg,png,jpg,webp', 'max:8192'],
        ], [
            'preferred_date.after' => __('Please choose a date from tomorrow onwards.'),
            'time_slot.in' => __('Please choose a valid time slot.'),
            'photos.max' => __('You can upload up to 3 photos.'),
            'photos.*.max' => __('Each photo must be smaller than 8 MB.'),
            'photos.*.uploaded' => __('A photo could not be uploaded. Please try a smaller photo.'),
            'photos.*.image' => __('Please upload photos only (JPG, PNG or WebP).'),
            'photos.*.mimes' => __('Please upload photos only (JPG, PNG or WebP).'),
        ]);

        $service = Service::find($validated['service']);
        $lawnSize = $validated['lawn_size'] ?? null;

        // Check capacity again on the server and lock while saving,
        // so a slot can never take more bookings than we have gardeners.
        $booking = DB::transaction(function () use ($validated, $service, $lawnSize) {
            $count = Booking::whereDate('preferred_date', $validated['preferred_date'])
                ->where('time_slot', $validated['time_slot'])
                ->whereIn('status', Booking::ACTIVE_STATUSES)
                ->lockForUpdate()
                ->count();

            if ($count >= Booking::slotCapacity()) {
                return null;
            }

            return Booking::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'address' => $validated['address'],
                'lawn_size' => $lawnSize,
                'estimated_price' => $service->estimateFor($lawnSize),
                'message' => $validated['message'] ?? null,
                'preferred_date' => $validated['preferred_date'],
                'time_slot' => $validated['time_slot'],
                'recurrence' => $validated['recurrence'] ?? 'none',
                'service_id' => $service->id,
                'user_id' => Auth::id(),
                'status' => 'pending',
            ]);
        });

        if (! $booking) {
            return back()
                ->withInput()
                ->withErrors(['time_slot' => __('Sorry, this time slot was just booked by someone else. Please pick another one.')]);
        }

        // Photos are saved only after the booking succeeded
        if ($req->hasFile('photos')) {
            $booking->update([
                'photos' => collect($req->file('photos'))->map(fn ($f) => ImageUpload::store($f, 'bookings'))->all(),
            ]);
        }

        // Remember the phone number for next time (unless another account already uses it)
        if (! Auth::user()->phone && ! \App\Models\User::where('phone', $validated['phone'])->exists()) {
            Auth::user()->update(['phone' => $validated['phone']]);
        }

        $booking->load('service');
        SafeNotify::toEmail($booking->email, new BookingReceived($booking));
        SafeNotify::toAdmin(new NewBookingForAdmin($booking));
        Sms::send($booking->phone, BookingWorkflow::smsText($booking));

        Alert::success(
            __('Thank You! 🌿'),
            __('Your quote request has been received. Our team will contact you shortly and assign a member for your service.')
        );

        return redirect()->back();
    }

    /** Returns the slots that are full on a date (JSON list). */
    public function checkSlots(Request $request)
    {
        $date = $request->query('date');

        if (! $date || strtotime($date) === false) {
            return response()->json([]);
        }

        return response()->json(Booking::fullSlots(date('Y-m-d', strtotime($date))));
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
            Alert::error(__('Not allowed'), __('Only pending or approved bookings can be cancelled.'));

            return back();
        }

        // Money has to be returned by a person, so paid bookings go through us
        if ($booking->isPaid()) {
            Alert::info(__('Please contact us'), __('This booking is already paid. Please contact us to cancel it and arrange your refund.'));

            return back();
        }

        BookingWorkflow::changeStatus($booking, 'cancelled', notify: false);
        Alert::success(__('Cancelled'), __('Your booking has been cancelled.'));

        return back();
    }

    public function rescheduleMyBooking(Request $req, Booking $booking)
    {
        $this->authorizeOwner($booking);

        if ($booking->status !== 'pending') {
            Alert::error(__('Not allowed'), __('Only pending bookings can be rescheduled. Please contact us for approved bookings.'));

            return back();
        }

        $data = $req->validate([
            'preferred_date' => ['required', 'date', 'after:today'],
            'time_slot' => ['required', Rule::in(Booking::TIME_SLOTS)],
        ]);

        if (Booking::slotIsTaken($data['preferred_date'], $data['time_slot'], $booking->id)) {
            Alert::error(__('Slot taken'), __('That time slot is already booked. Please choose another one.'));

            return back();
        }

        // A new time means the old gardener may not be free any more
        $booking->update($data + ['team_member_id' => null]);
        ActivityLog::record('booking.reschedule', "Customer moved booking #{$booking->id} to {$data['preferred_date']} {$data['time_slot']}", $booking);
        Alert::success(__('Rescheduled'), __('Your booking date has been updated.'));

        return back();
    }

    /** Customer stops future repeats of a recurring booking. */
    public function stopRepeating(Booking $booking)
    {
        $this->authorizeOwner($booking);

        $booking->update(['recurrence' => 'none']);
        Alert::success(__('Updated'), __('This booking will not repeat any more.'));

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

    /** Booking list query shared by the page and the CSV export. */
    private function filteredQuery(Request $request)
    {
        return Booking::with(['user', 'service', 'teamMember'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('payment'), fn ($q) => $q->where('payment_status', $request->payment))
            ->when($request->filled('date'), fn ($q) => $q->whereDate('preferred_date', $request->date))
            ->when($request->filled('search'), function ($q) use ($request) {
                $term = '%'.$request->search.'%';
                $q->where(function ($q) use ($term) {
                    $q->where('name', 'like', $term)
                        ->orWhere('email', 'like', $term)
                        ->orWhere('phone', 'like', $term)
                        ->orWhere('address', 'like', $term);
                });
            })
            ->latest();
    }

    public function viewAllBookings(Request $request)
    {
        $bookings = $this->filteredQuery($request)->paginate(15)->withQueryString();
        $teamMembers = Team::where('isActive', 1)->get();
        $statuses = Booking::STATUSES;

        return view('pages.admin-panel.booking.list', compact('bookings', 'teamMembers', 'statuses'));
    }

    /** Download the (filtered) booking list as a CSV file that opens in Excel. */
    public function exportCsv(Request $request)
    {
        $bookings = $this->filteredQuery($request)->get();
        $filename = 'bookings-'.now()->format('Y-m-d').'.csv';

        ActivityLog::record('booking.export', 'Exported '.$bookings->count().' bookings to CSV');

        return response()->streamDownload(function () use ($bookings) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // UTF-8 BOM so Excel shows Bangla text correctly

            fputcsv($out, [
                'ID', 'Created', 'Customer', 'Email', 'Phone', 'Address', 'Service', 'Lawn size (sq ft)',
                'Date', 'Time slot', 'Repeats', 'Status', 'Gardener', 'Price', 'Payment', 'Paid amount', 'Message',
            ]);

            // A cell starting with = + - @ would run as a formula in Excel,
            // so customer-typed text is prefixed with ' to keep it as plain text.
            $safe = fn ($v) => is_string($v) && $v !== '' && in_array($v[0], ['=', '+', '-', '@', "\t", "\r"]) ? "'".$v : $v;

            foreach ($bookings as $b) {
                fputcsv($out, array_map($safe, [
                    $b->id,
                    $b->created_at?->format('Y-m-d H:i'),
                    $b->name,
                    $b->email,
                    $b->phone,
                    $b->address,
                    $b->service?->title,
                    $b->lawn_size,
                    $b->preferred_date,
                    $b->time_slot,
                    Booking::RECURRENCES[$b->recurrence] ?? $b->recurrence,
                    $b->status,
                    $b->teamMember?->title,
                    $b->amountDue(),
                    $b->payment_status.($b->payment_method ? " ({$b->payment_method})" : ''),
                    $b->amount_paid,
                    $b->message,
                ]));
            }

            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function updateStatus(Request $req, $id)
    {
        $req->validate([
            'status' => ['required', Rule::in(['pending', 'approved', 'done', 'rejected'])],
        ]);

        BookingWorkflow::changeStatus(Booking::findOrFail($id), $req->status);
        Alert::success('Success', 'Booking status updated successfully');

        return redirect()->back();
    }

    /** Admin records a cash payment collected on site. */
    public function markPaid(Request $req, $id)
    {
        $booking = Booking::findOrFail($id);

        if ($req->boolean('unpaid')) {
            $booking->update(['payment_status' => 'unpaid', 'payment_method' => null, 'amount_paid' => null, 'paid_at' => null]);
            ActivityLog::record('payment.unpaid', "Booking #{$booking->id} marked unpaid", $booking);
            Alert::success('Updated', 'Booking marked as unpaid.');
        } else {
            BookingWorkflow::markPaid($booking, 'Cash');
            Alert::success('Updated', 'Cash payment recorded.');
        }

        return back();
    }
}
