<?php

use App\Models\Booking;
use App\Models\Service;
use App\Models\User;
use App\Services\BookingWorkflow;
use Illuminate\Support\Facades\Notification;

beforeEach(fn () => Notification::fake());

function regBooking(array $attrs = []): Booking
{
    return Booking::create(array_merge([
        'name' => 'Cust', 'email' => 'c@example.com', 'phone' => '01712345678', 'address' => 'Narail',
        'preferred_date' => now()->addDays(2)->toDateString(), 'time_slot' => Booking::TIME_SLOTS[0], 'status' => 'pending',
    ], $attrs));
}

it('does not crash when the booking phone already belongs to another account', function () {
    User::factory()->create(['phone' => '01799999999']);
    $googleUser = User::factory()->create(['phone' => null]);
    $service = Service::create(['title' => 'Mowing', 'description' => 'x']);

    $this->actingAs($googleUser)->post('/bookings', [
        'name' => 'Google Person', 'email' => $googleUser->email, 'phone' => '01799999999',
        'address' => 'Narail', 'service' => $service->id,
        'preferred_date' => now()->addDays(3)->toDateString(), 'time_slot' => Booking::TIME_SLOTS[0],
    ])->assertRedirect();

    expect(Booking::count())->toBe(1);
});

it('moves a repeat visit to the next free day when that day is full', function () {
    $first = regBooking(['recurrence' => 'weekly', 'status' => 'approved']);
    $nextWeek = now()->addDays(9)->toDateString();
    foreach (Booking::TIME_SLOTS as $slot) {
        regBooking(['preferred_date' => $nextWeek, 'time_slot' => $slot, 'status' => 'approved']);
    }

    BookingWorkflow::changeStatus($first, 'done', notify: false);

    $next = Booking::where('parent_booking_id', $first->id)->first();
    expect($next)->not->toBeNull()
        ->and($next->time_slot)->not->toBeNull()
        ->and(substr($next->preferred_date, 0, 10))->toBe(now()->addDays(10)->toDateString());
});

it('neutralises spreadsheet formulas in the CSV export', function () {
    $admin = User::factory()->admin()->create();
    regBooking(['name' => '=HYPERLINK("http://evil.test","click")']);

    $csv = $this->actingAs($admin)->get('/admin-panel/bookings/export')->streamedContent();

    expect($csv)->not->toContain(',"=HYPERLINK')->toContain("'=HYPERLINK");
});

it('does not let customers cancel a booking they already paid for online', function () {
    $user = User::factory()->create();
    $b = regBooking(['user_id' => $user->id, 'status' => 'approved', 'estimated_price' => 500]);
    BookingWorkflow::markPaid($b, 'Online (bKash)');

    $this->actingAs($user)->post("/my-bookings/{$b->id}/cancel");

    expect($b->fresh()->status)->toBe('approved');
});

it('uses Bangladesh time for "today"', function () {
    expect(config('app.timezone'))->toBe('Asia/Dhaka');
});

it('accepts normal phone photos (up to 8 MB)', function () {
    $service = Service::create(['title' => 'Mowing', 'description' => 'x']);
    $this->actingAs(User::factory()->create())->post('/bookings', [
        'name' => 'Big Photo', 'email' => 'b@example.com', 'phone' => '01712345678',
        'address' => 'Narail', 'service' => $service->id,
        'preferred_date' => now()->addDays(3)->toDateString(), 'time_slot' => Booking::TIME_SLOTS[0],
        'photos' => [\Illuminate\Http\UploadedFile::fake()->image('big.jpg')->size(6000)],
    ])->assertSessionHasNoErrors();

    \App\Support\ImageUpload::delete(Booking::first()->photos[0] ?? null);
});

it('lists a gardener\'s jobs in real time-of-day order', function () {
    $member = \App\Models\Team::create(['title' => 'G', 'position' => 'G', 'image_url' => 'x.jpg']);
    $gardener = User::factory()->create(['isGardener' => true]);
    $member->update(['user_id' => $gardener->id]);
    $today = now()->toDateString();
    regBooking(['name' => 'Afternoon Job', 'preferred_date' => $today, 'time_slot' => Booking::TIME_SLOTS[2], 'team_member_id' => $member->id, 'status' => 'approved']);
    regBooking(['name' => 'Morning Job', 'preferred_date' => $today, 'time_slot' => Booking::TIME_SLOTS[0], 'team_member_id' => $member->id, 'status' => 'approved']);

    $this->actingAs($gardener)->get('/gardener')->assertSeeInOrder(['Morning Job', 'Afternoon Job']);
});

it('hides the cancel button on paid bookings', function () {
    $user = User::factory()->create();
    $b = regBooking(['user_id' => $user->id, 'status' => 'approved', 'estimated_price' => 500]);
    BookingWorkflow::markPaid($b, 'Online (bKash)');

    $this->actingAs($user)->get('/my-dashboard')->assertDontSee(route('user.bookings.cancel', $b));
});

it('keeps a separate rate limit for each form', function () {
    // Several logins from one network must not block someone signing up
    $users = User::factory()->count(6)->create(['password' => 'password123']);
    foreach ($users as $u) {
        $this->post('/login', ['email' => $u->email, 'password' => 'password123']);
        $this->post('/logout');
    }

    $this->post('/signup', [
        'name' => 'New Person', 'email' => 'new@example.com', 'phone' => '01755555555',
        'password' => 'password123', 'password_confirmation' => 'password123',
    ])->assertRedirect(route('login'));

    expect(User::where('email', 'new@example.com')->exists())->toBeTrue();
});
