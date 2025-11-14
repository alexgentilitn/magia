<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\Utente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Controller: Dashboard Amministratore
 * Funzione: Gestisce la dashboard principale per amministratori
 */
class DashboardController extends Controller
{
    /**
     * Mostra la dashboard amministratore
     */
    public function index()
    {
        try {
            // Utente autenticato
            $utente = Auth::user();

            // === STATISTICHE CLIENTI ===
            
            // Totale clienti
            $totaleClienti = Cliente::count();
            
            // Clienti attivi (stato_cliente = 'attivo')
            $clientiAttivi = Cliente::where('stato_cliente', 'attivo')->count();
            
            // Nuovi clienti questo mese
            $nuoviMese = Cliente::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count();

            // === CLIENTI PER PROGRAMMA ===
            
            $clientiProgramma = Cliente::select('programma_attuale', DB::raw('COUNT(*) as totale'))
                ->where('stato_cliente', 'attivo')
                ->whereNotNull('programma_attuale')
                ->groupBy('programma_attuale')
                ->get()
                ->map(function($item) {
                    return [
                        'programma' => ucfirst(str_replace('_', ' ', $item->programma_attuale)),
                        'totale' => $item->totale
                    ];
                });

            // Se non ci sono programmi, crea array vuoto
            if ($clientiProgramma->isEmpty()) {
                $clientiProgramma = collect([]);
            }

            // === ULTIMI CLIENTI REGISTRATI ===
            
            $ultimiClienti = Cliente::orderBy('created_at', 'desc')
                ->limit(10)
                ->get();

            // === ALERT E NOTIFICHE ===
            
            // Certificati medici in scadenza (prossimi 30 giorni)
            $certificatiScadenza = Cliente::where('certificato_scadenza', '<=', now()->addDays(30))
                ->where('certificato_scadenza', '>=', now())
                ->count();

            // === GRAFICI ANDAMENTO ===
            
            // Iscrizioni ultimi 12 mesi
            $iscrizioniMesi = collect();
            for ($i = 11; $i >= 0; $i--) {
                $data = now()->subMonths($i);
                $totale = Cliente::whereMonth('created_at', $data->month)
                    ->whereYear('created_at', $data->year)
                    ->count();
                
                $iscrizioniMesi->push([
                    'mese' => $data->format('M Y'),
                    'totale' => $totale
                ]);
            }

            // Passa tutti i dati alla view
            return view('admin.dashboard', compact(
                'totaleClienti',
                'clientiAttivi',
                'nuoviMese',
                'certificatiScadenza',
                'ultimiClienti',
                'iscrizioniMesi',
                'clientiProgramma'
            ));

        } catch (\Exception $e) {
            // In caso di errore, mostra dashboard con valori di default
            return view('admin.dashboard', [
                'totaleClienti' => 0,
                'clientiAttivi' => 0,
                'nuoviMese' => 0,
                'certificatiScadenza' => 0,
                'ultimiClienti' => collect([]),
                'iscrizioniMesi' => collect([]),
                'clientiProgramma' => collect([])
            ]);
        }
    }

    /**
     * Ottiene statistiche rapide (AJAX)
     */
    public function statisticheRapide()
    {
        try {
            return response()->json([
                'totale_clienti' => Cliente::count(),
                'clienti_attivi' => Cliente::where('stato_cliente', 'attivo')->count(),
                'nuovi_oggi' => Cliente::whereDate('created_at', now()->today())->count(),
                'nuovi_settimana' => Cliente::whereBetween('created_at', [
                    now()->startOfWeek(),
                    now()->endOfWeek()
                ])->count(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'totale_clienti' => 0,
                'clienti_attivi' => 0,
                'nuovi_oggi' => 0,
                'nuovi_settimana' => 0,
            ]);
        }
    }
}