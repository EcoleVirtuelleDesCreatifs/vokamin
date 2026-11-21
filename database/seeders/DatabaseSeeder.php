<?php

namespace Database\Seeders;

use App\Models\ClientReference;
use App\Models\JobOffer;
use App\Models\Post;
use App\Models\Project;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $data = fn (string $f) => require __DIR__.'/data/'.$f.'.php';

        User::updateOrCreate(
            ['email' => config('vokamin.admin.email')],
            ['name' => config('vokamin.admin.name'), 'password' => config('vokamin.admin.password')]
        );

        foreach ($data('services') as $s) {
            Service::updateOrCreate(['slug' => $s['slug']], $s);
        }

        foreach ($data('projects') as $p) {
            $images = $p['images'];
            unset($p['images']);
            $project = Project::updateOrCreate(['slug' => $p['slug']], $p);
            $project->images()->delete();
            foreach ($images as $i => $img) {
                $project->images()->create($img + ['position' => $i + 1]);
            }
        }

        foreach ($data('posts') as $p) {
            Post::updateOrCreate(['slug' => $p['slug']], $p);
        }

        foreach ($data('references') as $r) {
            $slug = $r['project_slug'];
            unset($r['project_slug']);
            $r['project_id'] = $slug ? Project::where('slug', $slug)->value('id') : null;
            ClientReference::updateOrCreate(['name' => $r['name']], $r);
        }

        foreach ($data('jobs') as $j) {
            JobOffer::updateOrCreate(['title' => $j['title']], $j);
        }
    }
}
