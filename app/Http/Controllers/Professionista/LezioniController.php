<?php

namespace App\Http\Controllers\Professionista;

use App\Http\Controllers\Controller;
use App\Models\Lezione;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Controller: Lezioni Professionista
 * Funzione: Gestisce l'agenda personale del professionista
 * - Visualizza solo le lezioni assegnate al professionista
 * - Permette gestione presenze per le proprie lezioni
 * - Visualizza partecipanti iscritti
 */
class LezioniController extends Controller
{
    /**
     * Lista lezioni del professionista (filtrate)
     */
    public function index(Request $request)
    {
        $professionistaId = Auth::id();

        // Query base con filtro professionista
        $query = Lezione::with(['programma', 'sede'])
            ->where('professionista_id', $professionistaId);

        // Filtro per data
        if ($request->filled('data_da')) {
            $query->where('data', '>=', $request->data_da);
        }

        if ($request->filled('data_a')) {
            $query->where('data', '<=', $request->data_a);
        } else {
            // Default: mostra lezioni future e ultime 7 giorni
            $query->where('data', '>=', now()->subDays(7));
        }

        // Filtro per sede
        if ($request->filled('sede_id')) {
            $query->where('sede_id', $request->sede_id);
        }

        // Filtro per programma
        if ($request->filled('programma_id')) {
            $query->where('programma_id', $request->programma_id);
        }

        // Filtro per stato
        if ($request->filled('stato')) {
            $query->where('stato', $request->stato);
        }

        // Ordina per data e ora
        $lezioni = $query->orderBy('data', 'desc')
            ->orderBy('ora_inizio', 'desc')
            ->paginate(20);

        // Aggiungi conteggio partecipanti per ogni lezione
        foreach ($lezioni as $lezione) {
            $lezione->numero_partecipanti = DB::table('cliente_lezione')
                ->where('lezione_id', $lezione->id)
                ->whereIn('stato', ['prenotata', 'presente'])
                ->count();

            $lezione->numero_presenze = DB::table('cliente_lezione')
                ->where('lezione_id', $lezione->id)
                ->where('stato', 'presente')
                ->count();
        }

        // Statistiche rapide
        $stats = [
            'totale' => Lezione::where('professionista_id', $professionistaId)->count(),
            'oggi' => Lezione::where('professionista_id', $professionistaId)
                ->whereDate('data', now()->toDateString())
                ->count(),
            'settimana' => Lezione::where('professionista_id', $professionistaId)
                ->whereBetween('data', [now()->startOfWeek(), now()->endOfWeek()])
                ->count(),
            'mese' => Lezione::where('professionista_id', $professionistaId)
                ->whereMonth('data', now()->month)
                ->whereYear('data', now()->year)
                ->count(),
        ];

        // Ottieni sedi e programmi per i filtri (solo quelli del professionista)
        $sedi = Lezione::where('professionista_id', $professionistaId)
            ->whereNotNull('sede_id')
            ->with('sede')
            ->get()
            ->pluck('sede')
            ->unique('id');

        $programmi = Lezione::where('professionista_id', $professionistaId)
            ->whereNotNull('programma_id')
            ->with('programma')
            ->get()
            ->pluck('programma')
            ->unique('id');

        return view('professionista.lezioni.index', compact(
            'lezioni',
            'stats',
            'sedi',
            'programmi'
        ));
    }

    /**
     * Visualizza dettagli lezione con partecipanti
     */
    public function show($id)
    {
        $professionistaId = Auth::id();

        // Trova la lezione (solo se appartiene al professionista)
        $lezione = Lezione::with(['programma', 'sede'])
            ->where('id', $id)
            ->where('professionista_id', $professionistaId)
            ->firstOrFail();

        // Ottieni i partecipanti con il loro stato
        $partecipanti = DB::table('cliente_lezione')
            ->join('utenti', 'cliente_lezione.cliente_id', '=', 'utenti.id')
            ->where('cliente_lezione.lezione_id', $id)
            ->select(
                'utenti.*',
                'cliente_lezione.stato',
                'cliente_lezione.check_in_time',
                'cliente_lezione.check_out_time',
                'cliente_lezione.note'
            )
            ->get();

        return view('professionista.lezioni.show', compact('lezione', 'partecipanti'));
    }

