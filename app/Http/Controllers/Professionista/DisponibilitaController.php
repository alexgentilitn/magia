<?php

namespace App\Http\Controllers\Professionista;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Controller: Disponibilità Professionista
 * Funzione: Gestisce le disponibilità orarie del professionista
 * - Visualizzazione disponibilità settimanali
 * - Blocco orari non disponibili
 */
class DisponibilitaController extends Controller
{
    /**
     * Mostra gestione disponibilità
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

        // Ottieni disponibilità salvate (se esistono nel campo JSON)
        $disponibilita = $professionista->disponibilita_settimanale ?? [];

        return view('professionista.disponibilita.index', compact('professionista', 'disponibilita'));
    }

    /**
     * Salva disponibilità
     */
    public function salva(Request $request)
    {
        $utente = Auth::user();
        $professionista = $utente->professionista;

        // Verifica che il professionista abbia un profilo completo
        if (!$professionista) {
            return redirect()->route('professionista.dashboard')
                ->with('error', 'Profilo professionista non trovato. Contatta l\'amministratore per completare la configurazione.');
        }

        $validated = $request->validate([
            'disponibilita' => 'nullable|array',
        ]);

        // Salva le disponibilità nel campo JSON
        $professionista->update([
            'disponibilita_settimanale' => $validated['disponibilita'] ?? []
        ]);

        return redirect()->route('professionista.disponibilita.index')
            ->with('success', 'Disponibilità aggiornate con successo!');
    }
}
