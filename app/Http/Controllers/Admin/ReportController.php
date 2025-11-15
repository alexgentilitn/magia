<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lezione;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * Controller: Gestione Report e Statistiche
 * Report presenze, utilizzo, performance
 */
class ReportController extends Controller
{
    /**
     * Dashboard principale report
     */
    public function index(Request $request)
    {
        try {
            // Periodo default: ultimo mese
            $dataInizio = $request->input('data_inizio', now()->subMonth()->format('Y-m-d'));
            $dataFine = $request->input('data_fine', now()->format('Y-m-d'));

            // Statistiche generali
            $statistiche = $this->getStatisticheGenerali($dataInizio, $dataFine);

            // Statistiche presenze
            $statistichePresenze = $this->getStatistichePresenze($dataInizio, $dataFine);

            // Lezioni per stato
            $lezioniPerStato = $this->getLezioniPerStato($dataInizio, $dataFine);

            // Top professionisti
            $topProfessionisti = $this->getTopProfessionisti($dataInizio, $dataFine);

            // Top clienti più attivi
            $topClienti = $this->getTopClienti($dataInizio, $dataFine);

            // Trend giornaliero
            $trendGiornaliero = $this->getTrendGiornaliero($dataInizio, $dataFine);

            return view('admin.report.index', compact(
                'statistiche',
                'statistichePresenze',
                'lezioniPerStato',
                'topProfessionisti',
                'topClienti',
                'trendGiornaliero',
                'dataInizio',
                'dataFine'
            ));
        } catch (\Exception $e) {
            \Log::error('Errore nel caricamento report: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());

            return redirect()->route('admin.dashboard')
                ->with('error', 'Errore nel caricamento dei report: ' . $e->getMessage());
        }
    }

    /**
     * Debug report - Vista semplificata senza JavaScript
     */
    public function debug(Request $request)
    {
        try {
            $dataInizio = $request->input('data_inizio', now()->subMonth()->format('Y-m-d'));
            $dataFine = $request->input('data_fine', now()->format('Y-m-d'));

            $statistiche = $this->getStatisticheGenerali($dataInizio, $dataFine);
            $statistichePresenze = $this->getStatistichePresenze($dataInizio, $dataFine);
            $lezioniPerStato = $this->getLezioniPerStato($dataInizio, $dataFine);
            $topProfessionisti = $this->getTopProfessionisti($dataInizio, $dataFine);
            $topClienti = $this->getTopClienti($dataInizio, $dataFine);
            $trendGiornaliero = $this->getTrendGiornaliero($dataInizio, $dataFine);

            return view('admin.report.debug', compact(
                'statistiche',
                'statistichePresenze',
                'lezioniPerStato',
                'topProfessionisti',
                'topClienti',
                'trendGiornaliero',
                'dataInizio',
                'dataFine'
            ));
        } catch (\Exception $e) {
            \Log::error('Errore caricamento debug report: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());

            return view('admin.report.debug-simple', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Statistiche generali
     */
    private function getStatisticheGenerali($dataInizio, $dataFine)
    {
        $totaleLezioni = Lezione::whereBetween('data', [$dataInizio, $dataFine])->count();

        $lezioniCompletate = Lezione::whereBetween('data', [$dataInizio, $dataFine])
            ->where('stato', 'completata')
            ->count();

        $totalePartecipanti = DB::table('cliente_lezione')
            ->join('lezioni', 'cliente_lezione.lezione_id', '=', 'lezioni.id')
            ->whereBetween('lezioni.data', [$dataInizio, $dataFine])
            ->where('cliente_lezione.stato', '!=', 'lista_attesa')
            ->count();

        $postiTotali = Lezione::whereBetween('data', [$dataInizio, $dataFine])
            ->sum('posti_totali');

        $postiOccupati = Lezione::whereBetween('data', [$dataInizio, $dataFine])
            ->sum('posti_occupati');

        $tassoOccupazione = $postiTotali > 0 ? round(($postiOccupati / $postiTotali) * 100, 1) : 0;

        return [
            'totale_lezioni' => $totaleLezioni,
            'lezioni_completate' => $lezioniCompletate,
            'totale_partecipanti' => $totalePartecipanti,
            'posti_totali' => $postiTotali,
            'posti_occupati' => $postiOccupati,
            'tasso_occupazione' => $tassoOccupazione,
        ];
    }

    /**
     * Statistiche presenze
     */
    private function getStatistichePresenze($dataInizio, $dataFine)
    {
        $presenti = DB::table('cliente_lezione')
            ->join('lezioni', 'cliente_lezione.lezione_id', '=', 'lezioni.id')
            ->whereBetween('lezioni.data', [$dataInizio, $dataFine])
            ->where('cliente_lezione.stato', 'presente')
            ->count();

        $assenti = DB::table('cliente_lezione')
            ->join('lezioni', 'cliente_lezione.lezione_id', '=', 'lezioni.id')
            ->whereBetween('lezioni.data', [$dataInizio, $dataFine])
            ->where('cliente_lezione.stato', 'assente')
            ->count();

        $prenotati = DB::table('cliente_lezione')
            ->join('lezioni', 'cliente_lezione.lezione_id', '=', 'lezioni.id')
            ->whereBetween('lezioni.data', [$dataInizio, $dataFine])
            ->whereIn('cliente_lezione.stato', ['prenotato', 'confermato'])
            ->count();

        $totale = $presenti + $assenti;
        $tassoPresenza = $totale > 0 ? round(($presenti / $totale) * 100, 1) : 0;

        return [
            'presenti' => $presenti,
            'assenti' => $assenti,
            'prenotati' => $prenotati,
            'tasso_presenza' => $tassoPresenza,
        ];
    }

    /**
     * Lezioni per stato
     */
    private function getLezioniPerStato($dataInizio, $dataFine)
    {
        return Lezione::whereBetween('data', [$dataInizio, $dataFine])
            ->select('stato', DB::raw('count(*) as totale'))
            ->groupBy('stato')
            ->pluck('totale', 'stato')
            ->toArray();
    }

    /**
     * Top professionisti per numero lezioni
     */
    private function getTopProfessionisti($dataInizio, $dataFine, $limit = 5)
    {
        return DB::table('lezioni')
            ->join('utenti', 'lezioni.professionista_id', '=', 'utenti.id')
            ->whereBetween('lezioni.data', [$dataInizio, $dataFine])
            ->select(
                'lezioni.professionista_id',
                'utenti.nome as professionista_nome',
                'utenti.cognome as professionista_cognome',
                DB::raw('count(*) as totale_lezioni'),
                DB::raw('sum(lezioni.posti_occupati) as totale_partecipanti'),
                DB::raw('avg(lezioni.posti_occupati) as media_partecipanti')
            )
            ->groupBy('lezioni.professionista_id', 'utenti.nome', 'utenti.cognome')
            ->orderByDesc('totale_lezioni')
            ->limit($limit)
            ->get();
    }

    /**
     * Top clienti più attivi
     */
    private function getTopClienti($dataInizio, $dataFine, $limit = 10)
    {
        return DB::table('cliente_lezione')
            ->join('lezioni', 'cliente_lezione.lezione_id', '=', 'lezioni.id')
            ->join('clienti', 'cliente_lezione.cliente_id', '=', 'clienti.id')
            ->whereBetween('lezioni.data', [$dataInizio, $dataFine])
            ->whereIn('cliente_lezione.stato', ['presente', 'prenotato', 'confermato'])
            ->select(
                'clienti.id',
                'clienti.nome',
                'clienti.cognome',
                DB::raw('count(*) as totale_partecipazioni'),
                DB::raw('sum(case when cliente_lezione.stato = "presente" then 1 else 0 end) as presenze'),
                DB::raw('sum(case when cliente_lezione.stato = "assente" then 1 else 0 end) as assenze')
            )
            ->groupBy('clienti.id', 'clienti.nome', 'clienti.cognome')
            ->orderByDesc('totale_partecipazioni')
            ->limit($limit)
            ->get();
    }

    /**
     * Trend giornaliero partecipazioni
     */
    private function getTrendGiornaliero($dataInizio, $dataFine)
    {
        return Lezione::whereBetween('data', [$dataInizio, $dataFine])
            ->select(
                'data',
                DB::raw('count(*) as totale_lezioni'),
                DB::raw('sum(posti_occupati) as totale_partecipanti')
            )
            ->groupBy('data')
            ->orderBy('data')
            ->get();
    }

    /**
     * Report presenze dettagliato
     */
    public function presenze(Request $request)
    {
        $dataInizio = $request->input('data_inizio', now()->subMonth()->format('Y-m-d'));
        $dataFine = $request->input('data_fine', now()->format('Y-m-d'));
        $professionistaId = $request->input('professionista_id');
        $sedeId = $request->input('sede_id');

        $query = DB::table('cliente_lezione')
            ->join('lezioni', 'cliente_lezione.lezione_id', '=', 'lezioni.id')
            ->join('clienti', 'cliente_lezione.cliente_id', '=', 'clienti.id')
            ->whereBetween('lezioni.data', [$dataInizio, $dataFine]);

        if ($professionistaId) {
            $query->where('lezioni.professionista_id', $professionistaId);
        }

        if ($sedeId) {
            $query->where('lezioni.sede_id', $sedeId);
        }

        $presenze = $query->select(
                'lezioni.data',
                'lezioni.titolo as lezione_titolo',
                'clienti.nome',
                'clienti.cognome',
                'cliente_lezione.stato',
                'cliente_lezione.check_in',
                'cliente_lezione.check_out'
            )
            ->orderBy('lezioni.data', 'desc')
            ->paginate(50);

        return view('admin.report.presenze', compact('presenze', 'dataInizio', 'dataFine'));
    }

    /**
     * Report performance professionisti
     */
    public function professionisti(Request $request)
    {
        $dataInizio = $request->input('data_inizio', now()->subMonth()->format('Y-m-d'));
        $dataFine = $request->input('data_fine', now()->format('Y-m-d'));

        $performance = DB::table('lezioni')
            ->join('utenti', 'lezioni.professionista_id', '=', 'utenti.id')
            ->whereBetween('lezioni.data', [$dataInizio, $dataFine])
            ->select(
                'lezioni.professionista_id',
                'utenti.nome as professionista_nome',
                'utenti.cognome as professionista_cognome',
                DB::raw('count(*) as totale_lezioni'),
                DB::raw('sum(lezioni.posti_totali) as posti_totali'),
                DB::raw('sum(lezioni.posti_occupati) as posti_occupati'),
                DB::raw('round(avg(lezioni.posti_occupati / lezioni.posti_totali * 100), 1) as tasso_occupazione'),
                DB::raw('count(case when lezioni.stato = "completata" then 1 end) as lezioni_completate'),
                DB::raw('count(case when lezioni.stato = "cancellata" then 1 end) as lezioni_cancellate')
            )
            ->groupBy('lezioni.professionista_id', 'utenti.nome', 'utenti.cognome')
            ->orderByDesc('totale_lezioni')
            ->get();

        return view('admin.report.professionisti', compact('performance', 'dataInizio', 'dataFine'));
    }

    /**
     * Export report in formato CSV
     */
    public function exportCsv(Request $request)
    {
        $tipo = $request->input('tipo', 'generale');
        $dataInizio = $request->input('data_inizio', now()->subMonth()->format('Y-m-d'));
        $dataFine = $request->input('data_fine', now()->format('Y-m-d'));

        $filename = "report_{$tipo}_" . now()->format('Y-m-d') . ".csv";

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($tipo, $dataInizio, $dataFine) {
            $file = fopen('php://output', 'w');

            switch ($tipo) {
                case 'presenze':
                    $this->exportPresenzeCsv($file, $dataInizio, $dataFine);
                    break;
                case 'professionisti':
                    $this->exportProfessionistiCsv($file, $dataInizio, $dataFine);
                    break;
                default:
                    $this->exportGeneraleCsv($file, $dataInizio, $dataFine);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export presenze CSV
     */
    private function exportPresenzeCsv($file, $dataInizio, $dataFine)
    {
        fputcsv($file, ['Data', 'Lezione', 'Cliente', 'Stato', 'Check-in', 'Check-out']);

        $presenze = DB::table('cliente_lezione')
            ->join('lezioni', 'cliente_lezione.lezione_id', '=', 'lezioni.id')
            ->join('clienti', 'cliente_lezione.cliente_id', '=', 'clienti.id')
            ->whereBetween('lezioni.data', [$dataInizio, $dataFine])
            ->select(
                'lezioni.data',
                'lezioni.titolo',
                'clienti.nome',
                'clienti.cognome',
                'cliente_lezione.stato',
                'cliente_lezione.check_in',
                'cliente_lezione.check_out'
            )
            ->orderBy('lezioni.data')
            ->get();

        foreach ($presenze as $presenza) {
            fputcsv($file, [
                $presenza->data,
                $presenza->titolo,
                $presenza->nome . ' ' . $presenza->cognome,
                $presenza->stato,
                $presenza->check_in ?: '-',
                $presenza->check_out ?: '-',
            ]);
        }
    }

    /**
     * Export professionisti CSV
     */
    private function exportProfessionistiCsv($file, $dataInizio, $dataFine)
    {
        fputcsv($file, ['Professionista', 'Totale Lezioni', 'Posti Totali', 'Posti Occupati', 'Tasso Occupazione %']);

        $performance = DB::table('lezioni')
            ->join('utenti', 'lezioni.professionista_id', '=', 'utenti.id')
            ->whereBetween('lezioni.data', [$dataInizio, $dataFine])
            ->select(
                'utenti.nome as professionista_nome',
                'utenti.cognome as professionista_cognome',
                DB::raw('count(*) as totale_lezioni'),
                DB::raw('sum(lezioni.posti_totali) as posti_totali'),
                DB::raw('sum(lezioni.posti_occupati) as posti_occupati'),
                DB::raw('round(avg(lezioni.posti_occupati / lezioni.posti_totali * 100), 1) as tasso_occupazione')
            )
            ->groupBy('utenti.nome', 'utenti.cognome')
            ->get();

        foreach ($performance as $perf) {
            fputcsv($file, [
                $perf->professionista_nome . ' ' . $perf->professionista_cognome,
                $perf->totale_lezioni,
                $perf->posti_totali,
                $perf->posti_occupati,
                $perf->tasso_occupazione,
            ]);
        }
    }

    /**
     * Export generale CSV
     */
    private function exportGeneraleCsv($file, $dataInizio, $dataFine)
    {
        fputcsv($file, ['Report Generale - Periodo: ' . $dataInizio . ' - ' . $dataFine]);
        fputcsv($file, []);

        $statistiche = $this->getStatisticheGenerali($dataInizio, $dataFine);
        fputcsv($file, ['Metrica', 'Valore']);
        fputcsv($file, ['Totale Lezioni', $statistiche['totale_lezioni']]);
        fputcsv($file, ['Lezioni Completate', $statistiche['lezioni_completate']]);
        fputcsv($file, ['Totale Partecipanti', $statistiche['totale_partecipanti']]);
        fputcsv($file, ['Tasso Occupazione %', $statistiche['tasso_occupazione']]);
    }
}
