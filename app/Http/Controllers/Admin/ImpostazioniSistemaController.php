<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ImpostazioneSistema;
use Illuminate\Http\Request;

/**
 * Controller: Impostazioni Sistema
 * Gestisce valori configurabili (tipologie, stati, ecc.)
 */
class ImpostazioniSistemaController extends Controller
{
    /**
     * Mostra tutte le categorie
     */
    public function index()
    {
        // Raggruppa per categoria
        $impostazioni = ImpostazioneSistema::orderBy('categoria')
            ->orderBy('ordinamento')
            ->get()
            ->groupBy('categoria');

        // Nomi leggibili delle categorie
        $categorie = [
            'tipologia_lezione' => [
                'nome' => 'Tipologie Lezione',
                'descrizione' => 'Tipologie disponibili per le lezioni (individuale, gruppo, online, ecc.)',
                'icona' => 'fa-tag',
            ],
            'stato_lezione' => [
                'nome' => 'Stati Lezione',
                'descrizione' => 'Stati possibili per una lezione (programmata, confermata, ecc.)',
                'icona' => 'fa-flag',
            ],
            'frequenza_ricorrenza' => [
                'nome' => 'Frequenze Ricorrenza',
                'descrizione' => 'Frequenze disponibili per lezioni ricorrenti',
                'icona' => 'fa-repeat',
            ],
        ];

        return view('admin.impostazioni-sistema.index', compact('impostazioni', 'categorie'));
    }

    /**
     * Mostra form creazione
     */
    public function create(Request $request)
    {
        $categoria = $request->get('categoria');

        $categorie = [
            'tipologia_lezione' => 'Tipologia Lezione',
            'stato_lezione' => 'Stato Lezione',
            'frequenza_ricorrenza' => 'Frequenza Ricorrenza',
        ];

        return view('admin.impostazioni-sistema.create', compact('categoria', 'categorie'));
    }

    /**
     * Salva nuova impostazione
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'categoria' => 'required|in:tipologia_lezione,stato_lezione,frequenza_ricorrenza',
            'chiave' => 'required|string|max:50|regex:/^[a-z0-9_]+$/',
            'etichetta' => 'required|string|max:100',
            'descrizione' => 'nullable|string',
            'colore' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'icona' => 'nullable|string|max:50',
            'ordinamento' => 'nullable|integer|min:0',
            'attivo' => 'boolean',
        ]);

        // Verifica univocità
        $exists = ImpostazioneSistema::where('categoria', $validated['categoria'])
            ->where('chiave', $validated['chiave'])
            ->exists();

        if ($exists) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Esiste già un\'impostazione con questa chiave nella categoria selezionata.');
        }

        ImpostazioneSistema::create($validated);

        return redirect()->route('admin.impostazioni-sistema.index')
            ->with('success', 'Impostazione creata con successo!');
    }

    /**
     * Mostra form modifica
     */
    public function edit($id)
    {
        $impostazione = ImpostazioneSistema::findOrFail($id);

        $categorie = [
            'tipologia_lezione' => 'Tipologia Lezione',
            'stato_lezione' => 'Stato Lezione',
            'frequenza_ricorrenza' => 'Frequenza Ricorrenza',
        ];

        return view('admin.impostazioni-sistema.edit', compact('impostazione', 'categorie'));
    }

    /**
     * Aggiorna impostazione
     */
    public function update(Request $request, $id)
    {
        $impostazione = ImpostazioneSistema::findOrFail($id);

        $validated = $request->validate([
            'etichetta' => 'required|string|max:100',
            'descrizione' => 'nullable|string',
            'colore' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'icona' => 'nullable|string|max:50',
            'ordinamento' => 'nullable|integer|min:0',
            'attivo' => 'boolean',
        ]);

        // Non permettere modifica di categoria e chiave per impostazioni di sistema
        if (!$impostazione->di_sistema) {
            $validated['categoria'] = $request->input('categoria');
            $validated['chiave'] = $request->input('chiave');
        }

        $impostazione->update($validated);

        return redirect()->route('admin.impostazioni-sistema.index')
            ->with('success', 'Impostazione aggiornata con successo!');
    }

    /**
     * Elimina impostazione
     */
    public function destroy($id)
    {
        $impostazione = ImpostazioneSistema::findOrFail($id);

        if ($impostazione->di_sistema) {
            return redirect()->back()
                ->with('error', 'Non è possibile eliminare un\'impostazione di sistema. Puoi solo disattivarla.');
        }

        $impostazione->delete();

        return redirect()->route('admin.impostazioni-sistema.index')
            ->with('success', 'Impostazione eliminata con successo!');
    }

    /**
     * Attiva/Disattiva impostazione
     */
    public function toggleAttivo($id)
    {
        $impostazione = ImpostazioneSistema::findOrFail($id);
        $impostazione->attivo = !$impostazione->attivo;
        $impostazione->save();

        $stato = $impostazione->attivo ? 'attivata' : 'disattivata';

        return redirect()->back()
            ->with('success', "Impostazione $stato con successo!");
    }
}
