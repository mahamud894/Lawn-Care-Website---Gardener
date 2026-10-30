@extends('admin-panel')
@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card mt-4">
                <div class="card-header">Add New Team Member</div>
                <div class="card-body">
                    <form action="{{ route('admin.team.store') }}" method="post" enctype="multipart/form-data">
                        @csrf

                        <!-- Image File Input -->
                        <div class="mb-3">
                            <label for="image" class="form-label">Team Member Image</label>
                            <input type="file" name="image" id="image" class="form-control @error('image') is-invalid @enderror" accept="image/*">
                            @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}">
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="position" class="form-label">Position</label>
                            <input type="text" name="position" id="position" class="form-control @error('position') is-invalid @enderror" value="{{ old('position') }}">
                            @error('position')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        @include('pages.admin-panel.team.login-fields', ['team' => null])

                        <div class="d-flex justify-content-end">
                            <a href="{{ url()->previous() }}" class="btn btn-secondary me-2">Cancel</a>
                            <button type="submit" class="btn btn-primary">Save Team Member</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
