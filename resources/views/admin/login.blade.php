<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="robots" content="noindex, nofollow">
    <title>Connexion — Administration VOKAMIN</title>
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300..800&display=swap">
    @vite(['resources/css/admin.css'])
</head>
<body class="adm">
<section class="adm-login">
    <div class="adm-login-visual"><img src="{{ asset('images/oasis-villa-piscine.jpg') }}" alt=""><div><img src="{{ asset('images/logo-vokamin-blanc.png') }}" alt="VOKAMIN" style="height:70px;width:auto"><p class="display mt-4" style="font-size:2rem;color:#fff;max-width:16ch">Gérez votre site <em style="color:#9be3ad">sans toucher au code.</em></p></div></div>
    <div class="adm-login-form">
        <form method="POST" action="{{ route('admin.login.attempt') }}" class="form-vk w-100" style="max-width:380px">
            @csrf
            <img src="{{ asset('images/logo-vokamin.png') }}" alt="VOKAMIN" style="height:56px;width:auto" class="mb-4">
            <h1 class="display" style="font-size:2rem">Espace administration</h1>
            <p class="text-muted-vk small mb-4">Accès réservé à l'équipe VOKAMIN.</p>
            @if ($errors->any())<div class="alert alert-danger small py-2">{{ $errors->first() }}</div>@endif
            <div class="mb-3"><label class="form-label" for="email">Email</label><input class="form-control" id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="username" autofocus></div>
            <div class="mb-3"><label class="form-label" for="password">Mot de passe</label><input class="form-control" type="password" id="password" name="password" required autocomplete="current-password"></div>
            <div class="form-check mb-4 small"><input class="form-check-input" type="checkbox" id="remember" name="remember" value="1"><label class="form-check-label" for="remember">Rester connecté</label></div>
            <button class="btn-vk w-100" type="submit">Se connecter <i class="fa-solid fa-arrow-right"></i></button>
            <p class="small text-muted-vk mt-4 mb-0"><i class="fa-solid fa-shield-halved me-1" style="color:var(--vk-green)"></i>5 tentatives maximum par minute</p>
            <a class="small d-inline-block mt-3" href="{{ route('home') }}"><i class="fa-solid fa-arrow-left me-1"></i> Retour au site</a>
        </form>
    </div>
</section>
</body>
</html>
