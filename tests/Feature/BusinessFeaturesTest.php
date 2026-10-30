<?php

use App\Models\ActivityLog;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Service;
use App\Models\Team;
use App\Models\User;
use App\Services\BookingWorkflow;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;

beforeEach(fn () => Notification::fake());

function makeTeam(int $count): \Illuminate\Support\Collection
{
    return collect(range(1, $count))->map(fn ($i) => Team::create([
        'title' => "Gardener {$i}", 'position' => 'Gardener', 'image_url' => 'site/img/team-1.jpg',
    ]));
}

function newBooking(array $attrs = []): Booking
{
    return Booking::create(array_merge([
        'name' => 'Cust', 'email' => 'c@example.com', 'phone' => '01712345678', 'address' => 'Narail',
        'preferred_date' => now()->addDays(2)->toDateString(), 'time_slot' => Booking::TIME_SLOTS[0], 'status' => 'pending',
    ], $attrs));
}

function bookingPayload(Service $service, array $overrides = []): array
{
    return array_merge([
        'name' => 'Jane Doe', 'email' => 'jane@example.com', 'phone' => '01712345678',
        'address' => 'Kalia, Narail', 'service' => $service->id,
        'preferred_date' => now()->addDays(3)->toDateString(), 'time_slot' => Booking::TIME_SLOTS[0],
    ], $overrides);
}

/*
| Slot capacity & assignment
*/

it('lets a slot take one booking per active gardener', function () {
    makeTeam(2);
    $service = Service::create(['title' => 'Mowing', 'description' => 'x']);
    [$a, $b, $c] = User::factory()->count(3)->create();

    $this->actingAs($a)->post('/bookings', bookingPayload($service))->assertSessionHasNoErrors();
    $this->actingAs($b)->post('/bookings', bookingPayload($service))->assertSessionHasNoErrors();
    $this->actingAs($c)->post('/bookings', bookingPayload($service))->assertSessionHasErrors('time_slot');

    expect(Booking::count())->toBe(2);
});

it('only reports a slot as full when capacity is reached', function () {
    makeTeam(2);
    $date = now()->addDays(2)->toDateString();
    newBooking(['preferred_date' => $date]);

    $this->get("/check-available-slots?date={$date}")->assertExactJson([]);

    newBooking(['preferred_date' => $date]);
    $this->get("/check-available-slots?date={$date}")->assertExactJson([Booking::TIME_SLOTS[0]]);
});

it('will not assign a gardener who is busy at the same time', function () {
    [$g1] = makeTeam(2);
    $admin = User::factory()->admin()->create();
    newBooking(['team_member_id' => $g1->id, 'status' => 'approved']);
    $second = newBooking();

    $this->actingAs($admin)->post("/admin-panel/bookings/{$second->id}/assign", ['team_member_id' => $g1->id]);

    expect($second->fresh()->team_member_id)->toBeNull();
});

/*
| Gardener portal
*/

it('lets an admin give a team member a login and the gardener sees only their jobs', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)->post('/admin-panel/add-team', [
        'name' => 'Rahim', 'position' => 'Gardener',
        'image' => UploadedFile::fake()->image('r.jpg'),
        'login_email' => 'rahim@example.com', 'login_password' => 'gardener123',
    ])->assertRedirect(route('viewTeamList'));

    $member = Team::where('title', 'Rahim')->first();
    $gardener = User::where('email', 'rahim@example.com')->first();
    expect($gardener->isGardener)->toBeTrue()->and($member->user_id)->toBe($gardener->id);

    $other = Team::create(['title' => 'Karim', 'position' => 'Gardener', 'image_url' => 'x.jpg']);
    newBooking(['name' => 'Mine Customer', 'team_member_id' => $member->id, 'status' => 'approved', 'preferred_date' => now()->toDateString()]);
    newBooking(['name' => 'Other Customer', 'team_member_id' => $other->id, 'status' => 'approved']);

    auth()->logout();
    $this->post('/login', ['email' => 'rahim@example.com', 'password' => 'gardener123'])
        ->assertRedirect(route('gardener.dashboard'));

    $this->get('/gardener')->assertOk()->assertSee('Mine Customer')->assertDontSee('Other Customer');

    ImageUpload_cleanup($member->image_url);
});

