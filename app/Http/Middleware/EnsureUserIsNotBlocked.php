<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;
use Symfony\Component\HttpFoundation\Response;

/**
 * If an admin blocks a user while that user is logged in,
 * log them out on their next request.
 */
class EnsureUserIsNotBlocked
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->isBlocked) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            Alert::error('Account Blocked', 'Your account has been suspended by Admin.');

            return redirect()->route('login');
        }

        return $next($request);
    }
}
