@extends('layouts.app')
@section('title', 'Nos services BTP à Abidjan')
@section('description', 'Services de VOKAMIN : promotion immobilière, plans 2D/3D, construction de villas, rénovation, décoration, Building Maintenance, négoce de matériaux et enseignes à Abidjan.')

@section('content')
@include('partials.page-hero', ['title' => 'Tous les métiers du bâtiment. <em>Un seul interlocuteur.</em>', 'lead' => 'Promotion, conception, construction, rénovation, entretien, négoce et signalétique : VOKAMIN coordonne l\'ensemble de votre projet.', 'image' => asset('images/oasis-rendu.jpg'), 'crumbs' => ['Nos services' => null], 'eyebrow' => 'Nos services'])
<section class="section pt-4">
    <div class="container">
        @foreach ($services as $i => $s)
            <div class="svc-row" id="{{ $s->slug }}">
                <div class="svc-media img-reveal"><img src="{{ media_url($s->image) }}" alt="{{ $s->name }}" loading="lazy" decoding="async"></div>
                <div class="reveal">
                    <span class="svc-num">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }} / {{ str_pad($services->count(), 2, '0', STR_PAD_LEFT) }}</span>
                    <h2 class="display">{{ $s->name }}</h2>
                    <p class="lead-vk">{{ $s->teaser }}</p>
                    <ul class="chips">@foreach (array_slice($s->prestations ?? [], 0, 6) as $p)<li>{{ $p }}</li>@endforeach</ul>
                    <div class="d-flex flex-wrap gap-3 align-items-center"><a class="btn-vk blue" href="{{ route('services.show', $s) }}">Découvrir le service <i class="fa-solid fa-arrow-right"></i></a><a class="link-arrow" href="{{ route('quote') }}#{{ $s->slug }}">Demander un devis</a></div>
                </div>
            </div>
        @endforeach
    </div>
</section>
@include('partials.cta-band', ['ctaTitle' => 'Plusieurs métiers ? <em>Une seule équipe.</em>', 'ctaText' => 'C\'est notre force : une seule équipe coordonne conception, travaux, fournitures et finitions.'])
@endsection
