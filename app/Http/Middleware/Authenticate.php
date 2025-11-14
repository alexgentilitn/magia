<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

/**
 * Middleware: Authenticate
 * Funzione: Gestisce il redirect quando utente non autenticato
 * Reindirizzi: admin.login per admin, cliente.login per clienti
 */
class Authenticate extends Middleware
{
    /**
     * Ottiene il path di redirect quando utente non autenticato
     * 
     * Funzione: Controlla l'URL e decide dove reindirizzare
     * - Se URL contiene /admin → admin.login
     * - Se URL contiene /cliente → cliente.login
     * - Default → admin.login
     */
    protected function redirectTo(Request $request): ?string
    {
        // Se la richiesta è JSON (API), non redirigere
        if ($request->expectsJson()) {
            return null;
        }

        // Ottieni il path della richiesta
        $path = $request->path();

        // Se l'URL contiene "cliente", vai a login cliente
        if (str_contains($path, 'cliente')) {
            return route('cliente.login');
        }

        // Altrimenti vai a login admin (default)
        return route('admin.login');
    }
}