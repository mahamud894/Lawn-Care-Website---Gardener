<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // The admin panel uses Bootstrap 5, so render pagination links with it
        Paginator::useBootstrapFive();

        // A separate counter for each form, so using one form never blocks another.
        // (Plain "throttle:5,1" shares one counter across every route.)
        $byUserOrIp = fn (Request $r) => $r->user()?->id ?: $r->ip();
        RateLimiter::for('login', fn (Request $r) => Limit::perMinute(30)->by('login|'.$r->ip()));
        RateLimiter::for('signup', fn (Request $r) => Limit::perMinute(10)->by('signup|'.$r->ip()));
        RateLimiter::for('password-reset', fn (Request $r) => Limit::perMinute(5)->by('reset|'.$r->ip()));
        RateLimiter::for('contact', fn (Request $r) => Limit::perMinute(5)->by('contact|'.$byUserOrIp($r)));
        RateLimiter::for('booking', fn (Request $r) => Limit::perMinute(10)->by('booking|'.$byUserOrIp($r)));

        // Footer lists the real services (links to each service page)
        View::composer('common.site.footer', function ($view) {
            static $services;
            try {
                $services ??= \App\Support\SiteCache::remember('footer.services', fn () => Schema::hasTable('services')
                    ? \App\Models\Service::where('isActive', 1)->orderBy('id')->take(6)->get(['id', 'title', 'slug'])
                    : collect());
            } catch (\Throwable $e) {
                $services = collect();
            }
            $view->with('footerServices', $services);
        });
    }
}
