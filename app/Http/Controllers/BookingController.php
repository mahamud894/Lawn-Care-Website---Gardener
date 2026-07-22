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
            'time_slot' => ['required'],
        ];

        if (Schema::hasTable('services')) {
            $rules['service'] = ['nullable', 'exists:services,id'];
        }

        $validated = $req->validate($rules);

        $data["name"] = $req->name;
        $data["email"] = $req->email;
        $data["message"] = $req->message;
        $data["preferred_date"] = $req->preferred_date;
        $data["time_slot"] = $req->time_slot;
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
    // সব বুকিং তুলে আনা হচ্ছে
    $bookings = Booking::with(["user", "service", "teamMember"])->get();

    // ডাটাবেজ থেকে সব টিম মেম্বারদের তুলে আনা হচ্ছে 💡
    $teamMembers = \App\Models\Team::all();

    // বুকিং এবং টিম মেম্বার উভয় ডাটাই ব্লেড ফাইলে পাঠানো হলো
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
        // 💡 এখানে 'teamMember' রিলেশনটি লোড করে দেওয়া হলো, যেন ইউজার ড্যাশবোর্ডে ওয়ার্কারের নাম শো করে
        $bookings = Booking::where(["user_id" => Auth::id()])->with(["user", "service", "teamMember"])->get();
        return view("pages.user-dashboard", compact('bookings'));
   }

    public function checkSlots(Request $request) {
    // ওই নির্দিষ্ট ডেটে অলরেডি বুকড হওয়া স্লটগুলোর লিস্ট নেওয়া হচ্ছে
    $bookedSlots = Booking::where('preferred_date', $request->date)
                        ->whereIn('status', ['approved', 'done', 'pending'])
                        ->pluck('time_slot')
                        ->toArray();

    return response()->json($bookedSlots);
}
}
