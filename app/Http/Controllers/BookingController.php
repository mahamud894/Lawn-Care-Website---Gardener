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
            'preferred_date' => ['nullable', 'date'],
        ];

        if (Schema::hasTable('services')) {
            $rules['service'] = ['nullable', 'exists:services,id'];
        }

        $validated = $req->validate($rules);

        $data["name"] = $req->name;
        $data["email"] = $req->email;
        $data["message"] = $req->message;
        $data["preferred_date"] = $req->preferred_date;
        $data["service_id"] = $req->filled('service') ? $req->service : null;
        $data["user_id"] = Auth::check() ? Auth::id() : null;

        Booking::create($data);
        Alert::success("Success", "Booking submitted successfully");
        return redirect()->back();
    }

    public function viewQuotePage(Request $request){ // 💡 Request $request ইনজেক্ট করা হয়েছে
    $services = Schema::hasTable('services') ? Service::all() : collect();

    // URL থেকে service_id প্যারামিটারটি নেওয়া হচ্ছে (যেমন: /quote?service_id=2)
    $selectedServiceId = $request->query('service_id');

    // ব্লেড ফাইলে ডাটা পাঠানো হলো
    return view("pages.quote", compact("services", "selectedServiceId"));
   }
    public function viewAllBookings(){
        $bookings = Booking::with(["user","service"])->get();
        return view("pages.admin-panel.booking.list", ["bookings"=>$bookings]);
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
        $bookings = Booking::where(["user_id" => Auth::id()])->with(["user","service"])->get();
        return view("pages.user-dashboard", compact('bookings'));
}
}
