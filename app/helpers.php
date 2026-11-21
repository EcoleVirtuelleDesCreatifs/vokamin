<?php

use App\Support\Settings;

if (! function_exists('setting')) {
    /** Valeur d'un paramètre éditable (coordonnées, textes). */
    function setting(string $key, ?string $default = null): ?string
    {
        return app(Settings::class)->get($key, $default);
    }
}

if (! function_exists('media_url')) {
    /** URL d'une image : fichier livré dans public/images ou fichier téléversé (disque public). */
    function media_url(?string $path): string
    {
        if (! $path) {
            return asset('images/villa-signature.jpg');
        }
        if (str_starts_with($path, 'http')) {
            return $path;
        }
        if (str_starts_with($path, 'images/')) {
            return asset($path);
        }

        // URL construite à partir du domaine de la requête (et non d'APP_URL) :
        // l'image s'affiche même si APP_URL est resté sur « localhost ».
        return asset('storage/'.ltrim($path, '/'));
    }
}

if (! function_exists('tel_href')) {
    function tel_href(?string $number): string
    {
        return 'tel:'.preg_replace('/[^0-9+]/', '', (string) $number);
    }
}

if (! function_exists('whatsapp_url')) {
    function whatsapp_url(?string $text = null): string
    {
        $num = preg_replace('/[^0-9]/', '', (string) setting('whatsapp'));

        return 'https://wa.me/'.$num.'?text='.rawurlencode($text ?? (string) setting('whatsapp_message'));
    }
}
