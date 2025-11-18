<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware: VerificaPermesso
 *
 * Funzione: Verifica che l'utente abbia un permesso specifico
 * Supporta permessi individuali per collaboratori
 *
 * Utilizzo:
 * Route::middleware(['auth', 'permesso:gestione-presenze'])->group(...)
 */
class VerificaPermesso
{
    /**
     * Gestisce la richiesta in entrata
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$permessiRichiesti
     */
    public function handle(Request $request, Closure $next, string ...$permessiRichiesti): Response
    {
        // Se l'utente non è autenticato, redirect al login
        if (!Auth::check()) {
            return redirect()->route('home')
                ->with('error', 'Devi effettuare il login per accedere a questa pagina.');
        }

        $user = Auth::user();

        // Super Admin: accesso totale senza controlli
        if ($user->ruolo && $user->ruolo->isSuperAdmin()) {
            return $next($request);
        }

        // Verifica se l'utente ha ALMENO UNO dei permessi richiesti
        $haPermesso = false;

        foreach ($permessiRichiesti as $permesso) {
            if ($user->haPermesso($permesso)) {
                $haPermesso = true;
                break;
            }
        }

        if (!$haPermesso) {
            // Redirect in base al tipo utente
            $route = match($user->tipo_utente) {
                'cliente' => 'cliente.dashboard',
                'professionista' => 'professionista.dashboard',
                'amministratore' => 'admin.dashboard',
                default => 'home'
            };

            return redirect()->route($route)
                ->with('error', 'Non hai i permessi necessari per accedere a questa funzionalità.');
        }

        // Permesso OK, procedi
        return $next($request);
    }
}
