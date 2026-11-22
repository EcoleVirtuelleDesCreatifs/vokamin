<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;

/**
 * Sert les fichiers téléversés depuis l'admin (disque « public », storage/app/public)
 * via l'adresse /media/... : aucune dépendance au lien symbolique public/storage,
 * souvent absent, copié en dossier par FTP ou refusé par Apache en mutualisé.
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
