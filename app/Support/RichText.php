<?php

namespace App\Support;

/**
 * Contenu des articles. Les articles rédigés avec l'éditeur visuel sont en HTML
 * (nettoyé par HtmlSanitizer) ; les plus anciens utilisent la syntaxe simple : des articles saisis dans l'administration :
 *   - paragraphe séparé par une ligne vide
 *   - "## Titre" pour un intertitre
 *   - "> Texte" pour une citation
 *   - "- élément" pour une liste
 */
class RichText
{
    public static function render(?string $text): string
    {
        if (self::isHtml($text)) {
            return HtmlSanitizer::clean($text);
        }
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

    public static function isHtml(?string $text): bool
    {
        return (bool) preg_match('#^\s*<(p|h[1-6]|ul|ol|blockquote|figure|div|br|strong|em|b|i|a|img)[\s>/]#i', (string) $text);
    }

    /** Texte brut (temps de lecture, descriptions). */
    public static function plain(?string $text): string
    {
        return trim(preg_replace('/\s+/u', ' ', html_entity_decode(strip_tags(str_replace(['</p>', '<br>', '</li>', '</h2>', '</h3>'], ' ', self::render($text))), ENT_QUOTES | ENT_HTML5, 'UTF-8')));
    }
}
