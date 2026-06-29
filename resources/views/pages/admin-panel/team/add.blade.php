@extends('admin-panel')
@section('content')

<div class="container">
	<div class="row justify-content-center">
		<div class="col-md-8">
			<div class="card mt-4">
				<div class="card-header">Add New Team Member</div>
				<div class="card-body">
					<form action="/admin-panel/add-team" method="post">
						@csrf

						<div class="mb-3">
							<label for="image_url" class="form-label">Image URL</label>
							<input type="text" name="image_url" id="image_url" class="form-control @error('image_url') is-invalid @enderror" value="{{ old('image_url') }}">
							@error('image_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
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
