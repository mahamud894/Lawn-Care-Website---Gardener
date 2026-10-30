@extends('admin-panel')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card mt-4">
                <div class="card-header">{{ $project->exists ? 'Edit Project' : 'Add New Project' }}</div>
                <div class="card-body">
                    <form action="{{ $project->exists ? route('admin.projects.update', $project) : route('admin.projects.store') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        @if($project->exists) @method('PUT') @endif

                        <div class="mb-3">
                            <label for="title" class="form-label">Title</label>
                            <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $project->title) }}" required>
                            @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="category" class="form-label">Category</label>
                            <select name="category" id="category" class="form-select @error('category') is-invalid @enderror">
                                @foreach(\App\Models\Project::CATEGORIES as $key => $label)
                                    <option value="{{ $key }}" @selected(old('category', $project->category) === $key)>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('category')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="image" class="form-label">Photo {{ $project->exists ? '(leave empty to keep the current one)' : '' }}</label>
                            @if($project->image_url)
                                <div class="mb-2"><img loading="lazy" decoding="async" src="{{ asset($project->image_url) }}" alt="" style="max-height: 120px;" class="rounded"></div>
                            @endif
                            <input type="file" name="image" id="image" class="form-control @error('image') is-invalid @enderror" accept="image/*" {{ $project->exists ? '' : 'required' }}>
                            @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description (optional)</label>
                            <textarea name="description" id="description" rows="3" class="form-control @error('description') is-invalid @enderror">{{ old('description', $project->description) }}</textarea>
                            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="form-check mb-3">
                            <input type="hidden" name="isActive" value="0">
                            <input type="checkbox" name="isActive" value="1" id="isActive" class="form-check-input" @checked(old('isActive', $project->isActive))>
                            <label class="form-check-label" for="isActive">Show on website</label>
                        </div>

                        <div class="d-flex justify-content-end">
                            <a href="{{ route('admin.projects.list') }}" class="btn btn-secondary me-2">Cancel</a>
                            <button type="submit" class="btn btn-primary">Save Project</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
