<?php

namespace App\Support;

/** Aides pour les données structurées schema.org (JSON-LD). */
class Schema
{
    /**
     * Horaire « 08 h 00 – 18 h 00 » → « Mo-Fr 08:00-18:00 » ; null si fermé ou illisible.
     */
    public static function hours(string $days, ?string $text): ?string
    {
        if (! $text || ! preg_match_all('/(\d{1,2})\s*h\s*(\d{2})?/iu', $text, $m, PREG_SET_ORDER) || count($m) < 2) {
            return null;
        }
        $t = fn ($x) => sprintf('%02d:%02d', (int) $x[1], (int) ($x[2] ?? 0));

        return $days.' '.$t($m[0]).'-'.$t($m[1]);
    }

    /**
     * Fil d'Ariane : [libellé => url|null], l'accueil est ajouté en tête.
     */
    public static function breadcrumb(array $crumbs): string
    {
        $items = [['name' => 'Accueil', 'url' => route('home')]];
        foreach ($crumbs as $label => $url) {
            $items[] = ['name' => strip_tags(html_entity_decode((string) $label)), 'url' => $url ?: url()->current()];
        }

        return self::json([
            '@context' => 'https://schema.org', '@type' => 'BreadcrumbList',
            'itemListElement' => array_map(fn ($it, $i) => ['@type' => 'ListItem', 'position' => $i + 1, 'name' => $it['name'], 'item' => $it['url']], $items, array_keys($items)),
        ]);
    }

    public static function json(array $data): string
    {
        return '<script type="application/ld+json">'.json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG).'</script>';
    }
}
