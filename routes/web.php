<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\TeamController;

Route::get('/', [HomeController::class, 'viewHomePage'])->name('home');

Route::get('/about', [TeamController::class, 'viewTeamPage'])->name('about');

Route::get('/contact', function () {
    return view('pages.contact');
})->name('contact');

route::get('/project', function () {
    return view('pages.project');
})->name('project');

Route::get('/quote', [BookingController::class, 'viewQuotePage'])->name('quote');


Route::get('/service', [ServiceController::class, 'viewServicePage'])->name('service');

Route::get('/login', function () {
    return view('pages.login');
})->name('login');
Route::post('/login', [AuthController::class, 'loginCheck'])->name('loginCheck');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/signup', [AuthController::class, 'viewSignup'])->name('viewSignup');
Route::post('/signup', [AuthController::class, 'signup'])->name('signup');

Route::get('/admin-panel', [AdminController::class, 'viewAdminPanel'])->name('viewAdminPanel');
// Route to view the user list and change roles.
Route::get('/admin-panel/users', [AdminController::class, 'viewUsersList'])->name('admin.users.list')->middleware('auth');
Route::get('/admin-panel/users/toggle-role/{id}', [AdminController::class, 'toggleUserRole'])->name('admin.users.toggleRole')->middleware('auth');
// 💡 Route to delete users and admins
Route::get('/admin-panel/users/delete/{id}', [AdminController::class, 'deleteUser'])->name('admin.users.delete')->middleware('auth');
// Route to block/unblock users
Route::get('/admin-panel/users/toggle-block/{id}', [AdminController::class, 'toggleBlock'])->name('admin.users.toggleBlock')->middleware('auth');

Route::get('/admin-panel/add-service', [ServiceController::class, 'viewServiceAddForm'])->name('viewServiceAddForm');
Route::post('/admin-panel/add-service', [ServiceController::class, 'storeService'])->name('admin.service.store');
Route::get('/admin-panel/service-list', [ServiceController::class, 'viewServiceList'])->name('viewServiceList');
Route::get('/admin-panel/edit-service/{id}', [ServiceController::class, 'viewServiceEditForm'])->name('viewServiceEditForm');
Route::get('/admin-panel/delete-service/{id}', [ServiceController::class, 'deleteService'])->name('deleteService');
Route::post('/admin-panel/edit-service', [ServiceController::class, 'editService'])->name('editService');

Route::get('/admin-panel/add-team', [TeamController::class, 'viewTeamAddForm'])->name('viewTeamAddForm');
Route::post('/admin-panel/add-team', [TeamController::class, 'storeTeam'])->name('admin.team.store');
Route::get('/admin-panel/team-list', [TeamController::class, 'viewTeamList'])->name('viewTeamList');
Route::get('/admin-panel/edit-team/{id}', [TeamController::class, 'viewTeamEditForm'])->name('viewTeamEditForm');
Route::get('/admin-panel/delete-team/{id}', [TeamController::class, 'deleteTeam'])->name('deleteTeam');
Route::post('/admin-panel/edit-team', [TeamController::class, 'editTeam'])->name('editTeam');

// Route to check for available slots when the user selects a date.
Route::get('/check-available-slots', [BookingController::class, 'checkSlots']);

// Route for assigning team members from the admin panel
Route::post('/admin-panel/bookings/assign-team/{id}', [AdminController::class, 'assignTeam'])->name('admin.bookings.assignTeam')->middleware('auth');
Route::get('/admin-panel/bookings/assign-view/{id}', [AdminController::class, 'showAssignPage'])->name('admin.bookings.assignView')->middleware('auth');

Route::post('/admin-panel/create-booking', [BookingController::class, 'createBooking'])->name('createBooking');
Route::get('/admin-panel/booking-list', [BookingController::class, 'viewAllBookings'])->name('viewAllBookings');
Route::get('/admin-panel/booking-pending/{id}', [BookingController::class, 'pendingBooking'])->name('pendingBooking');
Route::get('/admin-panel/booking-done/{id}', [BookingController::class, 'doneBooking'])->name('doneBooking');
Route::get('/admin-panel/booking-approve/{id}', [BookingController::class, 'approveBooking'])->name('approveBooking');
Route::get('/admin-panel/booking-reject/{id}', [BookingController::class, 'rejectBooking'])->name('rejectBooking');
Route::get('/my-dashboard', [BookingController::class, 'viewMyBookings'])->name('user.dashboard')->middleware('auth');

