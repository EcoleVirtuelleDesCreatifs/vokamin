<a class="visually-hidden-focusable position-absolute top-0 start-0 m-2 btn-vk sm" href="#main" style="z-index:3000">Aller au contenu</a>
<header class="site-header">
    <div class="container">
        <a class="brand" href="{{ route('home') }}" aria-label="VOKAMIN Côte d'Ivoire — accueil">
            <img class="logo-white" src="{{ asset('images/logo-vokamin-blanc.png') }}" alt="VOKAMIN Côte d'Ivoire" width="89" height="54">
            <img class="logo-color" src="{{ asset('images/logo-vokamin.png') }}" alt="" width="89" height="54">
        </a>
        <nav class="main-nav" aria-label="Navigation principale">
            <a href="{{ route('home') }}" @class(['active' => request()->routeIs('home')])>Accueil</a>
            <a href="{{ route('company') }}" @class(['active' => request()->routeIs('company')])>L'entreprise</a>
            <div class="nav-drop">
                <a href="{{ route('services.index') }}" @class(['active' => request()->routeIs('services.*')])>Nos services <i class="fa-solid fa-chevron-down"></i></a>
                <div class="mega">
                    <div class="mega-list">
                        @foreach ($navServices as $s)
                            <a class="mega-item" href="{{ route('services.show', $s->slug) }}"><i class="fa-solid {{ $s->icon }}"></i><span><strong>{{ $s->short }}</strong><small>{{ \Illuminate\Support\Str::limit($s->teaser, 56) }}</small></span></a>
                        @endforeach
                        <a class="mega-item" href="{{ route('services.index') }}"><i class="fa-solid fa-arrow-right"></i><span><strong>Tous nos services</strong><small>Vue d'ensemble de nos métiers</small></span></a>
                    </div>
                    <a class="mega-feature" href="{{ route('services.index') }}"><img src="{{ asset('images/oasis-villa-piscine.jpg') }}" alt="" loading="lazy"><div><span>{{ $navServices->count() }} métiers intégrés</span><strong>Voir tous nos services</strong></div></a>
                </div>
            </div>
            <a href="{{ route('projects.index') }}" @class(['active' => request()->routeIs('projects.*')])>Nos réalisations</a>
            <a href="{{ route('references') }}" @class(['active' => request()->routeIs('references')])>Nos références</a>
            <a href="{{ route('posts.index') }}" @class(['active' => request()->routeIs('posts.*')])>Actualités</a>
            <a href="{{ route('contact') }}" @class(['active' => request()->routeIs('contact')])>Contact</a>
        </nav>
        <div class="header-actions">
            <a class="btn-vk sm d-none d-md-inline-flex" href="{{ route('quote') }}">Demander un devis</a>
            <button class="burger" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileNav" aria-controls="mobileNav" aria-label="Ouvrir le menu"><span></span><span></span><span></span></button>
        </div>
    </div>
</header>
<div class="offcanvas offcanvas-end mobile-nav" tabindex="-1" id="mobileNav" aria-label="Menu">
    <div class="offcanvas-header">
        <img src="{{ asset('images/logo-vokamin-blanc.png') }}" alt="VOKAMIN" style="height:44px;width:auto">
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Fermer le menu"></button>
    </div>
    <div class="offcanvas-body">
        @foreach ([['home', 'Accueil', 'home'], ['company', "L'entreprise", 'company'], ['services.index', 'Nos services', 'services.*'], ['projects.index', 'Nos réalisations', 'projects.*'], ['references', 'Nos références', 'references'], ['posts.index', 'Actualités', 'posts.*'], ['contact', 'Contact', 'contact']] as [$r, $label, $pattern])
            <a @class(['m-link', 'active' => request()->routeIs($pattern)]) href="{{ route($r) }}">{{ $label }}<i class="fa-solid fa-arrow-right-long" style="font-size:.9rem;opacity:.5"></i></a>
            @if ($r === 'services.index')
                <div class="m-sub">@foreach ($navServices as $s)<a href="{{ route('services.show', $s->slug) }}">{{ $s->short }}</a>@endforeach</div>
            @endif
        @endforeach
        <div class="mt-auto pt-4 d-grid gap-2">
            <a class="btn-vk" href="{{ route('quote') }}">Demander un devis <i class="fa-solid fa-arrow-right"></i></a>
            <a class="btn-vk wa" href="{{ whatsapp_url() }}" target="_blank" rel="noopener" data-track="whatsapp"><i class="fa-brands fa-whatsapp"></i> WhatsApp</a>
        </div>
    </div>
</div>
