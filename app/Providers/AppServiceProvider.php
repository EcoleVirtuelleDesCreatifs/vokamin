<?php

namespace App\Providers;

use App\Models\Service;
use App\Support\Settings;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Throwable;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(Settings::class);
    }

    public function boot(): void
    {
        \Illuminate\Support\Carbon::setLocale(config('app.locale', 'fr'));
        Schema::defaultStringLength(191); // compatibilité MySQL/MariaDB anciennes versions

        // Les services alimentent le méga-menu et le pied de page sur toutes les pages.
        View::composer(['partials.header', 'partials.footer'], function ($view) {
            static $services = null;
            if ($services === null) {
                try {
                    $services = Schema::hasTable('services') ? Service::published()->get(['slug', 'name', 'short', 'icon', 'teaser']) : collect();
                } catch (Throwable) {
                    $services = collect();
                }
            }
            $view->with('navServices', $services);
        });
    }
}
