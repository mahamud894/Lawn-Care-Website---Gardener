@extends('admin-panel')
@section("content")
<div class="row g-3 mx-2">
    <div class="col-lg-5">
        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="card-title">{{ $editing ? 'Edit question' : 'Add a question' }}</h5>
                <form action="{{ $editing ? route('admin.faqs.update', $editing) : route('admin.faqs.store') }}" method="POST">
                    @csrf
                    @if($editing) @method('PUT') @endif
                    <div class="mb-3">
                        <label class="form-label" for="question">Question</label>
                        <input type="text" name="question" id="question" class="form-control @error('question') is-invalid @enderror" value="{{ old('question', $editing->question ?? '') }}" required>
                        @error('question')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="answer">Answer</label>
                        <textarea name="answer" id="answer" rows="5" class="form-control @error('answer') is-invalid @enderror" required>{{ old('answer', $editing->answer ?? '') }}</textarea>
                        @error('answer')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label" for="sort_order">Order</label>
                            <input type="number" min="0" name="sort_order" id="sort_order" class="form-control" value="{{ old('sort_order', $editing->sort_order ?? 0) }}">
                        </div>
                        <div class="col-6 d-flex align-items-end">
                            <div class="form-check">
                                <input type="hidden" name="isActive" value="0">
                                <input type="checkbox" name="isActive" value="1" id="isActive" class="form-check-input" @checked(old('isActive', $editing->isActive ?? true))>
                                <label for="isActive" class="form-check-label">Show on website</label>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">{{ $editing ? 'Update' : 'Add question' }}</button>
                    @if($editing)<a href="{{ route('admin.faqs.list') }}" class="btn btn-light">Cancel</a>@endif
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5 class="card-title mb-0">FAQ ({{ $faqs->count() }})</h5>
                    <a href="{{ route('faq') }}" target="_blank" class="small">View page <i class="bi bi-box-arrow-up-right"></i></a>
                </div>
                @forelse($faqs as $faq)
                    <div class="border rounded p-3 mb-2 {{ $faq->isActive ? '' : 'opacity-50' }}">
                        <div class="d-flex justify-content-between gap-2">
                            <div>
                                <span class="badge bg-light text-dark border me-1">{{ $faq->sort_order }}</span>
                                <strong>{{ $faq->question }}</strong>
                                <div class="small text-muted mt-1">{{ \Illuminate\Support\Str::limit($faq->answer, 140) }}</div>
                            </div>
                            <div class="text-nowrap">
                                <a href="{{ route('admin.faqs.list', ['edit' => $faq->id]) }}" class="btn btn-info btn-sm text-white">Edit</a>
                                <form action="{{ route('admin.faqs.delete', $faq) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this question?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-danger btn-sm" type="submit">Delete</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-muted">No questions yet. Add common questions like prices, areas you cover, or how payment works.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
