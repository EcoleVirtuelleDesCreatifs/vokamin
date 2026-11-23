<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\FormController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SitemapController;
use App\Http\Middleware\TrackVisit;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Site public
|--------------------------------------------------------------------------
*/
Route::middleware(TrackVisit::class)->group(function () {
    Route::get('/', [PageController::class, 'home'])->name('home');
    Route::get('/entreprise', [PageController::class, 'company'])->name('company');
    Route::get('/services', [ServiceController::class, 'index'])->name('services.index');
    Route::get('/services/{service}', [ServiceController::class, 'show'])->name('services.show');
    Route::get('/realisations', [ProjectController::class, 'index'])->name('projects.index');
    Route::get('/realisations/{project}', [ProjectController::class, 'show'])->name('projects.show');
    Route::get('/references', [PageController::class, 'references'])->name('references');
    Route::get('/actualites', [PostController::class, 'index'])->name('posts.index');
    Route::get('/actualites/{post}', [PostController::class, 'show'])->name('posts.show');
    Route::get('/devis', [PageController::class, 'quote'])->name('quote');
    Route::get('/contact', [PageController::class, 'contact'])->name('contact');
    Route::get('/recrutement', [PageController::class, 'jobs'])->name('jobs');
    Route::get('/mentions-legales', [PageController::class, 'legal'])->name('legal');
    Route::get('/confidentialite', [PageController::class, 'privacy'])->name('privacy');
    Route::get('/cookies', [PageController::class, 'cookies'])->name('cookies');
    Route::get('/plan-du-site', [PageController::class, 'sitemap'])->name('sitemap.page');
});

Route::get('/sitemap.xml', SitemapController::class)->name('sitemap.xml');
// robots.txt généré : l'adresse du sitemap doit être absolue (domaine de la requête)
Route::get('/robots.txt', fn () => response("User-agent: *\nDisallow: /admin\n\nSitemap: ".url('/sitemap.xml')."\n", 200, ['Content-Type' => 'text/plain; charset=UTF-8']))->name('robots');

// Images téléversées depuis l'admin, livrées par Laravel : ne dépend pas du lien
// symbolique public/storage (absent, cassé ou refusé par Apache en mutualisé).
Route::get('/media/{path}', MediaController::class)->where('path', '.*')->name('media');
// Anciennes adresses /storage/... (liens déjà partagés) : même traitement si le lien manque
Route::get('/storage/{path}', MediaController::class)->where('path', '.*')->name('media.legacy');

// Formulaires (anti-spam : limitation de fréquence + champ piège)
Route::middleware('throttle:6,1')->group(function () {
    Route::post('/devis', [FormController::class, 'quote'])->name('forms.quote');
    Route::post('/contact', [FormController::class, 'contact'])->name('forms.contact');
    Route::post('/recrutement', [FormController::class, 'application'])->name('forms.application');
    Route::post('/plaquette', [FormController::class, 'brochure'])->name('forms.brochure');
    Route::post('/newsletter', [FormController::class, 'newsletter'])->name('forms.newsletter');
});
Route::post('/track', [FormController::class, 'track'])->middleware('throttle:60,1')->name('track');

/*
|--------------------------------------------------------------------------
| Administration
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/login', [LoginController::class, 'show'])->name('login');
        Route::post('/login', [LoginController::class, 'login'])->name('login.attempt');
    });

    Route::middleware('auth')->group(function () {
        Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
        Route::get('/', [Admin\DashboardController::class, 'app'])->name('dashboard');
        Route::get('/fichiers/devis/{file}', [Admin\RequestController::class, 'downloadQuoteFile'])->name('files.quote');
        Route::get('/fichiers/cv/{application}', [Admin\RequestController::class, 'downloadCv'])->name('files.cv');

        Route::prefix('api')->name('api.')->group(function () {
            Route::get('/stats', [Admin\DashboardController::class, 'stats']);

            Route::get('/requests', [Admin\RequestController::class, 'index']);
            Route::patch('/requests/{type}/{id}', [Admin\RequestController::class, 'update']);
            Route::delete('/requests/{type}/{id}', [Admin\RequestController::class, 'destroy']);

            Route::get('/projects', [Admin\ProjectController::class, 'index']);
            Route::post('/projects', [Admin\ProjectController::class, 'store']);
            Route::post('/projects/{project}', [Admin\ProjectController::class, 'update']);
            Route::delete('/projects/{project}', [Admin\ProjectController::class, 'destroy']);
            Route::post('/projects/{project}/images', [Admin\ProjectController::class, 'addImages']);
            Route::patch('/project-images/{image}', [Admin\ProjectController::class, 'updateImage']);
            Route::delete('/project-images/{image}', [Admin\ProjectController::class, 'deleteImage']);

            Route::get('/services', [Admin\ServiceController::class, 'index']);
            Route::post('/services/{service}', [Admin\ServiceController::class, 'update']);

            Route::get('/posts', [Admin\PostController::class, 'index']);
            Route::post('/posts', [Admin\PostController::class, 'store']);
            Route::post('/posts/{post}', [Admin\PostController::class, 'update']);
            Route::delete('/posts/{post}', [Admin\PostController::class, 'destroy']);
            Route::post('/posts/{post}/images', [Admin\PostController::class, 'addImages']);
            Route::post('/editor-images', [Admin\PostController::class, 'editorImage']);
            Route::patch('/post-images/{image}', [Admin\PostController::class, 'updateImage']);
            Route::delete('/post-images/{image}', [Admin\PostController::class, 'destroyImage']);

            Route::get('/references', [Admin\ReferenceController::class, 'index']);
            Route::post('/references', [Admin\ReferenceController::class, 'store']);
            Route::post('/references/{reference}', [Admin\ReferenceController::class, 'update']);
            Route::delete('/references/{reference}', [Admin\ReferenceController::class, 'destroy']);

            Route::get('/jobs', [Admin\JobOfferController::class, 'index']);
            Route::post('/jobs', [Admin\JobOfferController::class, 'store']);
            Route::post('/jobs/{job}', [Admin\JobOfferController::class, 'update']);
            Route::delete('/jobs/{job}', [Admin\JobOfferController::class, 'destroy']);

            Route::get('/settings', [Admin\SettingController::class, 'index']);
            Route::post('/settings', [Admin\SettingController::class, 'update']);
            Route::post('/account', [Admin\SettingController::class, 'account']);
        });
    });
});
