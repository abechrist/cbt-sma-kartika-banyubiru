<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

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
        if (request()->server('HTTP_X_FORWARDED_PROTO') === 'https'
            || request()->header('x-forwarded-proto') === 'https'
            || request()->isSecure()
            || str_contains(request()->header('host', ''), 'loca.lt')
            || app()->environment('production')) {
            URL::forceScheme('https');
        }
    }
}
