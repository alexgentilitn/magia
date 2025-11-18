<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Impostazione;
use App\Models\ImpostazioneSistema;
use App\Models\Segnalazione;
use App\Mail\TestEmailMail;
use App\Services\PayPalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

/**
 * Controller: Gestione Impostazioni Sistema
 * Configurazione SMTP, email, sistema generale
 */
class ImpostazioniController extends Controller
{
    /**
     * Mostra pagina principale impostazioni con tutti i tab
     */
    public function index()
    {
        // Dati per tab Email (SMTP)
        $impostazioniSmtp = Impostazione::getGruppo('smtp');

        // Dati per tab PayPal
        $impostazioniPaypal = Impostazione::getGruppo('paypal');
        $impostazioniPagamento = Impostazione::getGruppo('pagamento');

        // Dati per tab Valori Sistema
        $impostazioniSistema = ImpostazioneSistema::orderBy('categoria')
            ->orderBy('ordinamento')
            ->get()
            ->groupBy('categoria');

        $categorieSistema = [
            'tipologia_lezione' => [
                'nome' => 'Tipologie Lezione',
                'descrizione' => 'Tipologie disponibili per le lezioni (individuale, gruppo, online, ecc.)',
                'icona' => 'fa-tag',
            ],
            'stato_lezione' => [
                'nome' => 'Stati Lezione',
                'descrizione' => 'Stati possibili per una lezione (programmata, confermata, ecc.)',
                'icona' => 'fa-flag',
            ],
            'frequenza_ricorrenza' => [
                'nome' => 'Frequenze Ricorrenza',
                'descrizione' => 'Frequenze disponibili per lezioni ricorrenti',
                'icona' => 'fa-repeat',
            ],
        ];

        // Dati per tab Segnalazioni
        $segnalazioniQuery = Segnalazione::with(['utente', 'risolutore'])->recenti();

        // Se non è super admin, mostra solo le proprie segnalazioni
        if (Auth::user()->tipo_utente !== 'super_admin') {
            $segnalazioniQuery->where('utente_id', Auth::id());
        }

        $segnalazioni = $segnalazioniQuery->paginate(10);

        // Statistiche segnalazioni
        $statsSegnalazioni = [
            'totali' => Segnalazione::count(),
            'aperte' => Segnalazione::aperte()->count(),
            'in_lavorazione' => Segnalazione::inLavorazione()->count(),
            'risolte' => Segnalazione::risolte()->count(),
        ];

        $isSuperAdmin = Auth::user()->tipo_utente === 'super_admin';

        return view('admin.impostazioni.index', compact(
            'impostazioniSmtp',
            'impostazioniPaypal',
            'impostazioniPagamento',
            'impostazioniSistema',
            'categorieSistema',
            'segnalazioni',
            'statsSegnalazioni',
            'isSuperAdmin'
        ));
    }

    /**
     * Mostra form configurazione SMTP (legacy - redirect a index)
     */
    public function smtp()
    {
        $impostazioni = Impostazione::getGruppo('smtp');

        return view('admin.impostazioni.smtp', compact('impostazioni'));
    }

    /**
     * Salva configurazione SMTP
     */
    public function salvaSmtp(Request $request)
    {
        $validated = $request->validate([
            'smtp_host' => 'required|string|max:255',
            'smtp_porta' => 'required|integer|min:1|max:65535',
            'smtp_username' => 'required|email|max:255',
            'smtp_password' => 'nullable|string|max:255',
            'smtp_encryption' => 'required|in:tls,ssl,none',
            'mail_from_address' => 'required|email|max:255',
            'mail_from_name' => 'required|string|max:255',
        ]);

        // Se la password è vuota, mantieni quella esistente
        if (empty($validated['smtp_password'])) {
            unset($validated['smtp_password']);
        }

        // Aggiorna tutte le impostazioni
        $success = Impostazione::updateGruppo('smtp', $validated);

        if ($success) {
            // Applica immediatamente le nuove configurazioni
            Impostazione::applySmtpConfig();

            return redirect()->route('admin.impostazioni.smtp')
                ->with('success', 'Configurazione SMTP salvata con successo!');
        } else {
            return redirect()->route('admin.impostazioni.smtp')
                ->with('error', 'Errore nel salvataggio della configurazione.');
        }
    }

