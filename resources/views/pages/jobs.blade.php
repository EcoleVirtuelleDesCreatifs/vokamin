@extends('layouts.app')
@section('title', "Recrutement — offres d'emploi BTP")
@section('description', "Rejoignez VOKAMIN : offres d'emploi BTP à Abidjan et candidature spontanée.")

@section('content')
@include('partials.page-hero', ['title' => 'Bâtissez votre carrière <em>avec nous.</em>', 'lead' => 'Vous êtes passionné par le bâtiment, rigoureux et engagé ? Rejoignez une entreprise ivoirienne en croissance.', 'image' => asset('images/oasis-ferraillage.jpg'), 'crumbs' => ['Recrutement' => null], 'eyebrow' => 'Carrières'])
<section class="section">
    <div class="container">
        <div class="row g-4 mb-5">
            <div class="col-md-4"><div class="feature-box reveal"><i class="fa-solid fa-seedling"></i><h3>Des projets qui font grandir</h3><p>Villas, complexe résidentiel, sites professionnels : des chantiers variés.</p></div></div>
            <div class="col-md-4"><div class="feature-box reveal d1"><i class="fa-solid fa-people-group"></i><h3>Une équipe soudée</h3><p>Une structure à taille humaine où chaque idée est écoutée.</p></div></div>
            <div class="col-md-4"><div class="feature-box reveal d2"><i class="fa-solid fa-helmet-safety"></i><h3>La sécurité d'abord</h3><p>Équipements de protection et règles de sécurité sur chaque chantier.</p></div></div>
        </div>
        <div class="section-head"><div><span class="eyebrow">Offres d'emploi</span><h2 class="display">{{ $jobs->count() ? $jobs->count().' poste'.($jobs->count() > 1 ? 's' : '').' ouvert'.($jobs->count() > 1 ? 's' : '') : 'Candidature spontanée' }}</h2></div></div>
        <div style="border-bottom:1px solid var(--vk-line)">
            @foreach ($jobs as $j)
                <div class="job reveal"><div><h3>{{ $j->title }}</h3><div class="job-tags"><span><i class="fa-solid fa-file-contract"></i>{{ $j->contract }}</span><span><i class="fa-solid fa-location-dot"></i>{{ $j->location }}</span><span><i class="fa-solid fa-briefcase"></i>{{ $j->experience }}</span></div>
                    @if (! empty($j->missions))<details><summary><i class="fa-solid fa-plus me-1"></i> Voir les missions</summary><ul>@foreach ($j->missions as $m)<li>{{ $m }}</li>@endforeach</ul></details>@endif
                </div><a class="btn-vk sm" href="#candidature" data-apply="{{ $j->title }}">Postuler</a></div>
            @endforeach
        </div>
    </div>
</section>
<section class="section bg-paper" id="candidature">
    <div class="container"><div class="row g-5">
        <div class="col-lg-4"><span class="eyebrow">Candidature</span><h2 class="display" style="font-size:var(--fs-h2)">Postulez en ligne</h2><p class="text-muted-vk">Pour une offre précise ou en candidature spontanée.</p></div>
        <div class="col-lg-8"><div class="p-4 p-md-5 bg-white">
            <div data-vue="ApplicationForm" data-props="{{ json_encode(['action' => route('forms.application'), 'jobs' => $jobs->pluck('title'), 'privacyUrl' => route('privacy'), 'maxMb' => config('vokamin.upload.cv_max_kb') / 1024, 'accept' => config('vokamin.upload.cv_mimes')]) }}"></div>
        </div></div>
    </div></div>
</section>
@endsection
