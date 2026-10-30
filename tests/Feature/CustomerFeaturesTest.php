<?php

use App\Models\Booking;
use App\Models\ContactMessage;
use App\Models\Project;
use App\Models\Review;
use App\Models\User;
use App\Notifications\NewContactMessage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Notification;

beforeEach(fn () => Notification::fake());

function bookingFor(User $user, array $attrs = []): Booking
{
    return Booking::create(array_merge([
        'name' => $user->name, 'email' => $user->email, 'address' => 'Narail',
        'user_id' => $user->id, 'status' => 'pending',
        'preferred_date' => now()->addDays(2)->toDateString(), 'time_slot' => Booking::TIME_SLOTS[0],
    ], $attrs));
}

it('saves contact messages and e-mails the admin', function () {
    config(['app.admin_email' => 'owner@example.com']);

    $this->post('/contact', [
        'name' => 'Visitor', 'email' => 'visitor@example.com',
        'subject' => 'Price', 'message' => 'How much for weekly mowing?',
    ])->assertRedirect();

    expect(ContactMessage::count())->toBe(1);
    Notification::assertSentOnDemand(NewContactMessage::class);
});

it('ignores contact spam bots', function () {
    $this->post('/contact', [
        'name' => 'Bot', 'email' => 'bot@example.com', 'message' => 'spam', 'website' => 'http://spam.test',
    ]);

    expect(ContactMessage::count())->toBe(0);
});

it('lets a customer cancel their own booking', function () {
    $user = User::factory()->create();
    $booking = bookingFor($user);

    $this->actingAs($user)->post("/my-bookings/{$booking->id}/cancel");

    expect($booking->fresh()->status)->toBe('cancelled');
});

it('does not let a customer cancel someone else\'s booking', function () {
    $owner = User::factory()->create();
    $other = User::factory()->create();
    $booking = bookingFor($owner);

    $this->actingAs($other)->post("/my-bookings/{$booking->id}/cancel")->assertForbidden();
    expect($booking->fresh()->status)->toBe('pending');
});

it('lets a customer reschedule a pending booking to a free slot', function () {
    $user = User::factory()->create();
    $booking = bookingFor($user);
    $newDate = now()->addDays(5)->toDateString();

    $this->actingAs($user)->post("/my-bookings/{$booking->id}/reschedule", [
        'preferred_date' => $newDate, 'time_slot' => Booking::TIME_SLOTS[2],
    ]);

    $booking->refresh();
    expect($booking->preferred_date)->toStartWith($newDate)
        ->and($booking->time_slot)->toBe(Booking::TIME_SLOTS[2]);
});

it('lets a customer review a finished job once', function () {
    $user = User::factory()->create();
    $booking = bookingFor($user, ['status' => 'done']);

    $this->actingAs($user)->post("/my-bookings/{$booking->id}/review", ['rating' => 5, 'comment' => 'Great job, very tidy work!']);
    $this->actingAs($user)->post("/my-bookings/{$booking->id}/review", ['rating' => 1, 'comment' => 'Trying to review twice']);

    expect(Review::count())->toBe(1)
        ->and(Review::first()->isApproved)->toBeFalse();
});

it('shows approved reviews on the home page', function () {
    $user = User::factory()->create(['name' => 'Happy Client']);
    $booking = bookingFor($user, ['status' => 'done']);
    Review::create(['user_id' => $user->id, 'booking_id' => $booking->id, 'rating' => 5, 'comment' => 'Wonderful garden makeover', 'isApproved' => true]);

    $this->get('/')->assertOk()->assertSee('Wonderful garden makeover')->assertSee('Happy Client');
});

it('lets admins add a project that shows on the projects page', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)->post('/admin-panel/projects', [
        'title' => 'Rooftop Garden', 'category' => 'complete', 'isActive' => 1,
        'image' => UploadedFile::fake()->image('roof.jpg'),
    ])->assertRedirect(route('admin.projects.list'));

    $project = Project::first();
    expect(File::exists(public_path($project->image_url)))->toBeTrue();

    $this->get('/project')->assertOk()->assertSee('Rooftop Garden');

    // clean up the test upload
    $this->actingAs($admin)->delete("/admin-panel/projects/{$project->id}");
    expect(File::exists(public_path($project->image_url)))->toBeFalse();
});

it('renders every public page', function (string $url) {
    $this->get($url)->assertOk();
})->with(['/', '/about', '/service', '/project', '/contact', '/quote', '/login', '/signup', '/forgot-password']);

it('serves a sitemap', function () {
    $this->get('/sitemap.xml')->assertOk()->assertSee('<urlset', false);
});
