<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;
use RealRashid\SweetAlert\Facades\Alert;

class PasswordResetController extends Controller
{
    public function viewForgotForm()
    {
        return view('pages.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => ['required', 'email']]);

        try {
            Password::sendResetLink($request->only('email'));
        } catch (\Throwable $e) {
            Log::warning('Password reset mail failed: '.$e->getMessage());
        }

        // Same message whether or not the e-mail exists (doesn't leak accounts)
        Alert::success('Check your e-mail', 'If an account exists for that e-mail, a password reset link has been sent.');

        return redirect()->route('login');
    }

    public function viewResetForm(Request $request, string $token)
    {
        return view('pages.reset-password', [
            'token' => $token,
            'email' => $request->query('email'),
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', PasswordRule::min(8)],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            Alert::success('Password changed', 'You can now log in with your new password.');

            return redirect()->route('login');
        }

        return back()->withInput($request->only('email'))->withErrors(['email' => __($status)]);
    }
}
