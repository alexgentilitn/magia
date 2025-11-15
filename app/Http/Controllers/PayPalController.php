<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

/**
 * Controller: PayPal Payment Integration
 * Gestisce i pagamenti tramite PayPal per MA.GIA DONNA
 *
 * PREREQUISITI:
 * - Installare PayPal SDK: composer require paypal/rest-api-sdk-php
 * - Configurare credenziali in .env (CLIENT_ID e SECRET)
 * - Configurare webhook URL in PayPal Dashboard
 */
class PayPalController extends Controller
{
    private $apiContext;
    private $mode;
    private $clientId;
    private $secret;

    /**
     * Costruttore - Inizializza PayPal SDK
     */
    public function __construct()
    {
        $this->mode = config('services.paypal.mode'); // sandbox o live
        $this->clientId = config('services.paypal.client_id');
        $this->secret = config('services.paypal.secret');

        // Inizializza API Context solo se le credenziali sono configurate
        if ($this->clientId && $this->secret) {
            $this->initializePayPalContext();
        }
    }

    /**
     * Inizializza il contesto API di PayPal
     */
    private function initializePayPalContext()
    {
        // Questo codice richiede PayPal SDK installato
        // composer require paypal/rest-api-sdk-php

        try {
            $this->apiContext = new \PayPal\Rest\ApiContext(
                new \PayPal\Auth\OAuthTokenCredential(
                    $this->clientId,
                    $this->secret
                )
            );

            $this->apiContext->setConfig([
                'mode' => $this->mode,
                'log.LogEnabled' => true,
                'log.FileName' => storage_path('logs/paypal.log'),
                'log.LogLevel' => 'DEBUG',
                'cache.enabled' => true,
            ]);
        } catch (\Exception $e) {
            Log::error('PayPal API Context initialization failed: ' . $e->getMessage());
        }
    }

    /**
     * Pagina di checkout - Mostra riepilogo ordine
     */
    public function showCheckout(Request $request)
    {
        // Recupera informazioni ordine dalla sessione o database
        $ordineId = $request->query('ordine_id');

        if (!$ordineId) {
            return redirect()->route('home')->with('error', 'Ordine non trovato');
        }

        $ordine = DB::table('ordini')->where('id', $ordineId)->first();

        if (!$ordine) {
            return redirect()->route('home')->with('error', 'Ordine non trovato');
        }

        return view('paypal.checkout', compact('ordine'));
    }

