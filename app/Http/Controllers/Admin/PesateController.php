<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pesata;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * Controller: Gestione Pesate (Admin)
 * Gestisce CRUD pesate per i clienti
 */
class PesateController extends Controller
{
    /**
     * Lista pesate di un cliente con grafici
     */
    public function index($cliente_id)
    {
        $cliente = Cliente::findOrFail($cliente_id);

        $pesate = Pesata::where('cliente_id', $cliente_id)
                       ->orderBy('data_rilevazione', 'desc')
                       ->get();

        $statistiche = Pesata::getStatistiche($cliente_id);
        $grafici = Pesata::getDatiGrafici($cliente_id);

        return view('admin.pesate.index', compact('cliente', 'pesate', 'statistiche', 'grafici'));
    }

    /**
     * Form per aggiungere nuova pesata
     */
    public function create($cliente_id)
    {
        $cliente = Cliente::findOrFail($cliente_id);

        // Lista sedi disponibili
        $sedi = ['Calliano', 'Darè', 'Pieve di Bono', 'Riva', 'Trento'];

        return view('admin.pesate.create', compact('cliente', 'sedi'));
    }

    /**
     * Salva nuova pesata
     */
    public function store(Request $request, $cliente_id)
    {
        $cliente = Cliente::findOrFail($cliente_id);

        $validator = Validator::make($request->all(), [
            'data_rilevazione' => 'required|date',
            'peso' => 'required|numeric|min:20|max:300',
            'bmi' => 'nullable|numeric|min:10|max:60',
            'peso_corporeo_senza_grassi' => 'nullable|numeric|min:0|max:200',
            'muscolo_scheletrico' => 'nullable|numeric|min:0|max:100',
            'grasso_corporeo' => 'nullable|numeric|min:0|max:100',
            'grasso_sottocutaneo' => 'nullable|numeric|min:0|max:100',
            'grasso_viscerale' => 'nullable|integer|min:0|max:30',
            'acqua_corporea' => 'nullable|numeric|min:0|max:100',
            'massa_muscolare' => 'nullable|numeric|min:0|max:200',
            'massa_ossea' => 'nullable|numeric|min:0|max:10',
            'proteine' => 'nullable|numeric|min:0|max:100',
            'bmr' => 'nullable|integer|min:500|max:5000',
            'eta_metabolica' => 'nullable|integer|min:10|max:120',
            'sede' => 'nullable|string|max:100',
            'note' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                           ->withErrors($validator)
                           ->withInput();
        }

        $data = $request->all();
        $data['cliente_id'] = $cliente_id;

        Pesata::create($data);

        return redirect()->route('admin.clienti.pesate.index', $cliente_id)
                       ->with('success', 'Pesata aggiunta con successo!');
    }

    /**
     * Form per modificare una pesata
     */
    public function edit($cliente_id, $pesata_id)
    {
        $cliente = Cliente::findOrFail($cliente_id);
        $pesata = Pesata::where('id', $pesata_id)
                       ->where('cliente_id', $cliente_id)
                       ->firstOrFail();

        $sedi = ['Calliano', 'Darè', 'Pieve di Bono', 'Riva', 'Trento'];

        return view('admin.pesate.edit', compact('cliente', 'pesata', 'sedi'));
    }

    /**
     * Aggiorna una pesata
     */
    public function update(Request $request, $cliente_id, $pesata_id)
    {
        $cliente = Cliente::findOrFail($cliente_id);
        $pesata = Pesata::where('id', $pesata_id)
                       ->where('cliente_id', $cliente_id)
                       ->firstOrFail();

        $validator = Validator::make($request->all(), [
            'data_rilevazione' => 'required|date',
            'peso' => 'required|numeric|min:20|max:300',
            'bmi' => 'nullable|numeric|min:10|max:60',
            'peso_corporeo_senza_grassi' => 'nullable|numeric|min:0|max:200',
            'muscolo_scheletrico' => 'nullable|numeric|min:0|max:100',
            'grasso_corporeo' => 'nullable|numeric|min:0|max:100',
            'grasso_sottocutaneo' => 'nullable|numeric|min:0|max:100',
            'grasso_viscerale' => 'nullable|integer|min:0|max:30',
            'acqua_corporea' => 'nullable|numeric|min:0|max:100',
            'massa_muscolare' => 'nullable|numeric|min:0|max:200',
            'massa_ossea' => 'nullable|numeric|min:0|max:10',
            'proteine' => 'nullable|numeric|min:0|max:100',
            'bmr' => 'nullable|integer|min:500|max:5000',
            'eta_metabolica' => 'nullable|integer|min:10|max:120',
            'sede' => 'nullable|string|max:100',
            'note' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                           ->withErrors($validator)
                           ->withInput();
        }

        $pesata->update($request->all());

        return redirect()->route('admin.clienti.pesate.index', $cliente_id)
                       ->with('success', 'Pesata aggiornata con successo!');
    }

    /**
     * Elimina una pesata
     */
    public function destroy($cliente_id, $pesata_id)
    {
        $pesata = Pesata::where('id', $pesata_id)
                       ->where('cliente_id', $cliente_id)
                       ->firstOrFail();

        $pesata->delete();

        return redirect()->route('admin.clienti.pesate.index', $cliente_id)
                       ->with('success', 'Pesata eliminata con successo!');
    }

    /**
     * Lista tutti i clienti con pesate
     */
    public function listaClientiConPesate(Request $request)
    {
        $query = Cliente::query();

        // Ricerca
        if ($request->filled('cerca')) {
            $cerca = $request->cerca;
            $query->where(function($q) use ($cerca) {
                $q->where('nome', 'like', "%$cerca%")
                  ->orWhere('cognome', 'like', "%$cerca%")
                  ->orWhere('email', 'like', "%$cerca%");
            });
        }

        $clienti = $query->orderBy('cognome', 'asc')
                        ->orderBy('nome', 'asc')
                        ->get();

        // Aggiungi conteggio pesate
        $clienti->each(function($cliente) {
            $cliente->totale_pesate = Pesata::where('cliente_id', $cliente->id)->count();
            $ultimaPesata = Pesata::where('cliente_id', $cliente->id)
                                 ->orderBy('data_rilevazione', 'desc')
                                 ->first();
            $cliente->ultima_pesata = $ultimaPesata ? $ultimaPesata->data_rilevazione : null;
        });

        $statistiche = [
            'totale' => $clienti->count(),
            'con_pesate' => $clienti->where('totale_pesate', '>', 0)->count(),
            'senza_pesate' => $clienti->where('totale_pesate', 0)->count(),
        ];

        return view('admin.clienti-pesate.index', compact('clienti', 'statistiche'));
    }
}
