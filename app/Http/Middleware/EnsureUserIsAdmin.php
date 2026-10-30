<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Only logged-in, approved and non-blocked admins can pass.
 */
class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        if (! $user->isAdmin || ! $user->isAdminApproved || $user->isBlocked) {
            abort(403, 'You are not allowed to access the admin panel.');
        }

        return $next($request);
    }
}
