@extends('layouts.app')
@section('title', 'Plan du site')

@section('content')
@include('partials.page-hero', ['title' => 'Plan <em>du site.</em>', 'lead' => "Toutes les pages du site VOKAMIN, en un coup d'œil.", 'image' => asset('images/oasis-rendu.jpg'), 'crumbs' => ['Plan du site' => null], 'eyebrow' => 'Navigation'])
<section class="section"><div class="container"><div class="row g-5">
    @foreach ([
        'Principales' => ['Accueil' => route('home'), "L'entreprise" => route('company'), 'Nos services' => route('services.index'), 'Nos réalisations' => route('projects.index'), 'Nos références' => route('references'), 'Actualités' => route('posts.index'), 'Demande de devis' => route('quote'), 'Contact' => route('contact')],
        'Services' => $services->mapWithKeys(fn ($s) => [$s->short => route('services.show', $s)])->all(),
        'Réalisations' => $projects->mapWithKeys(fn ($p) => [$p->name => route('projects.show', $p)])->all(),
        'Informations' => ['Recrutement' => route('jobs'), 'Mentions légales' => route('legal'), 'Confidentialité' => route('privacy'), 'Cookies' => route('cookies')],
    ] as $title => $links)
        <div class="col-sm-6 col-lg-3"><h2 class="h6 text-uppercase fw-bold mb-3" style="letter-spacing:.16em;color:var(--vk-green)">{{ $title }}</h2>
            <ul class="list-unstyled d-grid gap-2">@foreach ($links as $label => $url)<li><a href="{{ $url }}">{{ $label }}</a></li>@endforeach</ul></div>
    @endforeach
</div></div></section>
@endsection
