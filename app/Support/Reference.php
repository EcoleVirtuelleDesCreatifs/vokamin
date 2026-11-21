<?php

namespace App\Support;

class Reference
{
    /** Génère une référence lisible, ex. VK-DEV-2026-4821 */
    public static function make(string $prefix, string $modelClass): string
    {
        do {
            $ref = sprintf('VK-%s-%s-%04d', $prefix, now()->format('Y'), random_int(1000, 9999));
        } while ($modelClass::where('reference', $ref)->exists());

        return $ref;
    }
}
