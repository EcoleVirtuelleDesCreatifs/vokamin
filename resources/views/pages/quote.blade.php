@extends('layouts.app')
@section('title', 'Demande de devis BTP gratuit')
@section('description', 'Demandez un devis gratuit à VOKAMIN pour votre projet de construction, rénovation, décoration, maintenance, négoce ou enseigne à Abidjan. Joignez vos plans et photos.')

@section('content')
@include('partials.page-hero', ['title' => 'Votre projet <em>commence ici.</em>', 'lead' => "Décrivez votre projet en quelques minutes. Un chargé d'affaires VOKAMIN vous recontacte pour en parler.", 'image' => asset('images/lirana-rendu.jpg'), 'crumbs' => ['Demande de devis' => null], 'eyebrow' => 'Devis gratuit et sans engagement'])
<section class="section bg-paper">
    <div class="container"><div class="row g-5">
        <div class="col-lg-8">
            <div data-vue="QuoteWizard" data-props="{{ json_encode([
                'action' => route('forms.quote'),
                'services' => $services->map(fn ($s) => ['value' => $s->slug, 'label' => $s->short, 'icon' => $s->icon])->push(['value' => 'autre', 'label' => 'Autre besoin', 'icon' => 'fa-ellipsis'])->values(),
                'privacyUrl' => route('privacy'),
                'whatsapp' => whatsapp_url(),
                'projectsUrl' => route('projects.index'),
                'maxFiles' => config('vokamin.upload.quote_max_files'),
                'maxMb' => config('vokamin.upload.quote_max_kb') / 1024,
                'accept' => config('vokamin.upload.quote_mimes'),
            ]) }}">
                <noscript><p class="p-4 bg-white">Activez JavaScript pour utiliser le formulaire, ou contactez-nous au {{ setting('tel1') }}.</p></noscript>
            </div>
        </div>
        <aside class="col-lg-4"><div class="svc-nav">
            <div class="aside-card"><h3>Après votre demande</h3>
                <ul class="mt-3">
                    <li><i class="fa-solid fa-envelope-circle-check"></i><span><strong class="text-white d-block">Confirmation immédiate</strong>Vous recevez un récapitulatif par email.</span></li>
                    <li><i class="fa-solid fa-phone-volume"></i><span><strong class="text-white d-block">Rappel</strong>Un chargé d'affaires étudie votre projet et vous appelle.</span></li>
                    <li><i class="fa-solid fa-ruler-combined"></i><span><strong class="text-white d-block">Visite et analyse</strong>Si nécessaire, nous visitons le site du projet.</span></li>
                    <li><i class="fa-solid fa-file-invoice"></i><span><strong class="text-white d-block">Devis détaillé</strong>Chiffrage poste par poste, sans engagement.</span></li>
                </ul>
            </div>
            <div class="p-4 bg-white mt-3">
                <p class="filter-group-label">Vous préférez parler à quelqu'un ?</p>
                <p class="mb-1"><a class="fw-bold" href="{{ tel_href(setting('tel1')) }}" data-track="call">{{ setting('tel1') }}</a> <button class="copy-btn" data-copy="{{ setting('tel1') }}" aria-label="Copier le numéro"><i class="fa-regular fa-copy"></i></button></p>
                @if (setting('tel2'))<p class="mb-3"><a class="fw-bold" href="{{ tel_href(setting('tel2')) }}" data-track="call">{{ setting('tel2') }}</a> <button class="copy-btn" data-copy="{{ setting('tel2') }}" aria-label="Copier le numéro"><i class="fa-regular fa-copy"></i></button></p>@endif
                <a class="btn-vk wa w-100 sm" href="{{ whatsapp_url() }}" target="_blank" rel="noopener" data-track="whatsapp"><i class="fa-brands fa-whatsapp"></i> Écrire sur WhatsApp</a>
            </div>
        </div></aside>
    </div></div>
</section>
@endsection
