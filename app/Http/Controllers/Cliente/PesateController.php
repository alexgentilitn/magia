<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use App\Models\Pesata;
use Illuminate\Http\Request;

/**
 * Controller: Pesate Cliente
 * Visualizzazione pesate per area privata cliente
 */
class PesateController extends Controller
{
    /**
     * Visualizza le pesate del cliente autenticato
     */
    public function index()
    {
        $user = auth()->user();

        // Ottieni l'ID cliente
        $cliente_id = $user->cliente_id ?? $user->id;

        // Ottieni tutte le pesate del cliente
        $pesate = Pesata::where('cliente_id', $cliente_id)
                       ->orderBy('data_rilevazione', 'desc')
                       ->get();

        // Statistiche
        $statistiche = Pesata::getStatistiche($cliente_id);

        // Dati per i grafici
        $grafici = Pesata::getDatiGrafici($cliente_id);

        return view('cliente.pesate.index', compact('pesate', 'statistiche', 'grafici'));
    }
}
