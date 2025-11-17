<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PagamentoProfessionista;
use App\Models\Utente;
use App\Models\Lezione;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * Controller: Pagamenti Professionisti
 *
 * Funzione: Gestisce i pagamenti effettivi ai professionisti
 * - CRUD pagamenti
 * - Calcolo compensi da lezioni
 * - Storico pagamenti
 */
class PagamentiProfessionistiController extends Controller
{
    /**
     * Lista pagamenti professionisti
     */
    public function index()
    {
        $pagamenti = PagamentoProfessionista::with(['professionista', 'amministratore'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Statistiche
        $totaleCompletati = PagamentoProfessionista::completati()->sum('importo_pagato');
        $totaleInAttesa = PagamentoProfessionista::inAttesa()->sum('importo_maturato');
        $pagamentiMese = PagamentoProfessionista::completati()
            ->whereMonth('data_pagamento', now()->month)
            ->whereYear('data_pagamento', now()->year)
            ->count();

        return view('admin.professionisti.pagamenti.index', compact(
            'pagamenti',
            'totaleCompletati',
            'totaleInAttesa',
            'pagamentiMese'
        ));
    }

    /**
     * Form creazione pagamento
     */
    public function create()
    {
        // Lista professionisti attivi
        $professionisti = Utente::where('tipo_utente', 'professionista')
            ->where('attivo', true)
            ->with('professionista')
            ->orderBy('nome')
            ->get();

        return view('admin.professionisti.pagamenti.create', compact('professionisti'));
    }

    /**
     * Calcola compenso per periodo (AJAX)
     */
    public function calcolaCompenso(Request $request)
    {
        $validated = $request->validate([
            'utente_id' => 'required|exists:utenti,id',
            'periodo_da' => 'required|date',
            'periodo_a' => 'required|date|after_or_equal:periodo_da',
        ]);

        $utente = Utente::with('professionista')->findOrFail($validated['utente_id']);
        $tariffaOraria = $utente->professionista->tariffa_oraria ?? 0;

        // Lezioni completate nel periodo
        $lezioni = Lezione::where('professionista_id', $utente->id)
            ->where('stato', 'completata')
            ->whereBetween('data', [$validated['periodo_da'], $validated['periodo_a']])
            ->get();

        $compensoMaturato = $lezioni->sum(function($lezione) use ($tariffaOraria) {
            $ore = ($lezione->durata_minuti ?? 60) / 60;
            return $ore * $tariffaOraria;
        });

        $ritenuta = $compensoMaturato * 0.20; // 20% ritenuta d'acconto
        $netto = $compensoMaturato - $ritenuta;

        return response()->json([
            'compenso_maturato' => number_format($compensoMaturato, 2, '.', ''),
            'ritenuta_fiscale' => number_format($ritenuta, 2, '.', ''),
            'importo_netto' => number_format($netto, 2, '.', ''),
            'numero_lezioni' => $lezioni->count(),
            'ore_totali' => $lezioni->sum('durata_minuti') / 60,
        ]);
    }

    /**
     * Salva pagamento
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'utente_id' => 'required|exists:utenti,id',
            'periodo_da' => 'required|date',
            'periodo_a' => 'required|date|after_or_equal:periodo_da',
            'importo_maturato' => 'required|numeric|min:0',
            'ritenuta_fiscale' => 'required|numeric|min:0',
            'importo_netto' => 'required|numeric|min:0',
            'metodo_pagamento' => 'required|in:bonifico,contante,assegno',
            'data_pagamento' => 'required|date',
            'numero_bonifico' => 'nullable|string|max:100',
            'iban' => 'nullable|string|max:34',
            'note' => 'nullable|string|max:1000',
        ]);

        $utente = Utente::with('professionista')->findOrFail($validated['utente_id']);

        DB::transaction(function() use ($validated, $utente) {
            PagamentoProfessionista::create([
                'professionista_id' => $utente->professionista->id,
                'utente_id' => $validated['utente_id'],
                'periodo_da' => $validated['periodo_da'],
                'periodo_a' => $validated['periodo_a'],
                'importo_maturato' => $validated['importo_maturato'],
                'importo_pagato' => $validated['importo_netto'],
                'ritenuta_fiscale' => $validated['ritenuta_fiscale'],
                'importo_netto' => $validated['importo_netto'],
                'metodo_pagamento' => $validated['metodo_pagamento'],
                'data_pagamento' => $validated['data_pagamento'],
                'numero_bonifico' => $validated['numero_bonifico'],
                'iban' => $validated['iban'],
                'note' => $validated['note'],
                'stato' => 'completato',
                'pagato_da' => auth()->id(),
            ]);
        });

        return redirect()
            ->route('admin.professionisti.pagamenti.index')
            ->with('success', 'Pagamento registrato con successo per ' . $utente->nome_completo);
    }

    /**
     * Mostra dettaglio pagamento
     */
    public function show($id)
    {
        $pagamento = PagamentoProfessionista::with(['professionista', 'amministratore'])
            ->findOrFail($id);

        // Lezioni del periodo
        $lezioni = Lezione::where('professionista_id', $pagamento->utente_id)
            ->where('stato', 'completata')
            ->whereBetween('data', [$pagamento->periodo_da, $pagamento->periodo_a])
            ->with(['programma', 'sede'])
            ->orderBy('data')
            ->get();

        return view('admin.professionisti.pagamenti.show', compact('pagamento', 'lezioni'));
    }

    /**
     * Form modifica pagamento
     */
    public function edit($id)
    {
        $pagamento = PagamentoProfessionista::with('professionista')->findOrFail($id);

        return view('admin.professionisti.pagamenti.edit', compact('pagamento'));
    }

    /**
     * Aggiorna pagamento
     */
    public function update(Request $request, $id)
    {
        $pagamento = PagamentoProfessionista::findOrFail($id);

        $validated = $request->validate([
            'data_pagamento' => 'required|date',
            'numero_bonifico' => 'nullable|string|max:100',
            'iban' => 'nullable|string|max:34',
            'note' => 'nullable|string|max:1000',
            'stato' => 'required|in:completato,in_attesa,annullato',
        ]);

        $pagamento->update($validated);

        return redirect()
            ->route('admin.professionisti.pagamenti.show', $pagamento->id)
            ->with('success', 'Pagamento aggiornato con successo');
    }

    /**
     * Elimina pagamento
     */
    public function destroy($id)
    {
        $pagamento = PagamentoProfessionista::findOrFail($id);
        $nomeProfessionista = $pagamento->professionista->nome_completo;

        $pagamento->delete();

        return redirect()
            ->route('admin.professionisti.pagamenti.index')
            ->with('success', 'Pagamento per ' . $nomeProfessionista . ' eliminato');
    }

    /**
     * Storico pagamenti per singolo professionista
     */
    public function storicoProfessionista($utente_id)
    {
        $utente = Utente::with('professionista')->findOrFail($utente_id);

        $pagamenti = PagamentoProfessionista::where('utente_id', $utente_id)
            ->orderBy('data_pagamento', 'desc')
            ->get();

        $totaleVersato = $pagamenti->where('stato', 'completato')->sum('importo_pagato');

        return view('admin.professionisti.pagamenti.storico', compact(
            'utente',
            'pagamenti',
            'totaleVersato'
        ));
    }
}
