<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Impostazione;
use App\Models\ImpostazioneSistema;
use App\Mail\TestEmailMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

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

        return view('admin.impostazioni.index', compact('impostazioniSmtp', 'impostazioniSistema', 'categorieSistema'));
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
}
