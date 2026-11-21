@extends('layouts.app')
@section('title', 'Contact — VOKAMIN à Cocody Angré')
@section('description', 'Contactez VOKAMIN : '.setting('address').', Abidjan. Tél. '.setting('tel1').', WhatsApp et email '.setting('email').'.')

@section('content')
@include('partials.page-hero', ['title' => 'Parlons de votre <em>prochain chantier.</em>', 'lead' => 'Une question, un rendez-vous, un renseignement ? Notre équipe vous répond par téléphone, WhatsApp, email ou dans nos bureaux de Cocody Angré.', 'image' => asset('images/villa-signature.jpg'), 'crumbs' => ['Contact' => null], 'eyebrow' => 'Contact'])
<section class="section">
    <div class="container"><div class="row g-5">
        <div class="col-lg-5">
            <span class="eyebrow">Nos coordonnées</span>
            <div class="contact-card"><i class="fa-solid fa-location-dot"></i><div><small>Adresse</small><span>{{ setting('address') }}<br>{{ setting('bp') }}</span></div></div>
            <div class="contact-card"><i class="fa-solid fa-phone"></i><div><small>Téléphones</small>
                <a href="{{ tel_href(setting('tel1')) }}" data-track="call">{{ setting('tel1') }}</a><button class="copy-btn" data-copy="{{ setting('tel1') }}" aria-label="Copier"><i class="fa-regular fa-copy"></i></button>
                @if (setting('tel2'))<br><a href="{{ tel_href(setting('tel2')) }}" data-track="call">{{ setting('tel2') }}</a><button class="copy-btn" data-copy="{{ setting('tel2') }}" aria-label="Copier"><i class="fa-regular fa-copy"></i></button>@endif
                @if (setting('fax'))<br><span class="small text-muted-vk fw-normal">Fax : {{ setting('fax') }}</span>@endif
            </div></div>
            <div class="contact-card"><i class="fa-solid fa-envelope"></i><div><small>Email</small><a href="mailto:{{ setting('email') }}">{{ setting('email') }}</a><button class="copy-btn" data-copy="{{ setting('email') }}" aria-label="Copier"><i class="fa-regular fa-copy"></i></button></div></div>
            <div class="contact-card"><i class="fa-regular fa-clock"></i><div class="w-100"><small>Horaires</small><div class="hours mt-2">
                <div><span>Lundi – vendredi</span><strong>{{ setting('hours_week') }}</strong></div>
                <div><span>Samedi</span><strong>{{ setting('hours_sat') }}</strong></div>
                <div><span>Dimanche</span><strong>{{ setting('hours_sun') }}</strong></div>
            </div></div></div>
            <div class="d-flex flex-wrap gap-2 mt-4"><a class="btn-vk wa" href="{{ whatsapp_url() }}" target="_blank" rel="noopener" data-track="whatsapp"><i class="fa-brands fa-whatsapp"></i> WhatsApp</a><a class="btn-vk ghost" href="{{ tel_href(setting('tel1')) }}" data-track="call"><i class="fa-solid fa-phone"></i> Appeler</a></div>
        </div>
        <div class="col-lg-7"><div class="p-4 p-md-5 bg-paper">
            <h2 class="display" style="font-size:2rem">Écrivez-nous</h2>
            <p class="text-muted-vk">Pour une demande chiffrée, utilisez plutôt notre <a href="{{ route('quote') }}">formulaire de devis</a>.</p>
            <div data-vue="ContactForm" data-props="{{ json_encode(['action' => route('forms.contact'), 'privacyUrl' => route('privacy')]) }}"></div>
        </div></div>
    </div></div>
</section>
<section class="map-embed" aria-label="Plan d'accès">
    <iframe title="Plan d'accès VOKAMIN — Cocody Angré" loading="lazy" referrerpolicy="no-referrer-when-downgrade"
        src="https://www.google.com/maps?q={{ urlencode(setting('address').', Abidjan, Côte d\'Ivoire') }}&output=embed"></iframe>
    <div class="map-card"><strong>{{ setting('company_name') }}</strong><p class="small text-muted-vk mb-2">{{ setting('address') }}, Abidjan</p><a class="link-arrow" href="https://www.google.com/maps/search/?api=1&query={{ urlencode(setting('address').', Abidjan') }}" target="_blank" rel="noopener">Itinéraire Google Maps <i class="fa-solid fa-arrow-up-right-from-square"></i></a></div>
</section>
@endsection
