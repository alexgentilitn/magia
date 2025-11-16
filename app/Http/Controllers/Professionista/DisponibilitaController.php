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
        $professionista = Auth::user();

        // Ottieni disponibilità salvate (se esistono nel campo JSON)
        $disponibilita = $professionista->disponibilita ?? [];

        return view('professionista.disponibilita.index', compact('professionista', 'disponibilita'));
    }

    /**
     * Salva disponibilità
     */
    public function salva(Request $request)
    {
        $professionista = Auth::user();

        $validated = $request->validate([
            'disponibilita' => 'nullable|array',
        ]);

        // Salva le disponibilità nel campo JSON
        $professionista->update([
            'disponibilita' => $validated['disponibilita'] ?? []
        ]);

        return redirect()->route('professionista.disponibilita.index')
            ->with('success', 'Disponibilità aggiornate con successo!');
    }
}
