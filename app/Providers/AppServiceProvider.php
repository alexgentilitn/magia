<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Impostazione;

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
        // Applica configurazioni SMTP dal database
        // Esegui solo se il database è disponibile (non durante migrations)
        try {
            if (\Schema::hasTable('impostazioni')) {
                Impostazione::applySmtpConfig();
            }
        } catch (\Exception $e) {
            // Ignora errori se il database non è ancora configurato
            \Log::debug('SMTP config non applicata: ' . $e->getMessage());
        }
    }
}
