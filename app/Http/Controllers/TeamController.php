<?php

namespace App\Http\Controllers;

use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\File;
use RealRashid\SweetAlert\Facades\Alert;

class TeamController extends Controller
{
    public function viewTeamAddForm()
    {
        return view('pages.admin-panel.team.add');
    }

    public function storeTeam(Request $req)
    {
        // 1. Validating image instead of url
        $req->validate([
            'name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048', // Max 2MB
        ]);

        // 2. Handling file upload
        $imagePath = null;
        if ($req->hasFile('image')) {
            $image = $req->file('image');
            $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();

            // Saved to public/uploads/team folder
            $image->move(public_path('uploads/team'), $imageName);
            $imagePath = 'uploads/team/' . $imageName;
        }

        // 3. Save to database
        Team::create([
            'title' => $req->name,
            'position' => $req->position,
            'image_url' => $imagePath, // Uploaded image path is saved
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
        $team = Team::findOrFail($req->id);

        // Image made nullable when editing
        $req->validate([
            'id' => 'required|exists:teams,id',
            'name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'isActive' => 'sometimes|in:on',
        ]);

        $imagePath = $team->image_url;

        // If a new image is uploaded, delete the old image and save the new one
        if ($req->hasFile('image')) {
            // Delete old image from public folder
            if ($team->image_url && File::exists(public_path($team->image_url))) {
                File::delete(public_path($team->image_url));
            }

            $image = $req->file('image');
            $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads/team'), $imageName);
            $imagePath = 'uploads/team/' . $imageName;
        }

        $team->update([
            'title' => $req->name,
            'position' => $req->position,
            'image_url' => $imagePath,
            'isActive' => $req->isActive === 'on' ? 1 : 0,
        ]);

        Alert::success('Success', 'Team member updated successfully');

        return redirect()->route('viewTeamList');
    }

    public function deleteTeam($id)
    {
        $team = Team::findOrFail($id);

        // Delete the team member's image from the folder when deleting the member
        if ($team->image_url && File::exists(public_path($team->image_url))) {
            File::delete(public_path($team->image_url));
        }

        $team->delete();

        Alert::success('Success', 'Team member deleted successfully');

        return redirect()->back();
    }
}
