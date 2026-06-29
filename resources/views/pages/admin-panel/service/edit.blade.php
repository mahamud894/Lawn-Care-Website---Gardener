@extends('admin-panel')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Edit Service</h5>
                </div>
                <div class="card-body">
                    <form action="/admin-panel/edit-service" method="post">
                        @csrf
                        <input type="text" name="id" value="{{$service->id}}">
                        <div class="mb-3">
                            <label for="image_url" class="form-label">Image URL</label>
                            <input type="url" value="{{$service->image_url}}" class="form-control" id="image_url" name="image_url" required>
                        </div>
                        <div class="mb-3">
                            <label for="icon_url" class="form-label">Icon URL</label>
                            <input type="url" value="{{$service->icon_url}}" class="form-control" id="icon_url" name="icon_url" required>
                        </div>
                        <div class="mb-3">
                            <label for="title" class="form-label">Title</label>
                            <input type="text" value="{{$service->title}}" class="form-control" id="title" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label for="short_description" class="form-label">Short Description</label>
                            <textarea class="form-control" id="short_description" name="short_description" rows="3" required>{{$service->description}}</textarea>
                        </div>
                        <div class="mb-3">
                            <input type="checkbox" name="isActive" class="mt-2" {{$service->isActive == 1 ? "Checked" : ""}}>
                            <label for="isActive" class="form-label">IsActive</label>
                        </div>
                        <button type="submit" class="btn btn-primary">Edit Service</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
