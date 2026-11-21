@extends('layouts.app')
@section('title', 'Nos réalisations — villas, complexe résidentiel et chantiers')
@section('description', "Portfolio VOKAMIN : Villa Kema à Bingerville, Villa Lirana à Riviera 3, Villa Maureen, Résidences Oasis à Bonoua et interventions pour des entreprises en Côte d'Ivoire.")

@section('content')
@include('partials.page-hero', ['title' => 'Des preuves, <em>pas des promesses.</em>', 'lead' => "Villas, complexe résidentiel, interventions en sites professionnels : nos réalisations, présentées telles qu'elles sont.", 'image' => asset('images/oasis-villa-piscine.jpg'), 'crumbs' => ['Nos réalisations' => null], 'eyebrow' => 'Portfolio'])
<section class="section">
    <div class="container">
        <div data-vue="PortfolioGrid" data-props="{{ json_encode([
            'filters' => $filters,
            'projects' => $projects->map(fn ($p) => ['name' => $p->name, 'location' => $p->location, 'category' => $p->category, 'tags' => $p->tag_list, 'cover' => $p->cover_url, 'url' => route('projects.show', $p)])->values(),
        ]) }}">
            {{-- Rendu serveur (référencement et affichage avant chargement de Vue) --}}
            <div class="portfolio">@foreach ($projects as $project)@include('partials.project-card', ['project' => $project])@endforeach</div>
        </div>
    </div>
</section>
@include('partials.cta-band', ['ctaTitle' => 'Le prochain projet <em>sera le vôtre.</em>'])
@endsection
