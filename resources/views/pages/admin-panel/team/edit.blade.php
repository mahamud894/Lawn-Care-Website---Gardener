@extends('admin-panel')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Edit Team Member</h5>
                </div>
                <div class="card-body">
                    <form action="/admin-panel/edit-team" method="post" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="id" value="{{$team->id}}">

                        <!-- Image Input & Preview -->
                        <div class="mb-3">
                            <label for="image" class="form-label">Team Member Image</label>

                            <!-- Recent image Preview -->
                            @if($team->image_url)
                                <div class="mb-2">
                                    <img src="{{ asset($team->image_url) }}" alt="{{ $team->title }}" style="width: 100px; height: 100px; object-fit: cover; border-radius: 8px;">
                                </div>
                            @endif

                            <input type="file" class="form-control @error('image') is-invalid @enderror" id="image" name="image" accept="image/*">
                            <small class="text-muted">Leave empty if you don't want to change the image.</small>
                            @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" value="{{ old('name', $team->title) }}" class="form-control @error('name') is-invalid @enderror" id="name" name="name" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="position" class="form-label">Position</label>
                            <input type="text" value="{{ old('position', $team->position) }}" class="form-control @error('position') is-invalid @enderror" id="position" name="position" required>
                            @error('position')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <input type="checkbox" name="isActive" id="isActive" class="mt-2" {{$team->isActive == 1 ? "checked" : ""}}>
                            <label for="isActive" class="form-label">Is Active</label>
                        </div>

                        <button type="submit" class="btn btn-primary">Update Team Member</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
