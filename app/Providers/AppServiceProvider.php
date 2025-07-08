<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Config;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (app()->environment('production')) {
            // Paksa HTTPS jika di production
            URL::forceScheme('https');

            // Set konfigurasi session agar cookie aman
            Config::set('session.secure', true);
            Config::set('session.same_site', 'lax');
            Config::set('session.domain', 'e-certification-cedec.up.railway.app');

        }
    }
}
