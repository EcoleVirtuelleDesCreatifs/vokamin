<section class="cta-band section on-ink">
    <img src="{{ asset('images/'.($ctaImage ?? 'villa-signature').'.jpg') }}" alt="" loading="lazy">
    <div class="container">
        <div class="row align-items-end g-4">
            <div class="col-lg-7"><span class="eyebrow">Passons à l'action</span><h2 class="display reveal">{!! $ctaTitle ?? 'Votre avenir commence <em>par un plan.</em>' !!}</h2></div>
            <div class="col-lg-5"><p class="lead-vk reveal d1">{{ $ctaText ?? 'Un terrain, des plans ou une simple idée : parlez-nous-en. Nous construisons la suite ensemble.' }}</p></div>
        </div>
        <div class="cta-options reveal d2">
            <a class="cta-opt" href="{{ route('quote') }}"><i class="fa-solid fa-file-pen"></i><span><small>En 3 minutes</small><strong>Demander un devis</strong></span></a>
            <a class="cta-opt" href="{{ tel_href(setting('tel1')) }}" data-track="call"><i class="fa-solid fa-phone"></i><span><small>Appeler VOKAMIN</small><strong>{{ setting('tel1') }}</strong></span></a>
            <a class="cta-opt" href="{{ whatsapp_url() }}" target="_blank" rel="noopener" data-track="whatsapp"><i class="fa-brands fa-whatsapp"></i><span><small>Réponse rapide</small><strong>Écrire sur WhatsApp</strong></span></a>
        </div>
    </div>
</section>
