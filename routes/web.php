<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\GardenerController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\TeamController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public pages
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'viewHomePage'])->name('home');
Route::get('/about', [TeamController::class, 'viewTeamPage'])->name('about');
Route::get('/service', [ServiceController::class, 'viewServicePage'])->name('service');
Route::get('/service/{slug}', [ServiceController::class, 'show'])->name('service.show');
Route::get('/blog', [PostController::class, 'index'])->name('blog');
Route::get('/blog/{post}', [PostController::class, 'show'])->name('blog.show');
Route::get('/faq', [FaqController::class, 'index'])->name('faq');

// Language switcher (English / বাংলা)
Route::get('/lang/{locale}', function (string $locale) {
    if (array_key_exists($locale, config('gardener.locales'))) {
        session(['locale' => $locale]);
    }

    return redirect()->back();
})->name('locale');
Route::get('/project', [ProjectController::class, 'viewProjectPage'])->name('project');

Route::get('/contact', [ContactController::class, 'viewContactPage'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store')->middleware('throttle:contact');

Route::get('/quote', [BookingController::class, 'viewQuotePage'])->name('quote');
Route::post('/bookings', [BookingController::class, 'createBooking'])->name('createBooking')->middleware('throttle:booking');
// Returns the time slots already taken on a date (used by the quote form)
Route::get('/check-available-slots', [BookingController::class, 'checkSlots'])->name('checkSlots');

// Sitemap for search engines (submit /sitemap.xml in Google Search Console)
Route::get('/sitemap.xml', function () {
    $urls = collect(['home', 'service', 'about', 'project', 'contact', 'quote', 'blog', 'faq'])->map(fn ($name) => route($name))
        ->merge(\App\Models\Service::where('isActive', 1)->pluck('slug')->filter()->map(fn ($slug) => route('service.show', $slug)))
        ->merge(\App\Models\Post::published()->pluck('slug')->map(fn ($slug) => route('blog.show', $slug)));

    $xml = '<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'
        .$urls->map(fn ($u) => '<url><loc>'.e($u).'</loc></url>')->implode('')
        .'</urlset>';

    return response($xml, 200, ['Content-Type' => 'application/xml']);
})->name('sitemap');

/*
|--------------------------------------------------------------------------
| Login / signup / password reset (only for guests)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'viewLogin'])->name('login');
    // Wrong passwords are limited per e-mail inside the controller; this only stops floods
    Route::post('/login', [AuthController::class, 'loginCheck'])->name('loginCheck')->middleware('throttle:login');

    Route::get('/signup', [AuthController::class, 'viewSignup'])->name('viewSignup');
    Route::post('/signup', [AuthController::class, 'signup'])->name('signup')->middleware('throttle:signup');

    Route::get('/forgot-password', [PasswordResetController::class, 'viewForgotForm'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink'])->name('password.email')->middleware('throttle:password-reset');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'viewResetForm'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'resetPassword'])->name('password.update');

    Route::get('/auth/google', [GoogleAuthController::class, 'redirect'])->name('google.redirect');
    Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])->name('google.callback');
});

/*
| SSLCommerz returns here (POST from their site, CSRF-exempt)
*/
Route::post('/payment/sslcommerz/success', [PaymentController::class, 'success'])->name('payment.success');
Route::post('/payment/sslcommerz/fail', [PaymentController::class, 'fail'])->name('payment.fail');
Route::post('/payment/sslcommerz/cancel', [PaymentController::class, 'cancel'])->name('payment.cancel');
Route::post('/payment/sslcommerz/ipn', [PaymentController::class, 'ipn'])->name('payment.ipn');

/*
|--------------------------------------------------------------------------
| Logged-in customers
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/my-dashboard', [BookingController::class, 'viewMyBookings'])->name('user.dashboard');
    Route::post('/my-bookings/{booking}/cancel', [BookingController::class, 'cancelMyBooking'])->name('user.bookings.cancel');
    Route::post('/my-bookings/{booking}/reschedule', [BookingController::class, 'rescheduleMyBooking'])->name('user.bookings.reschedule');
    Route::post('/my-bookings/{booking}/review', [ReviewController::class, 'store'])->name('user.bookings.review');
    Route::post('/my-bookings/{booking}/stop-repeat', [BookingController::class, 'stopRepeating'])->name('user.bookings.stopRepeat');
    Route::post('/my-bookings/{booking}/pay', [PaymentController::class, 'pay'])->name('user.bookings.pay');
    Route::get('/bookings/{booking}/invoice', [InvoiceController::class, 'show'])->name('bookings.invoice');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
});

/*
|--------------------------------------------------------------------------
| Gardener portal — team members with a login
|--------------------------------------------------------------------------
*/
Route::prefix('gardener')->middleware(['auth', 'gardener'])->group(function () {
    Route::get('/', [GardenerController::class, 'dashboard'])->name('gardener.dashboard');
    Route::post('/jobs/{booking}/done', [GardenerController::class, 'markDone'])->name('gardener.jobs.done');
});

/*
|--------------------------------------------------------------------------
| Admin panel — only approved, non-blocked admins
|--------------------------------------------------------------------------
*/
Route::prefix('admin-panel')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', [AdminController::class, 'viewAdminPanel'])->name('viewAdminPanel');

    // Users
    Route::get('/users', [AdminController::class, 'viewUsersList'])->name('admin.users.list');
    Route::post('/users/{id}/toggle-role', [AdminController::class, 'toggleUserRole'])->name('admin.users.toggleRole');
    Route::post('/users/{id}/toggle-block', [AdminController::class, 'toggleBlock'])->name('admin.users.toggleBlock');
    Route::delete('/users/{id}', [AdminController::class, 'deleteUser'])->name('admin.users.delete');

    // Services
    Route::get('/add-service', [ServiceController::class, 'viewServiceAddForm'])->name('viewServiceAddForm');
    Route::post('/add-service', [ServiceController::class, 'storeService'])->name('admin.service.store');
    Route::get('/service-list', [ServiceController::class, 'viewServiceList'])->name('viewServiceList');
    Route::get('/edit-service/{id}', [ServiceController::class, 'viewServiceEditForm'])->name('viewServiceEditForm');
    Route::post('/edit-service', [ServiceController::class, 'editService'])->name('editService');
    Route::delete('/services/{id}', [ServiceController::class, 'deleteService'])->name('deleteService');

    // Team
    Route::get('/add-team', [TeamController::class, 'viewTeamAddForm'])->name('viewTeamAddForm');
    Route::post('/add-team', [TeamController::class, 'storeTeam'])->name('admin.team.store');
    Route::get('/team-list', [TeamController::class, 'viewTeamList'])->name('viewTeamList');
    Route::get('/edit-team/{id}', [TeamController::class, 'viewTeamEditForm'])->name('viewTeamEditForm');
    Route::post('/edit-team', [TeamController::class, 'editTeam'])->name('editTeam');
    Route::delete('/team/{id}', [TeamController::class, 'deleteTeam'])->name('deleteTeam');

    // Bookings
    Route::get('/booking-list', [BookingController::class, 'viewAllBookings'])->name('viewAllBookings');
    Route::get('/bookings/export', [BookingController::class, 'exportCsv'])->name('admin.bookings.export');
    Route::post('/bookings/{id}/paid', [BookingController::class, 'markPaid'])->name('admin.bookings.paid');
    Route::get('/calendar', [CalendarController::class, 'index'])->name('admin.calendar');
    Route::get('/reports', [ReportController::class, 'index'])->name('admin.reports');
    Route::get('/activity', [AdminController::class, 'viewActivityLog'])->name('admin.activity');
    Route::post('/bookings/{id}/status', [BookingController::class, 'updateStatus'])->name('admin.bookings.status');
    Route::get('/bookings/{id}/assign', [AdminController::class, 'showAssignPage'])->name('admin.bookings.assignView');
    Route::post('/bookings/{id}/assign', [AdminController::class, 'assignTeam'])->name('admin.bookings.assignTeam');

    // Projects (gallery)
    Route::get('/projects', [ProjectController::class, 'index'])->name('admin.projects.list');
    Route::get('/projects/create', [ProjectController::class, 'create'])->name('admin.projects.create');
    Route::post('/projects', [ProjectController::class, 'store'])->name('admin.projects.store');
    Route::get('/projects/{project}/edit', [ProjectController::class, 'edit'])->name('admin.projects.edit');
    Route::put('/projects/{project}', [ProjectController::class, 'update'])->name('admin.projects.update');
    Route::delete('/projects/{project}', [ProjectController::class, 'destroy'])->name('admin.projects.delete');

    // Blog
    Route::get('/posts', [PostController::class, 'adminIndex'])->name('admin.posts.list');
    Route::get('/posts/create', [PostController::class, 'create'])->name('admin.posts.create');
    Route::post('/posts', [PostController::class, 'store'])->name('admin.posts.store');
    Route::get('/posts/{post:id}/edit', [PostController::class, 'edit'])->name('admin.posts.edit');
    Route::put('/posts/{post:id}', [PostController::class, 'update'])->name('admin.posts.update');
    Route::delete('/posts/{post:id}', [PostController::class, 'destroy'])->name('admin.posts.delete');

    // FAQ
    Route::get('/faqs', [FaqController::class, 'adminIndex'])->name('admin.faqs.list');
    Route::post('/faqs', [FaqController::class, 'store'])->name('admin.faqs.store');
    Route::put('/faqs/{faq}', [FaqController::class, 'update'])->name('admin.faqs.update');
    Route::delete('/faqs/{faq}', [FaqController::class, 'destroy'])->name('admin.faqs.delete');

    // Contact messages
    Route::get('/messages', [ContactController::class, 'index'])->name('admin.messages.list');
    Route::post('/messages/{message}/toggle-read', [ContactController::class, 'toggleRead'])->name('admin.messages.toggleRead');
    Route::delete('/messages/{message}', [ContactController::class, 'destroy'])->name('admin.messages.delete');

    // Reviews
    Route::get('/reviews', [ReviewController::class, 'index'])->name('admin.reviews.list');
    Route::post('/reviews/{review}/toggle-approve', [ReviewController::class, 'toggleApprove'])->name('admin.reviews.toggleApprove');
    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])->name('admin.reviews.delete');
});
