<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use RealRashid\SweetAlert\Facades\Alert;

class BookingController extends Controller
{
    public function createBooking(Request $req){
        $rules = [
            'name' => ['required', 'min:6'],
            'email' => ['required', 'email'],
            'address' => ['required', 'string'],
            'preferred_date' => ['nullable', 'date'],
            'time_slot' => ['required'],
        ];

        if (Schema::hasTable('services')) {
            $rules['service'] = ['nullable', 'exists:services,id'];
        }

        $validated = $req->validate($rules);

        $data["name"] = $req->name;
        $data["email"] = $req->email;
        $data["address"] = $req->address;
        $data["message"] = $req->message;
        $data["preferred_date"] = $req->preferred_date;
        $data["time_slot"] = $req->time_slot;
        $data["service_id"] = $req->filled('service') ? $req->service : null;
        $data["user_id"] = Auth::check() ? Auth::id() : null;

        Booking::create($data);
        //Alert::success("Success", "Booking submitted successfully");
        Alert::success(
        'Thank You! 🌿',
        'Your quote request has been received. Our team will contact you shortly and assign a member for your service.'
    );
        return redirect()->back();
    }

    public function viewQuotePage(Request $request){ // 💡 Request $request It has been injected.
    $services = Schema::hasTable('services') ? Service::all() : collect();

    // The service_id parameter is being retrieved from the URL (e.g., /quote?service_id=2).
    $selectedServiceId = $request->query('service_id');

    // The data is being passed to the blade file
    return view("pages.quote", compact("services", "selectedServiceId"));
   }

    public function viewAllBookings(){
    // All bookings are being retrieved
    $bookings = Booking::with(["user", "service", "teamMember"])->get();

    // All team members are being retrieved from the database. 💡
    $teamMembers = \App\Models\Team::all();

    // The data for both bookings and team members is being passed to the blade file
    return view("pages.admin-panel.booking.list", [
        "bookings" => $bookings,
        "teamMembers" => $teamMembers
      ]);
    }

    public function pendingBooking($id){
        $data["status"] = "pending";

        Booking::where(["id"=>$id])->update($data);
        Alert::success("Success", "Booking status updated successfully");
        return redirect()->back();
    }
    public function doneBooking($id){
        $data["status"] = "done";
        Booking::where(["id"=>$id])->update($data);
        Alert::success("Success", "Booking status updated successfully");
        return redirect()->back();
    }

    public function approveBooking($id){
        $data["status"] = "approved";

        Booking::where(["id"=>$id])->update($data);
        Alert::success("Success", "Booking status updated successfully");
        return redirect()->back();
    }

    public function rejectBooking($id){
        $data["status"] = "rejected";

        Booking::where(["id"=>$id])->update($data);
        Alert::success("Success", "Booking status updated successfully");
        return redirect()->back();
    }

   public function viewMyBookings(){
        // 💡 The 'teamMember' relationship is loaded here so that the worker's name is displayed on the user dashboard.
        $bookings = Booking::where(["user_id" => Auth::id()])->with(["user", "service", "teamMember"])->get();
        return view("pages.user-dashboard", compact('bookings'));
   }

    public function checkSlots(Request $request) {
        // Data was received using query('date').
        $date = $request->query('date');

        if (!$date) {
            return response()->json([]);
        }

        $bookedSlots = Booking::where('preferred_date', $date)
                        ->whereIn('status', ['approved', 'done', 'pending'])
                        ->pluck('time_slot')
                        ->toArray();

        return response()->json($bookedSlots);
    }
}
