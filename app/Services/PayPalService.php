<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Impostazione;

/**
 * Service: PayPal Integration
 *
 * Funzione: Gestisce l'integrazione con PayPal REST API v2
 * - Creazione ordini
 * - Cattura pagamenti
 * - Gestione token OAuth
 *
 * NOTA: Le credenziali vengono lette dal database (tabella impostazioni)
 *       configurabili dall'interfaccia admin in Impostazioni → PayPal
 *
 * Documentazione: https://developer.paypal.com/docs/api/orders/v2/
 */
class PayPalService
{
    private $clientId;
    private $clientSecret;
    private $mode;
    private $baseUrl;

    public function __construct()
    {
        // Leggi credenziali dal database (con fallback al .env)
        $this->clientId = Impostazione::get('paypal_client_id', config('services.paypal.client_id'));
        $this->clientSecret = Impostazione::get('paypal_client_secret', config('services.paypal.client_secret'));
        $this->mode = Impostazione::get('paypal_mode', config('services.paypal.mode', 'sandbox'));

        // URL base API (sandbox o production)
        $this->baseUrl = $this->mode === 'live'
            ? 'https://api-m.paypal.com'
            : 'https://api-m.sandbox.paypal.com';
    }

    /**
     * Ottieni access token OAuth
     */
    private function getAccessToken()
    {
        try {
            $response = Http::withBasicAuth($this->clientId, $this->clientSecret)
                ->asForm()
                ->post("{$this->baseUrl}/v1/oauth2/token", [
                    'grant_type' => 'client_credentials'
                ]);

            if ($response->successful()) {
                return $response->json()['access_token'];
            }

            throw new Exception('Errore ottenimento access token PayPal: ' . $response->body());

        } catch (Exception $e) {
            Log::error('PayPal getAccessToken failed', [
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Crea un ordine PayPal
     *
     * @param float $amount Importo in EUR
     * @param string $description Descrizione ordine
     * @param array $customData Dati custom da salvare (es. utente_id)
     * @return array PayPal order response
     */
    public function createOrder($amount, $description, $customData = [])
    {
        try {
            $token = $this->getAccessToken();

            $response = Http::withToken($token)
                ->post("{$this->baseUrl}/v2/checkout/orders", [
                    'intent' => 'CAPTURE',
                    'purchase_units' => [
                        [
                            'amount' => [
                                'currency_code' => 'EUR',
                                'value' => number_format($amount, 2, '.', '')
                            ],
                            'description' => $description,
                            'custom_id' => json_encode($customData), // Salva dati custom
                        ]
                    ],
                    'application_context' => [
                        'brand_name' => 'MA.GIA DONNA',
                        'locale' => 'it-IT',
                        'landing_page' => 'NO_PREFERENCE',
                        'shipping_preference' => 'NO_SHIPPING',
                        'user_action' => 'PAY_NOW',
                        'return_url' => route('pagamento.paypal.success'),
                        'cancel_url' => route('pagamento.paypal.cancel'),
                    ]
                ]);

            if ($response->successful()) {
                $order = $response->json();

                Log::info('PayPal order created', [
                    'order_id' => $order['id'],
                    'amount' => $amount,
                    'custom_data' => $customData
                ]);

                return $order;
            }

            throw new Exception('Errore creazione ordine PayPal: ' . $response->body());

        } catch (Exception $e) {
            Log::error('PayPal createOrder failed', [
                'error' => $e->getMessage(),
                'amount' => $amount
            ]);
            throw $e;
        }
    }

    /**
     * Cattura il pagamento di un ordine
     *
     * @param string $orderId ID ordine PayPal
     * @return array PayPal capture response
     */
    public function captureOrder($orderId)
    {
        try {
            $token = $this->getAccessToken();

            $response = Http::withToken($token)
                ->post("{$this->baseUrl}/v2/checkout/orders/{$orderId}/capture");

            if ($response->successful()) {
                $capture = $response->json();

                Log::info('PayPal payment captured', [
                    'order_id' => $orderId,
                    'status' => $capture['status']
                ]);

                return $capture;
            }

            throw new Exception('Errore cattura pagamento PayPal: ' . $response->body());

        } catch (Exception $e) {
            Log::error('PayPal captureOrder failed', [
                'error' => $e->getMessage(),
                'order_id' => $orderId
            ]);
            throw $e;
        }
    }

    /**
     * Ottieni dettagli ordine
     *
     * @param string $orderId ID ordine PayPal
     * @return array Order details
     */
    public function getOrderDetails($orderId)
    {
        try {
            $token = $this->getAccessToken();

            $response = Http::withToken($token)
                ->get("{$this->baseUrl}/v2/checkout/orders/{$orderId}");

            if ($response->successful()) {
                return $response->json();
            }

            throw new Exception('Errore recupero dettagli ordine PayPal: ' . $response->body());

        } catch (Exception $e) {
            Log::error('PayPal getOrderDetails failed', [
                'error' => $e->getMessage(),
                'order_id' => $orderId
            ]);
            throw $e;
        }
    }

    /**
     * Verifica se le credenziali PayPal sono configurate
     *
     * @return bool
     */
    public function isConfigured()
    {
        return !empty($this->clientId) && !empty($this->clientSecret);
    }

    /**
     * Test connessione PayPal
     *
     * @return bool
     */
    public function testConnection()
    {
        try {
            $this->getAccessToken();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Ottieni importo quota registrazione da database
     *
     * @return float
     */
    public static function getImportoRegistrazione()
    {
        return floatval(Impostazione::get('paypal_importo_registrazione', '50.00'));
    }

    /**
     * Verifica se PayPal è attivo
     *
     * @return bool
     */
    public static function isPayPalAttivo()
    {
        return Impostazione::get('paypal_attivo', '1') === '1';
    }

    /**
     * Verifica se Bonifico è attivo
     *
     * @return bool
     */
    public static function isBonificoAttivo()
    {
        return Impostazione::get('bonifico_attivo', '1') === '1';
    }

    /**
     * Ottieni dati bonifico bancario
     *
     * @return array
     */
    public static function getDatiBonifico()
    {
        return [
            'iban' => Impostazione::get('bonifico_iban', 'IT00X0000000000000000000000'),
            'intestatario' => Impostazione::get('bonifico_intestatario', 'MA.GIA DONNA S.R.L.'),
            'banca' => Impostazione::get('bonifico_banca', 'Banca Esempio'),
            'importo' => static::getImportoRegistrazione(),
        ];
    }
}
