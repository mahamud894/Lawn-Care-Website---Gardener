<?php

use App\Models\Booking;
use App\Models\Service;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\Notification;

beforeEach(fn () => Notification::fake());

it('sends guests to the login page', function (string $url) {
    $this->get($url)->assertRedirect(route('login'));
})->with([
    '/admin-panel',
    '/admin-panel/users',
    '/admin-panel/service-list',
    '/admin-panel/booking-list',
    '/admin-panel/team-list',
    '/admin-panel/projects',
    '/admin-panel/messages',
    '/admin-panel/reviews',
]);

it('blocks normal customers from the admin panel', function () {
    $this->actingAs(User::factory()->create())
        ->get('/admin-panel')
        ->assertForbidden();
});

it('blocks admins who are still waiting for approval', function () {
    $pending = User::factory()->create(['isAdmin' => 1, 'isAdminApproved' => 0]);

    $this->actingAs($pending)->get('/admin-panel')->assertForbidden();
});

it('lets approved admins in', function () {
    $this->actingAs(User::factory()->admin()->create())
        ->get('/admin-panel')
        ->assertOk();
});

it('does not let a customer delete services or users', function () {
    $customer = User::factory()->create();
    $victim = User::factory()->create();
    $service = Service::create(['title' => 'Pruning', 'description' => 'x']);

    $this->actingAs($customer)->delete("/admin-panel/services/{$service->id}")->assertForbidden();
    $this->actingAs($customer)->delete("/admin-panel/users/{$victim->id}")->assertForbidden();
    $this->actingAs($customer)->post("/admin-panel/users/{$customer->id}/toggle-role")->assertForbidden();

    expect(Service::find($service->id))->not->toBeNull()
        ->and(User::find($victim->id))->not->toBeNull()
        ->and($customer->fresh()->isAdmin)->toBeFalse();
});

it('no longer deletes anything through a plain link (GET)', function () {
    $admin = User::factory()->admin()->create();
    $service = Service::create(['title' => 'Pruning', 'description' => 'x']);

    $this->actingAs($admin)->get("/admin-panel/services/{$service->id}")->assertMethodNotAllowed();
    expect(Service::find($service->id))->not->toBeNull();
});

it('saves the assigned team member and approves the booking', function () {
    $admin = User::factory()->admin()->create();
    $member = Team::create(['title' => 'Rahim', 'position' => 'Gardener', 'image_url' => 'x.jpg']);
    $booking = Booking::create([
        'name' => 'Jane', 'email' => 'jane@example.com', 'address' => 'Narail',
        'preferred_date' => now()->addDay()->toDateString(), 'time_slot' => Booking::TIME_SLOTS[1],
    ]);

    $this->actingAs($admin)
        ->post("/admin-panel/bookings/{$booking->id}/assign", ['team_member_id' => $member->id])
        ->assertRedirect(route('viewAllBookings'));

    $booking->refresh();
    expect($booking->team_member_id)->toBe($member->id)
        ->and($booking->status)->toBe('approved');
});

it('updates booking status through the status form', function () {
    $admin = User::factory()->admin()->create();
    $booking = Booking::create(['name' => 'Jane', 'email' => 'jane@example.com', 'address' => 'Narail']);

    $this->actingAs($admin)->post("/admin-panel/bookings/{$booking->id}/status", ['status' => 'done']);

    expect($booking->fresh()->status)->toBe('done');
});

it('filters the booking list by status', function () {
    $admin = User::factory()->admin()->create();
    Booking::create(['name' => 'Pending Person', 'email' => 'a@example.com', 'status' => 'pending']);
    Booking::create(['name' => 'Finished Person', 'email' => 'b@example.com', 'status' => 'done']);

    $this->actingAs($admin)->get('/admin-panel/booking-list?status=done')
        ->assertOk()
        ->assertSee('Finished Person')
        ->assertDontSee('Pending Person');
});

it('logs out a user who gets blocked', function () {
    $user = User::factory()->create();
    $this->actingAs($user)->get('/my-dashboard')->assertOk();

    $user->update(['isBlocked' => true]);

    $this->actingAs($user)->get('/my-dashboard')->assertRedirect(route('login'));
    $this->assertGuest();
});
