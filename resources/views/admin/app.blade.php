<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="robots" content="noindex, nofollow">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Administration VOKAMIN</title>
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300..800&display=swap">
    @vite(['resources/css/admin.css', 'resources/js/admin.js'])
</head>
<body class="adm">
    <div id="admin-app" data-props="{{ json_encode([
        'user' => ['name' => auth()->user()->name, 'email' => auth()->user()->email],
        'base' => url('/admin/api'),
        'siteUrl' => route('home'),
        'logoutUrl' => route('admin.logout'),
        'logo' => asset('images/logo-vokamin-blanc.png'),
        'quoteFileUrl' => url('/admin/fichiers/devis'),
        'cvUrl' => url('/admin/fichiers/cv'),
    ]) }}"></div>
</body>
</html>
