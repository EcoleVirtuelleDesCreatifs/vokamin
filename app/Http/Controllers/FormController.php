<?php

namespace App\Http\Controllers;

use App\Http\Middleware\TrackVisit;
use App\Mail\SiteMail;
use App\Models\BrochureRequest;
use App\Models\ContactMessage;
use App\Models\JobApplication;
use App\Models\NewsletterSubscriber;
use App\Models\Project;
use App\Models\QuoteRequest;
use App\Models\Service;
use App\Models\SiteEvent;
use App\Support\Reference;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class FormController extends Controller
{
    private const PHONE = ['regex:/^[+0-9 ().-]{8,20}$/'];

    /** Demande de devis (formulaire en 4 étapes, pièces jointes). */
    public function quote(Request $request): JsonResponse
    {
        if ($spam = $this->honeypot($request)) {
            return $spam;
        }
        $cfg = config('vokamin.upload');
        $data = $request->validate([
            'prestation' => ['required', 'string', 'max:60'],
            'nature' => ['required', 'string', 'max:120'],
            'localisation' => ['required', 'string', 'max:160'],
            'surface' => ['nullable', 'integer', 'min:1', 'max:1000000'],
            'delai' => ['nullable', 'string', 'max:60'],
            'budget' => ['nullable', 'string', 'max:60'],
            'description' => ['required', 'string', 'min:20', 'max:5000'],
            'nom' => ['required', 'string', 'max:120'],
            'entreprise' => ['nullable', 'string', 'max:160'],
            'tel' => ['required', ...self::PHONE],
            'whatsapp' => ['nullable', ...self::PHONE],
            'email' => ['required', 'email', 'max:160'],
            'rdv' => ['nullable', 'string', 'max:80'],
            'consent' => ['accepted'],
            'files' => ['nullable', 'array', 'max:'.$cfg['quote_max_files']],
            'files.*' => ['file', 'mimes:'.$cfg['quote_mimes'], 'max:'.$cfg['quote_max_kb']],
        ]);

        $quote = QuoteRequest::create(collect($data)->except(['consent', 'files'])->all() + [
            'reference' => Reference::make('DEV', QuoteRequest::class),
            'ip' => $request->ip(),
        ]);

        foreach ($request->file('files', []) as $file) {
            $quote->files()->create([
                'path' => $file->store('devis/'.$quote->reference, 'local'),
                'original_name' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
            ]);
        }

        $label = Service::where('slug', $quote->prestation)->value('short') ?? ucfirst($quote->prestation);
        $this->notify('Nouvelle demande de devis '.$quote->reference, 'emails.quote-admin', ['quote' => $quote, 'label' => $label]);
        $this->mailTo($quote->email, 'Votre demande de devis '.$quote->reference, 'emails.quote-client', ['quote' => $quote, 'label' => $label]);
        $this->event($request, 'form');

        return response()->json(['ok' => true, 'reference' => $quote->reference, 'firstname' => explode(' ', trim($quote->nom))[0]]);
    }

    public function contact(Request $request): JsonResponse
    {
        if ($spam = $this->honeypot($request)) {
            return $spam;
        }
        $data = $request->validate([
            'nom' => ['required', 'string', 'max:120'],
            'tel' => ['nullable', ...self::PHONE],
            'email' => ['required', 'email', 'max:160'],
            'sujet' => ['required', 'string', 'max:120'],
            'message' => ['required', 'string', 'min:10', 'max:5000'],
            'consent' => ['accepted'],
        ]);
        $msg = ContactMessage::create(collect($data)->except('consent')->all() + [
            'reference' => Reference::make('MSG', ContactMessage::class), 'ip' => $request->ip(),
        ]);
        $this->notify('Nouveau message de contact '.$msg->reference, 'emails.contact-admin', ['msg' => $msg]);
        $this->event($request, 'form');

        return response()->json(['ok' => true, 'reference' => $msg->reference]);
    }

    public function application(Request $request): JsonResponse
    {
        if ($spam = $this->honeypot($request)) {
            return $spam;
        }
        $cfg = config('vokamin.upload');
        $data = $request->validate([
            'nom' => ['required', 'string', 'max:120'],
            'poste' => ['required', 'string', 'max:160'],
            'tel' => ['required', ...self::PHONE],
            'email' => ['required', 'email', 'max:160'],
            'message' => ['nullable', 'string', 'max:5000'],
            'consent' => ['accepted'],
            'cv' => ['nullable', 'file', 'mimes:'.$cfg['cv_mimes'], 'max:'.$cfg['cv_max_kb']],
        ]);
        $app = new JobApplication(collect($data)->except(['consent', 'cv'])->all() + [
            'reference' => Reference::make('CAND', JobApplication::class), 'ip' => $request->ip(),
        ]);
        if ($cv = $request->file('cv')) {
            $app->cv_path = $cv->store('candidatures', 'local');
            $app->cv_name = $cv->getClientOriginalName();
        }
        $app->save();
        $this->notify('Nouvelle candidature '.$app->reference, 'emails.application-admin', ['app' => $app]);
        $this->event($request, 'form');

        return response()->json(['ok' => true, 'reference' => $app->reference]);
    }

    public function brochure(Request $request): JsonResponse
    {
        if ($spam = $this->honeypot($request)) {
            return $spam;
        }
        $data = $request->validate([
            'nom' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:160'],
            'entreprise' => ['nullable', 'string', 'max:160'],
        ]);
        BrochureRequest::create($data);
        $path = setting('brochure_path');
        $this->mailTo($data['email'], "Plaquette VOKAMIN Côte d'Ivoire", 'emails.brochure', [
            'nom' => $data['nom'], 'url' => $path ? media_url($path) : null,
        ]);
        $this->notify('Demande de plaquette : '.$data['nom'], 'emails.generic', ['lines' => [
            'Nom : '.$data['nom'], 'Email : '.$data['email'], 'Entreprise : '.($data['entreprise'] ?? '—'),
        ]]);

        return response()->json(['ok' => true, 'url' => $path ? media_url($path) : null]);
    }

    public function newsletter(Request $request): JsonResponse
    {
        if ($spam = $this->honeypot($request)) {
            return $spam;
        }
        $data = $request->validate(['email' => ['required', 'email', 'max:160']]);
        NewsletterSubscriber::firstOrCreate(['email' => strtolower($data['email'])]);

        return response()->json(['ok' => true]);
    }

    /** Clics WhatsApp / appels depuis le site (statistiques). */
    public function track(Request $request): JsonResponse
    {
        $data = $request->validate([
            'type' => ['required', 'in:whatsapp,call'],
            'path' => ['nullable', 'string', 'max:255'],
        ]);
        $this->event($request, $data['type'], $data['path'] ?? null);

        return response()->json(['ok' => true]);
    }

    /* ------------------------------------------------------------------ */

    private function honeypot(Request $request): ?JsonResponse
    {
        // Un robot remplit le champ caché « website » : on répond « OK » sans rien enregistrer.
        return filled($request->input('website')) ? response()->json(['ok' => true, 'reference' => 'VK-OK']) : null;
    }

    private function event(Request $request, string $type, ?string $path = null): void
    {
        try {
            $path ??= parse_url((string) $request->headers->get('referer'), PHP_URL_PATH) ?: '/';
            $projectId = preg_match('#^/realisations/([\w-]+)#', $path, $m) ? Project::where('slug', $m[1])->value('id') : null;
            SiteEvent::create(['type' => $type, 'path' => $path, 'project_id' => $projectId, 'source' => TrackVisit::source($request)]);
        } catch (Throwable) {
        }
    }

    private function notify(string $subject, string $view, array $data): void
    {
        $this->mailTo(config('vokamin.notify_email'), $subject, $view, $data);
    }

    private function mailTo(?string $to, string $subject, string $view, array $data): void
    {
        if (! $to) {
            return;
        }
        try {
            Mail::to($to)->send(new SiteMail($subject, $view, $data));
        } catch (Throwable $e) {
            // La demande est déjà enregistrée en base : on journalise sans bloquer le visiteur.
            Log::warning('Envoi email impossible : '.$e->getMessage());
        }
    }
}
