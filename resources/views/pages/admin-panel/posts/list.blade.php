@extends('admin-panel')
@section("content")
<div class="card shadow-sm mx-3">
    <div class="card-body">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <div>
                <h5 class="card-title mb-1">Blog Posts</h5>
                <p class="text-muted mb-0 small">Garden tips and news — great for Google search.</p>
            </div>
            <a href="{{ route('admin.posts.create') }}" class="btn btn-primary btn-sm text-white"><i class="bi bi-plus-lg"></i> New Post</a>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light"><tr><th>Title</th><th>Status</th><th>Published</th><th>Actions</th></tr></thead>
                <tbody>
                    @forelse($posts as $post)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $post->title }}</div>
                                <small class="text-muted">/blog/{{ $post->slug }}</small>
                            </td>
                            <td>
                                @if($post->isPublished)
                                    <span class="badge bg-success">Published</span>
                                @else
                                    <span class="badge bg-secondary">Draft</span>
                                @endif
                            </td>
                            <td class="small">{{ $post->published_at?->format('d M Y') ?? '—' }}</td>
                            <td class="text-nowrap">
                                @if($post->isPublished)
                                    <a href="{{ route('blog.show', $post) }}" target="_blank" class="btn btn-light btn-sm">View</a>
                                @endif
                                <a href="{{ route('admin.posts.edit', $post->id) }}" class="btn btn-info btn-sm text-white">Edit</a>
                                <form action="{{ route('admin.posts.delete', $post->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this post?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-danger btn-sm" type="submit">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-muted py-4">No posts yet. Write your first garden tip!</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $posts->links() }}</div>
    </div>
</div>
@endsection
