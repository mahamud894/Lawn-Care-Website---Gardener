@extends('admin-panel')
@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card mt-4">
                <div class="card-header">Add New Service</div>
                <div class="card-body">
                    <form action="{{ route('admin.service.store') }}" method="post" enctype="multipart/form-data">
                        @csrf

                        <!-- Title -->
                        <div class="mb-3">
                            <label for="title" class="form-label">Title</label>
                            <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}">
                            @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <!-- Price Field Added -->
                        <div class="mb-3">
                            <label for="price" class="form-label">Base price ({{ config('gardener.currency_symbol') }})</label>
                            <input type="number" step="0.01" name="price" id="price" class="form-control @error('price') is-invalid @enderror" value="{{ old('price') }}" placeholder="e.g. 49.99">
                            @error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <!-- Price per 100 sq ft (for the lawn-size price estimate) -->
                        <div class="mb-3">
                            <label for="price_per_100_sqft" class="form-label">Extra price per 100 sq ft ({{ config('gardener.currency_symbol') }}) <span class="text-muted small">— optional</span></label>
                            <input type="number" step="0.01" min="0" name="price_per_100_sqft" id="price_per_100_sqft" class="form-control @error('price_per_100_sqft') is-invalid @enderror" value="{{ old('price_per_100_sqft', $service->price_per_100_sqft ?? '') }}" placeholder="e.g. 50">
                            <small class="text-muted">Customers who enter their lawn size see: base price + (size ÷ 100 × this amount).</small>
                            @error('price_per_100_sqft')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <!-- Image File Input -->
                        <div class="mb-3">
                            <label for="image_url" class="form-label">Service Main Image</label>
                            <input type="file" name="image_url" id="image_url" class="form-control @error('image_url') is-invalid @enderror" accept="image/*">
                            @error('image_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <!-- Icon File Input -->
                        <div class="mb-3">
                            <label for="icon_url" class="form-label">Service Image Icon</label>
                            <input type="file" name="icon_url" id="icon_url" class="form-control @error('icon_url') is-invalid @enderror" accept="image/*">
                            @error('icon_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label for="short_description" class="form-label">Short Description</label>
                            <textarea name="short_description" id="short_description" rows="4" class="form-control @error('short_description') is-invalid @enderror">{{ old('short_description') }}</textarea>
                            @error('short_description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <!-- Full description for the service's own page -->
                        <div class="mb-3">
                            <label for="long_description" class="form-label">Full Description <span class="text-muted small">— shown on the service's own page (optional)</span></label>
                            <textarea name="long_description" id="long_description" rows="6" class="form-control @error('long_description') is-invalid @enderror">{{ old('long_description', $service->long_description ?? '') }}</textarea>
                            @error('long_description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="d-flex justify-content-end">
                            <a href="{{ url()->previous() }}" class="btn btn-secondary me-2">Cancel</a>
                            <button type="submit" class="btn btn-primary">Save Service</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
