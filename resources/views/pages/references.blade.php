@extends('layouts.app')
@section('title', 'Nos références — clients et partenaires')
@section('description', "Références VOKAMIN : Standard Chartered Bank Côte d'Ivoire, Clinique Médicale Siloé Grand-Bassam, Azur Inter, SCI ATAFAY, EITBEL et de nombreux clients particuliers.")

@section('content')
@include('partials.page-hero', ['title' => 'La confiance se construit. <em>La nôtre se vérifie.</em>', 'lead' => 'Banques, établissements de santé, entreprises, sociétés immobilières et particuliers : nos références témoignent de la diversité de nos interventions.', 'image' => asset('images/maureen-facade-nuit.jpg'), 'crumbs' => ['Nos références' => null], 'eyebrow' => 'Références'])
<section class="on-ink"><div class="sector-bar">
    <div><i class="fa-solid fa-building-columns"></i><strong>Banque & finance</strong><span>Building Maintenance</span></div>
    <div><i class="fa-solid fa-hospital"></i><strong>Santé</strong><span>Design & enseignes</span></div>
    <div><i class="fa-solid fa-briefcase"></i><strong>Entreprises & SCI</strong><span>Prestations BTP</span></div>
    <div><i class="fa-solid fa-house-chimney"></i><strong>Particuliers</strong><span>Riviera, Bingerville, Grand-Bassam, Bonoua, Adiaké</span></div>
</div></section>
<section class="section">
    <div class="container">
        <div class="section-head"><div><span class="eyebrow">Nos clients</span><h2 class="display reveal">Des noms, <em>des chantiers réels.</em></h2></div><p class="text-muted-vk mb-0 small" style="max-width:40ch"><i class="fa-solid fa-circle-info me-1"></i>Les logos des clients sont affichés après obtention de leur autorisation.</p></div>
        <div class="ref-grid">
            @foreach ($references as $i => $r)
                <div class="ref-card reveal d{{ $i % 3 + 1 }}">
                    <div class="ref-logo">@if ($r->logo && $r->logo_authorized)<img src="{{ media_url($r->logo) }}" alt="{{ $r->name }}" style="max-height:56px;max-width:180px;object-fit:contain">@else{{ $r->name }}@endif</div>
                    <span class="sector">{{ $r->sector }}</span>
                    <p>{{ $r->prestation }}</p>
                    @if ($r->project && $r->project->is_published)
                        <a class="ref-link" href="{{ route('projects.show', $r->project) }}">Voir le projet associé <i class="fa-solid fa-arrow-right"></i></a>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</section>
@include('partials.cta-band', ['ctaTitle' => 'Rejoignez ceux qui <em>exigent le meilleur.</em>'])
@endsection
