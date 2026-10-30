<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Support\ImageUpload;
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
        // 1. Validating image instead of url
        $req->validate([
            'name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048', // Max 2MB
            'login_email' => 'nullable|email|max:255',
            'login_password' => 'nullable|string|min:8',
        ]);

        // 2. Handling file upload
        // Saved to public/uploads/team folder
        $imagePath = ImageUpload::store($req->file('image'), 'team');

        // 3. Save to database
        $team = Team::create([
            'title' => $req->name,
            'position' => $req->position,
            'image_url' => $imagePath, // Uploaded image path is saved
        ]);

        try {
            $this->syncLogin($req, $team);
        } catch (ValidationException $e) {
            // Undo so the admin can fix the login fields and try again
            ImageUpload::delete($team->image_url);
            $team->delete();
            throw $e;
        }
        ActivityLog::record('team.create', "Added team member {$team->title}", $team);

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
            'login_email' => 'nullable|email|max:255',
            'login_password' => 'nullable|string|min:8',
        ]);

        $imagePath = $team->image_url;

        // If a new image is uploaded, delete the old image and save the new one
        if ($req->hasFile('image')) {
            $imagePath = ImageUpload::replace($team->image_url, $req->file('image'), 'team');
        }

        $this->syncLogin($req, $team);

        $team->update([
            'title' => $req->name,
            'position' => $req->position,
            'image_url' => $imagePath,
            'isActive' => $req->isActive === 'on' ? 1 : 0,
        ]);
        ActivityLog::record('team.update', "Updated team member {$team->title}", $team);

        Alert::success('Success', 'Team member updated successfully');

        return redirect()->route('viewTeamList');
    }

    public function deleteTeam($id)
    {
        $team = Team::findOrFail($id);

        // Delete the team member's image from the folder when deleting the member
        ImageUpload::delete($team->image_url);
        $team->user?->update(['isGardener' => false]);
        ActivityLog::record('team.delete', "Deleted team member {$team->title}", $team);

        $team->delete();

        Alert::success('Success', 'Team member deleted successfully');

        return redirect()->back();
    }

    /**
     * Create, update or remove the gardener's login account
     * from the optional fields on the team form.
     */
    private function syncLogin(Request $req, Team $team): void
    {
        $current = $team->user;

        if ($req->boolean('remove_login') && $current) {
            $current->update(['isGardener' => false]);
            $team->update(['user_id' => null]);

            return;
        }

        $email = $req->input('login_email');
        if (! $email) {
            return;
        }

        $user = User::where('email', $email)->first();

        if ($user && $user->isAdmin) {
            throw ValidationException::withMessages(['login_email' => 'This e-mail belongs to an admin account.']);
        }

        if ($user && $user->teamMember && $user->teamMember->id !== $team->id) {
            throw ValidationException::withMessages(['login_email' => 'This e-mail is already linked to another team member.']);
        }

        if (! $user) {
            if (! $req->filled('login_password')) {
                throw ValidationException::withMessages(['login_password' => 'Set a password for the new login.']);
            }

            $user = User::create([
                'name' => $req->input('name', $team->title),
                'email' => $email,
                'password' => Hash::make($req->login_password),
                'isGardener' => true,
            ]);
        } else {
            $user->isGardener = true;
            if ($req->filled('login_password')) {
                $user->password = Hash::make($req->login_password);
            }
            $user->save();
        }

        // Unlink a previous account if the e-mail changed
        if ($current && $current->id !== $user->id) {
            $current->update(['isGardener' => false]);
        }

        $team->update(['user_id' => $user->id]);
    }
}
