<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Booking;
use App\Models\Team;
use App\Models\Service;

class AdminController extends Controller
{
    public function viewAdminPanel(){
        // ডাটাবেজ থেকে লাইভ কাউন্টগুলো তুলে আনা হচ্ছে
        $totalUsers = User::where('isAdmin', 0)->count(); // শুধু সাধারণ ইউজার
        $totalServices = Service::count();

        $pendingBookings = Booking::where('status', 'pending')->count();
        $approvedBookings = Booking::where('status', 'approved')->count();
        $doneBookings = Booking::where('status', 'done')->count();

        $totalTeam = Team::count();

        // সব ডাটা অ্যাডমিন প্যানেলের হোম পেজে পাঠানো হলো
        return view('pages.admin-panel.home', compact(
            'totalUsers',
            'totalServices',
            'pendingBookings',
            'approvedBookings',
            'doneBookings',
            'totalTeam'
        ));
    }

    public function viewUsersList() {
        // ডাটাবেজের সব ইউজারকে আইডি অনুযায়ী সিরিয়াল করে আনা হচ্ছে
        $users = User::orderBy('id', 'desc')->get();
        return view('pages.admin-panel.users.list', compact('users'));
    }

    // ২. ওয়ান-ক্লিকে রোল পরিবর্তন (User কে Admin / Admin কে User) করার মেথড
    public function toggleUserRole($id) {
    $user = User::findOrFail($id);

    // নিজের অ্যাকাউন্ট নিজে চেঞ্জ করা ব্লক করা হলো
    if (auth()->id() == $user->id) {
        \RealRashid\SweetAlert\Facades\Alert::error('Error', 'You cannot change your own status!');
        return redirect()->back();
    }

    // যদি পেন্ডিং কোনো অ্যাডমিনকে মেইন অ্যাডমিন ক্লিক করে, তাকে একটিভ অ্যাডমিন বানানো হবে
    if($user->isAdmin == 1 && $user->isAdminApproved == 0) {
        $user->isAdminApproved = 1;
    } else {
        // রেগুলার ইউজার বা অলরেডি একটিভ থাকা রোলগুলোর মধ্যে টগল (User <-> Admin) হবে
        $user->isAdmin = $user->isAdmin == 1 ? 0 : 1;
        $user->isAdminApproved = 1;
    }

    $user->save();
    \RealRashid\SweetAlert\Facades\Alert::success('Success', 'User status updated successfully');
    return redirect()->back();
}
public function deleteUser($id) {
    // ডাটাবেজ থেকে ইউজার বা অ্যাডমিনকে খুঁজে বের করা হচ্ছে
    $user = User::findOrFail($id);

    // 💡 নিরাপত্তা লজিক: লগইন থাকা মেইন এডমিন যাতে ভুল করে নিজেকে নিজে ডিলিট না করতে পারে
    if (auth()->id() == $user->id) {
        \RealRashid\SweetAlert\Facades\Alert::error('Error', 'You cannot delete your own logged-in account!');
        return redirect()->back();
    }

    // ডাটাবেজ থেকে পার্মানেন্টলি ডিলিট করা হচ্ছে
    $user->delete();

    \RealRashid\SweetAlert\Facades\Alert::success('Success', 'Account deleted successfully');
    return redirect()->back();
}
public function toggleBlock($id) {
    $user = User::findOrFail($id);

    if (auth()->id() == $user->id) {
        \RealRashid\SweetAlert\Facades\Alert::error('Error', 'You cannot block yourself!');
        return redirect()->back();
    }

    // ০ থাকলে ১ হবে, ১ থাকলে ০ হবে (Toggle)
    $user->isBlocked = $user->isBlocked == 1 ? 0 : 1;
    $user->save();

    $status = $user->isBlocked == 1 ? 'Blocked' : 'Unblocked';
    \RealRashid\SweetAlert\Facades\Alert::success('Success', "User has been {$status} successfully");
    return redirect()->back();
}

public function assignTeam(Request $request, $id) {
    $booking = Booking::findOrFail($id);
    $booking->team_member_id = $request->team_member_id;
    $booking->status = 'approved'; // টিম মেম্বার এসাইন করলে বুকিং অটো এপ্রুভড হবে
    $booking->save();

    \RealRashid\SweetAlert\Facades\Alert::success('Success', 'Team member assigned successfully!');
    return redirect()->back();
}

public function showAssignPage($id) {
    $booking = Booking::findOrFail($id);
    $teamMembers = Team::all();

    return view('pages.admin-panel.booking.assign', compact('booking', 'teamMembers'));
}
}
