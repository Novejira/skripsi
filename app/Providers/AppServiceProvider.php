<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Artisan;

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
            if (env('APP_ENV') === 'production') {
            URL::forceScheme('https');
        }
            if (app()->environment('production')) {
            Artisan::call('config:clear');
            Artisan::call('cache:clear');
        }
    }
}
