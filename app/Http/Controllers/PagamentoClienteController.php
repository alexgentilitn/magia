<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Utente;
use App\Services\PayPalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

/**
 * Controller: Pagamento Registrazione Cliente
 *
 * Funzione: Gestisce il pagamento della quota di iscrizione
 * - Metodo 1: PayPal (pagamento online immediato)
 * - Metodo 2: Bonifico bancario (upload ricevuta + verifica manuale admin)
 *
 * Flusso:
 * 1. Cliente completa registrazione
 * 2. Sceglie metodo pagamento
 * 3. Se PayPal: redirect PayPal → callback → attivazione account
 * 4. Se Bonifico: upload ricevuta → admin verifica → attivazione account
 */
class PagamentoClienteController extends Controller
{
    protected $paypalService;

    public function __construct(PayPalService $paypalService)
    {
        $this->paypalService = $paypalService;
    }

    /**
     * Mostra pagina scelta metodo pagamento
     * Chiamata dopo registrazione completata
     */
    public function sceltaMetodo($utente_id)
    {
        $utente = Utente::with('cliente')->findOrFail($utente_id);

        // Verifica che sia un cliente e che non abbia già pagato
        if ($utente->tipo_utente !== 'cliente') {
            abort(403, 'Accesso non autorizzato');
        }

        if ($utente->stato_pagamento === 'completato') {
            return redirect()->route('cliente.dashboard')
                ->with('info', 'Il pagamento è già stato completato.');
        }

        // Importo quota iscrizione (da impostazioni o fisso)
        $importo_base = 50.00; // TODO: Prendere da impostazioni sistema
        $commissione_percentuale = 4; // 4% commissione PayPal
        $commissione = round(($importo_base * $commissione_percentuale) / 100, 2);
        $importo_totale = $importo_base + $commissione;
        $descrizione = "Quota iscrizione MA.GIA DONNA";

        return view('registrazione.pagamento', compact('utente', 'importo_base', 'commissione', 'importo_totale', 'descrizione'));
    }

