<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PostController extends Controller
{
    use Uploads;

    public function index()
    {
        return Post::orderByDesc('published_at')->orderByDesc('id')->get()->map(fn ($p) => $p->toArray() + ['image_url' => media_url($p->image)]);
    }

    public function store(Request $request)
    {
        return $this->save($request, new Post());
    }

    public function update(Request $request, Post $post)
    {
        return $this->save($request, $post);
    }

    public function destroy(Post $post)
    {
        $this->removeFile($post->image);
        $post->delete();

        return response()->noContent();
    }

    private function save(Request $request, Post $post)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'category' => ['required', 'string', 'max:60'],
            'excerpt' => ['required', 'string', 'max:400'],
            'body' => ['required', 'string'],
            'published_at' => ['nullable', 'date'],
            'is_published' => ['nullable', 'boolean'],
            'image_file' => ['nullable', ...$this->imageRule()],
        ]);
        $post->fill(collect($data)->except('image_file')->all());
        $post->is_published = $request->boolean('is_published', true);
        $post->published_at = $data['published_at'] ?? ($post->published_at ?? now());
        if (! $post->slug) {
            $base = Str::slug($data['title']) ?: 'actualite';
            $slug = $base;
            $i = 2;
            while (Post::where('slug', $slug)->exists()) {
                $slug = $base.'-'.$i++;
            }
            $post->slug = $slug;
        }
        if ($f = $request->file('image_file')) {
            $this->removeFile($post->image);
            $post->image = $this->storeImage($f, 'actualites');
        }
        $post->save();

        return $post->toArray() + ['image_url' => media_url($post->image)];
    }
}
