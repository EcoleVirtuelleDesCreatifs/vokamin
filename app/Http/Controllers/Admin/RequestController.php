<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BrochureRequest;
use App\Models\ContactMessage;
use App\Models\JobApplication;
use App\Models\NewsletterSubscriber;
use App\Models\QuoteRequest;
use App\Models\QuoteRequestFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RequestController extends Controller
{
    private const MODELS = ['quote' => QuoteRequest::class, 'contact' => ContactMessage::class, 'application' => JobApplication::class];

    public function index(Request $request)
    {
        $type = $request->query('type', 'quote');
        abort_unless(array_key_exists($type, self::MODELS) || in_array($type, ['brochure', 'newsletter']), 404);

        if ($type === 'brochure') {
            return BrochureRequest::latest()->paginate(30);
        }
        if ($type === 'newsletter') {
            return NewsletterSubscriber::latest()->paginate(50);
        }

        $q = self::MODELS[$type]::query()->latest();
        if ($type === 'quote') {
            $q->with('files');
        }
        if ($s = $request->query('q')) {
            $q->where(fn ($w) => $w->where('reference', 'like', "%$s%")->orWhere('nom', 'like', "%$s%")->orWhere('email', 'like', "%$s%"));
        }
        if ($st = $request->query('status')) {
            $q->where('status', $st);
        }

        return $q->paginate(20);
    }

    public function update(Request $request, string $type, int $id)
    {
        $model = self::MODELS[$type] ?? abort(404);
        $item = $model::findOrFail($id);
        $data = $request->validate([
            'status' => ['sometimes', 'in:new,progress,done'],
            'notes' => ['sometimes', 'nullable', 'string', 'max:5000'],
        ]);
        if ($type !== 'quote') {
            unset($data['notes']);
        }
        $item->update($data);

        return $item;
    }

    public function destroy(string $type, int $id)
    {
        $model = self::MODELS[$type] ?? abort(404);
        $item = $model::findOrFail($id);
        if ($item instanceof QuoteRequest) {
            Storage::disk('local')->deleteDirectory('devis/'.$item->reference);
        }
        if ($item instanceof JobApplication && $item->cv_path) {
            Storage::disk('local')->delete($item->cv_path);
        }
        $item->delete();

        return response()->noContent();
    }

    public function downloadQuoteFile(QuoteRequestFile $file)
    {
        abort_unless(Storage::disk('local')->exists($file->path), 404);

        return Storage::disk('local')->download($file->path, $file->original_name);
    }

    public function downloadCv(JobApplication $application)
    {
        abort_unless($application->cv_path && Storage::disk('local')->exists($application->cv_path), 404);

        return Storage::disk('local')->download($application->cv_path, $application->cv_name);
    }
}
