<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/** Only gardener accounts linked to a team member can pass. */
class EnsureUserIsGardener
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! $user->isGardener || ! $user->teamMember) {
            abort(403, 'This page is only for our gardeners.');
        }

        return $next($request);
    }
}
