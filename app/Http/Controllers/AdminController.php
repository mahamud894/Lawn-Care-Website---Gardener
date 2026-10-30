<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Booking;
use App\Models\Team;
use App\Models\Service;
use App\Models\ContactMessage;
use App\Models\ActivityLog;
use App\Models\Review;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    public function viewAdminPanel(){
        // Live counts are being retrieved from the database
        $totalUsers = User::where('isAdmin', 0)->count(); // Only regular users
        $totalServices = Service::count();

        $pendingBookings = Booking::where('status', 'pending')->count();
        $approvedBookings = Booking::where('status', 'approved')->count();
        $doneBookings = Booking::where('status', 'done')->count();

        $totalTeam = Team::count();
        $unreadMessages = ContactMessage::where('isRead', false)->count();
        $pendingReviews = Review::where('isApproved', false)->count();

        // All data has been sent to the 'admin panel's home page.
        return view('pages.admin-panel.home', compact(
            'totalUsers',
            'totalServices',
            'pendingBookings',
            'approvedBookings',
            'doneBookings',
            'totalTeam',
            'unreadMessages',
            'pendingReviews'
        ));
    }

    public function viewActivityLog() {
        $logs = ActivityLog::with('user')->latest('id')->paginate(30);
        return view('pages.admin-panel.activity', compact('logs'));
    }

    public function viewUsersList() {
        //All users from the database are being retrieved and arranged sequentially by ID.
        $users = User::orderBy('id', 'desc')->get();
        // Note: the Users page shows customers and admins in two separate tables
        return view('pages.admin-panel.users.list', compact('users'));
    }

    // ২. one click role change (User to Admin / Admin to User) method
    public function toggleUserRole($id) {
    $user = User::findOrFail($id);

    // The ability to change or block one's own account has been disabled.
    if (auth()->id() == $user->id) {
        \RealRashid\SweetAlert\Facades\Alert::error('Error', 'You cannot change your own status!');
        return redirect()->back();
    }

    // If a main admin clicks on a pending admin, that person will be made an active admin.
    if($user->isAdmin == 1 && $user->isAdminApproved == 0) {
        $user->isAdminApproved = 1;
    } else {
        // There will be a toggle (User <-> Admin) between regular users and roles that are already active.
        $user->isAdmin = $user->isAdmin == 1 ? 0 : 1;
        $user->isAdminApproved = 1;
    }

    $user->save();
    ActivityLog::record('user.role', "{$user->email} is now ".($user->isAdmin ? 'an admin' : 'a regular user'), $user);
    \RealRashid\SweetAlert\Facades\Alert::success('Success', 'User status updated successfully');
    return redirect()->back();
}
public function deleteUser($id) {
    // here finding the user or admin from database
    $user = User::findOrFail($id);

    // security logic : main admin can not delete hisown logged-in account
    if (auth()->id() == $user->id) {
        \RealRashid\SweetAlert\Facades\Alert::error('Error', 'You cannot delete your own logged-in account!');
        return redirect()->back();
    }

    // Delete permanently from database
    ActivityLog::record('user.delete', "Deleted account {$user->email}", $user);
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

    // If 0 change in 1, If 1 change in 0  (Toggle)
    $user->isBlocked = $user->isBlocked == 1 ? 0 : 1;
    $user->save();

    $status = $user->isBlocked == 1 ? 'Blocked' : 'Unblocked';
    ActivityLog::record('user.block', "{$status} {$user->email}", $user);
    \RealRashid\SweetAlert\Facades\Alert::success('Success', "User has been {$status} successfully");
    return redirect()->back();
}

public function assignTeam(Request $request, $id) {
    $request->validate([
        'team_member_id' => ['nullable', Rule::exists('teams', 'id')],
    ]);

    $booking = Booking::findOrFail($id);

    // A gardener can't be in two places at the same time
    if ($request->team_member_id && in_array((int) $request->team_member_id, $booking->busyTeamMemberIds())) {
        \RealRashid\SweetAlert\Facades\Alert::error('Busy', 'This team member already has another job at the same date and time.');
        return redirect()->back();
    }

    $booking->team_member_id = $request->team_member_id ?: null;

    // Assigning a member approves a pending booking automatically
    if ($booking->team_member_id && $booking->status === 'pending') {
        $booking->status = 'approved';
    }

    $booking->save();
    $booking->load(['service', 'teamMember']);
    ActivityLog::record('booking.assign', "Booking #{$booking->id} assigned to ".($booking->teamMember?->title ?? 'nobody'), $booking);

    if ($booking->team_member_id) {
        \App\Services\BookingWorkflow::notifyCustomer($booking);
        \RealRashid\SweetAlert\Facades\Alert::success('Success', 'Team member assigned successfully!');
    } else {
        \RealRashid\SweetAlert\Facades\Alert::success('Success', 'Team member removed from this booking.');
    }

    return redirect()->route('viewAllBookings');
}

public function showAssignPage($id) {
    $booking = Booking::findOrFail($id);
    $teamMembers = Team::where('isActive', 1)->get();
    $busyIds = $booking->busyTeamMemberIds();

    return view('pages.admin-panel.booking.assign', compact('booking', 'teamMembers', 'busyIds'));
}
}
