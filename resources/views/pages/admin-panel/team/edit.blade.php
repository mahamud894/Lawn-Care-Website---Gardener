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
                    <form action="/admin-panel/edit-team" method="post">
                        @csrf
                        <input type="hidden" name="id" value="{{$team->id}}">
                        <div class="mb-3">
                            <label for="image_url" class="form-label">Image URL</label>
                            <input type="url" value="{{$team->image_url}}" class="form-control" id="image_url" name="image_url" required>
                        </div>
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" value="{{$team->title}}" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="position" class="form-label">Position</label>
                            <input type="text" value="{{$team->position}}" class="form-control" id="position" name="position" required>
                        </div>
                        <div class="mb-3">
                            <input type="checkbox" name="isActive" class="mt-2" {{$team->isActive == 1 ? "Checked" : ""}}>
                            <label for="isActive" class="form-label">IsActive</label>
                        </div>
                        <button type="submit" class="btn btn-primary">Edit Team Member</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
