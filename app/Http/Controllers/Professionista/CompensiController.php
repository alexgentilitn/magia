<?php

namespace App\Http\Controllers\Professionista;

use App\Http\Controllers\Controller;
use App\Models\Lezione;
use App\Models\PagamentoProfessionista;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Controller: Compensi Professionista
 * Funzione: Gestisce visualizzazione compensi e storico pagamenti
 * - Compenso totale maturato
 * - Compenso mensile
 * - Storico pagamenti
 * - Dettagli per periodo
 */
class CompensiController extends Controller
{
    /**
     * Dashboard compensi
     */
    public function index()
    {
        $utente = Auth::user();
        $professionistaId = $utente->id;
        $professionista = $utente->professionista;

        // Verifica che il professionista abbia un profilo completo
        if (!$professionista) {
            return redirect()->route('professionista.dashboard')
                ->with('error', 'Profilo professionista non trovato. Contatta l\'amministratore per completare la configurazione.');
        }

        $tariffaOraria = $professionista?->tariffa_oraria ?? 0;

        // Calcola compenso totale
        $lezioniCompletate = Lezione::where('professionista_id', $professionistaId)
            ->where('stato', 'completata')
            ->where('data', '<', now())
            ->get();

        $compensoTotale = $lezioniCompletate->sum(function($lezione) use ($tariffaOraria) {
            $ore = ($lezione->durata_minuti ?? 60) / 60;
            return $ore * $tariffaOraria;
        });

        // Compenso mese corrente
        $lezioniMeseCorrente = Lezione::where('professionista_id', $professionistaId)
            ->where('stato', 'completata')
            ->whereMonth('data', now()->month)
            ->whereYear('data', now()->year)
            ->where('data', '<', now())
            ->get();

        $compensoMeseCorrente = $lezioniMeseCorrente->sum(function($lezione) use ($tariffaOraria) {
            $ore = ($lezione->durata_minuti ?? 60) / 60;
            return $ore * $tariffaOraria;
        });

        // Compenso mese precedente
        $lezioniMesePrecedente = Lezione::where('professionista_id', $professionistaId)
            ->where('stato', 'completata')
            ->whereMonth('data', now()->subMonth()->month)
            ->whereYear('data', now()->subMonth()->year)
            ->get();

        $compensoMesePrecedente = $lezioniMesePrecedente->sum(function($lezione) use ($tariffaOraria) {
            $ore = ($lezione->durata_minuti ?? 60) / 60;
            return $ore * $tariffaOraria;
        });

        // Statistiche
        $totaleLezioniCompletate = $lezioniCompletate->count();
        $lezioniMeseCorrente = $lezioniMeseCorrente->count();
        $totaleOreInsegnate = $lezioniCompletate->sum('durata_minuti') / 60;

        // Pagamenti effettivi ricevuti
        $pagamentiRicevuti = PagamentoProfessionista::where('utente_id', $professionistaId)
            ->completati()
            ->get();

        $totalePagato = $pagamentiRicevuti->sum('importo_netto');
        $totaleRitenute = $pagamentiRicevuti->sum('ritenuta_fiscale');
        $numeroPagamenti = $pagamentiRicevuti->count();

        // Differenza tra compenso maturato e pagato
        $compensoDaPagare = $compensoTotale - $pagamentiRicevuti->sum('importo_maturato');

        // Pagamento mese corrente
        $pagamentoMeseCorrente = PagamentoProfessionista::where('utente_id', $professionistaId)
            ->completati()
            ->whereMonth('data_pagamento', now()->month)
            ->whereYear('data_pagamento', now()->year)
            ->sum('importo_netto');

        // Compensi per mese (ultimi 12 mesi) - CON PAGAMENTI
        $compensiPerMese = collect();
        for ($i = 11; $i >= 0; $i--) {
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

            // Pagamenti ricevuti nel mese
            $pagatoMese = PagamentoProfessionista::where('utente_id', $professionistaId)
                ->completati()
                ->whereMonth('data_pagamento', $data->month)
                ->whereYear('data_pagamento', $data->year)
                ->sum('importo_netto');

            $compensiPerMese->push([
                'mese' => $data->format('M Y'),
                'anno' => $data->year,
                'mese_num' => $data->month,
                'totale' => $totale,
                'pagato' => $pagatoMese,
                'lezioni' => $lezioniMese->count(),
            ]);
        }

        // Ultimi pagamenti ricevuti (per sidebar)
        $ultimiPagamenti = PagamentoProfessionista::where('utente_id', $professionistaId)
            ->completati()
            ->orderBy('data_pagamento', 'desc')
            ->limit(5)
            ->get();

        return view('professionista.compensi.index', compact(
            'professionista',
            'compensoTotale',
            'compensoMeseCorrente',
            'compensoMesePrecedente',
            'totaleLezioniCompletate',
            'lezioniMeseCorrente',
            'totaleOreInsegnate',
            'tariffaOraria',
            'compensiPerMese',
            'totalePagato',
            'totaleRitenute',
            'numeroPagamenti',
            'compensoDaPagare',
            'pagamentoMeseCorrente',
            'ultimiPagamenti'
        ));
    }

