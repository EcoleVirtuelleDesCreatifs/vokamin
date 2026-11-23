@extends('layouts.app')
@section('title', $service->name.' à Abidjan')
@section('description', $service->name." par VOKAMIN en Côte d'Ivoire : ".$service->teaser)
@section('og_image', media_url($service->hero_image))

@push('head')
{!! \App\Support\Schema::json([
    '@context' => 'https://schema.org', '@type' => 'Service', 'name' => $service->name, 'description' => $service->teaser,
    'serviceType' => $service->name, 'image' => media_url($service->hero_image), 'url' => route('services.show', $service),
    'areaServed' => ['@type' => 'Country', 'name' => "Côte d'Ivoire"],
    'provider' => ['@type' => 'GeneralContractor', 'name' => setting('company_name'), 'url' => url('/'), 'telephone' => setting('tel1'),
        'address' => ['@type' => 'PostalAddress', 'streetAddress' => setting('address'), 'addressLocality' => 'Abidjan', 'addressCountry' => 'CI']],
]) !!}
@endpush

@section('content')
@include('partials.page-hero', ['title' => e($service->name), 'lead' => $service->teaser, 'image' => media_url($service->hero_image), 'crumbs' => ['Nos services' => route('services.index'), $service->short => null], 'eyebrow' => 'Service'])
<section class="section">
    <div class="container"><div class="row g-5">
        <aside class="col-lg-3"><div class="svc-nav">
            <p class="filter-group-label">Nos services</p>
            @foreach ($services as $x)
                <a href="{{ route('services.show', $x->slug) }}" @class(['active' => $x->slug === $service->slug]) @if ($x->slug === $service->slug) aria-current="page" @endif>{{ $x->short }}<i class="fa-solid fa-arrow-right small"></i></a>
            @endforeach
            <div class="aside-card mt-4"><h3>Un projet ?</h3><p class="small" style="color:var(--vk-on-ink-muted)">Devis gratuit et sans engagement.</p><a class="btn-vk w-100 sm" href="{{ route('quote') }}#{{ $service->slug }}">Demander un devis</a><a class="btn-vk wa w-100 sm mt-2" href="{{ whatsapp_url() }}" target="_blank" rel="noopener" data-track="whatsapp"><i class="fa-brands fa-whatsapp"></i> WhatsApp</a></div>
        </div></aside>
        <div class="col-lg-9">
            <div class="row g-4 align-items-center mb-5">
                <div class="col-md-7"><span class="eyebrow">Description</span><p class="intro-statement reveal" style="font-size:clamp(1.25rem,2vw,1.7rem)">{{ $service->intro }}</p>@if ($service->intro2)<p class="text-muted-vk reveal d1">{{ $service->intro2 }}</p>@endif</div>
                <div class="col-md-5"><div class="img-reveal"><img src="{{ media_url($service->image2) }}" alt="{{ $service->name }} — réalisation VOKAMIN" class="w-100" style="aspect-ratio:4/5;object-fit:cover" loading="lazy"></div></div>
            </div>
            @if (! empty($service->prestations))
                <div class="mb-5"><h2 class="display" style="font-size:1.8rem">Prestations proposées</h2>
                    <ul class="check-list check-cols mt-4" style="display:grid">@foreach ($service->prestations as $p)<li><i class="fa-solid fa-check"></i><span>{{ $p }}</span></li>@endforeach</ul></div>
            @endif
            @if (! empty($service->beneficiaires))
                <div class="mb-5"><h2 class="display" style="font-size:1.8rem">Pour qui ?</h2><div class="row g-3 mt-2">
                    @foreach ($service->beneficiaires as $b)<div class="col-sm-6"><div class="feature-box"><i class="fa-solid {{ $b['icon'] ?? 'fa-user' }}"></i><h3>{{ $b['title'] ?? '' }}</h3><p>{{ $b['text'] ?? '' }}</p></div></div>@endforeach
                </div></div>
            @endif
            @if (! empty($service->methode))
                <div class="on-ink p-4 p-md-5 mb-5"><div class="row g-4 align-items-center">
                    <div class="col-md-5"><span class="eyebrow">Méthode d'intervention</span><h2 class="display" style="font-size:1.8rem">Comment nous travaillons</h2><p class="text-muted-vk small mb-0">Chaque étape est validée avec vous avant de passer à la suivante.</p></div>
                    <div class="col-md-7"><ol class="mini-steps">@foreach ($service->methode as $m)<li><span><strong>{{ $m['title'] ?? '' }}</strong><span>{{ $m['text'] ?? '' }}</span></span></li>@endforeach</ol></div>
                </div></div>
            @endif
            @if (! empty($service->avantages))
                <div class="mb-5"><h2 class="display" style="font-size:1.8rem">Vos avantages</h2><div class="row g-3 mt-2">
                    @foreach ($service->avantages as $i => $a)<div class="col-sm-6 col-xl-3"><div class="feature-box reveal d{{ $i % 3 + 1 }}"><i class="fa-solid {{ $a['icon'] ?? 'fa-check' }}"></i><h3>{{ $a['title'] ?? '' }}</h3><p>{{ $a['text'] ?? '' }}</p></div></div>@endforeach
                </div></div>
            @endif
        </div>
    </div></div>
</section>
@if ($projects->isNotEmpty())
<section class="section bg-paper">
    <div class="container">
        <div class="section-head"><div><span class="eyebrow">Réalisations associées</span><h2 class="display">La preuve <em>par l'exemple.</em></h2></div><a class="link-arrow" href="{{ route('projects.index') }}">Tout le portfolio <i class="fa-solid fa-arrow-right"></i></a></div>
        <div class="row g-4">@foreach ($projects as $project)<div class="col-md-6 col-lg-4">@include('partials.project-card', ['project' => $project])</div>@endforeach</div>
    </div>
</section>
@endif
@include('partials.cta-band', ['ctaTitle' => 'Parlons de votre projet de <em>'.e(mb_strtolower($service->short)).'.</em>'])
@endsection
