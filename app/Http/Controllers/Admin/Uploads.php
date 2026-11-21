<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\UploadedFile;

trait Uploads
{
    /**
     * Enregistre une image sur le disque public, redimensionnée à 1600 px max et
     * recompressée (GD) pour garder des pages légères.
     */
    protected function storeImage(UploadedFile $file, string $dir): string
    {
        $path = $file->store($dir, 'public');
        $full = storage_path('app/public/'.$path);
        try {
            [$w, $h, $type] = getimagesize($full);
            $max = 1600;
            if (function_exists('imagecreatefromjpeg') && max($w, $h) > $max && in_array($type, [IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_WEBP])) {
                $src = match ($type) {
                    IMAGETYPE_JPEG => imagecreatefromjpeg($full),
                    IMAGETYPE_PNG => imagecreatefrompng($full),
                    IMAGETYPE_WEBP => imagecreatefromwebp($full),
                };
                $ratio = $max / max($w, $h);
                $dst = imagescale($src, (int) round($w * $ratio), (int) round($h * $ratio));
                match ($type) {
                    IMAGETYPE_JPEG => imagejpeg($dst, $full, 80),
                    IMAGETYPE_PNG => imagepng($dst, $full, 8),
                    IMAGETYPE_WEBP => imagewebp($dst, $full, 80),
                };
                imagedestroy($src);
                imagedestroy($dst);
            }
        } catch (\Throwable) {
            // Image conservée telle quelle si le traitement échoue.
        }

        return $path;
    }

    protected function removeFile(?string $path): void
    {
        if ($path && ! str_starts_with($path, 'images/')) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($path);
        }
    }

    protected function imageRule(): array
    {
        return ['image', 'mimes:'.config('vokamin.upload.image_mimes'), 'max:'.config('vokamin.upload.image_max_kb')];
    }

    /** Champs JSON envoyés en FormData sous forme de chaîne. */
    protected function jsonField($value): array
    {
        if (is_array($value)) {
            return $value;
        }
        $decoded = json_decode((string) $value, true);

        return is_array($decoded) ? $decoded : [];
    }
}
