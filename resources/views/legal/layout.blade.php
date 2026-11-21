@extends('layouts.app')
@section('content')
@include('partials.page-hero', ['title' => $heroTitle, 'lead' => $heroLead, 'image' => asset('images/oasis-dalle.jpg'), 'crumbs' => [strip_tags($heroTitle) => null], 'eyebrow' => 'Informations légales'])
<section class="section"><div class="container"><div class="row g-5">
    <aside class="col-lg-3 d-none d-lg-block"><nav class="legal-toc" aria-label="Sommaire">@foreach ($toc as $id => $label)<a href="#{{ $id }}">{{ $label }}</a>@endforeach</nav></aside>
    <div class="col-lg-9"><div class="legal">@yield('legal')<p class="small text-muted-vk mt-5">Dernière mise à jour : {{ now()->translatedFormat('F Y') }}.</p></div></div>
</div></div></section>
@endsection
