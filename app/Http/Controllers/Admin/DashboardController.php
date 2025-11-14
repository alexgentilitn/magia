<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Utente;
use App\Models\Lezione;
use App\Models\Programma;
use App\Models\Pagamento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Controller: Dashboard Amministratore
 * Funzione: Gestisce la dashboard principale con statistiche aggregate
 */
class DashboardController extends Controller
{
    /**
     * Mostra la dashboard amministratore
     */
    public function index()
    {
        try {
            $utente = Auth::user();

            // ==========================================
            // STATISTICHE CLIENTI
            // ==========================================
            $totaleClienti = Utente::where('tipo_utente', 'cliente')->count();
            $clientiAttivi = Utente::where('tipo_utente', 'cliente')
                ->where('stato_cliente', 'attivo')
                ->count();
            $nuoviClientiMese = Utente::where('tipo_utente', 'cliente')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count();

            // ==========================================
            // STATISTICHE LEZIONI
            // ==========================================
            $totaleLezioni = Lezione::count();
            $lezioniFuture = Lezione::where('data', '>=', now())->count();
            $lezioniOggi = Lezione::whereDate('data', now()->toDateString())->count();
            $lezioniSettimana = Lezione::whereBetween('data', [
                now()->startOfWeek(),
                now()->endOfWeek()
            ])->count();

            // ==========================================
            // STATISTICHE PROGRAMMI
            // ==========================================
            $totaleProgrammi = Programma::count();
            $programmiAttivi = Programma::where('attivo', true)->count();
            $programmiInEvidenza = Programma::where('in_evidenza', true)->count();

            // ==========================================
            // STATISTICHE PAGAMENTI
            // ==========================================
            $totalePagamenti = Pagamento::count();
            $pagamentiCompletati = Pagamento::where('stato', 'completato')->count();
            $pagamentiInAttesa = Pagamento::where('stato', 'in_attesa')->count();
            $incassoTotale = Pagamento::where('stato', 'completato')->sum('importo_pagato');
            $incassoMese = Pagamento::where('stato', 'completato')
                ->whereMonth('data_pagamento', now()->month)
                ->whereYear('data_pagamento', now()->year)
                ->sum('importo_pagato');
            $daIncassare = Pagamento::where('stato', 'in_attesa')->sum('importo_residuo');

            // ==========================================
            // PROSSIME LEZIONI (oggi e domani)
            // ==========================================
            $prossimeLezioni = Lezione::with(['programma', 'sede', 'professionista'])
                ->whereBetween('data', [now(), now()->addDays(1)])
                ->orderBy('data')
                ->orderBy('ora_inizio')
                ->limit(5)
                ->get();

            // ==========================================
            // ULTIMI CLIENTI REGISTRATI
            // ==========================================
            $ultimiClienti = Utente::where('tipo_utente', 'cliente')
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();

            // ==========================================
            // PAGAMENTI IN SCADENZA
            // ==========================================
            $pagamentiScadenza = Pagamento::where('stato', 'in_attesa')
                ->where('data_scadenza', '<=', now()->addDays(7))
                ->where('data_scadenza', '>=', now())
                ->with('cliente')
                ->orderBy('data_scadenza')
                ->limit(5)
                ->get();

            // ==========================================
            // GRAFICO INCASSI ULTIMI 6 MESI
            // ==========================================
            $incassiMesi = collect();
            for ($i = 5; $i >= 0; $i--) {
                $data = now()->subMonths($i);
                $totale = Pagamento::where('stato', 'completato')
                    ->whereMonth('data_pagamento', $data->month)
                    ->whereYear('data_pagamento', $data->year)
                    ->sum('importo_pagato');

                $incassiMesi->push([
                    'mese' => $data->format('M Y'),
                    'totale' => $totale
                ]);
            }

            // ==========================================
            // GRAFICO NUOVI CLIENTI ULTIMI 6 MESI
            // ==========================================
            $clientiMesi = collect();
            for ($i = 5; $i >= 0; $i--) {
                $data = now()->subMonths($i);
                $totale = Utente::where('tipo_utente', 'cliente')
                    ->whereMonth('created_at', $data->month)
                    ->whereYear('created_at', $data->year)
                    ->count();

                $clientiMesi->push([
                    'mese' => $data->format('M Y'),
                    'totale' => $totale
                ]);
            }

            // ==========================================
            // ALERT E NOTIFICHE
            // ==========================================
            $certificatiScadenza = Utente::where('tipo_utente', 'cliente')
                ->whereNotNull('certificato_scadenza')
                ->where('certificato_scadenza', '<=', now()->addDays(30))
                ->where('certificato_scadenza', '>=', now())
                ->count();

            // Passa tutti i dati alla view
            return view('admin.dashboard', compact(
                'totaleClienti',
                'clientiAttivi',
                'nuoviClientiMese',
                'totaleLezioni',
                'lezioniFuture',
                'lezioniOggi',
                'lezioniSettimana',
                'totaleProgrammi',
                'programmiAttivi',
                'programmiInEvidenza',
                'totalePagamenti',
                'pagamentiCompletati',
                'pagamentiInAttesa',
                'incassoTotale',
                'incassoMese',
                'daIncassare',
                'prossimeLezioni',
                'ultimiClienti',
                'pagamentiScadenza',
                'incassiMesi',
                'clientiMesi',
                'certificatiScadenza'
            ));

        } catch (\Exception $e) {
            // In caso di errore, mostra dashboard con valori di default
            return view('admin.dashboard', [
                'totaleClienti' => 0,
                'clientiAttivi' => 0,
                'nuoviClientiMese' => 0,
                'totaleLezioni' => 0,
                'lezioniFuture' => 0,
                'lezioniOggi' => 0,
                'lezioniSettimana' => 0,
                'totaleProgrammi' => 0,
                'programmiAttivi' => 0,
                'programmiInEvidenza' => 0,
                'totalePagamenti' => 0,
                'pagamentiCompletati' => 0,
                'pagamentiInAttesa' => 0,
                'incassoTotale' => 0,
                'incassoMese' => 0,
                'daIncassare' => 0,
                'prossimeLezioni' => collect([]),
                'ultimiClienti' => collect([]),
                'pagamentiScadenza' => collect([]),
                'incassiMesi' => collect([]),
                'clientiMesi' => collect([]),
                'certificatiScadenza' => 0
            ]);
        }
    }
}
