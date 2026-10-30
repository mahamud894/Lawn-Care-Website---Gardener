<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
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

        // Footer lists the real services (links to each service page)
        View::composer('common.site.footer', function ($view) {
            static $services;
            try {
                $services ??= Schema::hasTable('services')
                    ? \App\Models\Service::where('isActive', 1)->orderBy('id')->take(6)->get(['id', 'title', 'slug'])
                    : collect();
            } catch (\Throwable $e) {
                $services = collect();
            }
            $view->with('footerServices', $services);
        });
    }
}
