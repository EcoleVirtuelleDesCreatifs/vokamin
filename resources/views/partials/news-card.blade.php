<a class="news-card reveal d{{ ($i ?? 0) % 3 + 1 }}" href="{{ route('posts.show', $post) }}">
    <div class="ph"><img src="{{ $post->cover_url }}" alt="{{ $post->title }}" loading="lazy" decoding="async"></div>
    <div class="news-meta"><span class="cat">{{ $post->category }}</span>@if ($post->published_at)<time datetime="{{ $post->published_at->toDateString() }}">{{ $post->published_at->translatedFormat('j F Y') }}</time>@endif<span>{{ $post->reading_time }} min de lecture</span></div>
    <h3>{{ $post->title }}</h3>
    <p>{{ $post->excerpt }}</p>
</a>
