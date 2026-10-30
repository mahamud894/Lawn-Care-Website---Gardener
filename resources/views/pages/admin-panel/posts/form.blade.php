@extends('admin-panel')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card mt-4">
                <div class="card-header">{{ $post->exists ? 'Edit Post' : 'New Blog Post' }}</div>
                <div class="card-body">
                    <form action="{{ $post->exists ? route('admin.posts.update', $post->id) : route('admin.posts.store') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        @if($post->exists) @method('PUT') @endif

                        <div class="mb-3">
                            <label class="form-label" for="title">Title</label>
                            <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $post->title) }}" required>
                            @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="slug">URL <span class="text-muted small">— leave empty to make it from the title</span></label>
                            <div class="input-group">
                                <span class="input-group-text">/blog/</span>
                                <input type="text" name="slug" id="slug" class="form-control @error('slug') is-invalid @enderror" value="{{ old('slug', $post->slug) }}" placeholder="winter-lawn-care-tips">
                                @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="excerpt">Short summary <span class="text-muted small">— shown on the blog list and in Google</span></label>
                            <textarea name="excerpt" id="excerpt" rows="2" maxlength="500" class="form-control @error('excerpt') is-invalid @enderror">{{ old('excerpt', $post->excerpt) }}</textarea>
                            @error('excerpt')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="body">Article</label>
                            <textarea name="body" id="body" rows="14" class="form-control @error('body') is-invalid @enderror" required>{{ old('body', $post->body) }}</textarea>
                            <small class="text-muted">Plain text — press Enter for new paragraphs.</small>
                            @error('body')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="image">Cover image</label>
                            @if($post->image_url)<div class="mb-2"><img src="{{ asset($post->image_url) }}" alt="" style="max-height: 120px;" class="rounded"></div>@endif
                            <input type="file" name="image" id="image" class="form-control @error('image') is-invalid @enderror" accept="image/*">
                            @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label" for="published_at">Publish date <span class="text-muted small">— future date = scheduled</span></label>
                                <input type="datetime-local" name="published_at" id="published_at" class="form-control" value="{{ old('published_at', $post->published_at?->format('Y-m-d\TH:i')) }}">
                            </div>
                            <div class="col-md-6 d-flex align-items-end">
                                <div class="form-check">
                                    <input type="hidden" name="isPublished" value="0">
                                    <input type="checkbox" name="isPublished" value="1" id="isPublished" class="form-check-input" @checked(old('isPublished', $post->isPublished))>
                                    <label for="isPublished" class="form-check-label">Published (visible on the website)</label>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <a href="{{ route('admin.posts.list') }}" class="btn btn-secondary me-2">Cancel</a>
                            <button type="submit" class="btn btn-primary">Save Post</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
