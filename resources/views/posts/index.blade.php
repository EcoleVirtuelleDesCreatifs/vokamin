@extends('layouts.app')
@section('title', 'Actualités et conseils BTP')
@section('description', "Actualités de VOKAMIN : projets, conseils BTP, partenariats et offres d'emploi en Côte d'Ivoire.")

@section('content')
@include('partials.page-hero', ['title' => 'Le chantier <em>en direct.</em>', 'lead' => 'Projets, conseils BTP, offres d\'emploi : suivez la vie de VOKAMIN.', 'image' => asset('images/oasis-elevation.jpg'), 'crumbs' => ['Actualités' => null], 'eyebrow' => 'Le journal VOKAMIN'])
<section class="section">
    <div class="container">
        @if ($featured)
            <a class="news-featured reveal" href="{{ route('posts.show', $featured) }}">
                <div class="ph"><img src="{{ $featured->cover_url }}" alt="{{ $featured->title }}"></div>
                <div><span class="eyebrow">À la une</span><div class="news-meta"><span class="cat">{{ $featured->category }}</span>@if ($featured->published_at)<time datetime="{{ $featured->published_at->toDateString() }}">{{ $featured->published_at->translatedFormat('j F Y') }}</time>@endif</div><h2 class="display">{{ $featured->title }}</h2><p class="lead-vk">{{ $featured->excerpt }}</p><span class="link-arrow mt-2">Lire l'article <i class="fa-solid fa-arrow-right"></i></span></div>
            </a>
            <hr class="my-5" style="border-color:var(--vk-line);opacity:1">
        @endif
        <div class="row g-4 g-lg-5">
            @forelse ($posts as $i => $post)
                @continue($featured && $post->is($featured))
                <div class="col-md-6 col-lg-4">@include('partials.news-card', ['post' => $post, 'i' => $i])</div>
            @empty
                <p class="text-muted-vk">Aucune actualité pour le moment.</p>
            @endforelse
        </div>
        @if ($posts->hasPages())
            <nav class="d-flex justify-content-center gap-2 mt-5" aria-label="Pagination">
                @if (! $posts->onFirstPage())<a class="btn-vk sm ghost" href="{{ $posts->previousPageUrl() }}"><i class="fa-solid fa-arrow-left"></i> Précédent</a>@endif
                <span class="btn-vk sm" aria-current="page">Page {{ $posts->currentPage() }} / {{ $posts->lastPage() }}</span>
                @if ($posts->hasMorePages())<a class="btn-vk sm ghost" href="{{ $posts->nextPageUrl() }}">Suivant <i class="fa-solid fa-arrow-right"></i></a>@endif
            </nav>
        @endif
    </div>
</section>
<section class="section-sm bg-paper">
    <div class="container"><div class="row g-4 align-items-center">
        <div class="col-lg-6"><span class="eyebrow">Lettre d'information</span><h2 class="display" style="font-size:2rem">Recevez nos actualités chantier</h2><p class="text-muted-vk mb-0">Un email par mois, pas plus.</p></div>
        <div class="col-lg-6"><div data-vue="NewsletterForm" data-props="{{ json_encode(['action' => route('forms.newsletter')]) }}"></div></div>
    </div></div>
</section>
@endsection
