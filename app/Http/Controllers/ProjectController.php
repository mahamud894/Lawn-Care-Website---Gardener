<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Support\ImageUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use RealRashid\SweetAlert\Facades\Alert;

class ProjectController extends Controller
{
    /** Public Projects page */
    public function viewProjectPage()
    {
        $projects = \App\Support\SiteCache::remember('projects.active', fn () => Schema::hasTable('projects')
            ? Project::where('isActive', 1)->latest()->get()
            : collect());

        return view('pages.project', compact('projects'));
    }

    /*
    | Admin
    */

    public function index()
    {
        $projects = Project::latest()->paginate(15);

        return view('pages.admin-panel.project.list', compact('projects'));
    }

    public function create()
    {
        return view('pages.admin-panel.project.form', ['project' => new Project(['isActive' => true])]);
    }

    public function store(Request $req)
    {
        $data = $this->validated($req, true);
        $data['image_url'] = ImageUpload::store($req->file('image'), 'projects');

        Project::create($data);
        Alert::success('Success', 'Project added successfully');

        return redirect()->route('admin.projects.list');
    }

    public function edit(Project $project)
    {
        return view('pages.admin-panel.project.form', compact('project'));
    }

    public function update(Request $req, Project $project)
    {
        $data = $this->validated($req, false);

        if ($req->hasFile('image')) {
            $data['image_url'] = ImageUpload::replace($project->image_url, $req->file('image'), 'projects');
        }

        $project->update($data);
        Alert::success('Success', 'Project updated successfully');

        return redirect()->route('admin.projects.list');
    }

    public function destroy(Project $project)
    {
        ImageUpload::delete($project->image_url);
        $project->delete();

        Alert::success('Success', 'Project deleted successfully');

        return redirect()->back();
    }

    private function validated(Request $req, bool $imageRequired): array
    {
        $req->validate([
            'title' => ['required', 'string', 'max:255'],
            'category' => ['required', Rule::in(array_keys(Project::CATEGORIES))],
            'description' => ['nullable', 'string', 'max:2000'],
            'image' => [$imageRequired ? 'required' : 'nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:3072'],
        ]);

        return [
            'title' => $req->title,
            'category' => $req->category,
            'description' => $req->description,
            'isActive' => $req->boolean('isActive'),
        ];
    }
}
