<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    use Uploads;

    public function index()
    {
        return Service::orderBy('position')->get()->map(fn ($s) => $s->toArray() + [
            'image_url' => media_url($s->image), 'image2_url' => media_url($s->image2), 'hero_url' => media_url($s->hero_image),
        ]);
    }

    public function update(Request $request, Service $service)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:160'],
            'short' => ['required', 'string', 'max:80'],
            'icon' => ['nullable', 'string', 'max:60'],
            'teaser' => ['required', 'string', 'max:400'],
            'intro' => ['required', 'string', 'max:3000'],
            'intro2' => ['nullable', 'string', 'max:3000'],
            'prestations' => ['nullable'], 'beneficiaires' => ['nullable'], 'methode' => ['nullable'], 'avantages' => ['nullable'],
            'is_published' => ['nullable', 'boolean'],
            'position' => ['nullable', 'integer'],
            'image_file' => ['nullable', ...$this->imageRule()],
            'image2_file' => ['nullable', ...$this->imageRule()],
            'hero_file' => ['nullable', ...$this->imageRule()],
        ]);

        $service->fill(collect($data)->only(['name', 'short', 'icon', 'teaser', 'intro', 'position'])->filter(fn ($v) => $v !== null)->all());
        $service->intro2 = $data['intro2'] ?? null;
        foreach (['prestations', 'beneficiaires', 'methode', 'avantages'] as $k) {
            if ($request->has($k)) {
                $service->$k = $this->jsonField($request->input($k));
            }
        }
        $service->is_published = $request->boolean('is_published', true);
        foreach (['image_file' => 'image', 'image2_file' => 'image2', 'hero_file' => 'hero_image'] as $field => $col) {
            if ($f = $request->file($field)) {
                $this->removeFile($service->$col);
                $service->$col = $this->storeImage($f, 'services');
            }
        }
        $service->save();

        return $service->toArray() + ['image_url' => media_url($service->image), 'image2_url' => media_url($service->image2), 'hero_url' => media_url($service->hero_image)];
    }
}
