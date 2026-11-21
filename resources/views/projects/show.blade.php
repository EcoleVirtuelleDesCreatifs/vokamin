@extends('layouts.app')
@section('title', $project->name.' — '.$project->location)
@section('description', $project->name.' ('.$project->location.') — '.$project->category.' : '.$project->description)
@section('og_image', $project->cover_url)
@section('preload', $project->cover_url)

@section('content')
<section class="page-hero" style="min-height:min(86svh,820px)">
    <img src="{{ $project->cover_url }}" alt="" style="opacity:.75">
    <div class="container">
        <nav class="crumbs" aria-label="Fil d'Ariane"><a href="{{ route('home') }}">Accueil</a><span class="sep">/</span><a href="{{ route('projects.index') }}">Réalisations</a><span class="sep">/</span><span aria-current="page">{{ $project->name }}</span></nav>
        <span class="eyebrow">{{ $project->category }}</span>
        <h1 class="display" style="font-size:var(--fs-hero)">{{ $project->name }}</h1>
        <p class="lead-vk mb-0"><i class="fa-solid fa-location-dot me-2" style="color:#8fd9a1"></i>{{ $project->location }} · {{ $project->programme ?: $project->category }}</p>
    </div>
</section>

<section class="section">
    <div class="container"><div class="row g-5">
        <div class="col-lg-7">
            <span class="eyebrow">Le projet</span>
            <p class="intro-statement reveal" style="font-size:clamp(1.25rem,2vw,1.7rem)">{{ $project->description }}</p>
            <div class="d-flex flex-wrap gap-3 mt-4">
                @if ($project->images->isNotEmpty())<a class="btn-vk blue" href="#galerie">Voir la galerie <i class="fa-solid fa-arrow-down"></i></a>@endif
                @if ($services->isNotEmpty())<a class="link-arrow align-self-center" href="{{ route('services.show', $services->first()) }}">Découvrir le service {{ $services->first()->short }} <i class="fa-solid fa-arrow-right"></i></a>@endif
            </div>
        </div>
        <div class="col-lg-5"><div class="p-4 p-md-5 bg-paper reveal d1">
            <p class="filter-group-label">Fiche projet</p>
            <table class="spec-table"><tbody>
                <tr><th>Localisation</th><td>{{ $project->location }}</td></tr>
                <tr><th>Domaine</th><td>{{ $project->category }}</td></tr>
                @if ($project->programme)<tr><th>Programme</th><td>{{ $project->programme }}</td></tr>@endif
                @if ($project->year)<tr><th>Année</th><td>{{ $project->year }}</td></tr>@endif
                @if ($project->status)<tr><th>Statut</th><td>{{ $project->status }}</td></tr>@endif
                @if ($services->isNotEmpty())<tr><th>Services</th><td>{!! $services->map(fn ($s) => '<a href="'.route('services.show', $s).'">'.e($s->short).'</a>')->implode(', ') !!}</td></tr>@endif
                @if ($project->images->isNotEmpty())<tr><th>Visuels</th><td>{{ $project->images->count() }} photos</td></tr>@endif
            </tbody></table>
            <a class="btn-vk w-100 mt-4" href="{{ route('quote') }}">Un projet similaire ? Demander un devis</a>
        </div></div>
    </div></div>
</section>

@if ($project->images->isNotEmpty())
<section class="section-sm pt-0" id="galerie">
    <div class="container">
        <div class="section-head mb-4"><div><span class="eyebrow">Galerie</span><h2 class="display">En images</h2></div><p class="text-muted-vk mb-0 small">{{ $project->images->count() }} photos · cliquez pour agrandir</p></div>
        <div data-vue="ProjectGallery" data-props="{{ json_encode(['images' => $project->images->map(fn ($i) => ['url' => $i->url, 'caption' => $i->caption])->values()]) }}">
            <div class="gallery gallery-{{ min($project->images->count(), 4) }}">@foreach ($project->images as $img)<a href="{{ $img->url }}"><img src="{{ $img->url }}" alt="{{ $img->caption }}" loading="lazy"></a>@endforeach</div>
        </div>
    </div>
</section>
@endif

@if ($project->ba_before && $project->ba_after)
<section class="section-sm pt-0"><div class="container">
    <div class="section-head mb-4"><div><span class="eyebrow">Avant / après</span><h2 class="display">De la structure <em>au résultat.</em></h2></div><p class="text-muted-vk mb-0 small">Faites glisser le curseur pour comparer.</p></div>
    <div data-vue="BeforeAfter" data-props="{{ json_encode(['before' => media_url($project->ba_before), 'after' => media_url($project->ba_after), 'labelBefore' => $project->ba_label_before ?: 'Avant', 'labelAfter' => $project->ba_label_after ?: 'Après']) }}"></div>
</div></section>
@endif

@if ($project->video_url)
<section class="section-sm pt-0"><div class="container">
    <a class="video-block" href="{{ $project->video_url }}" target="_blank" rel="noopener"><img src="{{ $project->cover_url }}" alt="" loading="lazy"><span class="play"><i class="fa-solid fa-play"></i></span><span class="vlabel">Vidéo · {{ $project->name }}</span></a>
</div></section>
@endif

@if ($project->testimonial)
<section class="section-sm bg-paper"><div class="container"><div class="row justify-content-center"><div class="col-lg-9 text-center">
    <span class="quote-mark" aria-hidden="true">“</span><p class="quote">{{ $project->testimonial }}</p>
    @if ($project->testimonial_author)<p class="mt-3 mb-0 small fw-bold text-uppercase" style="letter-spacing:.14em">{{ $project->testimonial_author }}</p>@endif
</div></div></div></section>
@endif

<section class="section-sm" style="border-top:1px solid var(--vk-line)">
    <div class="container d-flex flex-wrap justify-content-between gap-3">
        <a class="link-arrow" href="{{ route('projects.show', $prev) }}"><i class="fa-solid fa-arrow-left"></i> {{ $prev->name }}</a>
        <a class="link-arrow" href="{{ route('projects.index') }}">Tous les projets</a>
        <a class="link-arrow" href="{{ route('projects.show', $next) }}">{{ $next->name }} <i class="fa-solid fa-arrow-right"></i></a>
    </div>
</section>
@include('partials.cta-band')
@endsection
