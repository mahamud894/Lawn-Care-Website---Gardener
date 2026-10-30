@extends('admin-panel')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Edit Service</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('editService') }}" method="post" enctype="multipart/form-data">
                        @csrf

                        <!-- Hidden ID Input -->
                        <input type="hidden" name="id" value="{{ $service->id }}">

                        <!-- Title -->
                        <div class="mb-3">
                            <label for="title" class="form-label">Title</label>
                            <input type="text" value="{{ old('title', $service->title) }}" class="form-control" id="title" name="title" required>
                        </div>

                        <!-- Price -->
                        <div class="mb-3">
                            <label for="price" class="form-label">Base price ({{ config('gardener.currency_symbol') }})</label>
                            <input type="number" step="0.01" value="{{ old('price', $service->price) }}" class="form-control" id="price" name="price" placeholder="e.g. 49.99">
                        </div>

                        <!-- Price per 100 sq ft (for the lawn-size price estimate) -->
                        <div class="mb-3">
                            <label for="price_per_100_sqft" class="form-label">Extra price per 100 sq ft ({{ config('gardener.currency_symbol') }}) <span class="text-muted small">— optional</span></label>
                            <input type="number" step="0.01" min="0" name="price_per_100_sqft" id="price_per_100_sqft" class="form-control @error('price_per_100_sqft') is-invalid @enderror" value="{{ old('price_per_100_sqft', $service->price_per_100_sqft ?? '') }}" placeholder="e.g. 50">
                            <small class="text-muted">Customers who enter their lawn size see: base price + (size ÷ 100 × this amount).</small>
                            @error('price_per_100_sqft')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <!-- Service Main Image Input & Current Preview -->
                        <div class="mb-3">
                            <label for="image_url" class="form-label">Service Main Image</label>
                            @if($service->image_url)
                                <div class="mb-2">
                                    <img loading="lazy" decoding="async" src="{{ asset($service->image_url) }}" alt="Service Image" class="img-thumbnail" style="height: 80px; object-fit: cover;">
                                    <span class="text-muted d-block small">Current Image</span>
                                </div>
                            @endif
                            <input type="file" class="form-control" id="image_url" name="image_url" accept="image/*">
                            <small class="text-muted">Leave empty if you don't want to change the image.</small>
                        </div>

                        <!-- Icon Input & Current Preview -->
                        <div class="mb-3">
                            <label for="icon_url" class="form-label">Icon Image</label>
                            @if($service->icon_url)
                                <div class="mb-2">
                                    <img loading="lazy" decoding="async" src="{{ asset($service->icon_url) }}" alt="Service Icon" class="img-thumbnail" style="height: 50px; object-fit: contain; background: #f8f9fa;">
                                    <span class="text-muted d-block small">Current Icon</span>
                                </div>
                            @endif
                            <input type="file" class="form-control" id="icon_url" name="icon_url" accept="image/*">
                            <small class="text-muted">Leave empty if you don't want to change the icon.</small>
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label for="short_description" class="form-label">Short Description</label>
                            <textarea class="form-control" id="short_description" name="short_description" rows="3" required>{{ old('short_description', $service->description) }}</textarea>
                        </div>

                        <!-- Full description for the service's own page -->
                        <div class="mb-3">
                            <label for="long_description" class="form-label">Full Description <span class="text-muted small">— shown on the service's own page (optional)</span></label>
                            <textarea name="long_description" id="long_description" rows="6" class="form-control @error('long_description') is-invalid @enderror">{{ old('long_description', $service->long_description ?? '') }}</textarea>
                            @error('long_description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <!-- IsActive Checkbox -->
                        <div class="mb-3 form-check">
                            <input type="checkbox" name="isActive" class="form-check-input" id="isActive" value="1" {{ $service->isActive == 1 ? "checked" : "" }}>
                            <label for="isActive" class="form-check-label">IsActive</label>
                        </div>

                        <div class="d-flex justify-content-end">
                            <a href="{{ url()->previous() }}" class="btn btn-secondary me-2">Cancel</a>
                            <button type="submit" class="btn btn-primary">Update Service</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
