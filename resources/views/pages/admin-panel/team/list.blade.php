@extends('admin-panel')
@section("content")
<div class="card shadow-sm mx-3">
    <div class="card-body">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <div>
                <h5 class="card-title mb-1">Team List</h5>
                <p class="text-muted mb-0">Latest team member list.</p>
            </div>
            <a href="/admin-panel/add-team" class="btn btn-primary btn-sm text-white">Add Team Member</a>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Position</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $teamMember)
                    <tr>
                        <td><img src="{{$teamMember->image_url}}" alt="" height="64px"></td>
                        <td>{{$teamMember->title}}</td>
                        <td>{{$teamMember->position}}</td>
                        <td>
                            @if($teamMember->isActive == 1)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-danger">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{route('viewTeamEditForm', $teamMember->id)}}" class="btn btn-info btn-sm text-white">Edit</a>
                            <a href="{{route('deleteTeam', $teamMember->id)}}" class="btn btn-danger btn-sm">Delete</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
