<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use RealRashid\SweetAlert\Facades\Alert;

/**
 * "Sign in with Google" using Google's OAuth 2.0 / OpenID Connect.
 * Needs GOOGLE_CLIENT_ID and GOOGLE_CLIENT_SECRET in .env, and
 * {APP_URL}/auth/google/callback added as an authorised redirect URI.
 */
class GoogleAuthController extends Controller
{
    public static function enabled(): bool
    {
        return filled(config('services.google.client_id')) && filled(config('services.google.client_secret'));
    }

    public function redirect(Request $request)
    {
        abort_unless(self::enabled(), 404);

        $state = Str::random(40);
        $request->session()->put('google_oauth_state', $state);

        $query = http_build_query([
            'client_id' => config('services.google.client_id'),
            'redirect_uri' => route('google.callback'),
            'response_type' => 'code',
            'scope' => 'openid email profile',
            'state' => $state,
            'prompt' => 'select_account',
        ]);

        return redirect()->away('https://accounts.google.com/o/oauth2/v2/auth?'.$query);
    }

    public function callback(Request $request)
    {
        abort_unless(self::enabled(), 404);

        $expected = $request->session()->pull('google_oauth_state');
        if (! $expected || ! hash_equals($expected, (string) $request->query('state')) || ! $request->filled('code')) {
            Alert::error(__('Login failed'), __('Google sign-in was cancelled or expired. Please try again.'));

            return redirect()->route('login');
        }

        try {
            $token = Http::asForm()->timeout(15)->post('https://oauth2.googleapis.com/token', [
                'code' => $request->query('code'),
                'client_id' => config('services.google.client_id'),
                'client_secret' => config('services.google.client_secret'),
                'redirect_uri' => route('google.callback'),
                'grant_type' => 'authorization_code',
            ])->throw()->json();

            $profile = Http::withToken($token['access_token'] ?? '')->timeout(15)
                ->get('https://openidconnect.googleapis.com/v1/userinfo')->throw()->json();
        } catch (\Throwable $e) {
            Log::warning('Google login failed: '.$e->getMessage());
            Alert::error(__('Login failed'), __('Could not sign in with Google. Please try again.'));

            return redirect()->route('login');
        }

        if (empty($profile['sub']) || empty($profile['email']) || empty($profile['email_verified'])) {
            Alert::error(__('Login failed'), __('Your Google e-mail is not verified.'));

            return redirect()->route('login');
        }

        $user = User::where('google_id', $profile['sub'])->first()
            ?? User::where('email', $profile['email'])->first();

        if (! $user) {
            $user = User::create([
                'name' => $profile['name'] ?? Str::before($profile['email'], '@'),
                'email' => $profile['email'],
                'google_id' => $profile['sub'],
                'password' => Hash::make(Str::random(40)),
                'email_verified_at' => now(),
            ]);
        } elseif (! $user->google_id) {
            $user->update(['google_id' => $profile['sub']]);
        }

        if ($user->isBlocked) {
            Alert::error(__('Account Blocked'), __('Your account has been suspended by Admin.'));

            return redirect()->route('login');
        }

        if ($user->isAdmin && ! $user->isAdminApproved) {
            Alert::error(__('Access Denied'), __('Your Admin account is pending approval from Main Admin.'));

            return redirect()->route('login');
        }

        Auth::login($user, true);
        $request->session()->regenerate();
        Alert::success(__('Welcome'), __('Signed in with Google.'));

        return redirect()->intended($user->homeRoute());
    }
}
