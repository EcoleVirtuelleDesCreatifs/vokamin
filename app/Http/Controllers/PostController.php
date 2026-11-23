<?php

namespace App\Http\Controllers;

use App\Models\Post;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::published()->with('images')->paginate(7);

        return view('posts.index', ['posts' => $posts, 'featured' => $posts->onFirstPage() ? $posts->first() : null]);
    }

    public function show(Post $post)
    {
        abort_unless($post->is_published, 404);
        $post->load('images');

        return view('posts.show', [
            'post' => $post,
            'related' => Post::published()->with('images')->where('id', '!=', $post->id)->take(3)->get(),
        ]);
    }
}
