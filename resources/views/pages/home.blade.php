@extends('layouts.app')

@section('title', 'Entreprise BTP à Abidjan : construction, rénovation | VOKAMIN')
@section('description', 'VOKAMIN, entreprise BTP à Abidjan : construction de villas, plans 2D/3D, rénovation, décoration, maintenance de bâtiments et négoce en Côte d\'Ivoire.')
@if ($slides->isNotEmpty())
    @section('preload', $slides->first()->cover_url)
    @section('og_image', $slides->first()->cover_url)
@endif

@push('head')
<script type="application/ld+json">{!! json_encode([
    '@context' => 'https://schema.org', '@type' => 'GeneralContractor', 'name' => setting('company_name'), 'alternateName' => setting('brand'),
    'slogan' => setting('tagline'), 'url' => url('/'), 'logo' => asset('images/logo-vokamin.png'), 'image' => asset('images/villa-signature-piscine.jpg'),
    'telephone' => setting('tel1'), 'email' => setting('email'), 'foundingDate' => '2022-05-18',
    'address' => ['@type' => 'PostalAddress', 'streetAddress' => setting('address'), 'addressLocality' => 'Abidjan', 'addressRegion' => 'Cocody', 'postOfficeBoxNumber' => setting('bp'), 'addressCountry' => 'CI'],
    'contactPoint' => array_values(array_filter([
        ['@type' => 'ContactPoint', 'telephone' => setting('tel1'), 'contactType' => 'customer service', 'areaServed' => 'CI', 'availableLanguage' => 'French'],
        setting('tel2') ? ['@type' => 'ContactPoint', 'telephone' => setting('tel2'), 'contactType' => 'sales', 'areaServed' => 'CI', 'availableLanguage' => 'French'] : null,
    ])),
    'openingHours' => array_values(array_filter([
        \App\Support\Schema::hours('Mo-Fr', setting('hours_week')),
        \App\Support\Schema::hours('Sa', setting('hours_sat')),
        \App\Support\Schema::hours('Su', setting('hours_sun')),
    ])),
    'sameAs' => array_values(array_filter([setting('facebook'), setting('linkedin'), setting('instagram'), setting('youtube')])),
    'areaServed' => "Côte d'Ivoire",
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
@endpush

@section('content')
<section class="hero" aria-label="Présentation">
    <div data-vue="HeroSlider" data-props="{{ json_encode(['slides' => $slides->map(fn ($p) => ['image' => $p->cover_url, 'title' => $p->name, 'loc' => $p->location.' · '.$p->category, 'href' => route('projects.show', $p)])->values()]) }}" class="hero-slides">
        @if ($slides->isNotEmpty())<div class="hero-slide active"><img src="{{ $slides->first()->cover_url }}" alt="{{ $slides->first()->name }}, {{ $slides->first()->location }}" fetchpriority="high"></div>@endif
    </div>
    <div class="hero-content">
        <div class="container">
            <span class="eyebrow">Entreprise BTP à Abidjan · Promotion immobilière</span>
            <h1 class="display"><span class="visually-hidden">VOKAMIN, entreprise BTP à Abidjan : </span>{!! str_replace(['co-créons', ', un avenir.'], ['co&#8209;créons', ', <em>un avenir.</em>'], e(setting('tagline'))) !!}</h1>
            <p class="lead-vk">{{ setting('hero_lead') }}</p>
            <div class="hero-ctas">
                <a class="btn-vk" href="{{ route('projects.index') }}">Découvrir nos réalisations <i class="fa-solid fa-arrow-right"></i></a>
                <a class="btn-vk ghost-light" href="{{ route('quote') }}">Demander un devis</a>
            </div>
            <div class="hero-bar" id="heroBar"></div>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="row g-5 align-items-center">
            <div class="col-lg-6 order-lg-2">
                <span class="eyebrow">Qui sommes-nous</span>
                <h2 class="intro-statement reveal">Un projet. <strong>Un seul interlocuteur.</strong> Zéro compromis sur la qualité.</h2>
                <p class="lead-vk reveal d1 mt-4">Depuis 2022, VOKAMIN accompagne particuliers, entreprises, institutions et promoteurs. Chaque décision se prend avec vous ; un responsable unique coordonne tous les métiers.</p>
                <div class="d-flex flex-wrap gap-4 align-items-center mt-4 reveal d2">
                    <a class="btn-vk blue" href="{{ route('company') }}">Découvrir l'entreprise <i class="fa-solid fa-arrow-right"></i></a>
                    <a class="link-arrow" href="#" data-open-brochure>Recevoir la plaquette <i class="fa-solid fa-arrow-down"></i></a>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="split-img img-reveal">
                    <img class="main" src="{{ asset('images/villa-signature.jpg') }}" alt="Villa contemporaine conçue par VOKAMIN" loading="lazy">
                    <img class="float" src="{{ asset('images/maureen-piscine-1.jpg') }}" alt="Piscine de la Villa Maureen" loading="lazy">
                    <div class="tag" aria-hidden="true">Depuis<strong>2022</strong></div>
                </div>
            </div>
        </div>
        <div class="stats mt-5 pt-lg-4">
            <div class="stat reveal"><div class="num">2022</div><p>Création à Abidjan, siège à Cocody Angré</p></div>
            <div class="stat reveal d1"><div class="num" data-count="{{ $stats['services'] }}">{{ $stats['services'] }}</div><p>Métiers intégrés, de la conception à l'entretien</p></div>
            <div class="stat reveal d2"><div class="num" data-count="10">10</div><p>Villas haut standing aux Résidences Oasis</p></div>
            <div class="stat reveal d3"><div class="num"><span data-count="{{ $stats['references'] }}">{{ $stats['references'] }}</span><sup>+</sup></div><p>Références entreprises et institutions</p></div>
        </div>
    </div>
</section>

<section class="section bg-paper">
    <div class="container">
        <div class="section-head">
            <div><span class="eyebrow">Domaines d'intervention</span><h2 class="display reveal">Sept métiers. <em>Une seule exigence.</em></h2></div>
            <a class="link-arrow" href="{{ route('services.index') }}">Tous nos services <i class="fa-solid fa-arrow-right"></i></a>
        </div>
        @php $tileClasses = ['t-a', 't-b', 't-c', 't-d', 't-e', 't-f', 't-g']; @endphp
        <div class="bento">
            @foreach ($services as $i => $s)
                <a class="tile {{ $tileClasses[$i] ?? 't-d' }} reveal d{{ $i % 3 + 1 }}" href="{{ route('services.show', $s) }}">
                    <img src="{{ media_url($s->image) }}" alt="{{ $s->name }} à Abidjan — VOKAMIN" loading="lazy" decoding="async">
                    <span class="tile-idx"><b>{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</b></span>
                    <span class="tile-go"><i class="fa-solid fa-arrow-right"></i></span>
                    <h3>{{ $s->name }}</h3><p>{{ $s->teaser }}</p>
                </a>
            @endforeach
        </div>
    </div>
</section>

<section class="section" data-track-carousel>
    <div class="container">
        <div class="section-head">
            <div><span class="eyebrow">Réalisations</span><h2 class="display reveal">Nos chantiers <em>parlent pour nous.</em></h2></div>
            <div class="d-flex align-items-center gap-4">
                <a class="link-arrow d-none d-md-inline-flex" href="{{ route('projects.index') }}">Tout le portfolio <i class="fa-solid fa-arrow-right"></i></a>
                <div class="track-nav"><button type="button" data-prev aria-label="Projets précédents"><i class="fa-solid fa-arrow-left"></i></button><button type="button" data-next aria-label="Projets suivants"><i class="fa-solid fa-arrow-right"></i></button></div>
            </div>
        </div>
        <div class="proj-track">
            @foreach ($projects as $project)
                @include('partials.project-card', ['project' => $project])
            @endforeach
        </div>
    </div>
</section>

<section class="section on-ink">
    <div class="container">
        <div class="section-head">
            <div><span class="eyebrow">Notre méthode</span><h2 class="display reveal">Six étapes. <em>Aucune surprise.</em></h2></div>
            <p class="lead-vk mb-0" style="max-width:42ch">Délais, budget, qualité : tout est cadré et partagé avec vous, du premier rendez-vous à la livraison.</p>
        </div>
        <div class="process"><span class="bar"></span>
            @foreach ([['Écoute du besoin', 'Nous prenons le temps de comprendre votre projet, vos usages et votre budget.'], ['Étude du projet', 'Visite du site, faisabilité technique et première estimation.'], ['Conception', 'Plans 2D, rendus 3D et choix des matériaux, validés avec vous.'], ['Planification', "Devis détaillé, planning d'exécution et organisation du chantier."], ['Réalisation & contrôle', 'Exécution par nos équipes, contrôle qualité à chaque phase.'], ['Livraison', 'Réception contradictoire, levée des réserves et remise des clés.']] as $i => [$t, $d])
                <div class="step"><div class="dot">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</div><h3>{{ $t }}</h3><p>{{ $d }}</p></div>
            @endforeach
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="row g-5 align-items-center">
            <div class="col-lg-5"><div class="img-reveal"><img src="{{ asset('images/oasis-villa-piscine.jpg') }}" alt="Villa des Résidences Oasis" class="w-100" style="aspect-ratio:4/5;object-fit:cover" loading="lazy"></div></div>
            <div class="col-lg-7">
                <span class="eyebrow">Pourquoi VOKAMIN</span>
                <h2 class="display reveal" style="font-size:var(--fs-h2)">Votre vision. Notre savoir&#8209;faire. <em>Un avenir commun.</em></h2>
                <p class="lead-vk reveal d1 mb-4">Nous transformons vos idées en réalisations concrètes, fonctionnelles et durables, en plaçant l'esthétique, la qualité et l'innovation au centre de chaque chantier.</p>
                <div class="value reveal"><div class="ico"><i class="fa-solid fa-lightbulb"></i></div><div><h3>Créativité</h3><p>Imaginer des solutions innovantes et esthétiques, adaptées à votre terrain et à votre mode de vie.</p></div></div>
                <div class="value reveal d1"><div class="ico"><i class="fa-solid fa-bolt"></i></div><div><h3>Réactivité</h3><p>Répondre efficacement et rapidement aux besoins : des décisions claires, un chantier qui avance.</p></div></div>
                <div class="value reveal d2" style="border-bottom:1px solid var(--vk-line)"><div class="ico"><i class="fa-solid fa-scale-balanced"></i></div><div><h3>Intégrité</h3><p>Transparence et fiabilité dans toutes nos interventions, du devis à la livraison.</p></div></div>
            </div>
        </div>
    </div>
</section>

@if ($references->isNotEmpty())
<section class="section-sm bg-paper">
    <div class="container">
        <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
            <div><span class="eyebrow">Ils nous font confiance</span><h2 class="display mb-0" style="font-size:clamp(1.5rem,2.4vw,2rem)">Ils ont choisi <em>l'exigence.</em></h2></div>
            <a class="link-arrow" href="{{ route('references') }}">Nos références <i class="fa-solid fa-arrow-right"></i></a>
        </div>
    </div>
    <div class="marquee" aria-label="Références"><div class="marquee-track">
        @foreach ([1, 2] as $_)@foreach ($references as $r)<span class="marquee-item">{{ $r->name }}</span>@endforeach @endforeach
    </div></div>
</section>
@endif

@if ($posts->isNotEmpty())
<section class="section">
    <div class="container">
        <div class="section-head">
            <div><span class="eyebrow">Actualités</span><h2 class="display reveal">Sur le terrain, <em>chaque semaine.</em></h2></div>
            <a class="link-arrow" href="{{ route('posts.index') }}">Toutes les actualités <i class="fa-solid fa-arrow-right"></i></a>
        </div>
        <div class="row g-4 g-lg-5">
            @foreach ($posts as $i => $post)<div class="col-md-6 col-lg-4">@include('partials.news-card', ['post' => $post, 'i' => $i])</div>@endforeach
        </div>
    </div>
</section>
@endif

@include('partials.cta-band')
@endsection
