<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\JobApplication;
use App\Models\Project;
use App\Models\QuoteRequest;
use App\Models\SiteEvent;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function app()
    {
        return view('admin.app');
    }

    public function stats()
    {
        $since = now()->subDays(30);
        $prev = now()->subDays(60);
        $count = fn ($type, $from, $to = null) => SiteEvent::where('type', $type)->where('created_at', '>=', $from)
            ->when($to, fn ($q) => $q->where('created_at', '<', $to))->count();
        $visitors = fn ($from, $to = null) => SiteEvent::where('type', 'view')->where('created_at', '>=', $from)
            ->when($to, fn ($q) => $q->where('created_at', '<', $to))->distinct('visitor')->count('visitor');

        // Visiteurs uniques par semaine (12 semaines)
        $weeks = [];
        for ($i = 11; $i >= 0; $i--) {
            $start = Carbon::now()->startOfWeek()->subWeeks($i);
            $weeks[] = [
                'label' => $start->format('d/m'),
                'value' => SiteEvent::where('type', 'view')->whereBetween('created_at', [$start, (clone $start)->endOfWeek()])->distinct('visitor')->count('visitor'),
            ];
        }

        $sources = SiteEvent::where('type', 'view')->where('created_at', '>=', $since)
            ->select('source', DB::raw('count(*) as total'))->groupBy('source')->orderByDesc('total')->limit(6)->get();
        $totalSrc = max(1, $sources->sum('total'));

        $pages = SiteEvent::where('type', 'view')->where('created_at', '>=', $since)
            ->select('path', DB::raw('count(*) as total'))->groupBy('path')->orderByDesc('total')->limit(8)->get();

        return response()->json([
            'kpis' => [
                ['label' => 'Visiteurs (30 j)', 'value' => $visitors($since), 'prev' => $visitors($prev, $since)],
                ['label' => 'Pages vues (30 j)', 'value' => $count('view', $since), 'prev' => $count('view', $prev, $since)],
                ['label' => 'Formulaires envoyés', 'value' => $count('form', $since), 'prev' => $count('form', $prev, $since)],
                ['label' => 'Clics WhatsApp', 'value' => $count('whatsapp', $since), 'prev' => $count('whatsapp', $prev, $since)],
                ['label' => 'Appels depuis le site', 'value' => $count('call', $since), 'prev' => $count('call', $prev, $since)],
            ],
            'weeks' => $weeks,
            'sources' => $sources->map(fn ($s) => ['label' => $s->source ?: 'Inconnu', 'pct' => round($s->total * 100 / $totalSrc)]),
            'pages' => $pages,
            'topProjects' => Project::orderByDesc('views')->take(5)->get(['id', 'name', 'location', 'cover', 'views'])
                ->map(fn ($p) => $p->only(['id', 'name', 'location', 'views']) + ['cover_url' => media_url($p->cover)]),
            'pending' => [
                'quotes' => QuoteRequest::where('status', 'new')->count(),
                'messages' => ContactMessage::where('status', 'new')->count(),
                'applications' => JobApplication::where('status', 'new')->count(),
            ],
            'latest' => QuoteRequest::latest()->take(5)->get(['id', 'reference', 'nom', 'prestation', 'localisation', 'status', 'created_at']),
        ]);
    }
}
