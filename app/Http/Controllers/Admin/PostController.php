<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\PostImage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PostController extends Controller
{
    use Uploads;

    public function index()
    {
        return Post::with('images')->orderByDesc('published_at')->orderByDesc('id')->get()->map(fn ($p) => $this->payload($p));
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
        foreach ($post->images as $img) {
            $this->removeFile($img->path);
        }
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
            'images' => ['nullable', 'array', 'max:'.config('vokamin.upload.max_files', 20)],
            'images.*' => $this->imageRule(),
        ]);
        $post->fill(collect($data)->except(['image_file', 'images'])->all());
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
        $this->saveImages($request, $post);

        return $this->payload($post);
    }

    /** Ajoute des photos à la galerie d'une actualité existante. */
    public function addImages(Request $request, Post $post)
    {
        $request->validate([
            'images' => ['required', 'array', 'max:'.config('vokamin.upload.max_files', 20)],
            'images.*' => $this->imageRule(),
        ]);
        $this->saveImages($request, $post);

        return $this->payload($post);
    }

    /** Légende ou ordre d'une photo de la galerie. */
    public function updateImage(Request $request, PostImage $image)
    {
        $data = $request->validate([
            'caption' => ['nullable', 'string', 'max:255'],
            'position' => ['nullable', 'integer', 'min:0'],
        ]);
        if ($request->has('caption')) {
            $image->caption = $data['caption'];
        }
        if ($request->has('position')) {
            $image->position = (int) $data['position'];
        }
        $image->save();

        return $image;
    }

    public function destroyImage(PostImage $image)
    {
        $this->removeFile($image->path);
        $image->delete();

        return response()->noContent();
    }

    private function saveImages(Request $request, Post $post): void
    {
        $pos = (int) $post->images()->max('position');
        foreach ($request->file('images', []) as $file) {
            $path = $this->storeImage($file, 'actualites');
            $post->images()->create(['path' => $path, 'caption' => null, 'position' => ++$pos]);
        }
    }

    private function payload(Post $post): array
    {
        $post->load('images');

        return $post->toArray() + ['image_url' => $post->image ? media_url($post->image) : null, 'cover_url' => $post->cover_url];
    }
}