it('lets a gardener mark their job done and record cash', function () {
    [$member] = makeTeam(1);
    $gardener = User::factory()->create(['isGardener' => true]);
    $member->update(['user_id' => $gardener->id]);
    $service = Service::create(['title' => 'Mowing', 'description' => 'x', 'price' => 500]);
    $job = newBooking(['team_member_id' => $member->id, 'status' => 'approved', 'service_id' => $service->id]);

    $this->actingAs($gardener)->post("/gardener/jobs/{$job->id}/done", ['cash_collected' => 1]);

    $job->refresh();
    expect($job->status)->toBe('done')
        ->and($job->payment_status)->toBe('paid')
        ->and((float) $job->amount_paid)->toBe(500.0);
});

it('stops customers and other gardeners from using the gardener portal', function () {
    [$m1, $m2] = makeTeam(2);
    $g2 = User::factory()->create(['isGardener' => true]);
    $m2->update(['user_id' => $g2->id]);
    $job = newBooking(['team_member_id' => $m1->id, 'status' => 'approved']);

    $this->actingAs(User::factory()->create())->get('/gardener')->assertForbidden();
    $this->actingAs($g2)->post("/gardener/jobs/{$job->id}/done")->assertForbidden();
    expect($job->fresh()->status)->toBe('approved');
});

/*
| Calendar, reports, export, activity log
*/

it('shows bookings on the admin calendar', function () {
    $admin = User::factory()->admin()->create();
    newBooking(['name' => 'Calendar Person', 'preferred_date' => now()->startOfMonth()->addDays(10)->toDateString()]);

    $this->actingAs($admin)->get('/admin-panel/calendar')->assertOk()->assertSee('Calendar Person');
});

it('renders the reports page with revenue', function () {
    $admin = User::factory()->admin()->create();
    $b = newBooking(['status' => 'done']);
    BookingWorkflow::markPaid($b, 'Cash', 1234);

    $this->actingAs($admin)->get('/admin-panel/reports')->assertOk()->assertSee('1,234');
});

it('exports bookings as CSV', function () {
    $admin = User::factory()->admin()->create();
    newBooking(['name' => 'Export Me']);

    $response = $this->actingAs($admin)->get('/admin-panel/bookings/export');
    $response->assertOk();
    expect($response->streamedContent())->toContain('Export Me')->toContain('Time slot');
});

it('records admin actions in the activity log', function () {
    $admin = User::factory()->admin()->create();
    $b = newBooking();

    $this->actingAs($admin)->post("/admin-panel/bookings/{$b->id}/status", ['status' => 'approved']);

    expect(ActivityLog::where('action', 'booking.status')->exists())->toBeTrue();
    $this->actingAs($admin)->get('/admin-panel/activity')->assertOk()->assertSee('pending → approved');
});

/*
| Price estimate, recurring bookings, photos, SMS
*/

it('saves the price estimate from lawn size', function () {
    $service = Service::create(['title' => 'Mowing', 'description' => 'x', 'price' => 500, 'price_per_100_sqft' => 40]);
    $user = User::factory()->create();

    $this->actingAs($user)->post('/bookings', bookingPayload($service, ['lawn_size' => 1000]));

    expect((float) Booking::first()->estimated_price)->toBe(900.0); // 500 + 10 × 40
});

it('creates the next visit when a weekly booking is done', function () {
    $admin = User::factory()->admin()->create();
    $date = now()->addDays(2)->toDateString();
    $b = newBooking(['recurrence' => 'weekly', 'status' => 'approved', 'preferred_date' => $date]);

    $this->actingAs($admin)->post("/admin-panel/bookings/{$b->id}/status", ['status' => 'done']);

    $next = Booking::where('parent_booking_id', $b->id)->first();
    expect($next)->not->toBeNull()
        ->and(substr($next->preferred_date, 0, 10))->toBe(now()->addDays(9)->toDateString())
        ->and($next->status)->toBe('pending')
        ->and($next->recurrence)->toBe('weekly');

    // Marking done again must not create a duplicate
    BookingWorkflow::changeStatus($b->fresh(), 'approved');
    BookingWorkflow::changeStatus($b->fresh(), 'done');
    expect(Booking::where('parent_booking_id', $b->id)->count())->toBe(1);
});

