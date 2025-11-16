<?php

namespace App\Http\Controllers\Professionista;

use App\Http\Controllers\Controller;
use App\Models\Lezione;
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
        $professionista = Auth::user();
        $professionistaId = $professionista->id;
        $tariffaOraria = $professionista->tariffa_oraria ?? 0;

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

        // Compensi per mese (ultimi 12 mesi)
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

            $compensiPerMese->push([
                'mese' => $data->format('M Y'),
                'anno' => $data->year,
                'mese_num' => $data->month,
                'totale' => $totale,
                'lezioni' => $lezioniMese->count(),
            ]);
        }

        return view('professionista.compensi.index', compact(
            'professionista',
            'compensoTotale',
            'compensoMeseCorrente',
            'compensoMesePrecedente',
            'totaleLezioniCompletate',
            'lezioniMeseCorrente',
            'totaleOreInsegnate',
            'tariffaOraria',
            'compensiPerMese'
        ));
    }

    /**
     * Storico pagamenti (placeholder per future implementazioni)
     */
    public function storico()
    {
        $professionista = Auth::user();

        // Nota: Questa funzionalità richiede una tabella pagamenti_professionisti
        // Per ora mostriamo i compensi calcolati per mese

        return view('professionista.compensi.storico', compact('professionista'));
    }

    /**
     * Dettaglio compensi per periodo specifico
     */
    public function dettaglioPeriodo($anno, $mese)
    {
        $professionista = Auth::user();
        $professionistaId = $professionista->id;
        $tariffaOraria = $professionista->tariffa_oraria ?? 0;

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
