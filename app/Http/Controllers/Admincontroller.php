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

        // যদি লগইন থাকা মেইন অ্যাডমিন নিজের রোল পরিবর্তন করতে চায়, তবে তাকে বাধা দেওয়া হবে
        if (auth()->id() == $user->id) {
            \RealRashid\SweetAlert\Facades\Alert::error('Error', 'You cannot change your own admin status!');
            return redirect()->back();
        }

        // টগল লজিক: ০ থাকলে ১ হবে, ১ থাকলে ০ হবে
        $user->isAdmin = $user->isAdmin == 1 ? 0 : 1;
        $user->save();

        \RealRashid\SweetAlert\Facades\Alert::success('Success', 'User role updated successfully');
        return redirect()->back();
    }
}
