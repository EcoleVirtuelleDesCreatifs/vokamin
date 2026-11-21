@extends('layouts.app')
@section('title', "L'entreprise — mission, valeurs, informations")
@section('description', "Découvrez VOKAMIN SARL : mission, vision, valeurs, mot du gérant et informations administratives de l'entreprise BTP basée à Cocody Angré, Abidjan.")

@section('content')
@include('partials.page-hero', ['title' => 'Bâtir avec vous. <em>Bâtir pour durer.</em>', 'lead' => "Depuis 2022, VOKAMIN SARL conçoit, réalise et embellit des espaces de vie et de travail en plaçant la qualité, l'esthétique et la relation client au cœur de chaque projet.", 'image' => asset('images/villa-signature-piscine.jpg'), 'crumbs' => ["L'entreprise" => null], 'eyebrow' => "L'entreprise"])

<section class="section">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-5">
                <span class="eyebrow">Qui sommes-nous</span>
                <h2 class="display reveal" style="font-size:var(--fs-h2)">Née à Abidjan. <em>Pensée pour durer.</em></h2>
                <p class="lead-vk reveal d1">VOKAMIN est née d'une conviction : un projet de construction réussit quand le client est associé à chaque décision. Portée par une équipe dynamique, expérimentée et engagée, l'entreprise a fait de la co-création sa méthode de travail.</p>
                <p class="text-muted-vk reveal d2">VOKAMIN intervient sur l'ensemble du cycle de vie d'un bâtiment : promotion, conception, construction, rénovation, décoration, entretien, fourniture de matériaux et signalétique. Pour nous, chaque client est unique.</p>
                <button class="btn-vk blue mt-3" type="button" data-open-brochure><i class="fa-solid fa-file-arrow-down"></i> Recevoir la plaquette</button>
            </div>
            <div class="col-lg-6 offset-lg-1"><div class="timeline">
                @foreach ([['2022', 'Création de VOKAMIN SARL', 'Immatriculée le '.setting('creation').' à Abidjan (RCCM '.setting('rccm').').'], ['5 M', 'Capital social', 'Société au capital de '.setting('capital').', portée par une équipe dynamique, expérimentée et engagée.'], [$servicesCount, "Domaines d'activité", 'Promotion immobilière, conception 2D/3D, construction, rénovation et décoration, Building Maintenance, négoce, design et enseignes.'], ['10', 'Villas haut standing', 'Le complexe des Résidences Oasis à Bonoua, réalisation phare de VOKAMIN.']] as [$y, $t, $d])
                    <div class="tl-item reveal"><div class="yr">{{ $y }}</div><div><h3>{{ $t }}</h3><p>{{ $d }}</p></div></div>
                @endforeach
            </div></div>
        </div>
    </div>
</section>

<section class="section-sm pt-0">
    <div class="container"><div class="row g-3">
        <div class="col-md-6"><div class="mv-card mission reveal"><i class="fa-solid fa-bullseye big-ico"></i><span class="eyebrow" style="color:#bcd0ff">Notre mission</span><h3>Contribuer durablement au bien-être</h3><p class="mb-0">Concevoir, réaliser et embellir des espaces de vie et de travail esthétiques, fonctionnels et durables, au service de nos clients et partenaires.</p></div></div>
        <div class="col-md-6"><div class="mv-card vision reveal d1"><i class="fa-solid fa-binoculars big-ico"></i><span class="eyebrow" style="color:#c6f0cf">Notre vision</span><h3>Une référence du BTP intégré</h3><p class="mb-0">Devenir une entreprise de référence en Côte d'Ivoire, reconnue pour la qualité de ses réalisations, son innovation et une relation client fondée sur la confiance.</p></div></div>
    </div></div>
</section>

<section class="section bg-paper">
    <div class="container">
        <div class="section-head"><div><span class="eyebrow">Nos valeurs</span><h2 class="display reveal">Trois valeurs. <em>Zéro exception.</em></h2></div></div>
        <div class="row g-4">
            <div class="col-md-4"><div class="feature-box reveal"><i class="fa-solid fa-lightbulb"></i><h3>Créativité</h3><p>Imaginer des solutions innovantes et esthétiques.</p></div></div>
            <div class="col-md-4"><div class="feature-box reveal d1"><i class="fa-solid fa-bolt"></i><h3>Réactivité</h3><p>Répondre efficacement et rapidement aux besoins.</p></div></div>
            <div class="col-md-4"><div class="feature-box reveal d2"><i class="fa-solid fa-scale-balanced"></i><h3>Intégrité</h3><p>Garantir transparence et fiabilité dans toutes nos interventions.</p></div></div>
        </div>
    </div>
