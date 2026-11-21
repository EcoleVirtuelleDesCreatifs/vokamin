<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClientReference;
use Illuminate\Http\Request;

class ReferenceController extends Controller
{
    use Uploads;

    public function index()
    {
        return ClientReference::orderBy('position')->get()->map(fn ($r) => $r->toArray() + ['logo_url' => $r->logo ? media_url($r->logo) : null]);
    }

    public function store(Request $request)
    {
        return $this->save($request, new ClientReference(['position' => (int) ClientReference::max('position') + 1]));
    }

    public function update(Request $request, ClientReference $reference)
    {
        return $this->save($request, $reference);
    }

    public function destroy(ClientReference $reference)
    {
        $this->removeFile($reference->logo);
        $reference->delete();

        return response()->noContent();
    }

    private function save(Request $request, ClientReference $ref)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:160'],
            'sector' => ['required', 'string', 'max:80'],
            'prestation' => ['required', 'string', 'max:300'],
            'project_id' => ['nullable', 'integer', 'exists:projects,id'],
            'logo_authorized' => ['nullable', 'boolean'],
            'is_published' => ['nullable', 'boolean'],
            'position' => ['nullable', 'integer'],
            'logo_file' => ['nullable', ...$this->imageRule()],
        ]);
        $ref->fill(collect($data)->except('logo_file')->all());
        $ref->logo_authorized = $request->boolean('logo_authorized');
        $ref->is_published = $request->boolean('is_published', true);
        if ($f = $request->file('logo_file')) {
            $this->removeFile($ref->logo);
            $ref->logo = $this->storeImage($f, 'logos');
        }
        $ref->save();

        return $ref->toArray() + ['logo_url' => $ref->logo ? media_url($ref->logo) : null];
    }
}
