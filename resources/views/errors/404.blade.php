@extends('layouts.app')
@section('title', 'Page introuvable')
@section('no_footer', '1')

@section('content')
<section class="e404 on-ink">
    <img src="{{ asset('images/oasis-gros-oeuvre.jpg') }}" alt="">
    <div class="container">
        <div class="code">404</div>
        <h1 class="display mt-3" style="font-size:var(--fs-h2)">Cette page est encore <em>en chantier.</em></h1>
        <p class="lead-vk mx-auto mb-4">La page demandée n'existe pas ou a été déplacée. Reprenez la visite depuis l'une de ces rubriques.</p>
        <div class="d-flex flex-wrap gap-3 justify-content-center"><a class="btn-vk" href="{{ route('home') }}">Retour à l'accueil</a><a class="btn-vk ghost-light" href="{{ route('projects.index') }}">Nos réalisations</a><a class="btn-vk ghost-light" href="{{ route('contact') }}">Nous contacter</a></div>
    </div>
</section>
@endsection
