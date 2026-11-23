@extends('layouts.app')
@section('title', $post->title)
@section('description', mb_strlen($post->excerpt) < 110 ? $post->excerpt.' — '.\Illuminate\Support\Str::limit(trim(preg_replace('/\s+/u', ' ', strip_tags(\App\Support\RichText::render($post->body)))), 200) : $post->excerpt)
@section('og_image', $post->cover_url)

@push('head')
{!! \App\Support\Schema::breadcrumb(['Actualités' => route('posts.index'), $post->title => null]) !!}
{!! \App\Support\Schema::json([
    '@context' => 'https://schema.org', '@type' => 'NewsArticle', 'headline' => \Illuminate\Support\Str::limit($post->title, 110),
    'description' => $post->excerpt, 'image' => array_values(array_unique(array_merge([$post->cover_url], $post->images->pluck('url')->all()))),
    'datePublished' => optional($post->published_at)->toIso8601String(), 'dateModified' => $post->updated_at?->toIso8601String(),
    'mainEntityOfPage' => route('posts.show', $post), 'articleSection' => $post->category, 'inLanguage' => 'fr',
    'author' => ['@type' => 'Organization', 'name' => setting('company_name'), 'url' => url('/')],
    'publisher' => ['@type' => 'Organization', 'name' => setting('company_name'), 'logo' => ['@type' => 'ImageObject', 'url' => asset('images/logo-vokamin.png')]],
]) !!}
@endpush

@section('content')
<section class="page-hero" style="min-height:560px">
    <img src="{{ $post->cover_url }}" alt="{{ $post->title }}">
    <div class="container">
        <nav class="crumbs" aria-label="Fil d'Ariane"><a href="{{ route('home') }}">Accueil</a><span class="sep">/</span><a href="{{ route('posts.index') }}">Actualités</a><span class="sep">/</span><span aria-current="page">{{ $post->category }}</span></nav>
        <div class="news-meta" style="color:rgba(255,255,255,.7)"><span class="cat" style="color:#8fd9a1">{{ $post->category }}</span>@if ($post->published_at)<time datetime="{{ $post->published_at->toDateString() }}">{{ $post->published_at->translatedFormat('j F Y') }}</time>@endif<span>{{ $post->reading_time }} min de lecture</span></div>
        <h1 class="display" style="max-width:22ch">{{ $post->title }}</h1>
    </div>
</section>
<section class="section">
    <div class="container"><div class="row g-5">
        <article class="col-lg-8">
            <div class="article-body">{!! \App\Support\RichText::render($post->body) !!}</div>
            @if ($post->images->isNotEmpty())
            <div id="galerie" class="mt-5">
                <div class="section-head mb-4"><div><span class="eyebrow">Galerie</span><h2 class="display">En images</h2></div><p class="text-muted-vk mb-0 small">{{ $post->images->count() }} {{ $post->images->count() > 1 ? 'photos' : 'photo' }} · cliquez pour agrandir</p></div>
                <div data-vue="ProjectGallery" data-props="{{ json_encode(['images' => $post->images->map(fn ($i) => ['url' => $i->url, 'caption' => $i->caption, 'alt' => $i->caption ?: $post->title])->values()]) }}">
                    <div class="gallery gallery-{{ min($post->images->count(), 4) }}">@foreach ($post->images as $k => $img)<a href="{{ $img->url }}" aria-label="Agrandir la photo {{ $k + 1 }} : {{ $img->caption ?: $post->title }}"><img src="{{ $img->url }}" alt="{{ $img->caption ?: $post->title.' — photo '.($k + 1) }}" loading="lazy"></a>@endforeach</div>
                </div>
            </div>
            @endif
            @php $share = urlencode(route('posts.show', $post)); @endphp
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mt-5 pt-4" style="border-top:1px solid var(--vk-line)">
                <div class="share"><span>Partager</span>
                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ $share }}" target="_blank" rel="noopener" aria-label="Partager sur Facebook"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ $share }}" target="_blank" rel="noopener" aria-label="Partager sur LinkedIn"><i class="fa-brands fa-linkedin-in"></i></a>
                    <a href="https://wa.me/?text={{ urlencode($post->title.' '.route('posts.show', $post)) }}" target="_blank" rel="noopener" aria-label="Partager sur WhatsApp"><i class="fa-brands fa-whatsapp"></i></a>
                    <button type="button" data-copy="{{ route('posts.show', $post) }}" data-copy-label="Lien de l'article copié." aria-label="Copier le lien"><i class="fa-solid fa-link"></i></button>
                </div>
                <a class="link-arrow" href="{{ route('posts.index') }}"><i class="fa-solid fa-arrow-left"></i> Toutes les actualités</a>
            </div>
        </article>
        <aside class="col-lg-4"><div class="svc-nav"><div class="aside-card"><h3>Un projet en tête ?</h3><p class="small" style="color:var(--vk-on-ink-muted)">Devis gratuit et sans engagement.</p><a class="btn-vk w-100 sm" href="{{ route('quote') }}">Demander un devis</a><a class="btn-vk wa w-100 sm mt-2" href="{{ whatsapp_url() }}" target="_blank" rel="noopener" data-track="whatsapp"><i class="fa-brands fa-whatsapp"></i> WhatsApp</a></div></div></aside>
    </div></div>
</section>
@if ($related->isNotEmpty())
<section class="section bg-paper"><div class="container">
    <div class="section-head"><div><span class="eyebrow">À lire aussi</span><h2 class="display">Autres actualités</h2></div></div>
    <div class="row g-4">@foreach ($related as $i => $r)<div class="col-md-4">@include('partials.news-card', ['post' => $r, 'i' => $i])</div>@endforeach</div>
</div></section>
@endif
@endsection