    /**
     * Gestione presenze per una lezione
     */
    public function gestionePresenze($id)
    {
        $professionistaId = Auth::id();

        // Trova la lezione (solo se appartiene al professionista)
        $lezione = Lezione::with(['programma', 'sede'])
            ->where('id', $id)
            ->where('professionista_id', $professionistaId)
            ->firstOrFail();

        // Ottieni i partecipanti
        $partecipanti = DB::table('cliente_lezione')
            ->join('utenti', 'cliente_lezione.cliente_id', '=', 'utenti.id')
            ->where('cliente_lezione.lezione_id', $id)
            ->select(
                'utenti.*',
                'cliente_lezione.stato',
                'cliente_lezione.check_in_time',
                'cliente_lezione.check_out_time'
            )
            ->get();

        return view('professionista.lezioni.presenze', compact('lezione', 'partecipanti'));
    }

    /**
     * Check-in di un partecipante
     */
    public function checkIn($lezioneId, $clienteId)
    {
        $professionistaId = Auth::id();

        // Verifica che la lezione appartenga al professionista
        $lezione = Lezione::where('id', $lezioneId)
            ->where('professionista_id', $professionistaId)
            ->firstOrFail();

        // Effettua il check-in
        DB::table('cliente_lezione')
            ->where('lezione_id', $lezioneId)
            ->where('cliente_id', $clienteId)
            ->update([
                'stato' => 'presente',
                'check_in_time' => now(),
                'updated_at' => now()
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Check-in effettuato con successo'
        ]);
    }

    /**
     * Check-out di un partecipante
     */
    public function checkOut($lezioneId, $clienteId)
    {
        $professionistaId = Auth::id();

        // Verifica che la lezione appartenga al professionista
        $lezione = Lezione::where('id', $lezioneId)
            ->where('professionista_id', $professionistaId)
            ->firstOrFail();

        // Effettua il check-out
        DB::table('cliente_lezione')
            ->where('lezione_id', $lezioneId)
            ->where('cliente_id', $clienteId)
            ->update([
                'check_out_time' => now(),
                'updated_at' => now()
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Check-out effettuato con successo'
        ]);
    }

    /**
     * Segna un partecipante come assente
     */
    public function segnaAssente($lezioneId, $clienteId)
    {
        $professionistaId = Auth::id();

        // Verifica che la lezione appartenga al professionista
        $lezione = Lezione::where('id', $lezioneId)
            ->where('professionista_id', $professionistaId)
            ->firstOrFail();

        // Segna come assente
        DB::table('cliente_lezione')
            ->where('lezione_id', $lezioneId)
            ->where('cliente_id', $clienteId)
            ->update([
                'stato' => 'assente',
                'updated_at' => now()
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Partecipante segnato come assente'
        ]);
    }

    /**
     * Annulla assenza (ripristina stato prenotata)
     */
    public function annullaAssenza($lezioneId, $clienteId)
    {
        $professionistaId = Auth::id();

        // Verifica che la lezione appartenga al professionista
        $lezione = Lezione::where('id', $lezioneId)
            ->where('professionista_id', $professionistaId)
            ->firstOrFail();

        // Ripristina a prenotata
        DB::table('cliente_lezione')
            ->where('lezione_id', $lezioneId)
            ->where('cliente_id', $clienteId)
            ->update([
                'stato' => 'prenotata',
                'check_in_time' => null,
                'check_out_time' => null,
                'updated_at' => now()
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Assenza annullata'
        ]);
    }
}
