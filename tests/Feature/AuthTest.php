<?php

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Notification;

it('signs up a customer', function () {
    $this->post('/signup', [
        'name' => 'Karim', 'email' => 'karim@example.com', 'phone' => '01711111111',
        'password' => 'password123', 'password_confirmation' => 'password123', 'role' => 0,
    ])->assertRedirect(route('login'));

    $user = User::where('email', 'karim@example.com')->first();
    expect($user->isAdmin)->toBeFalse()->and($user->isAdminApproved)->toBeTrue();
});

it('creates admin requests as pending', function () {
    $this->post('/signup', [
        'name' => 'Helper', 'email' => 'helper@example.com', 'phone' => '01722222222',
        'password' => 'password123', 'password_confirmation' => 'password123', 'role' => 1,
    ]);

    $user = User::where('email', 'helper@example.com')->first();
    expect($user->isAdmin)->toBeTrue()->and($user->isAdminApproved)->toBeFalse();
});

it('rejects duplicate e-mails and short passwords', function () {
    User::factory()->create(['email' => 'taken@example.com']);

    $this->post('/signup', [
        'name' => 'X', 'email' => 'taken@example.com', 'phone' => '01733333333',
        'password' => '123', 'password_confirmation' => '123',
    ])->assertSessionHasErrors(['email', 'password']);
});

it('logs in with correct credentials', function () {
    $user = User::factory()->create(['password' => 'password123']);

    $this->post('/login', ['email' => $user->email, 'password' => 'password123'])
        ->assertRedirect(route('home'));
    $this->assertAuthenticatedAs($user);
});

it('does not reveal blocked status to someone with a wrong password', function () {
    $user = User::factory()->create(['password' => 'password123', 'isBlocked' => true]);

    $this->post('/login', ['email' => $user->email, 'password' => 'wrong-pass']);
    $this->assertGuest();
});

it('refuses blocked users even with the right password', function () {
    $user = User::factory()->create(['password' => 'password123', 'isBlocked' => true]);

    $this->post('/login', ['email' => $user->email, 'password' => 'password123']);
    $this->assertGuest();
});

it('rate-limits repeated login attempts', function () {
    $user = User::factory()->create();

    foreach (range(1, 5) as $i) {
        $this->post('/login', ['email' => $user->email, 'password' => 'wrong']);
    }

    $this->post('/login', ['email' => $user->email, 'password' => 'wrong'])->assertStatus(429);
});

it('only logs out via POST', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->get('/logout')->assertMethodNotAllowed();
    $this->actingAs($user)->post('/logout')->assertRedirect(route('login'));
    $this->assertGuest();
});

it('sends a password reset link and resets the password', function () {
    Notification::fake();
    $user = User::factory()->create();

    $this->get('/forgot-password')->assertOk();
    $this->post('/forgot-password', ['email' => $user->email])->assertRedirect(route('login'));

    Notification::assertSentTo($user, ResetPassword::class, function ($notification) use ($user) {
        $this->post('/reset-password', [
            'token' => $notification->token,
            'email' => $user->email,
            'password' => 'brand-new-pass',
            'password_confirmation' => 'brand-new-pass',
        ])->assertRedirect(route('login'));

        return true;
    });

    $this->post('/login', ['email' => $user->email, 'password' => 'brand-new-pass']);
    $this->assertAuthenticatedAs($user);
});
