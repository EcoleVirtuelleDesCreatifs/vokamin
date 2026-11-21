<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;

/**
 * Sert les fichiers téléversés depuis l'admin (disque « public ») lorsque le lien
 * public/storage est absent ou cassé (hébergement mutualisé, envoi FTP…).
 * Si le lien existe, le serveur web livre le fichier directement et cette route
 * n'est jamais appelée.
 */
class MediaController extends Controller
{
    public function __invoke(string $path)
    {
        $path = ltrim(str_replace('\\', '/', $path), '/');
        abort_if($path === '' || str_contains($path, '..'), 404);

        $disk = Storage::disk('public');
        abort_unless($disk->exists($path), 404);

        return response()->file($disk->path($path), [
            'Cache-Control' => 'public, max-age=31536000, immutable',
        ]);
    }
}
