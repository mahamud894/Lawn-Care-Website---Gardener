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
                        <th>Price</th> <!-- Price Column Added -->
                        <th>Description</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $service)
                    <tr>
                        <!-- Image with asset() helper -->
                        <td>
                            @if($service->image_url)
                                <img src="{{ asset($service->image_url) }}" alt="{{ $service->title }}" height="64px" class="rounded object-fit-cover">
                            @else
                                <span class="text-muted small">No Image</span>
                            @endif
                        </td>

                        <!-- Icon with asset() helper -->
                        <td>
                            @if($service->icon_url)
                                <img src="{{ asset($service->icon_url) }}" alt="Icon" height="32px" class="rounded">
                            @else
                                <span class="text-muted small">No Icon</span>
                            @endif
                        </td>

                        <td>{{ $service->title }}</td>

                        <!-- Price display -->
                        <td>
                            <strong>{{ money($service->price) }}</strong>
                        </td>

                        <td>{{ Str::limit($service->description, 50) }}</td>

                        <td>
                            @if($service->isActive == 1)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-danger">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('viewServiceEditForm', $service->id) }}" class="btn btn-info btn-sm text-white">Edit</a>
                            <form action="{{ route('deleteService', $service->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this service?')">@csrf @method('DELETE')<button type="submit" class="btn btn-danger btn-sm">Delete</button></form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
