<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Post;
use App\Support\ImageUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use RealRashid\SweetAlert\Facades\Alert;

class PostController extends Controller
{
    /*
    | Public blog
    */

    public function index()
    {
        $posts = Schema::hasTable('posts') ? Post::published()->latest('published_at')->latest()->paginate(9) : collect();

        return view('pages.blog.index', compact('posts'));
    }

    public function show(Post $post)
    {
        abort_unless($post->isPublished && (! $post->published_at || $post->published_at->isPast()), 404);

        $recent = Post::published()->where('id', '!=', $post->id)->latest('published_at')->take(3)->get();

        return view('pages.blog.show', compact('post', 'recent'));
    }

    /*
    | Admin
    */

    public function adminIndex()
    {
        $posts = Post::latest()->paginate(15);

        return view('pages.admin-panel.posts.list', compact('posts'));
    }

    public function create()
    {
        return view('pages.admin-panel.posts.form', ['post' => new Post(['isPublished' => true])]);
    }

    public function store(Request $req)
    {
        $data = $this->validated($req);

        if ($req->hasFile('image')) {
            $data['image_url'] = ImageUpload::store($req->file('image'), 'posts');
        }

        $post = Post::create($data);
        ActivityLog::record('post.create', "Created blog post \"{$post->title}\"", $post);
        Alert::success('Success', 'Blog post saved');

        return redirect()->route('admin.posts.list');
    }

    public function edit(Post $post)
    {
        return view('pages.admin-panel.posts.form', compact('post'));
    }

    public function update(Request $req, Post $post)
    {
        $data = $this->validated($req, $post);

        if ($req->hasFile('image')) {
            $data['image_url'] = ImageUpload::replace($post->image_url, $req->file('image'), 'posts');
        }

        $post->update($data);
        ActivityLog::record('post.update', "Updated blog post \"{$post->title}\"", $post);
        Alert::success('Success', 'Blog post updated');

        return redirect()->route('admin.posts.list');
    }

    public function destroy(Post $post)
    {
        ImageUpload::delete($post->image_url);
        ActivityLog::record('post.delete', "Deleted blog post \"{$post->title}\"", $post);
        $post->delete();
        Alert::success('Success', 'Blog post deleted');

        return back();
    }

    private function validated(Request $req, ?Post $post = null): array
    {
        $req->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'alpha_dash', Rule::unique('posts', 'slug')->ignore($post?->id)],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'body' => ['required', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:3072'],
            'published_at' => ['nullable', 'date'],
        ]);

        $slug = $req->filled('slug') ? Str::slug($req->slug) : ($post?->slug ?? $this->uniqueSlug($req->title));
        $published = $req->boolean('isPublished');

        return [
            'title' => $req->title,
            'slug' => $slug,
            'excerpt' => $req->excerpt,
            'body' => $req->body,
            'isPublished' => $published,
            'published_at' => $req->published_at ?: ($published ? ($post?->published_at ?? now()) : null),
        ];
    }

    private function uniqueSlug(string $title): string
    {
        $base = Str::slug($title) ?: 'post';
        $slug = $base;
        $i = 2;
        while (Post::where('slug', $slug)->exists()) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }
}
