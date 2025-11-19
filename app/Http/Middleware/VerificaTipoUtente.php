<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware: VerificaTipoUtente
 * 
 * Funzione: Verifica che l'utente autenticato abbia il tipo corretto per accedere alla route
 * 
 * Utilizzo nelle routes:
 * Route::middleware(['auth', 'tipo_utente:amministratore,professionista'])->group(...)
 * Route::middleware(['auth', 'tipo_utente:cliente'])->group(...)
 */

class VerificaTipoUtente
{
    /**
     * Gestisce la richiesta in entrata
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$tipiConsentiti
     */
    public function handle(Request $request, Closure $next, string ...$tipiConsentiti): Response
    {
        // Se l'utente non è autenticato, redirect al login
        if (!Auth::check()) {
            return redirect()->route('home')
                ->with('error', 'Devi effettuare il login per accedere a questa pagina.');
        }

        $user = Auth::user();

        // Verifica se il tipo utente è tra quelli consentiti
        if (!in_array($user->tipo_utente, $tipiConsentiti)) {
            // Redirect diverso in base al tipo utente attuale
            if ($user->tipo_utente === 'cliente') {
                // Cliente tenta di accedere ad area riservata (admin o professionista)
                return redirect()->route('cliente.dashboard')
                    ->with('error', 'Non hai i permessi per accedere a questa sezione.');
            } elseif ($user->tipo_utente === 'professionista') {
                // Professionista tenta di accedere all'area admin o cliente
                return redirect()->route('professionista.dashboard')
                    ->with('error', 'Non hai i permessi per accedere a questa sezione.');
            } elseif ($user->tipo_utente === 'amministratore') {
                // Admin tenta di accedere all'area cliente o professionista
                return redirect()->route('admin.dashboard')
                    ->with('error', 'Non hai i permessi per accedere a questa sezione.');
            } else {
                // Tipo utente sconosciuto
                return redirect()->route('home')
                    ->with('error', 'Tipo utente non riconosciuto.');
            }
        }

        // Tutto ok, procedi
        return $next($request);
    }
}
