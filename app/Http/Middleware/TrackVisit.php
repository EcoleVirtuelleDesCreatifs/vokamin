<?php

namespace App\Http\Middleware;

use App\Models\SiteEvent;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/** Mesure d'audience interne : pages vues et provenance, sans cookie ni donnée personnelle. */
class TrackVisit
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($request->isMethod('GET') && $response->getStatusCode() === 200 && ! $this->isBot($request)) {
            try {
                SiteEvent::create([
                    'type' => 'view',
                    'path' => '/'.ltrim($request->path(), '/'),
                    'source' => self::source($request),
                    'visitor' => hash('sha256', $request->ip().'|'.$request->userAgent().'|'.now()->toDateString()),
                ]);
            } catch (Throwable) {
                // La mesure d'audience ne doit jamais bloquer l'affichage.
            }
        }

        return $response;
    }

    public static function source(Request $request): string
    {
        $ref = (string) $request->headers->get('referer');
        $host = $ref ? (parse_url($ref, PHP_URL_HOST) ?: '') : '';
        if ($host === '' || $host === $request->getHost()) {
            return 'Accès direct';
        }
        foreach (['google' => 'Google', 'bing' => 'Bing', 'facebook' => 'Facebook', 'fb.' => 'Facebook', 'instagram' => 'Instagram',
                  'linkedin' => 'LinkedIn', 'whatsapp' => 'WhatsApp', 'wa.me' => 'WhatsApp', 't.co' => 'X / Twitter', 'youtube' => 'YouTube'] as $k => $label) {
            if (str_contains($host, $k)) {
                return $label;
            }
        }

        return substr($host, 0, 60);
    }

    protected function isBot(Request $request): bool
    {
        return (bool) preg_match('/bot|crawl|spider|slurp|facebookexternalhit|preview|lighthouse/i', (string) $request->userAgent());
    }
}
