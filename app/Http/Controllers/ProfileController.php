<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use RealRashid\SweetAlert\Facades\Alert;

class ProfileController extends Controller
{
    public function edit()
    {
        return view('pages.profile', ['user' => Auth::user()]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:20', Rule::unique('users', 'phone')->ignore($user->id)],
        ]);

        $user->update($data);
        Alert::success(__('Saved'), __('Your profile has been updated.'));

        return back();
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        // Google-only accounts have never set a password, so don't ask for the old one
        $rules = ['password' => ['required', 'confirmed', Password::min(8)]];
        if (! $user->google_id || $request->filled('current_password')) {
            $rules['current_password'] = ['required', 'current_password'];
        }

        $request->validate($rules);

        $user->update(['password' => Hash::make($request->password)]);
        Alert::success(__('Saved'), __('Your password has been changed.'));

        return back();
    }
}