</section>

<section class="section">
    <div class="container"><div class="manager">
        <div class="manager-photo reveal"><span class="initials">KAE</span><small>Photo du gérant à ajouter</small></div>
        <div>
            <span class="eyebrow">Le mot du gérant</span>
            <h2 class="display reveal" style="font-size:var(--fs-h2)">« Bâtir bien plus qu'une structure : <em>un cadre de vie.</em> »</h2>
            <div class="measure reveal d1">
                <p class="lead-vk">Chez VOKAMIN, nous croyons que chaque projet est une opportunité de bâtir bien plus qu'une structure : un cadre de vie, une vision, un avenir durable.</p>
                <p class="text-muted-vk">Notre approche repose sur une démarche collaborative : chaque projet est une co-création avec nos clients. Nous nous engageons à transformer leurs idées en réalisations concrètes, fonctionnelles et durables.</p>
            </div>
            <p class="signature mt-3 mb-0">{{ setting('gerant') }}</p>
            <p class="small text-muted-vk text-uppercase fw-bold" style="letter-spacing:.14em">Gérant, {{ setting('company_name') }}</p>
            <div class="creds">
                <div><i class="fa-solid fa-graduation-cap"></i><span><strong>MBA</strong> — Université Laval, Québec (Canada)</span></div>
                <div><i class="fa-solid fa-building-columns"></i><span><strong>Certificat de compétence en Technologie du Bâtiment</strong> — École d'Architecture d'Abidjan</span></div>
            </div>
        </div>
    </div></div>
</section>

<section class="section bg-paper">
    <div class="container">
        <div class="section-head"><div><span class="eyebrow">Notre organisation</span><h2 class="display reveal">Toutes les expertises. <em>Une seule équipe.</em></h2></div></div>
        <div class="row g-4">
            @foreach ([['fa-compass-drafting', 'Études & conception', 'Plans 2D, rendus 3D et dossiers techniques.'], ['fa-helmet-safety', 'Travaux', 'Gros œuvre, second œuvre et finitions.'], ['fa-broom', 'Building Maintenance', 'Nettoyage, entretien, traitements et espaces verts.'], ['fa-boxes-stacked', 'Négoce & design', 'Fourniture de matériaux, éclairage et enseignes.']] as $i => [$ic, $t, $d])
                <div class="col-sm-6 col-lg-3"><div class="feature-box reveal d{{ $i % 3 + 1 }}"><i class="fa-solid {{ $ic }}"></i><h3>{{ $t }}</h3><p>{{ $d }}</p></div></div>
            @endforeach
        </div>
    </div>
</section>

<section class="section">
    <div class="container"><div class="row g-5 align-items-start">
        <div class="col-lg-4">
            <span class="eyebrow">Informations officielles</span>
            <h2 class="display reveal" style="font-size:var(--fs-h2)">Transparence <em>totale.</em></h2>
            <p class="text-muted-vk">Les informations administratives de {{ setting('company_name') }}, à l'attention des entreprises, institutions, investisseurs et partenaires.</p>
            <button class="btn-vk ghost mt-2" type="button" data-open-brochure><i class="fa-solid fa-file-pdf"></i> Plaquette institutionnelle</button>
        </div>
        <div class="col-lg-8"><div class="id-card reveal">
            <div class="head"><strong>{{ setting('company_name') }}</strong></div>
            <div class="id-grid">
                @foreach (['Raison sociale' => setting('company_name'), 'Nom commercial' => setting('brand'), 'Forme juridique' => setting('forme'), 'Capital social' => setting('capital'), 'RCCM' => setting('rccm'), 'Compte contribuable' => setting('cc'), 'Date de création' => setting('creation'), 'Secteur' => 'Bâtiment et Travaux Publics', 'Siège' => setting('address'), 'Boîte postale' => setting('bp'), 'Téléphones' => trim(setting('tel1').' / '.setting('tel2'), ' /'), 'Email' => setting('email')] as $k => $v)
                    <div><small>{{ $k }}</small><span>{{ $v }}</span></div>
                @endforeach
            </div>
        </div></div>
    </div></div>
</section>

@include('partials.cta-band')
@endsection
