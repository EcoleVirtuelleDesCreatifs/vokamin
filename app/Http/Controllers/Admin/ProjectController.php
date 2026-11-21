<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectImage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ProjectController extends Controller
{
    use Uploads;

    public function index()
    {
        return Project::with('images')->orderBy('position')->orderByDesc('id')->get()
            ->map(fn ($p) => $p->toArray() + ['cover_url' => $p->cover_url]);
    }

    public function store(Request $request)
    {
        $project = new Project();
        $this->fill($request, $project);
        $project->position = (int) Project::max('position') + 1;
        $project->save();
        $this->saveImages($request, $project);

        return $this->payload($project);
    }

    public function update(Request $request, Project $project)
    {
        $this->fill($request, $project);
        $project->save();
        $this->saveImages($request, $project);

        return $this->payload($project);
    }

    public function destroy(Project $project)
    {
        foreach ($project->images as $img) {
            $this->removeFile($img->path);
        }
        $this->removeFile($project->cover);
        $project->delete();

        return response()->noContent();
    }

    public function addImages(Request $request, Project $project)
    {
        $request->validate(['images' => ['required', 'array', 'max:20'], 'images.*' => $this->imageRule()]);
        $this->saveImages($request, $project);

        return $this->payload($project);
    }

    public function updateImage(Request $request, ProjectImage $image)
    {
        $data = $request->validate(['caption' => ['nullable', 'string', 'max:160'], 'position' => ['nullable', 'integer']]);
        $image->update(array_filter($data, fn ($v) => $v !== null));

        return $image;
    }

    public function deleteImage(ProjectImage $image)
    {
        $project = $image->project;
        if ($project->cover === $image->path) {
            $project->update(['cover' => null]);
        }
        $this->removeFile($image->path);
        $image->delete();

        return response()->noContent();
    }

    private function fill(Request $request, Project $project): void
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:160'],
            'slug' => ['nullable', 'string', 'max:160', Rule::unique('projects', 'slug')->ignore($project->id)],
            'location' => ['required', 'string', 'max:160'],
            'category' => ['required', 'string', 'max:80'],
            'tags' => ['nullable', 'string', 'max:160'],
            'description' => ['required', 'string', 'max:5000'],
            'programme' => ['nullable', 'string', 'max:200'],
            'year' => ['nullable', 'string', 'max:20'],
            'status' => ['nullable', 'string', 'max:60'],
            'service_slugs' => ['nullable'],
            'video_url' => ['nullable', 'url', 'max:255'],
            'testimonial' => ['nullable', 'string', 'max:2000'],
            'testimonial_author' => ['nullable', 'string', 'max:160'],
            'ba_before' => ['nullable', 'string', 'max:255'],
            'ba_after' => ['nullable', 'string', 'max:255'],
            'ba_label_before' => ['nullable', 'string', 'max:60'],
            'ba_label_after' => ['nullable', 'string', 'max:60'],
            'is_featured' => ['nullable', 'boolean'],
            'is_published' => ['nullable', 'boolean'],
            'position' => ['nullable', 'integer'],
            'cover_file' => ['nullable', ...$this->imageRule()],
            'cover_from_gallery' => ['nullable', 'string', 'max:255'],
            'images' => ['nullable', 'array', 'max:20'],
            'images.*' => $this->imageRule(),
        ]);

        $project->fill(collect($data)->except(['cover_file', 'cover_from_gallery', 'images', 'service_slugs', 'slug'])->all());
        $project->slug = Str::slug($data['slug'] ?? '') ?: ($project->slug ?: $this->uniqueSlug($data['name']));
        $project->service_slugs = $this->jsonField($request->input('service_slugs'));
        $project->is_featured = $request->boolean('is_featured');
        $project->is_published = $request->boolean('is_published', true);

        if ($file = $request->file('cover_file')) {
            $this->removeFile($project->cover);
            $project->cover = $this->storeImage($file, 'projets');
        } elseif ($c = $request->input('cover_from_gallery')) {
            $project->cover = $c;
        }
    }

    private function saveImages(Request $request, Project $project): void
    {
        $pos = (int) $project->images()->max('position');
        foreach ($request->file('images', []) as $file) {
            $path = $this->storeImage($file, 'projets/'.$project->slug);
            $project->images()->create(['path' => $path, 'caption' => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME), 'position' => ++$pos]);
            if (! $project->cover) {
                $project->update(['cover' => $path]);
            }
        }
    }

    private function uniqueSlug(string $name): string
    {
        $base = Str::slug($name) ?: 'projet';
        $slug = $base;
        $i = 2;
        while (Project::where('slug', $slug)->exists()) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }

    private function payload(Project $project): array
    {
        $project->load('images');

        return $project->toArray() + ['cover_url' => $project->cover_url];
    }
}