    /**
     * Storico pagamenti ricevuti
     */
    public function storico()
    {
        $utente = Auth::user();
        $professionistaId = $utente->id;

        // Ottieni tutti i pagamenti ricevuti
        $pagamenti = PagamentoProfessionista::where('utente_id', $professionistaId)
            ->orderBy('data_pagamento', 'desc')
            ->get();

        // Statistiche totali
        $totaleVersato = $pagamenti->where('stato', 'completato')->sum('importo_netto');
        $totaleRitenute = $pagamenti->where('stato', 'completato')->sum('ritenuta_fiscale');
        $numeroCompletati = $pagamenti->where('stato', 'completato')->count();
        $numeroInAttesa = $pagamenti->where('stato', 'in_attesa')->count();

        // Pagamenti per anno
        $pagamentiPerAnno = $pagamenti->groupBy(function($pagamento) {
            return $pagamento->data_pagamento->format('Y');
        });

        return view('professionista.compensi.storico', compact(
            'utente',
            'pagamenti',
            'totaleVersato',
            'totaleRitenute',
            'numeroCompletati',
            'numeroInAttesa',
            'pagamentiPerAnno'
        ));
    }

    /**
     * Dettaglio compensi per periodo specifico
     */
    public function dettaglioPeriodo($anno, $mese)
    {
        $utente = Auth::user();
        $professionistaId = $utente->id;
        $professionista = $utente->professionista;

        // Verifica che il professionista abbia un profilo completo
        if (!$professionista) {
            return redirect()->route('professionista.dashboard')
                ->with('error', 'Profilo professionista non trovato. Contatta l\'amministratore per completare la configurazione.');
        }

        $tariffaOraria = $professionista?->tariffa_oraria ?? 0;

        // Ottieni tutte le lezioni completate nel periodo
        $lezioni = Lezione::with(['programma', 'sede'])
            ->where('professionista_id', $professionistaId)
            ->where('stato', 'completata')
            ->whereMonth('data', $mese)
            ->whereYear('data', $anno)
            ->where('data', '<', now())
            ->orderBy('data', 'desc')
            ->get();

        // Calcola compenso per ogni lezione
        foreach ($lezioni as $lezione) {
            $ore = ($lezione->durata_minuti ?? 60) / 60;
            $lezione->compenso = $ore * $tariffaOraria;

            // Conta partecipanti presenti
            $lezione->presenze = DB::table('cliente_lezione')
                ->where('lezione_id', $lezione->id)
                ->where('stato', 'presente')
                ->count();
        }

        $compensoTotale = $lezioni->sum('compenso');
        $totaleLezioni = $lezioni->count();
        $totaleOre = $lezioni->sum('durata_minuti') / 60;

        $periodo = \Carbon\Carbon::create($anno, $mese, 1)->format('F Y');

        return view('professionista.compensi.periodo', compact(
            'lezioni',
            'compensoTotale',
            'totaleLezioni',
            'totaleOre',
            'tariffaOraria',
            'periodo',
            'anno',
            'mese'
        ));
    }
}
