<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

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
        // Partage les données de la topbar avec toutes les vues qui chargent 'partials.topbar'
        View::composer('partials.topbar', \App\Http\ViewComposers\TopbarComposer::class);
    }
}
