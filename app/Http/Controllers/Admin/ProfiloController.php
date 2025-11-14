<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Professionista;

/**
 * Controller: Gestione Profilo Utente
 * Permette a admin e professionisti di gestire il proprio profilo e password
 */
class ProfiloController extends Controller
{
    /**
     * Mostra il profilo dell'utente loggato
     */
    public function index()
    {
        $utente = Auth::user();
        $professionista = null;

        // Se è un professionista, carica il profilo completo
        if ($utente->tipo_utente === 'professionista') {
            $professionista = Professionista::where('utente_id', $utente->id)->first();
        }

        return view('admin.profilo.index', compact('utente', 'professionista'));
    }

    /**
     * Mostra il form cambio password
     */
    public function cambiaPassword()
    {
        return view('admin.profilo.cambia-password');
    }

    /**
     * Salva la nuova password
     */
    public function salvaNuovaPassword(Request $request)
    {
        $validated = $request->validate([
            'password_attuale' => 'required',
            'password_nuova' => 'required|min:8|confirmed',
        ], [
            'password_attuale.required' => 'Inserisci la password attuale',
            'password_nuova.required' => 'Inserisci la nuova password',
            'password_nuova.min' => 'La password deve essere di almeno 8 caratteri',
            'password_nuova.confirmed' => 'Le password non coincidono',
        ]);

        $utente = Auth::user();

        // Verifica password attuale
        if (!Hash::check($validated['password_attuale'], $utente->password)) {
            return redirect()->back()
                ->withErrors(['password_attuale' => 'Password attuale non corretta'])
                ->withInput();
        }

        // Aggiorna password
        $utente->update([
            'password' => Hash::make($validated['password_nuova'])
        ]);

        return redirect()->route('admin.profilo.index')
            ->with('success', 'Password aggiornata con successo!');
    }

    /**
     * Aggiorna i dati del profilo
     */
    public function aggiornaProfilo(Request $request)
    {
        $utente = Auth::user();

        $validated = $request->validate([
            'nome' => 'required|string|max:100',
            'cognome' => 'required|string|max:100',
            'telefono' => 'nullable|string|max:20',
            'email' => 'required|email|unique:utenti,email,' . $utente->id,
        ]);

        $utente->update($validated);

        // Se è un professionista, aggiorna anche il profilo professionista
        if ($utente->tipo_utente === 'professionista' && $utente->professionista) {
            $utente->professionista->update([
                'nome' => $validated['nome'],
                'cognome' => $validated['cognome'],
                'telefono_mobile' => $validated['telefono'],
                'email' => $validated['email'],
            ]);
        }

        return redirect()->route('admin.profilo.index')
            ->with('success', 'Profilo aggiornato con successo!');
    }
}
