<a class="wa-float" href="{{ whatsapp_url() }}" target="_blank" rel="noopener" aria-label="Écrire à VOKAMIN sur WhatsApp" data-track="whatsapp"><i class="fa-brands fa-whatsapp"></i><span class="wa-tip">Une question ? Écrivez-nous</span></a>
<button class="to-top" type="button" aria-label="Revenir en haut de page"><i class="fa-solid fa-arrow-up"></i></button>
<div data-vue="CookieBanner" data-props="{{ json_encode(['policyUrl' => route('cookies')]) }}"></div>
<div class="page-curtain" aria-hidden="true"><img src="{{ asset('images/logo-vokamin-blanc.png') }}" alt=""></div>
<div data-vue="BrochureModal" data-props="{{ json_encode(['action' => route('forms.brochure')]) }}"></div>
