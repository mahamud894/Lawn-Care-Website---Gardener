@extends('admin-panel')
@section("content")
<div class="card admin-card">
    <div class="card-body">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <div>
                <h5 class="card-title mb-1">Service List</h5>
                <p class="text-muted mb-0">Latest service list.</p>
            </div>
            <a href="/admin-panel/add-service" class="btn btn-primary btn-sm text-white">Add Service</a>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Image</th>
                        <th>Icon</th>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $service)
                    <tr>
                        <td><img src="{{$service->image_url}}" alt="" height="64px"></td>
                        <td><img src="{{$service->icon_url}}" alt="" height="32px"></td>
                        <td>{{$service->title}}</td>
                        <td>{{$service->description}}</td>
                        <td>
                            @if($service->isActive == 1)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-danger">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <!-- <a href="/admin-panel/edit-service/{{$service->id}}" class="btn btn-info btn-sm text-white">Edit</a> -->
                            <a href="{{route("viewServiceEditForm", $service->id)}}" class="btn btn-info btn-sm text-white">Edit</a>
                            <a href="{{route("deleteService", $service->id)}}" class="btn btn-danger btn-sm">Delete</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
