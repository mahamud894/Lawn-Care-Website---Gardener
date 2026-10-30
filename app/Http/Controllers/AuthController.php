<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use RealRashid\SweetAlert\Facades\Alert;

class AuthController extends Controller
{
    public function viewLogin()
    {
        return view('pages.login');
    }

    public function viewSignup()
    {
        return view('pages.signup');
    }

    public function signup(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:20', 'unique:users,phone'],
            'password' => ['required', 'confirmed', Password::min(8)],
            // 0 = customer, 1 = admin assistant (needs main admin approval)
            'role' => ['nullable', 'in:0,1'],
        ]);

        $wantsAdmin = (int) ($data['role'] ?? 0) === 1;

        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'password' => Hash::make($data['password']),
            'isAdmin' => $wantsAdmin ? 1 : 0,
            // An admin request stays locked until the main admin approves it
            'isAdminApproved' => $wantsAdmin ? 0 : 1,
        ]);

        if ($wantsAdmin) {
            Alert::info('Requested', 'Admin account created! Wait for Main Admin approval.');
        } else {
            Alert::success('Success', 'Signup successful! Please login.');
        }

        return redirect()->route('login');
    }

    public function loginCheck(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        // Only wrong passwords count towards the limit (5 per minute for one
        // e-mail from one IP), so people sharing a Wi-Fi can still log in.
        $throttleKey = 'login:'.Str::lower($credentials['email']).'|'.$request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            Alert::error(__('Too many attempts'), __('Please wait :seconds seconds and try again.', ['seconds' => $seconds]));

            return redirect()->back()->withInput($request->only('email'));
        }

        // Check the password first, so we never reveal whether an e-mail
        // is registered / blocked / pending to someone who doesn't know it.
        if (! Auth::validate($credentials)) {
            RateLimiter::hit($throttleKey, 60);
            Alert::error('Error', 'Invalid Credentials!');

            return redirect()->back()->withInput($request->only('email'));
        }

        RateLimiter::clear($throttleKey);

        $user = User::where('email', $credentials['email'])->first();

        if ($user->isBlocked) {
            Alert::error('Account Blocked', 'Your account has been suspended by Admin.');

            return redirect()->back();
        }

        if ($user->isAdmin && ! $user->isAdminApproved) {
            Alert::error('Access Denied', 'Your Admin account is pending approval from Main Admin.');

            return redirect()->back();
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        Alert::success('Success', $user->isAdmin ? 'Admin Login Successful' : 'Login Successful');

        return redirect()->intended($user->homeRoute());
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        Alert::success('Success', 'Logout Successfull');

        return redirect()->route('login');
    }
}
