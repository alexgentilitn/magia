<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use App\Models\ConsensoPrivacy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Controller per gestione Privacy e Consensi - Area Cliente
 */
class PrivacyController extends Controller
{
    /**
     * Dashboard consensi cliente
     */
    public function index()
    {
        $cliente = Auth::user()->cliente;

        if (!$cliente) {
            abort(403, 'Utente non autorizzato');
        }

        // Consensi raggruppati per tipo
        $consensi = $cliente->consensiPrivacy()
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('tipo_consenso');

        // Stato consensi attuali
        $stato_consensi = [
            'privacy_policy' => $cliente->haConsenso('privacy_policy'),
            'trattamento_dati' => $cliente->haConsenso('trattamento_dati'),
            'marketing' => $cliente->haConsenso('marketing'),
            'profilazione' => $cliente->haConsenso('profilazione'),
            'cookie_analytics' => $cliente->haConsenso('cookie_analytics'),
            'cookie_marketing' => $cliente->haConsenso('cookie_marketing'),
        ];

        return view('cliente.privacy.index', compact('cliente', 'consensi', 'stato_consensi'));
    }

    /**
     * Aggiorna consensi cliente
     */
    public function update(Request $request)
    {
        $cliente = Auth::user()->cliente;

        if (!$cliente) {
            return response()->json([
                'success' => false,
                'message' => 'Cliente non trovato'
            ], 404);
        }

        $validated = $request->validate([
            'marketing' => 'nullable|boolean',
            'profilazione' => 'nullable|boolean',
            'cookie_analytics' => 'nullable|boolean',
            'cookie_marketing' => 'nullable|boolean',
        ]);

        try {
            // Registra ogni consenso modificato
            foreach ($validated as $tipo => $valore) {
                $cliente->registraConsenso($tipo, $valore ?? false);
            }

            return response()->json([
                'success' => true,
                'message' => 'Preferenze privacy aggiornate con successo'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Errore durante l\'aggiornamento: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Revoca tutti i consensi (Diritto all'oblio parziale)
     */
    public function revocaAll(Request $request)
    {
        $cliente = Auth::user()->cliente;

        if (!$cliente) {
            return response()->json([
                'success' => false,
                'message' => 'Cliente non trovato'
            ], 404);
        }

        $motivazione = $request->input('motivazione', 'Revoca volontaria da area cliente');

        try {
            // Revoca tutti i consensi attivi
            $cliente->consensiPrivacy()
                ->where('stato', 'attivo')
                ->update([
                    'stato' => 'revocato',
                    'data_revoca' => now(),
                    'note' => $motivazione
                ]);

            // Aggiorna anche i flag nel record cliente
            $cliente->update([
                'marketing_accettato' => false,
                'consenso_privacy' => false,
                'consenso_marketing' => false,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Tutti i consensi sono stati revocati'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Errore durante la revoca: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Esporta dati personali (GDPR - Portabilità)
     */
    public function exportDati()
    {
        $cliente = Auth::user()->cliente;

        if (!$cliente) {
            abort(404, 'Cliente non trovato');
        }

        // Prepara dati completi del cliente
        $dati = [
            'anagrafica' => [
                'nome' => $cliente->nome,
                'cognome' => $cliente->cognome,
                'email' => $cliente->email,
                'telefono' => $cliente->telefono_mobile,
                'codice_fiscale' => $cliente->codice_fiscale,
                'data_nascita' => $cliente->data_nascita?->format('d/m/Y'),
                'indirizzo' => $cliente->indirizzo_via . ', ' . $cliente->indirizzo_citta . ' ' . $cliente->indirizzo_cap,
            ],
            'programma' => [
                'programma_attuale' => $cliente->programma_attuale,
                'stato' => $cliente->stato_programma,
                'data_iscrizione' => $cliente->data_iscrizione?->format('d/m/Y'),
            ],
            'misure' => [
                'peso' => $cliente->peso_iniziale,
                'altezza' => $cliente->altezza,
                'imc' => $cliente->imc,
            ],
            'consensi' => $cliente->consensiPrivacy->map(function($c) {
                return [
                    'tipo' => $c->tipo_consenso_label,
                    'consenso_dato' => $c->consenso_dato ? 'Sì' : 'No',
                    'data' => $c->data_consenso->format('d/m/Y H:i'),
                    'stato' => $c->stato,
                ];
            })->toArray()
        ];

        $filename = 'dati_personali_' . $cliente->codice_cliente . '_' . now()->format('Y-m-d') . '.json';

        return response()->json($dati)
            ->header('Content-Disposition', "attachment; filename=\"$filename\"")
            ->header('Content-Type', 'application/json');
    }

    /**
     * Richiesta cancellazione account (Diritto all'oblio)
     */
    public function richiestaCancellazione(Request $request)
    {
        $cliente = Auth::user()->cliente;

        if (!$cliente) {
            return response()->json([
                'success' => false,
                'message' => 'Cliente non trovato'
            ], 404);
        }

        $validated = $request->validate([
            'motivazione' => 'nullable|string|max:500'
        ]);

        try {
            // Marca il cliente come "richiesta cancellazione"
            $cliente->update([
                'stato_cliente' => 'richiesta_cancellazione',
                'note_interne' => 'RICHIESTA CANCELLAZIONE DATI GDPR - ' . now()->format('d/m/Y H:i') .
                    "\nMotivazione: " . ($validated['motivazione'] ?? 'Non specificata')
            ]);

            // Revoca tutti i consensi
            $cliente->consensiPrivacy()
                ->where('stato', 'attivo')
                ->update([
                    'stato' => 'revocato',
                    'data_revoca' => now(),
                    'note' => 'Richiesta cancellazione account GDPR'
                ]);

            // TODO: Inviare email notifica agli admin

            return response()->json([
                'success' => true,
                'message' => 'Richiesta di cancellazione inviata. Sarai contattata entro 30 giorni.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Errore durante la richiesta: ' . $e->getMessage()
            ], 500);
        }
    }
}
