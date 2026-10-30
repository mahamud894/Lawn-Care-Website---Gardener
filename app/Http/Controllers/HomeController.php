<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Review;
use App\Models\Service;
use App\Models\Team;
use Illuminate\Support\Facades\Schema;

class HomeController extends Controller
{
    public function viewHomePage()
    {
        return view('pages.home', [
            'services' => $this->safe('services', fn () => Service::where('isActive', 1)->get()),
            'team' => $this->safe('teams', fn () => Team::where('isActive', 1)->get()),
            'projects' => $this->safe('projects', fn () => Project::where('isActive', 1)->latest()->take(6)->get()),
            'reviews' => $this->safe('reviews', fn () => Review::with(['user', 'booking.service'])
                ->where('isApproved', 1)->latest()->take(10)->get()),
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
