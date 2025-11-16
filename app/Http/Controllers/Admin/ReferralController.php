<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Referral;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Controller per gestione Sistema Referral "Porta un'Amica" - Area Admin
 */
class ReferralController extends Controller
{
    /**
     * Dashboard referral con statistiche
     */
    public function index(Request $request)
    {
        // Statistiche generali
        $statistiche = [
            'totale_inviti' => Referral::count(),
            'inviti_pending' => Referral::where('stato', 'pending')->count(),
            'inviti_registrati' => Referral::where('stato', 'registrato')->count(),
            'inviti_convertiti' => Referral::where('stato', 'convertito')->count(),
            'tasso_conversione' => 0,
            'clienti_attivi_referral' => Referral::distinct('cliente_invitante_id')->count(),
        ];

        // Calcola tasso conversione
        if ($statistiche['totale_inviti'] > 0) {
            $statistiche['tasso_conversione'] = round(
                ($statistiche['inviti_convertiti'] / $statistiche['totale_inviti']) * 100,
                2
            );
        }

        // Top 10 clienti invitanti
        $topInvitanti = Referral::select('cliente_invitante_id', DB::raw('COUNT(*) as totale_inviti'), DB::raw('SUM(CASE WHEN stato = "convertito" THEN 1 ELSE 0 END) as inviti_convertiti'))
            ->groupBy('cliente_invitante_id')
            ->orderByDesc('inviti_convertiti')
            ->orderByDesc('totale_inviti')
            ->limit(10)
            ->with('clienteInvitante')
            ->get();

        // Filtri
        $query = Referral::with(['clienteInvitante', 'clienteInvitato']);

        if ($request->has('stato') && $request->stato != '') {
            $query->where('stato', $request->stato);
        }

        if ($request->has('ricerca') && $request->ricerca != '') {
            $ricerca = $request->ricerca;
            $query->where(function($q) use ($ricerca) {
                $q->where('email_invitato', 'like', "%{$ricerca}%")
                  ->orWhere('codice_invito', 'like', "%{$ricerca}%")
                  ->orWhereHas('clienteInvitante', function($q2) use ($ricerca) {
                      $q2->where('nome', 'like', "%{$ricerca}%")
                         ->orWhere('cognome', 'like', "%{$ricerca}%")
                         ->orWhere('email', 'like', "%{$ricerca}%");
                  });
            });
        }

        $referrals = $query->latest('data_invito')->paginate(50);

        return view('admin.referral.index', compact('statistiche', 'topInvitanti', 'referrals'));
    }

    /**
     * Visualizza dettagli referral specifico
     */
    public function show($id)
    {
        $referral = Referral::with(['clienteInvitante', 'clienteInvitato'])->findOrFail($id);

        return view('admin.referral.show', compact('referral'));
    }

    /**
     * Report conversioni
     */
    public function report(Request $request)
    {
        $dataInizio = $request->get('data_inizio', now()->subMonths(3));
        $dataFine = $request->get('data_fine', now());

        // Conversioni per mese
        $conversioniPerMese = Referral::where('stato', 'convertito')
            ->whereBetween('data_conversione', [$dataInizio, $dataFine])
            ->select(DB::raw('DATE_FORMAT(data_conversione, "%Y-%m") as mese'), DB::raw('COUNT(*) as totale'))
            ->groupBy('mese')
            ->orderBy('mese')
            ->get();

        // Statistiche periodo
        $statistichePeriodo = [
            'inviti_inviati' => Referral::whereBetween('data_invito', [$dataInizio, $dataFine])->count(),
            'conversioni' => Referral::where('stato', 'convertito')
                ->whereBetween('data_conversione', [$dataInizio, $dataFine])
                ->count(),
            'valore_sconti_erogati' => Referral::where('stato', 'convertito')
                ->whereBetween('data_conversione', [$dataInizio, $dataFine])
                ->sum(DB::raw('sconto_invitante + sconto_invitato')),
        ];

        return view('admin.referral.report', compact('conversioniPerMese', 'statistichePeriodo', 'dataInizio', 'dataFine'));
    }

