<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Utente;
use App\Models\Permesso;
use Illuminate\Http\Request;

/**
 * Controller: Gestione Permessi Collaboratori
 *
 * Funzione: Gestisce l'assegnazione di permessi individuali ai collaboratori
 * Permette agli admin di personalizzare i permessi per ogni professionista
 */
class PermessiCollaboratoreController extends Controller
{
    /**
     * Mostra form gestione permessi per un collaboratore
     */
    public function edit($id)
    {
        $utente = Utente::with(['ruolo', 'professionista', 'permessiIndividuali'])
            ->where('tipo_utente', 'professionista')
            ->findOrFail($id);

        // Ottieni tutti i permessi disponibili raggruppati per categoria
        $permessiDisponibili = Permesso::attivi()->ordinati()->get()->groupBy('categoria');

        // Ottieni gli ID dei permessi già assegnati (individuali + ruolo)
        $permessiAssegnati = $utente->permessiIndividuali->pluck('id')->toArray();
        $permessiRuolo = $utente->ruolo ? $utente->ruolo->permessi->pluck('id')->toArray() : [];

        return view('admin.professionisti.permessi', compact(
            'utente',
            'permessiDisponibili',
            'permessiAssegnati',
            'permessiRuolo'
        ));
    }

    /**
     * Salva i permessi individuali per un collaboratore
     */
    public function update(Request $request, $id)
    {
        $utente = Utente::where('tipo_utente', 'professionista')->findOrFail($id);

        // Valida i permessi
        $request->validate([
            'permessi' => 'nullable|array',
            'permessi.*' => 'exists:permessi,id',
        ]);

        // Sincronizza i permessi individuali
        $permessi = $request->input('permessi', []);
        $utente->sincronizzaPermessiIndividuali($permessi);

        return redirect()
            ->route('admin.professionisti.permessi.edit', $id)
            ->with('success', 'Permessi aggiornati con successo per ' . $utente->nome_completo);
    }

    /**
     * Resetta i permessi individuali (usa solo quelli del ruolo)
     */
    public function reset($id)
    {
        $utente = Utente::where('tipo_utente', 'professionista')->findOrFail($id);

        // Rimuovi tutti i permessi individuali
        $utente->sincronizzaPermessiIndividuali([]);

        return redirect()
            ->route('admin.professionisti.permessi.edit', $id)
            ->with('success', 'Permessi individuali rimossi. L\'utente ora utilizza solo i permessi del ruolo.');
    }

    /**
     * API: Ottieni i permessi attuali di un collaboratore
     */
    public function getPermessi($id)
    {
        $utente = Utente::with(['permessiIndividuali', 'ruolo.permessi'])
            ->where('tipo_utente', 'professionista')
            ->findOrFail($id);

        return response()->json([
            'permessi_individuali' => $utente->permessiIndividuali->pluck('id'),
            'permessi_ruolo' => $utente->ruolo ? $utente->ruolo->permessi->pluck('id') : [],
            'tutti_permessi' => $utente->tuttiIPermessi()->pluck('id'),
        ]);
    }
}
