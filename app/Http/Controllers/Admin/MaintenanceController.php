<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lezione;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Controller: Manutenzione Database
 * Operazioni di correzione e manutenzione del database
 */
class MaintenanceController extends Controller
{
    /**
     * Pagina principale manutenzione
     */
    public function index()
    {
        // Conta le lezioni con problemi
        $lezioniSenzaVisibilita = Lezione::whereNull('visibile_calendario')
            ->orWhere('visibile_calendario', false)
            ->count();

        return view('admin.maintenance.index', compact('lezioniSenzaVisibilita'));
    }

    /**
     * Correggi visibilità calendario per tutte le lezioni
     */
    public function fixVisibilitaCalendario(Request $request)
    {
        try {
            // Conta prima dell'aggiornamento
            $lezioniDaAggiornare = Lezione::whereNull('visibile_calendario')
                ->orWhere('visibile_calendario', false)
                ->count();

            if ($lezioniDaAggiornare === 0) {
                return redirect()->route('admin.maintenance.index')
                    ->with('info', 'Nessuna lezione da aggiornare. Tutte le lezioni sono già visibili nel calendario.');
            }

            // Aggiorna tutte le lezioni
            DB::table('lezioni')
                ->whereNull('visibile_calendario')
                ->orWhere('visibile_calendario', false)
                ->update(['visibile_calendario' => true, 'updated_at' => now()]);

            // Verifica il risultato
            $lezioniAggiornate = $lezioniDaAggiornare;
            $totalLezioni = Lezione::count();
            $lezioniVisibili = Lezione::where('visibile_calendario', true)->count();

            return redirect()->route('admin.maintenance.index')
                ->with('success', "✅ Operazione completata con successo!

                📊 Riepilogo:
                • Lezioni aggiornate: {$lezioniAggiornate}
                • Totale lezioni: {$totalLezioni}
                • Lezioni ora visibili: {$lezioniVisibili}

                Tutte le lezioni sono ora visibili nel calendario.");

        } catch (\Exception $e) {
            return redirect()->route('admin.maintenance.index')
                ->with('error', 'Errore durante l\'aggiornamento: ' . $e->getMessage());
        }
    }

    /**
     * Verifica integrità database
     */
    public function verificaIntegrita()
    {
        try {
            $problemi = [];

            // 1. Lezioni senza visibilità calendario
            $lezioniSenzaVisibilita = Lezione::whereNull('visibile_calendario')
                ->orWhere('visibile_calendario', false)
                ->count();
            if ($lezioniSenzaVisibilita > 0) {
                $problemi[] = [
                    'tipo' => 'warning',
                    'categoria' => 'Calendario',
                    'descrizione' => "{$lezioniSenzaVisibilita} lezioni non sono visibili nel calendario",
                    'azione' => 'fix-visibilita'
                ];
            }

            // 2. Lezioni con posti occupati ma senza clienti
            $lezioniIncoerenti = Lezione::where('posti_occupati', '>', 0)
                ->whereDoesntHave('clienti')
                ->count();
            if ($lezioniIncoerenti > 0) {
                $problemi[] = [
                    'tipo' => 'error',
                    'categoria' => 'Prenotazioni',
                    'descrizione' => "{$lezioniIncoerenti} lezioni hanno posti occupati ma nessun cliente associato",
                    'azione' => null
                ];
            }

            // 3. Lezioni con clienti ma posti_occupati = 0
            $lezioniPostiSbagliati = Lezione::has('clienti')
                ->where('posti_occupati', 0)
                ->count();
            if ($lezioniPostiSbagliati > 0) {
                $problemi[] = [
                    'tipo' => 'error',
                    'categoria' => 'Prenotazioni',
                    'descrizione' => "{$lezioniPostiSbagliati} lezioni hanno clienti ma posti_occupati è 0",
                    'azione' => null
                ];
            }

            // 4. Lezioni figlie senza padre
            $lezioniFiglieOrfane = Lezione::whereNotNull('lezione_padre_id')
                ->whereDoesntHave('lezionePadre')
                ->count();
            if ($lezioniFiglieOrfane > 0) {
                $problemi[] = [
                    'tipo' => 'warning',
                    'categoria' => 'Ricorrenze',
                    'descrizione' => "{$lezioniFiglieOrfane} lezioni figlie hanno un padre inesistente",
                    'azione' => null
                ];
            }

            return view('admin.maintenance.verifica', compact('problemi'));

        } catch (\Exception $e) {
            return redirect()->route('admin.maintenance.index')
                ->with('error', 'Errore durante la verifica: ' . $e->getMessage());
        }
    }
}
