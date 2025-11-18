<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\ParametroCorporeo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Controller: ParametriCorporeiController
 * Funzione: Gestisce lo storico dei parametri corporei delle clienti
 */
class ParametriCorporeiController extends Controller
{
    /**
     * Visualizza storico parametri di una cliente
     */
    public function index($cliente_id)
    {
        $cliente = Cliente::with(['parametriCorporei', 'ultimoParametro'])->findOrFail($cliente_id);

        $statistiche = [
            'totale_rilevazioni' => $cliente->parametriCorporei->count(),
            'prima_rilevazione' => $cliente->parametriCorporei->last()?->data_rilevazione?->format('d/m/Y'),
            'ultima_rilevazione' => $cliente->parametriCorporei->first()?->data_rilevazione?->format('d/m/Y'),
            'variazione_peso' => $this->calcolaVariazionePeso($cliente),
        ];

        // Dati per grafici
        $grafici = $this->preparaDatiGrafici($cliente);

        return view('admin.parametri-corporei.index', compact('cliente', 'statistiche', 'grafici'));
    }

    /**
     * Form per creare nuova rilevazione
     */
    public function create($cliente_id)
    {
        $cliente = Cliente::with('ultimoParametro')->findOrFail($cliente_id);

        return view('admin.parametri-corporei.create', compact('cliente'));
    }