    /**
     * Testa connessione SMTP inviando email di prova
     */
    public function testSmtp(Request $request)
    {
        $validated = $request->validate([
            'email_test' => 'required|email|max:255',
        ]);

        try {
            // Applica configurazioni SMTP correnti
            Impostazione::applySmtpConfig();

            // Ottieni configurazione per mostrare nel messaggio
            $config = Impostazione::getSmtpConfig();

            // Invia email di test
            Mail::to($validated['email_test'])->send(new TestEmailMail($config));

            return redirect()->route('admin.impostazioni.smtp')
                ->with('success', "Email di test inviata con successo a {$validated['email_test']}! Controlla la casella di posta.");
        } catch (\Exception $e) {
            return redirect()->route('admin.impostazioni.smtp')
                ->with('error', 'Errore nell\'invio dell\'email di test: ' . $e->getMessage());
        }
    }

    /**
     * Test connessione SMTP senza inviare email
     * Verifica solo la connessione al server
     */
    public function testConnessione(Request $request)
    {
        try {
            // Applica configurazioni SMTP correnti
            Impostazione::applySmtpConfig();

            $config = Impostazione::getSmtpConfig();

            // Tenta di creare una connessione SMTP
            $transport = Mail::getSwiftMailer()->getTransport();

            return response()->json([
                'success' => true,
                'message' => 'Connessione SMTP riuscita!',
                'config' => [
                    'host' => $config['host'],
                    'port' => $config['port'],
                    'encryption' => $config['encryption'],
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Errore connessione: ' . $e->getMessage(),
            ], 500);
        }
    }

    // ============================================
    // PAYPAL CONFIGURATION
    // ============================================

    /**
     * Salva configurazione PayPal
     */
    public function salvaPaypal(Request $request)
    {
        $validated = $request->validate([
            'paypal_mode' => 'required|in:sandbox,live',
            'paypal_client_id' => 'nullable|string|max:255',
            'paypal_client_secret' => 'nullable|string|max:255',
            'paypal_importo_registrazione' => 'required|numeric|min:0',
            'paypal_attivo' => 'nullable|boolean',
            'bonifico_attivo' => 'nullable|boolean',
            'bonifico_iban' => 'required|string|max:34',
            'bonifico_intestatario' => 'required|string|max:255',
            'bonifico_banca' => 'required|string|max:255',
        ]);

        // Converte checkbox in 1/0
        $validated['paypal_attivo'] = $request->has('paypal_attivo') ? '1' : '0';
        $validated['bonifico_attivo'] = $request->has('bonifico_attivo') ? '1' : '0';

        // Se Client ID o Secret sono vuoti, mantieni quelli esistenti
        if (empty($validated['paypal_client_id'])) {
            unset($validated['paypal_client_id']);
        }
        if (empty($validated['paypal_client_secret'])) {
            unset($validated['paypal_client_secret']);
        }

        // Separa le impostazioni per gruppo
        $paypalData = [];
        $pagamentoData = [];

        foreach ($validated as $key => $value) {
            if (strpos($key, 'bonifico_') === 0) {
                $pagamentoData[$key] = $value;
            } else {
                $paypalData[$key] = $value;
            }
        }

        // Aggiorna gruppi separati
        $successPaypal = Impostazione::updateGruppo('paypal', $paypalData);
        $successPagamento = Impostazione::updateGruppo('pagamento', $pagamentoData);

        if ($successPaypal && $successPagamento) {
            return redirect()->route('admin.impostazioni.index')
                ->with('success', 'Configurazione pagamenti salvata con successo!')
                ->with('active_tab', 'paypal');
        } else {
            return redirect()->route('admin.impostazioni.index')
                ->with('error', 'Errore nel salvataggio della configurazione.')
                ->with('active_tab', 'paypal');
        }
    }

    /**
     * Testa connessione PayPal
     */
    public function testPaypal(Request $request)
    {
        try {
            $paypalService = new PayPalService();

            // Verifica se le credenziali sono configurate
            if (!$paypalService->isConfigured()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Credenziali PayPal non configurate. Inserisci Client ID e Secret.',
                ], 400);
            }

            // Testa la connessione
            if ($paypalService->testConnection()) {
                $mode = Impostazione::get('paypal_mode', 'sandbox');
                $modeLabel = $mode === 'live' ? 'Produzione' : 'Sandbox';

                return response()->json([
                    'success' => true,
                    'message' => "✅ Connessione PayPal riuscita! Modalità: {$modeLabel}",
                    'mode' => $mode,
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Impossibile connettersi a PayPal. Verifica le credenziali.',
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Errore connessione PayPal: ' . $e->getMessage(),
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
