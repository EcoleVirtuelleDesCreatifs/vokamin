<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\Settings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class SettingController extends Controller
{
    public function index(Settings $settings)
    {
        $all = $settings->all();

        return $all + ['brochure_url' => ! empty($all['brochure_path']) ? media_url($all['brochure_path']) : null];
    }

    public function update(Request $request, Settings $settings)
    {
        $keys = array_keys(config('vokamin.defaults'));
        $request->validate([
            'email' => ['nullable', 'email'],
            'facebook' => ['nullable', 'url'], 'linkedin' => ['nullable', 'url'], 'instagram' => ['nullable', 'url'], 'youtube' => ['nullable', 'url'],
            'brochure_file' => ['nullable', 'file', 'mimes:pdf', 'max:20480'],
        ]);
        $values = collect($request->only($keys))->except('brochure_path')->map(fn ($v) => is_string($v) ? trim($v) : $v)->all();

        if ($f = $request->file('brochure_file')) {
            $old = $settings->get('brochure_path');
            if ($old) {
                Storage::disk('public')->delete($old);
            }
            $values['brochure_path'] = $f->storeAs('documents', 'plaquette-vokamin.pdf', 'public');
        }
        $settings->set($values);

        return $this->index($settings);
    }

    public function account(Request $request)
    {
        $data = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(10)->letters()->numbers()],
        ]);
        $request->user()->update(['password' => $data['password']]);

        return response()->json(['ok' => true]);
    }
}
