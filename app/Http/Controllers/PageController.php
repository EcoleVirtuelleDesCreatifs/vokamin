<?php

namespace App\Http\Controllers;

use App\Models\ClientReference;
use App\Models\JobOffer;
use App\Models\Post;
use App\Models\Project;
use App\Models\Service;

class PageController extends Controller
{
    public function home()
    {
        $featured = Project::published()->where('is_featured', true)->with('images')->get();

        return view('pages.home', [
            'services' => Service::published()->get(),
            'projects' => $featured->isNotEmpty() ? $featured : Project::published()->with('images')->take(6)->get(),
            'slides' => $featured->take(4),
            'posts' => Post::published()->with('images')->take(3)->get(),
            'references' => ClientReference::where('is_published', true)->orderBy('position')->get(),
            'stats' => [
                'services' => Service::published()->count(),
                'references' => ClientReference::where('is_published', true)->where('name', '!=', 'Clients particuliers')->count(),
            ],
        ]);
    }

    public function company()
    {
        return view('pages.company', ['servicesCount' => Service::published()->count()]);
    }

    public function references()
    {
        return view('pages.references', [
            'references' => ClientReference::where('is_published', true)->with('project')->orderBy('position')->get(),
        ]);
    }

    public function quote()
    {
        return view('pages.quote', ['services' => Service::published()->get(['slug', 'short', 'icon'])]);
    }

    public function contact()
    {
        return view('pages.contact');
    }

    public function jobs()
    {
        return view('pages.jobs', ['jobs' => JobOffer::where('is_published', true)->orderBy('position')->get()]);
    }

    public function legal()
    {
        return view('legal.mentions');
    }

    public function privacy()
    {
        return view('legal.privacy');
    }

    public function cookies()
    {
        return view('legal.cookies');
    }

    public function sitemap()
    {
        return view('pages.sitemap', [
            'services' => Service::published()->get(['slug', 'short']),
            'projects' => Project::published()->get(['slug', 'name']),
            'posts' => Post::published()->get(['slug', 'title']),
        ]);
    }
}
