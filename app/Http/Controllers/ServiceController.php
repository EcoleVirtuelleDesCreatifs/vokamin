<?php

namespace App\Http\Controllers;

use App\Models\Service;

class ServiceController extends Controller
{
    public function index()
    {
        return view('services.index', ['services' => Service::published()->get()]);
    }

    public function show(Service $service)
    {
        abort_unless($service->is_published, 404);

        return view('services.show', [
            'service' => $service,
            'services' => Service::published()->get(['slug', 'short']),
            'projects' => $service->projects()->with('images')->take(6)->get(),
        ]);
    }
}
