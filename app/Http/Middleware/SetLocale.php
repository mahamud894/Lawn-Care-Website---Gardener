<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/** Uses the language the visitor picked in the switcher (stored in the session). */
class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->session()->get('locale');

        if ($locale && array_key_exists($locale, config('gardener.locales', []))) {
            app()->setLocale($locale);
        }

        return $next($request);
    }
}
