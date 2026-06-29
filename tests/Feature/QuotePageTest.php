<?php

use App\Models\Booking;
use App\Models\Service;

it('renders the public quote page', function () {
    $response = $this->get('/quote');

    $response->assertStatus(200);
    $response->assertSee('Request A Quote');
    $response->assertSee('preferred_date');
});

it('stores a booking with a preferred date', function () {
    $response = $this->post('/admin-panel/create-booking', [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'service' => 1,
        'preferred_date' => '2026-06-20',
        'message' => 'Please visit soon.',
    ]);

    $response->assertRedirect();
});