    /**
     * Visualizza referral di un cliente specifico
     */
    public function cliente($clienteId)
    {
        $cliente = Cliente::findOrFail($clienteId);

        $referrals = Referral::where('cliente_invitante_id', $clienteId)
            ->with('clienteInvitato')
            ->latest('data_invito')
            ->get();

        $statistiche = [
            'totale_inviti' => $referrals->count(),
            'pending' => $referrals->where('stato', 'pending')->count(),
            'registrati' => $referrals->where('stato', 'registrato')->count(),
            'convertiti' => $referrals->where('stato', 'convertito')->count(),
            'totale_sconti' => $referrals->where('stato', 'convertito')->sum('sconto_invitante'),
        ];

        return view('admin.referral.cliente', compact('cliente', 'referrals', 'statistiche'));
    }

    /**
     * Marca manualmente come convertito
     */
    public function marcaConvertito($id)
    {
        try {
            $referral = Referral::findOrFail($id);

            if ($referral->stato == 'convertito') {
                return response()->json([
                    'success' => false,
                    'message' => 'Questo referral è già stato convertito'
                ], 400);
            }

            $referral->marcaConvertito();

            return response()->json([
                'success' => true,
                'message' => 'Referral marcato come convertito con successo'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Errore: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Elimina referral
     */
    public function destroy($id)
    {
        try {
            $referral = Referral::findOrFail($id);
            $referral->delete();

            return response()->json([
                'success' => true,
                'message' => 'Referral eliminato con successo'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Errore: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export CSV
     */
    public function export(Request $request)
    {
        $query = Referral::with(['clienteInvitante', 'clienteInvitato']);

        if ($request->has('stato') && $request->stato != '') {
            $query->where('stato', $request->stato);
        }

        $referrals = $query->get();

        $filename = 'referral_porta_un_amica_' . now()->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
        ];

        $callback = function() use ($referrals) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($file, [
                'ID', 'Cliente Invitante', 'Email Invitante', 'Email Invitato', 'Cliente Invitato',
                'Codice Invito', 'Stato', 'Data Invito', 'Data Registrazione', 'Data Conversione',
                'Sconto Invitante', 'Sconto Invitato', 'Sconto Applicato'
            ]);

            foreach ($referrals as $ref) {
                fputcsv($file, [
                    $ref->id,
                    $ref->clienteInvitante->nome_completo ?? '',
                    $ref->clienteInvitante->email ?? '',
                    $ref->email_invitato,
                    $ref->clienteInvitato->nome_completo ?? 'Non registrato',
                    $ref->codice_invito,
                    $ref->stato,
                    $ref->data_invito ? $ref->data_invito->format('d/m/Y H:i') : '',
                    $ref->data_registrazione ? $ref->data_registrazione->format('d/m/Y H:i') : '',
                    $ref->data_conversione ? $ref->data_conversione->format('d/m/Y H:i') : '',
                    $ref->sconto_invitante,
                    $ref->sconto_invitato,
                    $ref->sconto_applicato ? 'Sì' : 'No'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Statistiche avanzate
     */
    public function statistiche()
    {
        // Conversioni ultimi 12 mesi
        $conversioniAnno = Referral::where('stato', 'convertito')
            ->where('data_conversione', '>=', now()->subYear())
            ->select(DB::raw('DATE_FORMAT(data_conversione, "%Y-%m") as mese'), DB::raw('COUNT(*) as totale'))
            ->groupBy('mese')
            ->orderBy('mese')
            ->get();

        // Performance per cliente
        $performanceClienti = Referral::select(
                'cliente_invitante_id',
                DB::raw('COUNT(*) as totale_inviti'),
                DB::raw('SUM(CASE WHEN stato = "pending" THEN 1 ELSE 0 END) as pending'),
                DB::raw('SUM(CASE WHEN stato = "registrato" THEN 1 ELSE 0 END) as registrati'),
                DB::raw('SUM(CASE WHEN stato = "convertito" THEN 1 ELSE 0 END) as convertiti'),
                DB::raw('SUM(sconto_invitante) as totale_sconti')
            )
            ->groupBy('cliente_invitante_id')
            ->having('totale_inviti', '>', 0)
            ->with('clienteInvitante')
            ->get();

        // Media giorni da invito a conversione
        $mediaGiorniConversione = Referral::where('stato', 'convertito')
            ->whereNotNull('data_invito')
            ->whereNotNull('data_conversione')
            ->get()
            ->avg(function($ref) {
                return $ref->data_invito->diffInDays($ref->data_conversione);
            });

        return view('admin.referral.statistiche', compact('conversioniAnno', 'performanceClienti', 'mediaGiorniConversione'));
    }
}
