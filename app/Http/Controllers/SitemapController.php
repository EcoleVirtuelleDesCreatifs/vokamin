<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Project;
use App\Models\Service;

class SitemapController extends Controller
{
    public function __invoke()
    {
        $urls = collect(['home', 'company', 'services.index', 'projects.index', 'references', 'posts.index', 'quote', 'contact', 'jobs', 'legal', 'privacy', 'cookies'])
            ->map(fn ($r) => ['loc' => route($r), 'lastmod' => null]);
        foreach (Service::published()->get() as $s) {
            $urls->push(['loc' => route('services.show', $s), 'lastmod' => $s->updated_at]);
        }
        foreach (Project::published()->get() as $p) {
            $urls->push(['loc' => route('projects.show', $p), 'lastmod' => $p->updated_at]);
        }
        foreach (Post::published()->get() as $p) {
            $urls->push(['loc' => route('posts.show', $p), 'lastmod' => $p->updated_at]);
        }

        return response()->view('sitemap', ['urls' => $urls])->header('Content-Type', 'application/xml');
    }
}