it('lets a customer stop a repeating booking', function () {
    $user = User::factory()->create();
    $b = newBooking(['user_id' => $user->id, 'recurrence' => 'monthly']);

    $this->actingAs($user)->post("/my-bookings/{$b->id}/stop-repeat");

    expect($b->fresh()->recurrence)->toBe('none');
});

it('stores lawn photos with the booking', function () {
    $service = Service::create(['title' => 'Mowing', 'description' => 'x']);
    $user = User::factory()->create();

    $this->actingAs($user)->post('/bookings', bookingPayload($service, [
        'photos' => [UploadedFile::fake()->image('a.jpg'), UploadedFile::fake()->image('b.jpg')],
    ]))->assertSessionHasNoErrors();

    $photos = Booking::first()->photos;
    expect($photos)->toHaveCount(2);
    foreach ($photos as $p) {
        expect(File::exists(public_path($p)))->toBeTrue();
        ImageUpload_cleanup($p);
    }
});

it('sends booking SMS through the configured gateway', function () {
    config(['gardener.sms' => ['driver' => 'bulksmsbd', 'api_key' => 'key', 'sender_id' => 'GARDENER', 'url' => 'https://sms.test/api']]);
    Http::fake(['sms.test/*' => Http::response('ok')]);
    $service = Service::create(['title' => 'Mowing', 'description' => 'x']);

    $this->actingAs(User::factory()->create())->post('/bookings', bookingPayload($service));

    Http::assertSent(fn ($req) => str_contains($req->url(), 'sms.test') && $req['number'] === '8801712345678');
});

/*
| Payments & invoice
*/

it('pays online through SSLCommerz and marks the booking paid', function () {
    config(['gardener.sslcommerz' => ['store_id' => 'test', 'store_password' => 'secret', 'sandbox' => true]]);
    $user = User::factory()->create();
    $service = Service::create(['title' => 'Mowing', 'description' => 'x', 'price' => 750]);
    $b = newBooking(['user_id' => $user->id, 'service_id' => $service->id]);

    Http::fake([
        'sandbox.sslcommerz.com/gwprocess/*' => Http::response(['status' => 'SUCCESS', 'GatewayPageURL' => 'https://sandbox.sslcommerz.com/pay/abc']),
        'sandbox.sslcommerz.com/validator/*' => function ($request) {
            $payment = Payment::first();

            return Http::response(['status' => 'VALID', 'tran_id' => $payment->tran_id, 'amount' => '750.00', 'currency_type' => 'BDT', 'card_issuer' => 'bKash']);
        },
    ]);

    $this->actingAs($user)->post("/my-bookings/{$b->id}/pay")->assertRedirect('https://sandbox.sslcommerz.com/pay/abc');

    $payment = Payment::first();
    expect((float) $payment->amount)->toBe(750.0);

    // SSLCommerz posts back without a CSRF token
    $this->post('/payment/sslcommerz/success', ['tran_id' => $payment->tran_id, 'val_id' => 'VAL123'])
        ->assertRedirect(route('user.dashboard', ['payment' => 'success']));

    $b->refresh();
    expect($b->payment_status)->toBe('paid')->and($b->payment_method)->toContain('bKash');
});

it('rejects a payment whose amount does not match', function () {
    config(['gardener.sslcommerz' => ['store_id' => 'test', 'store_password' => 'secret', 'sandbox' => true]]);
    $b = newBooking(['estimated_price' => 1000]);
    $payment = Payment::create(['booking_id' => $b->id, 'tran_id' => 'T1', 'amount' => 1000, 'currency' => 'BDT']);

    Http::fake(['sandbox.sslcommerz.com/validator/*' => Http::response(['status' => 'VALID', 'tran_id' => 'T1', 'amount' => '10.00', 'currency_type' => 'BDT'])]);

    $this->post('/payment/sslcommerz/ipn', ['tran_id' => 'T1', 'val_id' => 'X']);

    expect($b->fresh()->payment_status)->toBe('unpaid')->and($payment->fresh()->status)->toBe('failed');
});

it('shows invoices only to the owner and admins', function () {
    $owner = User::factory()->create();
    $b = newBooking(['user_id' => $owner->id, 'estimated_price' => 300]);

    $this->actingAs($owner)->get("/bookings/{$b->id}/invoice")->assertOk()->assertSee($b->invoiceNumber());
    $this->actingAs(User::factory()->admin()->create())->get("/bookings/{$b->id}/invoice")->assertOk();
    $this->actingAs(User::factory()->create())->get("/bookings/{$b->id}/invoice")->assertForbidden();
});

