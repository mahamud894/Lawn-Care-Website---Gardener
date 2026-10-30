@extends('admin-panel')
@section("content")
<div class="card shadow-sm mx-3">
    <div class="card-body">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <div>
                <h5 class="card-title mb-1">Projects</h5>
                <p class="text-muted mb-0">Photos shown on the Projects page and home page gallery.</p>
            </div>
            <a href="{{ route('admin.projects.create') }}" class="btn btn-primary btn-sm text-white"><i class="bi bi-plus-lg"></i> Add Project</a>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Image</th>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($projects as $project)
                    <tr>
                        <td><img loading="lazy" decoding="async" src="{{ asset($project->image_url) }}" alt="{{ $project->title }}" style="width: 80px; height: 60px; object-fit: cover;" class="rounded"></td>
                        <td>
                            <div class="fw-semibold">{{ $project->title }}</div>
                            <small class="text-muted">{{ \Illuminate\Support\Str::limit($project->description, 60) }}</small>
                        </td>
                        <td>{{ \App\Models\Project::CATEGORIES[$project->category] ?? $project->category }}</td>
                        <td>
                            @if($project->isActive)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-danger">Hidden</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.projects.edit', $project) }}" class="btn btn-info btn-sm text-white">Edit</a>
                            <form action="{{ route('admin.projects.delete', $project) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this project?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">
                            No projects yet — the site is showing sample photos. Add your first project!
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $projects->links() }}</div>
    </div>
</div>
@endsection
