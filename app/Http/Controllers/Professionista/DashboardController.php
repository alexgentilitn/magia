<?php

namespace App\Http\Controllers\Professionista;

use App\Http\Controllers\Controller;
use App\Models\Lezione;
use App\Models\Sede;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Controller: Dashboard Professionista
 * Funzione: Gestisce la dashboard del professionista con statistiche personalizzate
 * - Visualizzazione compensi (totale, mensile, storico)
 * - Numero lezioni effettuate
 * - Sedi di lavoro
 * - Dettaglio lezioni con partecipanti
 */
class DashboardController extends Controller
{
    /**
     * Mostra la dashboard professionista
     */
    public function index()
    {
        try {
            $utente = Auth::user();
            $professionistaId = $utente->id;

            // Carica il profilo professionista con i dettagli estesi
            $professionista = $utente->professionista;

            // ==========================================
            // STATISTICHE LEZIONI (solo proprie)
            // ==========================================
            $totaleLezioni = Lezione::where('professionista_id', $professionistaId)->count();

            $lezioniOggi = Lezione::where('professionista_id', $professionistaId)
                ->whereDate('data', now()->toDateString())
                ->count();

            $lezioniSettimana = Lezione::where('professionista_id', $professionistaId)
                ->whereBetween('data', [
                    now()->startOfWeek(),
                    now()->endOfWeek()
                ])->count();

            $lezioniMese = Lezione::where('professionista_id', $professionistaId)
                ->whereMonth('data', now()->month)
                ->whereYear('data', now()->year)
                ->count();

            // ==========================================
            // SEDI DI LAVORO (dove ha tenuto lezioni)
            // ==========================================
            $sediLavoro = Lezione::where('professionista_id', $professionistaId)
                ->whereNotNull('sede_id')
                ->distinct()
                ->pluck('sede_id');

            $sedi = Sede::whereIn('id', $sediLavoro)->get();
            $totaleSedi = $sedi->count();

            // ==========================================
            // COMPENSI - CALCOLO BASATO SU TARIFFA
            // ==========================================
            // Nota: Il compenso si basa sulla tariffa_oraria del professionista
            // Se non esiste un profilo professionista, usa 0 come default
            $tariffaOraria = $professionista->tariffa_oraria ?? 0;

            // Calcola compenso totale (lezioni completate)
            $lezioniCompletate = Lezione::where('professionista_id', $professionistaId)
                ->where('stato', 'completata')
                ->where('data', '<', now())
                ->get();

            $compensoTotale = $lezioniCompletate->sum(function($lezione) use ($tariffaOraria) {
                // Calcola ore basandosi su durata_minuti
                $ore = ($lezione->durata_minuti ?? 60) / 60;
                return $ore * $tariffaOraria;
            });

            // Calcola compenso mese corrente
            $lezioniMeseCompletate = Lezione::where('professionista_id', $professionistaId)
                ->where('stato', 'completata')
                ->whereMonth('data', now()->month)
                ->whereYear('data', now()->year)
                ->where('data', '<', now())
                ->get();

            $compensoMese = $lezioniMeseCompletate->sum(function($lezione) use ($tariffaOraria) {
                $ore = ($lezione->durata_minuti ?? 60) / 60;
                return $ore * $tariffaOraria;
            });

            // ==========================================
            // PARTECIPANTI TOTALI (presenze)
            // ==========================================
            $totalePartecipanti = DB::table('cliente_lezione')
                ->join('lezioni', 'cliente_lezione.lezione_id', '=', 'lezioni.id')
                ->where('lezioni.professionista_id', $professionistaId)
                ->where('cliente_lezione.stato', 'presente')
                ->count();

            // Media partecipanti per lezione
            $mediaPartecipanti = $totaleLezioni > 0
                ? round($totalePartecipanti / $totaleLezioni, 1)
                : 0;

            // ==========================================
            // PROSSIME LEZIONI (oggi e prossimi 7 giorni)
            // ==========================================
            $prossimeLezioni = Lezione::with(['programma', 'sede'])
                ->where('professionista_id', $professionistaId)
                ->whereBetween('data', [now(), now()->addDays(7)])
                ->orderBy('data')
                ->orderBy('ora_inizio')
                ->limit(10)
                ->get();

            // Aggiungi conteggio partecipanti per ogni lezione
            foreach ($prossimeLezioni as $lezione) {
                $lezione->numero_partecipanti = DB::table('cliente_lezione')
                    ->where('lezione_id', $lezione->id)
                    ->whereIn('stato', ['prenotata', 'presente'])
                    ->count();
            }

            // ==========================================
            // LEZIONI RECENTI (ultime 5 completate)
            // ==========================================
            $lezioniRecenti = Lezione::with(['programma', 'sede'])
                ->where('professionista_id', $professionistaId)
                ->where('data', '<', now())
                ->orderBy('data', 'desc')
                ->orderBy('ora_inizio', 'desc')
                ->limit(5)
                ->get();

            // Aggiungi conteggio presenze per ogni lezione recente
            foreach ($lezioniRecenti as $lezione) {
                $lezione->numero_presenze = DB::table('cliente_lezione')
                    ->where('lezione_id', $lezione->id)
                    ->where('stato', 'presente')
                    ->count();
            }

            // ==========================================
            // GRAFICO LEZIONI ULTIMI 6 MESI
            // ==========================================
            $lezioniMesi = collect();
            for ($i = 5; $i >= 0; $i--) {
                $data = now()->subMonths($i);
                $totale = Lezione::where('professionista_id', $professionistaId)
                    ->whereMonth('data', $data->month)
                    ->whereYear('data', $data->year)
                    ->count();

                $lezioniMesi->push([
                    'mese' => $data->format('M Y'),
                    'totale' => $totale
                ]);
            }

            // ==========================================
            // GRAFICO COMPENSI ULTIMI 6 MESI
            // ==========================================
            $compensiMesi = collect();
            for ($i = 5; $i >= 0; $i--) {
                $data = now()->subMonths($i);
                $lezioniMese = Lezione::where('professionista_id', $professionistaId)
                    ->where('stato', 'completata')
                    ->whereMonth('data', $data->month)
                    ->whereYear('data', $data->year)
                    ->where('data', '<', now())
                    ->get();

                $totale = $lezioniMese->sum(function($lezione) use ($tariffaOraria) {
                    $ore = ($lezione->durata_minuti ?? 60) / 60;
                    return $ore * $tariffaOraria;
                });

                $compensiMesi->push([
                    'mese' => $data->format('M Y'),
                    'totale' => $totale
                ]);
            }

            // ==========================================
            // GRAFICO PRESENZE ULTIMI 6 MESI
            // ==========================================
            $presenzeMesi = collect();
            for ($i = 5; $i >= 0; $i--) {
                $data = now()->subMonths($i);
                $totale = DB::table('cliente_lezione')
                    ->join('lezioni', 'cliente_lezione.lezione_id', '=', 'lezioni.id')
                    ->where('lezioni.professionista_id', $professionistaId)
                    ->whereMonth('lezioni.data', $data->month)
                    ->whereYear('lezioni.data', $data->year)
                    ->where('cliente_lezione.stato', 'presente')
                    ->count();

                $presenzeMesi->push([
                    'mese' => $data->format('M Y'),
                    'totale' => $totale
                ]);
            }

            // Passa tutti i dati alla view
            return view('professionista.dashboard', compact(
                'utente',
                'professionista',
                'totaleLezioni',
                'lezioniOggi',
                'lezioniSettimana',
                'lezioniMese',
                'sedi',
                'totaleSedi',
                'compensoTotale',
                'compensoMese',
                'tariffaOraria',
                'totalePartecipanti',
                'mediaPartecipanti',
                'prossimeLezioni',
                'lezioniRecenti',
                'lezioniMesi',
                'compensiMesi',
                'presenzeMesi'
            ));

        } catch (\Exception $e) {
            // In caso di errore, mostra dashboard con valori di default
            return view('professionista.dashboard', [
                'utente' => Auth::user(),
                'professionista' => Auth::user()->professionista ?? null,
                'totaleLezioni' => 0,
                'lezioniOggi' => 0,
                'lezioniSettimana' => 0,
                'lezioniMese' => 0,
                'sedi' => collect([]),
                'totaleSedi' => 0,
                'compensoTotale' => 0,
                'compensoMese' => 0,
                'tariffaOraria' => 0,
                'totalePartecipanti' => 0,
                'mediaPartecipanti' => 0,
                'prossimeLezioni' => collect([]),
                'lezioniRecenti' => collect([]),
                'lezioniMesi' => collect([]),
                'compensiMesi' => collect([]),
                'presenzeMesi' => collect([])
            ]);
        }
    }
}
