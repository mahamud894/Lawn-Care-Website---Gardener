<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class HomeController extends Controller
{
    public function viewHomePage()
    {
        // If the services table doesn't exist or is broken, avoid throwing an exception
        if (Schema::hasTable('services')) {
            try {
                $services = Service::where(['isActive' => 1])->get();
            } catch (\Exception $e) {
                $services = collect();
            }
        } else {
            $services = collect();
        }
        // Guard team query as well in case teams table is missing or broken
        if (Schema::hasTable('teams')) {
            try {
                $team = Team::where(['isActive' => 1])->get();
            } catch (\Exception $e) {
                $team = collect();
            }
        } else {
            $team = collect();
        }

        return view('pages.home', [
            'services' => $services,
            'team' => $team,
        ]);
    }

}
