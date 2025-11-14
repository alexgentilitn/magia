<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pagamento;
use App\Models\Utente;
use App\Models\Programma;
use App\Models\Lezione;
use Illuminate\Http\Request;

class PagamentiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Pagamento::with(['cliente', 'programma', 'lezione']);

        // Filtro per stato
        if ($request->filled('stato')) {
            $query->where('stato', $request->stato);
        }

        // Filtro per tipo
        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        // Filtro per metodo
        if ($request->filled('metodo')) {
            $query->where('metodo', $request->metodo);
        }

        // Filtro per cliente
        if ($request->filled('cliente_id')) {
            $query->where('cliente_id', $request->cliente_id);
        }

        // Filtro solo scaduti
        if ($request->boolean('solo_scaduti')) {
            $query->scaduti();
        }

        // Filtro solo in attesa
        if ($request->boolean('solo_in_attesa')) {
            $query->inAttesa();
        }

        // Filtro per range date emissione
        if ($request->filled('data_da')) {
            $query->where('data_emissione', '>=', $request->data_da);
        }
        if ($request->filled('data_a')) {
            $query->where('data_emissione', '<=', $request->data_a);
        }

        // Ricerca per numero fattura o riferimento
        if ($request->filled('ricerca')) {
            $ricerca = $request->ricerca;
            $query->where(function($q) use ($ricerca) {
                $q->where('numero_fattura', 'like', "%{$ricerca}%")
                  ->orWhere('riferimento_transazione', 'like', "%{$ricerca}%");
            });
        }

        // Ordinamento
        $query->orderBy('data_emissione', 'desc');

        $pagamenti = $query->paginate(20)->withQueryString();

        // Statistiche
        $statistiche = [
            'totale' => Pagamento::count(),
            'in_attesa' => Pagamento::where('stato', 'in_attesa')->count(),
            'completati' => Pagamento::where('stato', 'completato')->count(),
            'scaduti' => Pagamento::scaduti()->count(),
            'importo_totale' => Pagamento::where('stato', 'completato')->sum('importo_pagato'),
            'importo_in_attesa' => Pagamento::where('stato', 'in_attesa')->sum('importo_residuo'),
        ];

        // Dati per i filtri
        $clienti = Utente::where('tipo_utente', 'cliente')->orderBy('nome')->get();

        return view('admin.pagamenti.index', compact('pagamenti', 'statistiche', 'clienti'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $clienti = Utente::where('tipo_utente', 'cliente')->orderBy('nome')->get();
        $programmi = Programma::attivi()->orderBy('nome')->get();
        $lezioni = Lezione::where('data', '>=', now())->orderBy('data')->limit(50)->get();

        // Pre-selezione da query string
        $clienteSelezionato = $request->cliente_id;
        $programmaSelezionato = $request->programma_id;

        return view('admin.pagamenti.create', compact('clienti', 'programmi', 'lezioni', 'clienteSelezionato', 'programmaSelezionato'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'cliente_id' => 'required|exists:utenti,id',
            'programma_id' => 'nullable|exists:programmi,id',
            'lezione_id' => 'nullable|exists:lezioni,id',
            'importo' => 'required|numeric|min:0',
            'importo_pagato' => 'nullable|numeric|min:0',
            'tipo' => 'required|in:programma,lezione,abbonamento,altro',
            'metodo' => 'required|in:contanti,bonifico,carta,pos,paypal,satispay,altro',
            'stato' => 'required|in:in_attesa,parziale,completato,scaduto,rimborsato,cancellato',
            'data_emissione' => 'required|date',
            'data_scadenza' => 'nullable|date|after_or_equal:data_emissione',
            'data_pagamento' => 'nullable|date',
            'numero_fattura' => 'nullable|string|unique:pagamenti,numero_fattura',
            'riferimento_transazione' => 'nullable|string',
            'note' => 'nullable|string',
            'fatturato' => 'boolean',
        ]);

        // Calcola importo pagato e residuo
        $importoPagato = $validated['importo_pagato'] ?? 0;
        $validated['importo_pagato'] = $importoPagato;
        $validated['importo_residuo'] = $validated['importo'] - $importoPagato;

        // Se completamente pagato, imposta data pagamento se non fornita
        if ($importoPagato >= $validated['importo'] && !$validated['data_pagamento']) {
            $validated['data_pagamento'] = now();
            $validated['stato'] = 'completato';
        }

        // Genera numero fattura se fatturato e non fornito
        if ($request->boolean('fatturato') && empty($validated['numero_fattura'])) {
            $validated['numero_fattura'] = Pagamento::generaNumeroFattura();
        }

        $validated['fatturato'] = $request->boolean('fatturato', false);

        $pagamento = Pagamento::create($validated);

        return redirect()
            ->route('admin.pagamenti.show', $pagamento->id)
            ->with('success', 'Pagamento registrato con successo!');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $pagamento = Pagamento::with(['cliente', 'programma', 'lezione'])
            ->findOrFail($id);

        return view('admin.pagamenti.show', compact('pagamento'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $pagamento = Pagamento::findOrFail($id);
        $clienti = Utente::where('tipo_utente', 'cliente')->orderBy('nome')->get();
        $programmi = Programma::attivi()->orderBy('nome')->get();
        $lezioni = Lezione::where('data', '>=', now())->orderBy('data')->limit(50)->get();

        return view('admin.pagamenti.edit', compact('pagamento', 'clienti', 'programmi', 'lezioni'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $pagamento = Pagamento::findOrFail($id);

        $validated = $request->validate([
            'cliente_id' => 'required|exists:utenti,id',
            'programma_id' => 'nullable|exists:programmi,id',
            'lezione_id' => 'nullable|exists:lezioni,id',
            'importo' => 'required|numeric|min:0',
            'importo_pagato' => 'nullable|numeric|min:0',
            'tipo' => 'required|in:programma,lezione,abbonamento,altro',
            'metodo' => 'required|in:contanti,bonifico,carta,pos,paypal,satispay,altro',
            'stato' => 'required|in:in_attesa,parziale,completato,scaduto,rimborsato,cancellato',
            'data_emissione' => 'required|date',
            'data_scadenza' => 'nullable|date|after_or_equal:data_emissione',
            'data_pagamento' => 'nullable|date',
            'numero_fattura' => 'nullable|string|unique:pagamenti,numero_fattura,' . $id,
            'riferimento_transazione' => 'nullable|string',
            'note' => 'nullable|string',
            'fatturato' => 'boolean',
        ]);

        // Calcola importo pagato e residuo
        $importoPagato = $validated['importo_pagato'] ?? 0;
        $validated['importo_pagato'] = $importoPagato;
        $validated['importo_residuo'] = $validated['importo'] - $importoPagato;

        // Aggiorna stato in base all'importo pagato
        if ($importoPagato >= $validated['importo']) {
            $validated['stato'] = 'completato';
            if (!$validated['data_pagamento']) {
                $validated['data_pagamento'] = now();
            }
        } elseif ($importoPagato > 0) {
            $validated['stato'] = 'parziale';
        }

        $validated['fatturato'] = $request->boolean('fatturato');

        $pagamento->update($validated);

        return redirect()
            ->route('admin.pagamenti.show', $pagamento->id)
            ->with('success', 'Pagamento aggiornato con successo!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $pagamento = Pagamento::findOrFail($id);

        // Non permettere eliminazione di pagamenti completati o fatturati
        if ($pagamento->stato === 'completato' && $pagamento->fatturato) {
            return redirect()
                ->route('admin.pagamenti.index')
                ->with('error', 'Impossibile eliminare un pagamento completato e fatturato. Esegui un rimborso invece.');
        }

        $pagamento->delete();

        return redirect()
            ->route('admin.pagamenti.index')
            ->with('success', 'Pagamento eliminato con successo!');
    }

    /**
     * Registra un pagamento parziale
     */
    public function registraPagamentoParziale(Request $request, $id)
    {
        $pagamento = Pagamento::findOrFail($id);

        $validated = $request->validate([
            'importo' => 'required|numeric|min:0|max:' . $pagamento->importo_residuo,
            'metodo' => 'required|in:contanti,bonifico,carta,pos,paypal,satispay,altro',
            'data_pagamento' => 'required|date',
            'riferimento_transazione' => 'nullable|string',
            'note' => 'nullable|string',
        ]);

        $nuovoImportoPagato = $pagamento->importo_pagato + $validated['importo'];
        $nuovoImportoResiduo = $pagamento->importo - $nuovoImportoPagato;

        $pagamento->update([
            'importo_pagato' => $nuovoImportoPagato,
            'importo_residuo' => $nuovoImportoResiduo,
            'metodo' => $validated['metodo'],
            'stato' => $nuovoImportoResiduo <= 0 ? 'completato' : 'parziale',
            'data_pagamento' => $nuovoImportoResiduo <= 0 ? $validated['data_pagamento'] : null,
            'riferimento_transazione' => $validated['riferimento_transazione'],
            'note' => $pagamento->note . "\n" . now()->format('d/m/Y') . ": Pagato €{$validated['importo']} - " . ($validated['note'] ?? ''),
        ]);

        return redirect()
            ->route('admin.pagamenti.show', $pagamento->id)
            ->with('success', 'Pagamento parziale registrato con successo!');
    }

    /**
     * Marca come completato
     */
    public function marcaCompletato(Request $request, $id)
    {
        $pagamento = Pagamento::findOrFail($id);

        $validated = $request->validate([
            'data_pagamento' => 'required|date',
            'metodo' => 'required|in:contanti,bonifico,carta,pos,paypal,satispay,altro',
        ]);

        $pagamento->update([
            'importo_pagato' => $pagamento->importo,
            'importo_residuo' => 0,
            'stato' => 'completato',
            'data_pagamento' => $validated['data_pagamento'],
            'metodo' => $validated['metodo'],
        ]);

        return redirect()
            ->route('admin.pagamenti.show', $pagamento->id)
            ->with('success', 'Pagamento marcato come completato!');
    }

    /**
     * Esegui rimborso
     */
    public function rimborsa(Request $request, $id)
    {
        $pagamento = Pagamento::findOrFail($id);

        if ($pagamento->stato !== 'completato') {
            return redirect()
                ->route('admin.pagamenti.show', $pagamento->id)
                ->with('error', 'Solo i pagamenti completati possono essere rimborsati.');
        }

        $validated = $request->validate([
            'note' => 'required|string',
        ]);

        $pagamento->update([
            'stato' => 'rimborsato',
            'note' => $pagamento->note . "\n" . now()->format('d/m/Y') . ": RIMBORSATO - " . $validated['note'],
        ]);

        return redirect()
            ->route('admin.pagamenti.show', $pagamento->id)
            ->with('success', 'Rimborso eseguito con successo!');
    }
}
