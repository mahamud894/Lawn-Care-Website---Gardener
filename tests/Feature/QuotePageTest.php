<?php

use App\Models\Booking;
use App\Models\Service;
use App\Models\User;
use Illuminate\Support\Facades\Notification;

function bookingData(array $overrides = []): array
{
    return array_merge([
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'address' => '12 Green Road, Narail',
        'service' => Service::create(['title' => 'Lawn Mowing', 'description' => 'Mowing'])->id,
        'preferred_date' => now()->addDays(3)->toDateString(),
        'time_slot' => Booking::TIME_SLOTS[0],
        'message' => 'Please visit soon.',
    ], $overrides);
}

beforeEach(fn () => Notification::fake());

it('renders the public quote page', function () {
    $this->get('/quote')
        ->assertOk()
        ->assertSee('Request A Quote')
        ->assertSee('preferred_date');
});

it('asks guests to log in before booking', function () {
    $this->post('/bookings', bookingData())->assertRedirect(route('login'));

    expect(Booking::count())->toBe(0);
});

it('stores a booking for a logged-in customer', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->post('/bookings', bookingData())->assertRedirect();

    $booking = Booking::first();
    expect($booking)->not->toBeNull()
        ->and($booking->user_id)->toBe($user->id)
        ->and($booking->status)->toBe('pending')
        ->and($booking->address)->toBe('12 Green Road, Narail');
});

it('rejects a date in the past', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post('/bookings', bookingData(['preferred_date' => now()->subDay()->toDateString()]))
        ->assertSessionHasErrors('preferred_date');
});

it('rejects a time slot that does not exist', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post('/bookings', bookingData(['time_slot' => '03:00 AM - 04:00 AM']))
        ->assertSessionHasErrors('time_slot');
});

it('does not allow two bookings in the same date and slot', function () {
    [$a, $b] = User::factory()->count(2)->create();
    $data = bookingData();

    $this->actingAs($a)->post('/bookings', $data);
    $this->actingAs($b)->post('/bookings', $data)->assertSessionHasErrors('time_slot');

    expect(Booking::count())->toBe(1);
});

it('frees a slot again when the booking is cancelled', function () {
    $user = User::factory()->create();
    $data = bookingData();

    $this->actingAs($user)->post('/bookings', $data);
    Booking::first()->update(['status' => 'cancelled']);

    $this->get('/check-available-slots?date='.$data['preferred_date'])->assertExactJson([]);
    $this->actingAs($user)->post('/bookings', $data)->assertSessionHasNoErrors();
});

it('lists taken slots for a date', function () {
    $user = User::factory()->create();
    $data = bookingData();
    $this->actingAs($user)->post('/bookings', $data);

    $this->get('/check-available-slots?date='.$data['preferred_date'])
        ->assertExactJson([Booking::TIME_SLOTS[0]]);
});
