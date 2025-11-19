<?php

namespace App\Http\Controllers\Professionista;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

/**
 * Controller: Profilo Professionista
 * Funzione: Gestisce il profilo personale del professionista
 */
class ProfiloController extends Controller
{
    /**
     * Mostra il profilo
     */
    public function index()
    {
        $utente = Auth::user();
        $professionista = $utente->professionista;

        // Verifica che il professionista abbia un profilo completo
        if (!$professionista) {
            return redirect()->route('professionista.dashboard')
                ->with('error', 'Profilo professionista non trovato. Contatta l\'amministratore per completare la configurazione.');
        }

        return view('professionista.profilo.index', compact('utente', 'professionista'));
    }

    /**
     * Aggiorna il profilo
     */
    public function aggiornaProfilo(Request $request)
    {
        $utente = Auth::user();
        $professionista = $utente->professionista;

        // Verifica che il professionista abbia un profilo completo
        if (!$professionista) {
            return redirect()->route('professionista.dashboard')
                ->with('error', 'Profilo professionista non trovato. Contatta l\'amministratore per completare la configurazione.');
        }

        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'cognome' => 'required|string|max:255',
            'email' => 'required|email|unique:utenti,email,' . $utente->id,
            'telefono' => 'nullable|string|max:20',
            'bio' => 'nullable|string|max:1000',
        ]);

        // Aggiorna campi utente
        $utente->update([
            'nome' => $validated['nome'],
            'cognome' => $validated['cognome'],
            'email' => $validated['email'],
            'telefono' => $validated['telefono'] ?? null,
        ]);

        // Aggiorna campi professionista
        if (isset($validated['bio'])) {
            $professionista->update([
                'bio' => $validated['bio'],
            ]);
        }

        return redirect()->route('professionista.profilo.index')
            ->with('success', 'Profilo aggiornato con successo!');
    }

    /**
     * Mostra form cambio password
     */
    public function cambiaPassword()
    {
        return view('professionista.profilo.cambia-password');
    }

    /**
     * Salva nuova password
     */
    public function salvaNuovaPassword(Request $request)
    {
        $validated = $request->validate([
            'password_attuale' => 'required',
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $utente = Auth::user();

        // Verifica password attuale
        if (!Hash::check($request->password_attuale, $utente->password)) {
            return back()->withErrors(['password_attuale' => 'La password attuale non è corretta']);
        }

        // Aggiorna password
        $utente->update([
            'password' => Hash::make($request->password),
            'deve_cambiare_password' => false,  // Segna che la password è stata cambiata
        ]);

        return redirect()->route('professionista.profilo.index')
            ->with('success', 'Password cambiata con successo!');
    }
}
