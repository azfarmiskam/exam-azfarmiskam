<?php

namespace App\Providers;

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
        \Illuminate\Support\Facades\Schema::defaultStringLength(191);

        // Share logo URL with all views
        view()->composer('*', function ($view) {
            $customLogo = storage_path('app/public/logo.png');
            $logoUrl = file_exists($customLogo) ? '/storage/logo.png' : '/images/logo.png';
            $view->with('logoUrl', $logoUrl);
        });
    }
}
