<?php

namespace App\Support;

use DOMDocument;
use DOMElement;
use DOMNode;

/**
 * Nettoie le HTML produit par l'éditeur de l'admin : seules les balises de mise
 * en forme d'article sont conservées, sans style, script ni attribut libre.
 */
class HtmlSanitizer
{
    /** Balise autorisée => attributs conservés. */
    private const ALLOWED = [
        'p' => [], 'br' => [], 'h2' => [], 'h3' => [], 'strong' => [], 'em' => [], 'u' => [],
        'ul' => [], 'ol' => [], 'li' => [], 'blockquote' => [], 'a' => ['href'],
        'figure' => [], 'figcaption' => [], 'img' => ['src', 'alt'],
    ];

    /** Balises renommées (gras/italique du navigateur, titres trop grands…). */
    private const RENAME = ['b' => 'strong', 'i' => 'em', 'h1' => 'h2', 'h4' => 'h3', 'h5' => 'h3', 'h6' => 'h3', 'div' => 'p'];

    /** Balises supprimées avec leur contenu. */
    private const DROP = ['script', 'style', 'iframe', 'object', 'embed', 'form', 'input', 'button', 'select', 'textarea', 'svg', 'math', 'template', 'head', 'meta', 'link', 'title'];

    public static function clean(?string $html): string
    {
        $html = trim((string) $html);
        if ($html === '') {
            return '';
        }
        $doc = new DOMDocument('1.0', 'UTF-8');
        $prev = libxml_use_internal_errors(true);
        $doc->loadHTML('<?xml encoding="UTF-8"><body><div id="__root">'.$html.'</div></body>', LIBXML_NONET);
        libxml_clear_errors();
        libxml_use_internal_errors($prev);

        $root = $doc->getElementById('__root');
        if (! $root) {
            return '';
        }
        self::walk($root);

        $out = '';
        foreach ($root->childNodes as $child) {
            $out .= $doc->saveHTML($child);
        }
        // Paragraphes vides laissés par l'éditeur
        $out = preg_replace('#<p>(\s|&nbsp;|<br>)*</p>#u', '', $out);

        return trim($out);
    }

    private static function walk(DOMNode $node): void
    {
        foreach (iterator_to_array($node->childNodes) as $child) {
            if ($child->nodeType === XML_COMMENT_NODE) {
                $node->removeChild($child);
                continue;
            }
            if (! $child instanceof DOMElement) {
                continue;
            }
            $tag = strtolower($child->tagName);
            if (in_array($tag, self::DROP, true)) {
                $node->removeChild($child);
                continue;
            }
            if (isset(self::RENAME[$tag])) {
                $child = self::rename($child, self::RENAME[$tag]);
                $tag = self::RENAME[$tag];
            }
            self::walk($child);
            if (! array_key_exists($tag, self::ALLOWED)) {
                // Balise inconnue (span, font…) : on garde le texte, pas la balise
                while ($child->firstChild) {
                    $node->insertBefore($child->firstChild, $child);
                }
                $node->removeChild($child);
                continue;
            }
            foreach (iterator_to_array($child->attributes) as $attr) {
                if (! in_array(strtolower($attr->name), self::ALLOWED[$tag], true)) {
                    $child->removeAttribute($attr->name);
                }
            }
            if ($tag === 'a') {
                $href = trim($child->getAttribute('href'));
                if (! preg_match('#^(https?://|mailto:|tel:|/|\#)#i', $href)) {
                    $child->removeAttribute('href');
                } elseif (preg_match('#^https?://#i', $href) && ! str_starts_with($href, url('/'))) {
                    $child->setAttribute('target', '_blank');
                    $child->setAttribute('rel', 'noopener');
                }
            }
            if ($tag === 'img') {
                $src = trim($child->getAttribute('src'));
                if (! preg_match('#^(https?://|/)#i', $src)) {
                    $node->removeChild($child);
                    continue;
                }
                $child->setAttribute('loading', 'lazy');
                $child->setAttribute('alt', trim($child->getAttribute('alt')));
            }
        }
    }

    private static function rename(DOMElement $el, string $tag): DOMElement
    {
        $new = $el->ownerDocument->createElement($tag);
        while ($el->firstChild) {
            $new->appendChild($el->firstChild);
        }
        $el->parentNode->replaceChild($new, $el);

        return $new;
    }
}