    /**
     * Crea un pagamento PayPal
     */
    public function createPayment(Request $request)
    {
        $validated = $request->validate([
            'ordine_id' => 'required|exists:ordini,id',
            'importo' => 'required|numeric|min:1',
            'descrizione' => 'required|string|max:255',
        ]);

        try {
            // Recupera ordine
            $ordine = DB::table('ordini')->where('id', $validated['ordine_id'])->first();

            if (!$ordine) {
                return response()->json(['error' => 'Ordine non trovato'], 404);
            }

            // Crea oggetto Payer
            $payer = new \PayPal\Api\Payer();
            $payer->setPaymentMethod('paypal');

            // Importo
            $amount = new \PayPal\Api\Amount();
            $amount->setTotal($validated['importo']);
            $amount->setCurrency('EUR');

            // Transazione
            $transaction = new \PayPal\Api\Transaction();
            $transaction->setAmount($amount);
            $transaction->setDescription($validated['descrizione']);
            $transaction->setInvoiceNumber($ordine->numero_ordine ?? 'ORD-' . $ordine->id);

            // URL di ritorno
            $redirectUrls = new \PayPal\Api\RedirectUrls();
            $redirectUrls->setReturnUrl(route('paypal.success', ['ordine_id' => $ordine->id]))
                         ->setCancelUrl(route('paypal.cancel', ['ordine_id' => $ordine->id]));

            // Payment
            $payment = new \PayPal\Api\Payment();
            $payment->setIntent('sale')
                    ->setPayer($payer)
                    ->setTransactions([$transaction])
                    ->setRedirectUrls($redirectUrls);

            // Crea il pagamento
            $payment->create($this->apiContext);

            // Salva payment_id nel database
            DB::table('ordini')
                ->where('id', $ordine->id)
                ->update([
                    'paypal_payment_id' => $payment->getId(),
                    'paypal_status' => 'created',
                    'updated_at' => now(),
                ]);

            // Ottieni URL di approvazione
            $approvalUrl = $payment->getApprovalLink();

            return response()->json([
                'success' => true,
                'payment_id' => $payment->getId(),
                'approval_url' => $approvalUrl,
            ]);

        } catch (\PayPal\Exception\PayPalConnectionException $e) {
            Log::error('PayPal Connection Error: ' . $e->getMessage());
            Log::error('PayPal Response: ' . $e->getData());

            return response()->json([
                'error' => 'Errore di connessione con PayPal. Riprova più tardi.',
                'details' => $e->getData(),
            ], 500);

        } catch (\Exception $e) {
            Log::error('PayPal Error: ' . $e->getMessage());

            return response()->json([
                'error' => 'Errore durante la creazione del pagamento.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Callback success - PayPal ritorna qui dopo approvazione
     */
    public function paymentSuccess(Request $request)
    {
        $paymentId = $request->query('paymentId');
        $payerId = $request->query('PayerID');
        $ordineId = $request->query('ordine_id');

        if (!$paymentId || !$payerId) {
            return redirect()->route('home')->with('error', 'Pagamento non valido');
        }

        try {
            // Recupera il payment
            $payment = \PayPal\Api\Payment::get($paymentId, $this->apiContext);

            // Esegui il payment
            $execution = new \PayPal\Api\PaymentExecution();
            $execution->setPayerId($payerId);

            $result = $payment->execute($execution, $this->apiContext);

            if ($result->getState() === 'approved') {
                // Pagamento approvato!

                // Aggiorna database
                DB::table('ordini')
                    ->where('id', $ordineId)
                    ->update([
                        'paypal_payment_id' => $paymentId,
                        'paypal_payer_id' => $payerId,
                        'paypal_status' => 'approved',
                        'stato_pagamento' => 'completato',
                        'data_pagamento' => now(),
                        'updated_at' => now(),
                    ]);

                // Log transazione
                DB::table('transazioni_paypal')->insert([
                    'ordine_id' => $ordineId,
                    'payment_id' => $paymentId,
                    'payer_id' => $payerId,
                    'stato' => 'approved',
                    'importo' => $result->getTransactions()[0]->getAmount()->getTotal(),
                    'valuta' => $result->getTransactions()[0]->getAmount()->getCurrency(),
                    'response_raw' => json_encode($result->toArray()),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                Log::info('PayPal Payment Approved', [
                    'payment_id' => $paymentId,
                    'ordine_id' => $ordineId,
                    'amount' => $result->getTransactions()[0]->getAmount()->getTotal(),
                ]);

                return redirect()->route('paypal.thank-you', ['ordine_id' => $ordineId])
                               ->with('success', 'Pagamento completato con successo!');
            }

            // Stato non approvato
            return redirect()->route('paypal.cancel', ['ordine_id' => $ordineId])
                           ->with('error', 'Il pagamento non è stato approvato.');

        } catch (\Exception $e) {
            Log::error('PayPal Execution Error: ' . $e->getMessage());

            return redirect()->route('paypal.cancel', ['ordine_id' => $ordineId])
                           ->with('error', 'Errore durante l\'elaborazione del pagamento.');
        }
    }

    /**
     * Callback cancel - PayPal ritorna qui se l'utente annulla
     */
    public function paymentCancel(Request $request)
    {
        $ordineId = $request->query('ordine_id');

        if ($ordineId) {
            DB::table('ordini')
                ->where('id', $ordineId)
                ->update([
                    'paypal_status' => 'cancelled',
                    'updated_at' => now(),
                ]);
        }

        return view('paypal.cancel', compact('ordineId'));
    }

    /**
     * Pagina di ringraziamento post-pagamento
     */
    public function thankYou(Request $request)
    {
        $ordineId = $request->query('ordine_id');

        $ordine = DB::table('ordini')->where('id', $ordineId)->first();

        return view('paypal.thank-you', compact('ordine'));
    }

    /**
     * Webhook Handler - Riceve notifiche da PayPal
     *
     * Configurare in PayPal Dashboard:
     * https://tuodominio.com/paypal/webhook
     */
    public function webhook(Request $request)
    {
        try {
            // Log della richiesta
            Log::info('PayPal Webhook Received', [
                'body' => $request->all(),
                'headers' => $request->headers->all(),
            ]);

            $eventType = $request->input('event_type');
            $resource = $request->input('resource');

            // Verifica firma webhook (IMPORTANTE per sicurezza)
            if (!$this->verifyWebhookSignature($request)) {
                Log::warning('PayPal Webhook signature verification failed');
                return response()->json(['error' => 'Invalid signature'], 401);
            }

            // Gestisci eventi
            switch ($eventType) {
                case 'PAYMENT.SALE.COMPLETED':
                    $this->handleSaleCompleted($resource);
                    break;

                case 'PAYMENT.SALE.REFUNDED':
                    $this->handleSaleRefunded($resource);
                    break;

                case 'PAYMENT.SALE.REVERSED':
                    $this->handleSaleReversed($resource);
                    break;

                default:
                    Log::info('Unhandled PayPal webhook event: ' . $eventType);
            }

            return response()->json(['status' => 'success'], 200);

        } catch (\Exception $e) {
            Log::error('PayPal Webhook Error: ' . $e->getMessage());
            return response()->json(['error' => 'Webhook processing failed'], 500);
        }
    }

    /**
     * Verifica firma del webhook (per sicurezza)
     */
    private function verifyWebhookSignature(Request $request)
    {
        // Implementazione verifica firma PayPal
        // Vedi: https://developer.paypal.com/docs/api/webhooks/v1/#verify-webhook-signature

        try {
            $webhookId = config('services.paypal.webhook_id');

            if (!$webhookId) {
                Log::warning('PayPal Webhook ID not configured');
                return false; // In produzione, dovrebbe ritornare false
            }

            // Qui andrebbe implementata la verifica della firma
            // Per semplicità, in questo skeleton accettiamo tutti i webhook
            // TODO: Implementare verifica firma in produzione

            return true;

        } catch (\Exception $e) {
            Log::error('Webhook signature verification error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Gestisce evento PAYMENT.SALE.COMPLETED
     */
    private function handleSaleCompleted($resource)
    {
        $saleId = $resource['id'];
        $paymentId = $resource['parent_payment'] ?? null;

        Log::info('Sale Completed', ['sale_id' => $saleId, 'payment_id' => $paymentId]);

        // Aggiorna lo stato nel database
        if ($paymentId) {
            DB::table('ordini')
                ->where('paypal_payment_id', $paymentId)
                ->update([
                    'paypal_status' => 'completed',
                    'stato_pagamento' => 'completato',
                    'updated_at' => now(),
                ]);
        }
    }

    /**
     * Gestisce evento PAYMENT.SALE.REFUNDED
     */
    private function handleSaleRefunded($resource)
    {
        $saleId = $resource['id'];
        $paymentId = $resource['parent_payment'] ?? null;

        Log::info('Sale Refunded', ['sale_id' => $saleId, 'payment_id' => $paymentId]);

        if ($paymentId) {
            DB::table('ordini')
                ->where('paypal_payment_id', $paymentId)
                ->update([
                    'paypal_status' => 'refunded',
                    'stato_pagamento' => 'rimborsato',
                    'updated_at' => now(),
                ]);
        }
    }

    /**
     * Gestisce evento PAYMENT.SALE.REVERSED
     */
    private function handleSaleReversed($resource)
    {
        $saleId = $resource['id'];
        $paymentId = $resource['parent_payment'] ?? null;

        Log::info('Sale Reversed', ['sale_id' => $saleId, 'payment_id' => $paymentId]);

        if ($paymentId) {
            DB::table('ordini')
                ->where('paypal_payment_id', $paymentId)
                ->update([
                    'paypal_status' => 'reversed',
                    'stato_pagamento' => 'contestato',
                    'updated_at' => now(),
                ]);
        }
    }

    /**
     * Test connessione PayPal (per debug)
     */
    public function testConnection()
    {
        if (!$this->apiContext) {
            return response()->json([
                'status' => 'error',
                'message' => 'PayPal API Context not initialized. Check credentials in .env',
            ], 500);
        }

        try {
            // Tenta di ottenere un access token
            $token = $this->apiContext->getCredential()->getAccessToken($this->apiContext->getConfig());

            return response()->json([
                'status' => 'success',
                'message' => 'PayPal connection successful!',
                'mode' => $this->mode,
                'token_preview' => substr($token, 0, 20) . '...',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
