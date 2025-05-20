<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
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
        // Définir le nom de l'application
        config(['app.name' => 'FamFinance']);
        
        // Définir la longueur par défaut des chaînes de caractères dans le schéma
        Schema::defaultStringLength(191);
    }
}
