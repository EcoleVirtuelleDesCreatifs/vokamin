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
            // Une photo de 4000 × 3000 px occupe ~50 Mo en mémoire une fois décodée :
            // on relève la limite si l'hébergeur le permet, sinon on garde l'original.
            $needed = (int) ($w * $h * 5 * 1.8) + 32 * 1024 * 1024;
            if ($this->memoryLimit() < $needed) {
                @ini_set('memory_limit', (string) max($needed, 256 * 1024 * 1024));
            }
            $canResize = $this->memoryLimit() >= $needed
                && ($type !== IMAGETYPE_WEBP || function_exists('imagecreatefromwebp'));
            if ($canResize && function_exists('imagecreatefromjpeg') && max($w, $h) > $max && in_array($type, [IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_WEBP])) {
                $src = match ($type) {
                    IMAGETYPE_JPEG => imagecreatefromjpeg($full),
                    IMAGETYPE_PNG => imagecreatefrompng($full),
                    IMAGETYPE_WEBP => imagecreatefromwebp($full),
                };
                if ($type !== IMAGETYPE_JPEG) {
                    imagealphablending($src, false);
                    imagesavealpha($src, true);
                }
                $ratio = $max / max($w, $h);
                $dst = imagescale($src, (int) round($w * $ratio), (int) round($h * $ratio));
                if ($type !== IMAGETYPE_JPEG) {
                    imagealphablending($dst, false);
                    imagesavealpha($dst, true);
                }
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

    /** Limite mémoire PHP en octets (PHP_INT_MAX si illimitée). */
    private function memoryLimit(): int
    {
        $v = trim((string) ini_get('memory_limit'));
        if ($v === '' || $v === '-1') {
            return PHP_INT_MAX;
        }
        $n = (int) $v;

        return match (strtolower(substr($v, -1))) {
            'g' => $n * 1024 ** 3,
            'm' => $n * 1024 ** 2,
            'k' => $n * 1024,
            default => $n,
        };
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
