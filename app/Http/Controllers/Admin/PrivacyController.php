<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\ConsensoPrivacy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Controller per gestione Privacy e Consensi GDPR - Area Admin
 */
class PrivacyController extends Controller
{
    /**
     * Visualizza dashboard consensi
     */
    public function index()
    {
        $statistiche = [
            'totale_clienti' => Cliente::count(),
            'con_privacy' => ConsensoPrivacy::tipo('privacy_policy')->attivi()->distinct('cliente_id')->count(),
            'con_marketing' => ConsensoPrivacy::tipo('marketing')->attivi()->distinct('cliente_id')->count(),
            'con_profilazione' => ConsensoPrivacy::tipo('profilazione')->attivi()->distinct('cliente_id')->count(),
            'consensi_totali' => ConsensoPrivacy::attivi()->count(),
            'consensi_revocati' => ConsensoPrivacy::where('stato', 'revocato')->count(),
            'consensi_scaduti' => ConsensoPrivacy::where('stato', 'scaduto')->count(),
        ];

        $consensi_recenti = ConsensoPrivacy::with('cliente')
            ->latest()
            ->limit(20)
            ->get();

        return view('admin.privacy.index', compact('statistiche', 'consensi_recenti'));
    }

    /**
     * Visualizza consensi di un cliente specifico
     */
    public function showCliente($clienteId)
    {
        $cliente = Cliente::with('consensiPrivacy')->findOrFail($clienteId);

        $consensi = $cliente->consensiPrivacy()
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('tipo_consenso');

        return view('admin.privacy.cliente', compact('cliente', 'consensi'));
    }

    /**
     * Report consensi per tipo
     */
    public function report(Request $request)
    {
        $tipo = $request->get('tipo', 'privacy_policy');
        $dataInizio = $request->get('data_inizio', now()->subMonths(3));
        $dataFine = $request->get('data_fine', now());

        $consensi = ConsensoPrivacy::with('cliente')
            ->where('tipo_consenso', $tipo)
            ->whereBetween('data_consenso', [$dataInizio, $dataFine])
            ->orderBy('data_consenso', 'desc')
            ->get();

        $statistiche = [
            'accordati' => $consensi->where('consenso_dato', true)->count(),
            'negati' => $consensi->where('consenso_dato', false)->count(),
            'attivi' => $consensi->where('stato', 'attivo')->count(),
            'revocati' => $consensi->where('stato', 'revocato')->count(),
        ];

        return view('admin.privacy.report', compact('consensi', 'statistiche', 'tipo'));
    }

    /**
     * Export consensi (CSV)
     */
    public function export(Request $request)
    {
        $tipo = $request->get('tipo');

        $query = ConsensoPrivacy::with('cliente');

        if ($tipo) {
            $query->where('tipo_consenso', $tipo);
        }

        $consensi = $query->get();

        $filename = 'consensi_privacy_' . now()->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
        ];

        $callback = function() use ($consensi) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($file, [
                'ID', 'Cliente', 'Email', 'Tipo', 'Consenso', 'Data', 'Stato', 'IP', 'Versione'
            ]);

            foreach ($consensi as $consenso) {
                fputcsv($file, [
                    $consenso->id,
                    $consenso->cliente->nome_completo ?? '',
                    $consenso->cliente->email ?? '',
                    $consenso->tipo_consenso_label,
                    $consenso->consenso_dato ? 'Sì' : 'No',
                    $consenso->data_consenso ? $consenso->data_consenso->format('d/m/Y H:i') : '',
                    $consenso->stato,
                    $consenso->ip_address,
                    $consenso->versione_policy
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Revoca consenso
     */
    public function destroy($id)
    {
        try {
            $consenso = ConsensoPrivacy::findOrFail($id);

            $consenso->update([
                'stato' => 'revocato',
                'data_revoca' => now(),
                'note' => 'Revocato da amministratore'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Consenso revocato con successo'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Errore: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Dashboard conformità GDPR
     */
    public function conformita()
    {
        $clienti_senza_privacy = Cliente::whereDoesntHave('consensiPrivacy', function($query) {
            $query->where('tipo_consenso', 'privacy_policy')
                  ->where('consenso_dato', true)
                  ->where('stato', 'attivo');
        })->get();

        $consensi_scaduti = ConsensoPrivacy::where('stato', 'attivo')
            ->where('data_consenso', '<=', now()->subYears(2))
            ->with('cliente')
            ->get();

        return view('admin.privacy.conformita', compact('clienti_senza_privacy', 'consensi_scaduti'));
    }
}
