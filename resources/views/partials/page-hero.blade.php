{{-- Paramètres : $title (HTML autorisé), $lead, $image, $crumbs (tableau [libellé => url|null]), $eyebrow --}}
@push('head')
{!! \App\Support\Schema::breadcrumb($crumbs ?? []) !!}
@endpush
<section class="page-hero">
    <img src="{{ $image }}" alt="">
    <div class="container">
        <nav class="crumbs" aria-label="Fil d'Ariane">
            <a href="{{ route('home') }}">Accueil</a>
            @foreach ($crumbs ?? [] as $label => $url)
                <span class="sep">/</span>
                @if ($url)<a href="{{ $url }}">{{ $label }}</a>@else<span aria-current="page">{{ $label }}</span>@endif
            @endforeach
        </nav>
        @isset($eyebrow)<span class="eyebrow">{{ $eyebrow }}</span><br>@endisset
        <h1 class="display">{!! $title !!}</h1>
        @isset($lead)<p class="lead-vk">{{ $lead }}</p>@endisset
    </div>
</section>
