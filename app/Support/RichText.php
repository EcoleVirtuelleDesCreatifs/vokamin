<?php

namespace App\Support;

/**
 * Mise en forme simple des articles saisis dans l'administration :
 *   - paragraphe séparé par une ligne vide
 *   - "## Titre" pour un intertitre
 *   - "> Texte" pour une citation
 *   - "- élément" pour une liste
 */
class RichText
{
    public static function render(?string $text): string
    {
        $blocks = preg_split("/\R{2,}/", trim((string) $text));
        $html = '';
        foreach ($blocks as $block) {
            $block = trim($block);
            if ($block === '') {
                continue;
            }
            if (str_starts_with($block, '## ')) {
                $html .= '<h2>'.e(substr($block, 3)).'</h2>';
            } elseif (str_starts_with($block, '> ')) {
                $html .= '<blockquote>'.e(substr($block, 2)).'</blockquote>';
            } elseif (str_starts_with($block, '- ')) {
                $items = array_map(fn ($l) => '<li>'.e(ltrim($l, '- ')).'</li>', preg_split("/\R/", $block));
                $html .= '<ul>'.implode('', $items).'</ul>';
            } else {
                $html .= '<p>'.nl2br(e($block)).'</p>';
            }
        }

        return $html;
    }
}
