<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php
        $y = fn ($k) => html_entity_decode(trim($__env->yieldContent($k)), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $pageTitle = $y('title');
        // Titre ≤ 65 caractères et description ≤ 155 : au-delà, Google les tronque.
        $fullTitle = $pageTitle ? (str_contains($pageTitle, 'VOKAMIN') || mb_strlen($pageTitle) > 54 ? $pageTitle : $pageTitle.' | VOKAMIN') : 'Entreprise BTP à Abidjan | VOKAMIN Côte d\'Ivoire';
        if (mb_strlen($fullTitle) > 65) {
            $fullTitle = rtrim(mb_substr($fullTitle, 0, mb_strrpos(mb_substr($fullTitle, 0, 64), ' ') ?: 64), ' ,:;—–-|').'…';
        }
        $desc = preg_replace('/\s+/u', ' ', $y('description') ?: 'VOKAMIN SARL, entreprise BTP à Abidjan : construction, conception 2D/3D, rénovation, maintenance, négoce et enseignes en Côte d\'Ivoire.');
        if (mb_strlen($desc) > 155) {
            $desc = rtrim(mb_substr($desc, 0, mb_strrpos(mb_substr($desc, 0, 154), ' ') ?: 154), ' ,:;—–-(').'…';
        }
        $ogImage = $y('og_image') ?: asset('images/villa-signature-piscine.jpg');
    @endphp
    <title>{{ $fullTitle }}</title>
    <meta name="description" content="{{ $desc }}">
    <meta name="theme-color" content="#08153a">
    <link rel="canonical" href="{{ url()->current() }}">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="VOKAMIN Côte d'Ivoire">
    <meta property="og:title" content="{{ $fullTitle }}">
    <meta property="og:description" content="{{ $desc }}">
    <meta property="og:image" content="{{ $ogImage }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:locale" content="fr_CI">
    <meta name="twitter:card" content="summary_large_image">
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="icon" href="{{ asset('favicon-32.png') }}" type="image/png" sizes="32x32">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">
    @hasSection('preload')<link rel="preload" as="image" href="{{ $y('preload') }}">@endif
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300..800&display=swap">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
    @if (config('vokamin.ga_id'))
        <script>window.VK_GA_ID = @json(config('vokamin.ga_id'));</script>
    @endif
</head>
<body class="@yield('body_class')">
    @include('partials.header')
    <main id="main">
        @yield('content')
    </main>
    @unless ($__env->hasSection('no_footer'))
        @include('partials.footer')
    @endunless
    @include('partials.floating')
</body>
</html>