    /**
     * Salva nuova rilevazione
     */
    public function store(Request $request, $cliente_id)
    {
        $validated = $request->validate([
            'data_rilevazione' => 'required|date',
            'peso' => 'required|numeric|min:20|max:300',
            'altezza' => 'required|numeric|min:100|max:250',
            'circonferenza_vita' => 'nullable|numeric|min:40|max:200',
            'circonferenza_fianchi' => 'nullable|numeric|min:50|max:200',
            'circonferenza_braccia' => 'nullable|numeric|min:15|max:80',
            'circonferenza_cosce' => 'nullable|numeric|min:30|max:120',
            'massa_grassa' => 'nullable|numeric|min:0|max:100',
            'massa_magra' => 'nullable|numeric|min:0|max:100',
            'acqua_corporea' => 'nullable|numeric|min:0|max:100',
            'metabolismo_basale' => 'nullable|numeric|min:500|max:5000',
            'massa_ossea' => 'nullable|numeric|min:0|max:10',
            'grasso_viscerale' => 'nullable|numeric|min:0|max:30',
            'proteine' => 'nullable|numeric|min:0|max:50',
            'eta_metabolica' => 'nullable|integer|min:10|max:120',
            'note' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();

        try {
            $cliente = Cliente::findOrFail($cliente_id);

            ParametroCorporeo::create([
                'cliente_id' => $cliente->id,
                'data_rilevazione' => $validated['data_rilevazione'],
                'peso' => $validated['peso'],
                'altezza' => $validated['altezza'],
                'circonferenza_vita' => $validated['circonferenza_vita'] ?? null,
                'circonferenza_fianchi' => $validated['circonferenza_fianchi'] ?? null,
                'circonferenza_braccia' => $validated['circonferenza_braccia'] ?? null,
                'circonferenza_cosce' => $validated['circonferenza_cosce'] ?? null,
                'massa_grassa' => $validated['massa_grassa'] ?? null,
                'massa_magra' => $validated['massa_magra'] ?? null,
                'acqua_corporea' => $validated['acqua_corporea'] ?? null,
                'metabolismo_basale' => $validated['metabolismo_basale'] ?? null,
                'massa_ossea' => $validated['massa_ossea'] ?? null,
                'grasso_viscerale' => $validated['grasso_viscerale'] ?? null,
                'proteine' => $validated['proteine'] ?? null,
                'eta_metabolica' => $validated['eta_metabolica'] ?? null,
                'note' => $validated['note'] ?? null,
                'rilevato_da' => auth()->user()->nome_completo,
            ]);

            // Aggiorna anche i campi nel cliente (per retrocompatibilità)
            $cliente->update([
                'peso_iniziale' => $validated['peso'],
                'altezza' => $validated['altezza'],
                'circonferenza_vita' => $validated['circonferenza_vita'] ?? $cliente->circonferenza_vita,
                'circonferenza_fianchi' => $validated['circonferenza_fianchi'] ?? $cliente->circonferenza_fianchi,
                'massa_grassa' => $validated['massa_grassa'] ?? $cliente->massa_grassa,
                'massa_magra' => $validated['massa_magra'] ?? $cliente->massa_magra,
            ]);

            DB::commit();

            return redirect()->route('admin.clienti.parametri-corporei.index', $cliente_id)
                           ->with('success', 'Parametri corporei salvati con successo!');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Errore salvataggio parametri corporei: ' . $e->getMessage());

            return back()->withInput()
                       ->with('error', 'Errore durante il salvataggio dei parametri.');
        }
    }

    /**
     * Form modifica parametro
     */
    public function edit($cliente_id, $parametro_id)
    {
        $cliente = Cliente::findOrFail($cliente_id);
        $parametro = ParametroCorporeo::where('cliente_id', $cliente_id)
                                      ->findOrFail($parametro_id);

        return view('admin.parametri-corporei.edit', compact('cliente', 'parametro'));
    }

    /**
     * Aggiorna parametro
     */
    public function update(Request $request, $cliente_id, $parametro_id)
    {
        $validated = $request->validate([
            'data_rilevazione' => 'required|date',
            'peso' => 'required|numeric|min:20|max:300',
            'altezza' => 'required|numeric|min:100|max:250',
            'circonferenza_vita' => 'nullable|numeric|min:40|max:200',
            'circonferenza_fianchi' => 'nullable|numeric|min:50|max:200',
            'circonferenza_braccia' => 'nullable|numeric|min:15|max:80',
            'circonferenza_cosce' => 'nullable|numeric|min:30|max:120',
            'massa_grassa' => 'nullable|numeric|min:0|max:100',
            'massa_magra' => 'nullable|numeric|min:0|max:100',
            'acqua_corporea' => 'nullable|numeric|min:0|max:100',
            'metabolismo_basale' => 'nullable|numeric|min:500|max:5000',
            'massa_ossea' => 'nullable|numeric|min:0|max:10',
            'grasso_viscerale' => 'nullable|numeric|min:0|max:30',
            'proteine' => 'nullable|numeric|min:0|max:50',
            'eta_metabolica' => 'nullable|integer|min:10|max:120',
            'note' => 'nullable|string|max:1000',
        ]);

        try {
            $parametro = ParametroCorporeo::where('cliente_id', $cliente_id)
                                          ->findOrFail($parametro_id);

            $parametro->update($validated);

            return redirect()->route('admin.clienti.parametri-corporei.index', $cliente_id)
                           ->with('success', 'Parametri aggiornati con successo!');

        } catch (\Exception $e) {
            \Log::error('Errore aggiornamento parametri: ' . $e->getMessage());

            return back()->withInput()
                       ->with('error', 'Errore durante l\'aggiornamento.');
        }
    }

    /**
     * Elimina parametro
     */
    public function destroy($cliente_id, $parametro_id)
    {
        try {
            $parametro = ParametroCorporeo::where('cliente_id', $cliente_id)
                                          ->findOrFail($parametro_id);

            $parametro->delete();

            return back()->with('success', 'Parametro eliminato con successo!');

        } catch (\Exception $e) {
            \Log::error('Errore eliminazione parametro: ' . $e->getMessage());

            return back()->with('error', 'Errore durante l\'eliminazione.');
        }
    }

    /**
     * Calcola variazione peso tra prima e ultima rilevazione
     */
    private function calcolaVariazionePeso($cliente)
    {
        if ($cliente->parametriCorporei->count() < 2) {
            return null;
        }

        $primo = $cliente->parametriCorporei->last();
        $ultimo = $cliente->parametriCorporei->first();

        $variazione = $ultimo->peso - $primo->peso;

        return [
            'valore' => round($variazione, 2),
            'percentuale' => round(($variazione / $primo->peso) * 100, 2),
            'tipo' => $variazione >= 0 ? 'aumento' : 'diminuzione',
        ];
    }

    /**
     * Prepara dati per grafici evoluzione
     */
    private function preparaDatiGrafici($cliente)
    {
        $parametri = $cliente->parametriCorporei()->orderBy('data_rilevazione', 'asc')->get();

        return [
            'date' => $parametri->pluck('data_rilevazione')->map(fn($d) => $d->format('d/m/Y'))->toArray(),
            'peso' => $parametri->pluck('peso')->toArray(),
            'bmi' => $parametri->pluck('bmi')->toArray(),
            'massa_grassa' => $parametri->pluck('massa_grassa')->toArray(),
            'massa_magra' => $parametri->pluck('massa_magra')->toArray(),
            'circonferenza_vita' => $parametri->pluck('circonferenza_vita')->toArray(),
        ];
    }
}
