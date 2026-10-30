<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Review;
use App\Models\Service;
use App\Models\Team;
use App\Support\SiteCache;
use Illuminate\Support\Facades\Schema;

class HomeController extends Controller
{
    public function viewHomePage()
    {
        // Cached — cleared automatically when an admin changes any of these
        return view('pages.home', [
            'services' => SiteCache::remember('home.services', fn () => $this->safe('services', fn () => Service::where('isActive', 1)->get())),
            'team' => SiteCache::remember('home.team', fn () => $this->safe('teams', fn () => Team::where('isActive', 1)->get())),
            'projects' => SiteCache::remember('home.projects', fn () => $this->safe('projects', fn () => Project::where('isActive', 1)->latest()->take(6)->get())),
            'reviews' => SiteCache::remember('home.reviews', fn () => $this->safe('reviews', fn () => Review::with(['user', 'booking.service'])
                ->where('isApproved', 1)->latest()->take(10)->get())),
        ]);
    }

    /**
     * Run a query only if its table exists, and never let a database
     * problem take the whole home page down.
     */
    private function safe(string $table, callable $query)
    {
        try {
            return Schema::hasTable($table) ? $query() : collect();
        } catch (\Exception $e) {
            return collect();
        }
    }
}
