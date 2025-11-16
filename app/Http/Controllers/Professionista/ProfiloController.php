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
        $professionista = Auth::user();

        return view('professionista.profilo.index', compact('professionista'));
    }

    /**
     * Aggiorna il profilo
     */
    public function aggiornaProfilo(Request $request)
    {
        $professionista = Auth::user();

        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'cognome' => 'required|string|max:255',
            'email' => 'required|email|unique:utenti,email,' . $professionista->id,
            'telefono' => 'nullable|string|max:20',
            'bio' => 'nullable|string|max:1000',
        ]);

        $professionista->update($validated);

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

        $professionista = Auth::user();

        // Verifica password attuale
        if (!Hash::check($request->password_attuale, $professionista->password)) {
            return back()->withErrors(['password_attuale' => 'La password attuale non è corretta']);
        }

        // Aggiorna password
        $professionista->update([
            'password' => Hash::make($request->password)
        ]);

        return redirect()->route('professionista.profilo.index')
            ->with('success', 'Password cambiata con successo!');
    }
}
