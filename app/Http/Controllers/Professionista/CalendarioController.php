<?php

namespace App\Http\Controllers\Professionista;

use App\Http\Controllers\Controller;
use App\Models\Lezione;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Controller: Calendario Professionista
 * Funzione: Gestisce il calendario personale del professionista
 * - Mostra solo le lezioni del professionista
 * - Permette gestione rapida presenze
 */
class CalendarioController extends Controller
{
    /**
     * Mostra il calendario
     */
    public function index()
    {
        return view('professionista.calendario.index');
    }

    /**
     * API: Ottieni eventi per il calendario (filtrati per professionista)
     */
    public function getEvents(Request $request)
    {
        $professionistaId = Auth::id();

        // Ottieni le lezioni del professionista nel range richiesto
        $lezioni = Lezione::with(['programma', 'sede'])
            ->where('professionista_id', $professionistaId)
            ->when($request->filled('start'), function($q) use ($request) {
                return $q->where('data', '>=', $request->start);
            })
            ->when($request->filled('end'), function($q) use ($request) {
                return $q->where('data', '<=', $request->end);
            })
            ->get();

        // Formatta per FullCalendar
        $events = $lezioni->map(function($lezione) {
            // Conta partecipanti
            $numeroPartecipanti = DB::table('cliente_lezione')
                ->where('lezione_id', $lezione->id)
                ->whereIn('stato', ['prenotata', 'presente'])
                ->count();

            return [
                'id' => $lezione->id,
                'title' => $lezione->programma->nome ?? 'Lezione',
                'start' => $lezione->data->format('Y-m-d') . 'T' . $lezione->ora_inizio,
                'end' => $lezione->data->format('Y-m-d') . 'T' . $lezione->ora_fine,
                'backgroundColor' => $this->getColorByStato($lezione->stato),
                'borderColor' => $this->getColorByStato($lezione->stato),
                'extendedProps' => [
                    'sede' => $lezione->sede->nome ?? 'Sede non specificata',
                    'programma' => $lezione->programma->nome ?? '',
                    'partecipanti' => $numeroPartecipanti,
                    'stato' => $lezione->stato,
                ],
            ];
        });

        return response()->json($events);
    }

    /**
     * Visualizza dettagli lezione
     */
    public function show($id)
    {
        $professionistaId = Auth::id();

        $lezione = Lezione::with(['programma', 'sede'])
            ->where('id', $id)
            ->where('professionista_id', $professionistaId)
            ->firstOrFail();

        // Ottieni partecipanti
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

        return response()->json([
            'lezione' => $lezione,
            'partecipanti' => $partecipanti
        ]);
    }

    /**
     * Check-in rapido dal calendario
     */
    public function checkIn($lezioneId, $clienteId)
    {
        $professionistaId = Auth::id();

        // Verifica che la lezione appartenga al professionista
        Lezione::where('id', $lezioneId)
            ->where('professionista_id', $professionistaId)
            ->firstOrFail();

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
            'message' => 'Check-in effettuato'
        ]);
    }

    /**
     * Check-out rapido dal calendario
     */
    public function checkOut($lezioneId, $clienteId)
    {
        $professionistaId = Auth::id();

        Lezione::where('id', $lezioneId)
            ->where('professionista_id', $professionistaId)
            ->firstOrFail();

        DB::table('cliente_lezione')
            ->where('lezione_id', $lezioneId)
            ->where('cliente_id', $clienteId)
            ->update([
                'check_out_time' => now(),
                'updated_at' => now()
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Check-out effettuato'
        ]);
    }

    /**
     * Segna assente dal calendario
     */
    public function segnaAssente($lezioneId, $clienteId)
    {
        $professionistaId = Auth::id();

        Lezione::where('id', $lezioneId)
            ->where('professionista_id', $professionistaId)
            ->firstOrFail();

        DB::table('cliente_lezione')
            ->where('lezione_id', $lezioneId)
            ->where('cliente_id', $clienteId)
            ->update([
                'stato' => 'assente',
                'updated_at' => now()
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Segnato come assente'
        ]);
    }

    /**
     * Annulla assenza dal calendario
     */
    public function annullaAssenza($lezioneId, $clienteId)
    {
        $professionistaId = Auth::id();

        Lezione::where('id', $lezioneId)
            ->where('professionista_id', $professionistaId)
            ->firstOrFail();

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

    /**
     * Helper: Ottieni colore in base allo stato
     */
    private function getColorByStato($stato)
    {
        return match($stato) {
            'programmata' => '#3b82f6', // blue
            'in_corso' => '#10b981',    // green
            'completata' => '#6b7280',  // gray
            'cancellata' => '#ef4444',  // red
            default => '#8b5cf6',       // purple
        };
    }
}
