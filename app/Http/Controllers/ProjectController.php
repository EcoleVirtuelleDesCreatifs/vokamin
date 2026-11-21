<?php

namespace App\Http\Controllers;

use App\Models\Project;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::published()->with('images')->get();
        $filters = collect([
            'construction' => 'Construction', 'promotion' => 'Promotion immobilière', 'maintenance' => 'Building Maintenance',
            'negoce' => 'Négoce', 'design' => 'Design & enseignes', 'renovation' => 'Rénovation & décoration',
        ])->filter(fn ($label, $tag) => $projects->contains(fn ($p) => in_array($tag, $p->tag_list)));

        return view('projects.index', compact('projects', 'filters'));
    }

    public function show(Project $project)
    {
        abort_unless($project->is_published, 404);
        $project->increment('views');
        $project->load('images');

        $all = Project::published()->get(['id', 'slug', 'name']);
        $i = $all->search(fn ($p) => $p->id === $project->id);

        return view('projects.show', [
            'project' => $project,
            'services' => $project->services(),
            'prev' => $all[($i - 1 + $all->count()) % $all->count()],
            'next' => $all[($i + 1) % $all->count()],
        ]);
    }
}
