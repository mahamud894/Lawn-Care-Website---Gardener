<?php

namespace App\Http\Controllers;

use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use RealRashid\SweetAlert\Facades\Alert;

class TeamController extends Controller
{
    public function viewTeamAddForm()
    {
        return view('pages.admin-panel.team.add');
    }

    public function storeTeam(Request $req)
    {
        $req->validate([
            'name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'image_url' => 'required|url|max:2048',
        ]);

        Team::create([
            'title' => $req->name,
            'position' => $req->position,
            'image_url' => $req->image_url,
        ]);

        Alert::success('Success', 'Team member added successfully');

        return redirect()->route('viewTeamList');
    }

    public function viewTeamPage()
    {
        $team = Schema::hasTable('teams') ? Team::where(['isActive' => 1])->get() : collect();

        return view('pages.about', compact('team'));
    }
    public function viewTeamList()
    {
        $teams = Team::get();

        return view('pages.admin-panel.team.list', ['data' => $teams]);
    }

    public function viewTeamEditForm($id)
    {
        $team = Team::findOrFail($id);

        return view('pages.admin-panel.team.edit', ['team' => $team]);
    }

    public function editTeam(Request $req)
    {
        $req->validate([
            'id' => 'required|exists:teams,id',
            'name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'image_url' => 'required|url|max:2048',
            'isActive' => 'sometimes|in:on',
        ]);

        Team::where('id', $req->id)->update([
            'title' => $req->name,
            'position' => $req->position,
            'image_url' => $req->image_url,
            'isActive' => $req->isActive === 'on' ? 1 : 0,
        ]);

        Alert::success('Success', 'Team member updated successfully');

        return redirect()->route('viewTeamList');
    }

    public function deleteTeam($id)
    {
        Team::findOrFail($id)->delete();

        Alert::success('Success', 'Team member deleted successfully');

        return redirect()->back();
    }
}
