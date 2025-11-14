<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lezione;
use App\Models\Professionista;
use App\Models\Sede;
use Illuminate\Http\Request;
use Carbon\Carbon;

/**
 * Controller: Gestione Calendario Visuale
 * Visualizzazione e gestione lezioni in formato calendario
 */
class CalendarioController extends Controller
{
    /**
     * Mostra il calendario visuale
     */
    public function index(Request $request)
    {
        // Ottieni professionisti per filtro
        $professionisti = Professionista::with('utente')
            ->where('stato', 'attivo')
            ->orderBy('cognome')
            ->orderBy('nome')
            ->get();

        // Ottieni sedi per filtro
        $sedi = Sede::where('attiva', true)
            ->orderBy('nome')
            ->get();

        return view('admin.calendario.index', compact('professionisti', 'sedi'));
    }

    /**
     * API: Ottieni eventi per il calendario (formato FullCalendar)
     */
    public function getEvents(Request $request)
    {
        $query = Lezione::with(['professionista', 'sede', 'programma'])
            ->where('visibile_calendario', true);

        // Filtro per date (richiesto da FullCalendar)
        if ($request->has('start') && $request->has('end')) {
            $start = Carbon::parse($request->start)->startOfDay();
            $end = Carbon::parse($request->end)->endOfDay();
            $query->whereBetween('data', [$start, $end]);
        }

        // Filtro per professionista
        if ($request->filled('professionista_id')) {
            $query->where('professionista_id', $request->professionista_id);
        }

        // Filtro per sede
        if ($request->filled('sede_id')) {
            $query->where('sede_id', $request->sede_id);
        }

        // Filtro per tipologia
        if ($request->filled('tipologia')) {
            $query->where('tipologia', $request->tipologia);
        }

        // Filtro per stato
        if ($request->filled('stato')) {
            $query->where('stato', $request->stato);
        }

        $lezioni = $query->orderBy('data')
            ->orderBy('ora_inizio')
            ->get();

        // Converti in formato FullCalendar
        $events = $lezioni->map(function ($lezione) {
            return [
                'id' => $lezione->id,
                'title' => $this->formatEventTitle($lezione),
                'start' => $lezione->data->format('Y-m-d') . 'T' . Carbon::parse($lezione->ora_inizio)->format('H:i:s'),
                'end' => $lezione->data->format('Y-m-d') . 'T' . Carbon::parse($lezione->ora_fine)->format('H:i:s'),
                'backgroundColor' => $this->getColorByType($lezione->tipologia),
                'borderColor' => $this->getBorderColorByStatus($lezione->stato),
                'textColor' => '#ffffff',
                'extendedProps' => [
                    'tipologia' => $lezione->tipologia,
                    'stato' => $lezione->stato,
                    'professionista' => $lezione->professionista ? $lezione->professionista->nome . ' ' . $lezione->professionista->cognome : 'Non assegnato',
                    'sede' => $lezione->sede ? $lezione->sede->nome : 'Non specificata',
                    'posti' => $lezione->posti_occupati . '/' . $lezione->posti_totali,
                    'descrizione' => $lezione->descrizione,
                    'programma' => $lezione->programma ? $lezione->programma->nome : null,
                ],
            ];
        });

        return response()->json($events);
    }

    /**
     * Formatta il titolo dell'evento per il calendario
     */
    private function formatEventTitle($lezione)
    {
        $title = $lezione->titolo;

        // Aggiungi posti se gruppo
        if ($lezione->tipologia === 'gruppo') {
            $title .= " ({$lezione->posti_occupati}/{$lezione->posti_totali})";
        }

        return $title;
    }

    /**
     * Ottieni colore in base alla tipologia
     */
    private function getColorByType($tipologia)
    {
        return match($tipologia) {
            'gruppo' => '#9c27b0',      // viola-magia
            'individuale' => '#e91e63', // fucsia-magia
            'online' => '#2196f3',      // blue
            'ibrida' => '#ff9800',      // orange
            default => '#757575',       // gray
        };
    }

    /**
     * Ottieni colore bordo in base allo stato
     */
    private function getBorderColorByStatus($stato)
    {
        return match($stato) {
            'programmata' => '#ffa726',   // orange
            'confermata' => '#66bb6a',    // green
            'in_corso' => '#42a5f5',      // blue
            'completata' => '#26a69a',    // teal
            'cancellata' => '#ef5350',    // red
            'rinviata' => '#ffa726',      // orange
            default => '#9e9e9e',         // gray
        };
    }

    /**
     * Mostra dettagli lezione in modal
     */
    public function show($id)
    {
        $lezione = Lezione::with([
            'professionista',
            'sede',
            'programma',
            'clienti'
        ])->findOrFail($id);

        return response()->json([
            'lezione' => $lezione,
            'html' => view('admin.calendario.partials.modal-dettagli', compact('lezione'))->render()
        ]);
    }
}