    /**
     * Inizia processo pagamento PayPal
     */
    public function iniziaPayPal(Request $request)
    {
        $validated = $request->validate([
            'utente_id' => 'required|exists:utenti,id',
            'importo' => 'required|numeric|min:0.01',
        ]);

        $utente = Utente::findOrFail($validated['utente_id']);

        // Verifica che non abbia già pagato
        if ($utente->stato_pagamento === 'completato') {
            return redirect()->route('cliente.dashboard')
                ->with('error', 'Il pagamento è già stato completato.');
        }

        try {
            // Crea ordine PayPal
            $order = $this->paypalService->createOrder(
                $validated['importo'],
                "Quota iscrizione MA.GIA DONNA - " . $utente->nome_completo,
                [
                    'utente_id' => $utente->id,
                    'tipo' => 'iscrizione'
                ]
            );

            // Salva ID ordine PayPal nel database
            $utente->update([
                'paypal_order_id' => $order['id'],
                'stato_pagamento' => 'in_attesa',
                'importo_pagamento' => $validated['importo'],
            ]);

            // Redirect a PayPal
            $approveUrl = collect($order['links'])->firstWhere('rel', 'approve')['href'];
            return redirect($approveUrl);

        } catch (\Exception $e) {
            Log::error('Errore creazione ordine PayPal', [
                'utente_id' => $utente->id,
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'Errore durante la creazione del pagamento PayPal. Riprova o usa bonifico bancario.');
        }
    }

    /**
     * Callback success PayPal
     */
    public function paypalSuccess(Request $request)
    {
        $token = $request->query('token'); // PayPal order ID

        try {
            // Trova utente tramite order ID
            $utente = Utente::where('paypal_order_id', $token)->firstOrFail();

            // Cattura pagamento
            $capture = $this->paypalService->captureOrder($token);

            if ($capture['status'] === 'COMPLETED') {
                // Pagamento completato con successo
                DB::transaction(function () use ($utente, $capture) {
                    $utente->update([
                        'stato_pagamento' => 'completato',
                        'metodo_pagamento' => 'paypal',
                        'paypal_transaction_id' => $capture['purchase_units'][0]['payments']['captures'][0]['id'] ?? null,
                        'data_pagamento' => now(),
                        'attivo' => true, // Attiva account
                    ]);

                    // Invia email di conferma
                    $this->inviaEmailConferma($utente);
                });

                return redirect()->route('cliente.dashboard')
                    ->with('success', 'Pagamento completato con successo! Benvenuta in MA.GIA DONNA.');
            }

            throw new \Exception('Pagamento non completato');

        } catch (\Exception $e) {
            Log::error('Errore callback PayPal success', [
                'token' => $token,
                'error' => $e->getMessage()
            ]);

            return redirect()->route('pagamento.scelta', ['utente_id' => $utente->id ?? 0])
                ->with('error', 'Si è verificato un errore durante il completamento del pagamento. Contatta l\'assistenza.');
        }
    }

    /**
     * Callback cancel PayPal
     */
    public function paypalCancel(Request $request)
    {
        $token = $request->query('token');

        try {
            $utente = Utente::where('paypal_order_id', $token)->first();

            if ($utente) {
                $utente->update([
                    'stato_pagamento' => 'annullato',
                ]);
            }

            return redirect()->route('pagamento.scelta', ['utente_id' => $utente->id])
                ->with('warning', 'Pagamento PayPal annullato. Puoi riprovare o scegliere il bonifico bancario.');

        } catch (\Exception $e) {
            return redirect()->route('home')
                ->with('error', 'Sessione scaduta. Riprova ad effettuare l\'accesso.');
        }
    }

    /**
     * Mostra form upload ricevuta bonifico
     */
    public function formBonifico($utente_id)
    {
        $utente = Utente::with('cliente')->findOrFail($utente_id);

        // Verifica che sia un cliente
        if ($utente->tipo_utente !== 'cliente') {
            abort(403, 'Accesso non autorizzato');
        }

        if ($utente->stato_pagamento === 'completato') {
            return redirect()->route('cliente.dashboard')
                ->with('info', 'Il pagamento è già stato completato.');
        }

        // Coordinate bancarie
        $coordinate = [
            'intestatario' => 'MA.GIA DONNA SRL',
            'iban' => 'IT00A0000000000000000000000', // TODO: IBAN reale
            'banca' => 'Banca Esempio',
            'causale' => 'Iscrizione ' . $utente->nome_completo . ' - ID ' . $utente->id,
            'importo' => '50,00',
        ];

        return view('registrazione.bonifico', compact('utente', 'coordinate'));
    }

    /**
     * Salva ricevuta bonifico caricata dal cliente
     */
    public function salvaBonifico(Request $request)
    {
        $validated = $request->validate([
            'utente_id' => 'required|exists:utenti,id',
            'ricevuta' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120', // Max 5MB
            'data_bonifico' => 'required|date|before_or_equal:today',
            'importo_bonifico' => 'required|numeric|min:0.01',
        ], [
            'ricevuta.required' => 'Devi caricare la ricevuta del bonifico',
            'ricevuta.mimes' => 'La ricevuta deve essere in formato PDF, JPG o PNG',
            'ricevuta.max' => 'La ricevuta non può superare 5MB',
            'data_bonifico.required' => 'Inserisci la data del bonifico',
            'data_bonifico.before_or_equal' => 'La data non può essere futura',
            'importo_bonifico.required' => 'Inserisci l\'importo del bonifico',
        ]);

        $utente = Utente::findOrFail($validated['utente_id']);

        try {
            // Salva file ricevuta
            $file = $request->file('ricevuta');
            $nomeFile = 'bonifico_' . $utente->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $percorso = $file->storeAs('bonifici', $nomeFile, 'private');

            // Aggiorna utente
            $utente->update([
                'metodo_pagamento' => 'bonifico',
                'stato_pagamento' => 'in_verifica',
                'ricevuta_bonifico_path' => $percorso,
                'data_bonifico' => $validated['data_bonifico'],
                'importo_pagamento' => $validated['importo_bonifico'],
                'attivo' => false, // Account non ancora attivo fino a verifica
            ]);

            // Notifica admin (TODO: implementare notifica)
            Log::info('Nuova ricevuta bonifico caricata', [
                'utente_id' => $utente->id,
                'importo' => $validated['importo_bonifico'],
            ]);

            return view('registrazione.bonifico-caricato', compact('utente'))
                ->with('success', 'Ricevuta caricata con successo! Riceverai una email appena l\'amministratore avrà verificato il pagamento.');

        } catch (\Exception $e) {
            Log::error('Errore caricamento ricevuta bonifico', [
                'utente_id' => $utente->id,
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'Errore durante il caricamento della ricevuta. Riprova.');
        }
    }

    /**
     * Vista admin: Lista bonifici da verificare
     */
    public function listaVerifiche()
    {
        // Solo admin
        $this->authorize('gestione_pagamenti');

        $bonificiDaVerificare = Utente::where('tipo_utente', 'cliente')
            ->where('stato_pagamento', 'in_verifica')
            ->whereNotNull('ricevuta_bonifico_path')
            ->with('cliente')
            ->orderBy('created_at', 'desc')
            ->get();

        $bonificiVerificati = Utente::where('tipo_utente', 'cliente')
            ->where('metodo_pagamento', 'bonifico')
            ->whereIn('stato_pagamento', ['completato', 'rifiutato'])
            ->with('cliente')
            ->orderBy('data_pagamento', 'desc')
            ->limit(50)
            ->get();

        return view('admin.pagamenti.bonifici', compact('bonificiDaVerificare', 'bonificiVerificati'));
    }

    /**
     * Admin: Approva bonifico
     */
    public function approvaBonifico(Request $request, $utente_id)
    {
        $this->authorize('gestione_pagamenti');

        $utente = Utente::findOrFail($utente_id);

        DB::transaction(function () use ($utente, $request) {
            $utente->update([
                'stato_pagamento' => 'completato',
                'data_pagamento' => now(),
                'attivo' => true,
                'note_verifica_bonifico' => $request->input('note'),
                'verificato_da' => auth()->id(),
            ]);

            // Invia email conferma
            $this->inviaEmailConferma($utente);
        });

        return back()->with('success', 'Bonifico approvato. L\'account è ora attivo e l\'utente ha ricevuto l\'email di conferma.');
    }

    /**
     * Admin: Rifiuta bonifico
     */
    public function rifiutaBonifico(Request $request, $utente_id)
    {
        $this->authorize('gestione_pagamenti');

        $validated = $request->validate([
            'motivo' => 'required|string|max:500',
        ]);

        $utente = Utente::findOrFail($utente_id);

        $utente->update([
            'stato_pagamento' => 'rifiutato',
            'note_verifica_bonifico' => $validated['motivo'],
            'verificato_da' => auth()->id(),
            'attivo' => false,
        ]);

        // Invia email rifiuto (TODO: template email)
        // Mail::to($utente->email)->send(new BonificoRifiutato($utente, $validated['motivo']));

        return back()->with('success', 'Bonifico rifiutato. L\'utente è stato notificato.');
    }

    /**
     * Admin: Visualizza ricevuta bonifico
     */
    public function visualizzaRicevuta($utente_id)
    {
        $this->authorize('gestione_pagamenti');

        $utente = Utente::findOrFail($utente_id);

        if (!$utente->ricevuta_bonifico_path) {
            abort(404, 'Ricevuta non trovata');
        }

        $percorso = storage_path('app/private/' . $utente->ricevuta_bonifico_path);

        if (!file_exists($percorso)) {
            abort(404, 'File ricevuta non trovato');
        }

        return response()->file($percorso);
    }

    /**
     * Invia email di conferma registrazione completata
     */
    private function inviaEmailConferma($utente)
    {
        try {
            // TODO: Usare template email personalizzato dal DB
            Mail::to($utente->email)->send(new \App\Mail\ConfermaRegistrazione($utente));

            Log::info('Email conferma registrazione inviata', [
                'utente_id' => $utente->id,
                'email' => $utente->email
            ]);

        } catch (\Exception $e) {
            Log::error('Errore invio email conferma registrazione', [
                'utente_id' => $utente->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}