it('lets admins record a cash payment', function () {
    $admin = User::factory()->admin()->create();
    $b = newBooking(['estimated_price' => 400]);

    $this->actingAs($admin)->post("/admin-panel/bookings/{$b->id}/paid");

    expect($b->fresh()->payment_status)->toBe('paid');
});

/*
| Profile & Google login
*/

it('lets users update their profile and password', function () {
    $user = User::factory()->create(['password' => 'oldpassword']);

    $this->actingAs($user)->post('/profile', ['name' => 'New Name', 'email' => $user->email, 'phone' => '01899999999']);
    expect($user->fresh()->name)->toBe('New Name');

    $this->actingAs($user)->post('/profile/password', ['current_password' => 'wrong', 'password' => 'newpassword', 'password_confirmation' => 'newpassword'])
        ->assertSessionHasErrors('current_password');

    $this->actingAs($user)->post('/profile/password', ['current_password' => 'oldpassword', 'password' => 'newpassword', 'password_confirmation' => 'newpassword']);
    expect(\Illuminate\Support\Facades\Hash::check('newpassword', $user->fresh()->password))->toBeTrue();
});

it('signs in with Google and creates the account', function () {
    config(['services.google' => ['client_id' => 'cid', 'client_secret' => 'secret']]);
    Http::fake([
        'oauth2.googleapis.com/token' => Http::response(['access_token' => 'tok']),
        'openidconnect.googleapis.com/*' => Http::response(['sub' => 'g-123', 'email' => 'g@example.com', 'email_verified' => true, 'name' => 'Google User']),
    ]);

    $redirect = $this->get('/auth/google');
    expect($redirect->headers->get('Location'))->toContain('accounts.google.com');
    $state = session('google_oauth_state');

    $this->get("/auth/google/callback?code=abc&state={$state}")->assertRedirect(route('home'));

    $user = User::where('email', 'g@example.com')->first();
    expect($user->google_id)->toBe('g-123');
    $this->assertAuthenticatedAs($user);
});

it('rejects a Google callback with a bad state', function () {
    config(['services.google' => ['client_id' => 'cid', 'client_secret' => 'secret']]);
    $this->get('/auth/google');

    $this->get('/auth/google/callback?code=abc&state=wrong')->assertRedirect(route('login'));
    $this->assertGuest();
});

/*
| Service pages, blog, FAQ, language
*/

it('gives every service its own page', function () {
    $s = Service::create(['title' => 'Hedge Trimming', 'description' => 'Neat hedges', 'long_description' => 'All about hedges', 'price' => 300]);

    expect($s->slug)->toBe('hedge-trimming');
    $this->get('/service/hedge-trimming')->assertOk()->assertSee('All about hedges');
});

it('publishes blog posts from the admin panel', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)->post('/admin-panel/posts', [
        'title' => 'Winter Lawn Tips', 'body' => 'Water less in winter.', 'isPublished' => 1,
    ])->assertRedirect(route('admin.posts.list'));

    $this->get('/blog')->assertOk()->assertSee('Winter Lawn Tips');
    $this->get('/blog/winter-lawn-tips')->assertOk()->assertSee('Water less in winter.');
    $this->get('/sitemap.xml')->assertSee('/blog/winter-lawn-tips', false);
});

it('hides draft blog posts', function () {
    \App\Models\Post::create(['title' => 'Secret Draft', 'slug' => 'secret-draft', 'body' => 'x', 'isPublished' => false]);

    $this->get('/blog')->assertDontSee('Secret Draft');
    $this->get('/blog/secret-draft')->assertNotFound();
});

it('manages FAQs', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)->post('/admin-panel/faqs', ['question' => 'Do you work on Fridays?', 'answer' => 'Yes, every day.', 'isActive' => 1]);

    $this->get('/faq')->assertOk()->assertSee('Do you work on Fridays?')->assertSee('FAQPage', false);
});

it('switches the site to Bangla', function () {
    $this->get('/lang/bn')->assertRedirect();
    $this->get('/')->assertSee('হোম');

    $this->get('/lang/en');
    $this->get('/')->assertSee('Home');
});

/** Remove files created by upload tests */
function ImageUpload_cleanup(?string $path): void
{
    \App\Support\ImageUpload::delete($path);
}
