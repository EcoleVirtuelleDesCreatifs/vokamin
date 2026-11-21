<footer class="site-footer">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-4">
                <img src="{{ asset('images/logo-vokamin-blanc.png') }}" alt="VOKAMIN Côte d'Ivoire" style="height:64px;width:auto" loading="lazy">
                <p class="footer-sign">Nous co-créons plus qu'un bâtiment, <em>un avenir.</em></p>
                <div class="socials" aria-label="Réseaux sociaux">
                    @foreach (['facebook' => 'fa-facebook-f', 'linkedin' => 'fa-linkedin-in', 'instagram' => 'fa-instagram', 'youtube' => 'fa-youtube'] as $net => $icon)
                        @if (setting($net))
                            <a href="{{ setting($net) }}" target="_blank" rel="noopener" aria-label="{{ ucfirst($net) }}"><i class="fa-brands {{ $icon }}"></i></a>
                        @endif
                    @endforeach
                </div>
            </div>
            <div class="col-6 col-lg-2">
                <h4>Entreprise</h4>
                <ul>
                    <li><a href="{{ route('company') }}">L'entreprise</a></li>
                    <li><a href="{{ route('projects.index') }}">Réalisations</a></li>
                    <li><a href="{{ route('references') }}">Références</a></li>
                    <li><a href="{{ route('posts.index') }}">Actualités</a></li>
                    <li><a href="{{ route('jobs') }}">Recrutement</a></li>
                    <li><a href="{{ route('quote') }}">Demande de devis</a></li>
                </ul>
            </div>
            <div class="col-6 col-lg-3">
                <h4>Services</h4>
                <ul>@foreach ($navServices as $s)<li><a href="{{ route('services.show', $s->slug) }}">{{ $s->short }}</a></li>@endforeach</ul>
            </div>
            <div class="col-lg-3">
                <h4>Contact</h4>
                <ul class="footer-contact">
                    <li><i class="fa-solid fa-location-dot"></i><span>{{ setting('address') }}<br>{{ setting('bp') }}</span></li>
                    <li><i class="fa-solid fa-phone"></i><span><a href="{{ tel_href(setting('tel1')) }}" data-track="call">{{ setting('tel1') }}</a>@if (setting('tel2'))<br><a href="{{ tel_href(setting('tel2')) }}" data-track="call">{{ setting('tel2') }}</a>@endif</span></li>
                    <li><i class="fa-solid fa-envelope"></i><a href="mailto:{{ setting('email') }}">{{ setting('email') }}</a></li>
                    <li><i class="fa-brands fa-whatsapp"></i><a href="{{ whatsapp_url() }}" target="_blank" rel="noopener" data-track="whatsapp">Écrire sur WhatsApp</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-legal">
            <span class="ids">© {{ date('Y') }} {{ setting('company_name') }} · RCCM {{ setting('rccm') }} · CC {{ setting('cc') }}</span>
            <nav aria-label="Liens légaux">
                <a href="{{ route('legal') }}">Mentions légales</a>
                <a href="{{ route('privacy') }}">Confidentialité</a>
                <a href="{{ route('cookies') }}">Cookies</a>
                <a href="{{ route('sitemap.page') }}">Plan du site</a>
            </nav>
        </div>
    </div>
</footer>
