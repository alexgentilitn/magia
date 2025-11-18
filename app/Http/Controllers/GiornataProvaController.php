<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Utente;
use App\Models\Sede;
use App\Models\Notifica;
use App\Models\Analytics;
use App\Services\EmailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

/**
 * Controller: Giornata di Prova
 * Funzione: Gestisce la landing page per richiedere una giornata di prova gratuita
 */
class GiornataProvaController extends Controller
{
    protected $emailService;

    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }

    /**
     * Mostra landing page giornata di prova
     */
    public function index()
    {
        // Traccia visualizzazione pagina
        Analytics::traccia('page_view', 'Giornata di Prova', 'landing_page_view');

        return view('giornata-prova.index');
    }

    /**
     * Gestisce invio form richiesta giornata di prova
     */
    public function richiedi(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nome' => 'required|string|max:100',
            'cognome' => 'required|string|max:100',
            'email' => 'required|email|max:255',
            'telefono' => 'required|string|max:20',
            'data_preferenza' => 'nullable|date|after:today',
            'messaggio' => 'nullable|string|max:1000',
            'privacy' => 'required|accepted',
        ], [
            'nome.required' => 'Il nome è obbligatorio',
            'cognome.required' => 'Il cognome è obbligatorio',
            'email.required' => 'L\'email è obbligatoria',
            'email.email' => 'Inserisci un\'email valida',
            'telefono.required' => 'Il numero di telefono è obbligatorio',
            'data_preferenza.after' => 'La data deve essere futura',
            'privacy.required' => 'Devi accettare l\'informativa privacy',
            'privacy.accepted' => 'Devi accettare l\'informativa privacy',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            // Salva richiesta nella tabella richieste_prova (o clienti con flag)
            // Per semplicità, salvo in una nota nelle impostazioni o invio solo email

            // Traccia evento
            Analytics::traccia('form_submit', 'Giornata di Prova', 'richiesta_inviata', [
                'email' => $request->email,
                'data_preferenza' => $request->data_preferenza,
            ]);

            // Invia email notifica agli admin
            $this->inviaNotificaAdmin($request);

            // Invia email conferma al richiedente
            $this->inviaConfermaRichiedente($request);

            return redirect()->route('giornata-prova.index')
                           ->with('success', 'Richiesta inviata con successo! Ti contatteremo a breve.');

        } catch (\Exception $e) {
            return back()->with('error', 'Si è verificato un errore. Riprova più tardi.')
                        ->withInput();
        }
    }

    /**
     * Invia notifica email agli amministratori
     */
    protected function inviaNotificaAdmin($request)
    {
        $admin_email = env('MAIL_ADMIN', 'info@magiadonna.it');

        $oggetto = "Nuova Richiesta Giornata di Prova - {$request->nome} {$request->cognome}";

        $corpo = "
            <h2 style='color: #8B5CF6;'>Nuova Richiesta Giornata di Prova</h2>

            <p><strong>Nome:</strong> {$request->nome} {$request->cognome}</p>
            <p><strong>Email:</strong> {$request->email}</p>
            <p><strong>Telefono:</strong> {$request->telefono}</p>

            " . ($request->data_preferenza ? "<p><strong>Data Preferenza:</strong> " . date('d/m/Y', strtotime($request->data_preferenza)) . "</p>" : "") . "

            " . ($request->messaggio ? "<p><strong>Messaggio:</strong><br>" . nl2br(e($request->messaggio)) . "</p>" : "") . "

            <hr style='margin: 20px 0;'>
            <p style='color: #666; font-size: 14px;'>
                Ricevuto il: " . now()->format('d/m/Y H:i') . "
            </p>
        ";

        $this->emailService->inviaEmail($admin_email, $oggetto, $corpo);
    }

    /**
     * Invia conferma al richiedente
     */
    protected function inviaConfermaRichiedente($request)
    {
        $oggetto = "Richiesta Giornata di Prova Ricevuta - MA.GIA DONNA";

        $corpo = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                <div style='background: linear-gradient(135deg, #8B5CF6 0%, #EC4899 100%); padding: 30px; text-align: center;'>
                    <h1 style='color: white; margin: 0;'>MA.GIA DONNA</h1>
                    <p style='color: white; margin: 10px 0 0 0;'>Centro Benessere Femminile</p>
                </div>

                <div style='padding: 30px; background: #f9fafb;'>
                    <h2 style='color: #8B5CF6;'>Ciao {$request->nome}!</h2>

                    <p>Grazie per il tuo interesse verso MA.GIA DONNA!</p>

                    <p>Abbiamo ricevuto la tua richiesta per una <strong>giornata di prova gratuita</strong>.</p>

                    <div style='background: white; padding: 20px; border-radius: 8px; margin: 20px 0;'>
                        <h3 style='color: #8B5CF6; margin-top: 0;'>I tuoi dati:</h3>
                        <p><strong>Nome:</strong> {$request->nome} {$request->cognome}</p>
                        <p><strong>Email:</strong> {$request->email}</p>
                        <p><strong>Telefono:</strong> {$request->telefono}</p>
                        " . ($request->data_preferenza ? "<p><strong>Data Preferenza:</strong> " . date('d/m/Y', strtotime($request->data_preferenza)) . "</p>" : "") . "
                    </div>

                    <p>Il nostro team ti contatterà entro <strong>24-48 ore</strong> per confermare la tua giornata di prova e fissare un appuntamento.</p>

                    <p><strong>Cosa aspettarti:</strong></p>
                    <ul>
                        <li>Visita guidata del centro</li>
                        <li>Colloquio conoscitivo personalizzato</li>
                        <li>Lezione di prova gratuita</li>
                        <li>Presentazione dei nostri programmi</li>
                    </ul>

                    <p style='margin-top: 30px;'>A presto!</p>
                    <p><strong>Il Team MA.GIA DONNA</strong></p>
                </div>

                <div style='padding: 20px; text-align: center; background: #1f2937; color: white; font-size: 12px;'>
                    <p>MA.GIA DONNA - Centro Benessere Femminile</p>
                    <p>Per informazioni: info@magiadonna.it | Tel: +39 XXX XXXXXXX</p>
                </div>
            </div>
        ";

        $this->emailService->inviaEmail($request->email, $oggetto, $corpo);
    }

    /**
     * Registra cliente di prova nel database (metodo completo)
     */
    public function registraClienteProva(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nome' => 'required|string|max:100',
            'cognome' => 'required|string|max:100',
            'email' => 'required|email|max:255|unique:utenti,email',
            'telefono_mobile' => 'required|string|max:20',
            'data_nascita' => 'nullable|date',
            'sede_preferita_id' => 'nullable|exists:sedi,id',
            'note' => 'nullable|string|max:1000',
            'privacy' => 'required|accepted',
        ], [
            'nome.required' => 'Il nome è obbligatorio',
            'cognome.required' => 'Il cognome è obbligatorio',
            'email.required' => 'L\'email è obbligatoria',
            'email.email' => 'Inserisci un\'email valida',
            'email.unique' => 'Questa email è già registrata',
            'telefono_mobile.required' => 'Il numero di telefono è obbligatorio',
            'privacy.required' => 'Devi accettare l\'informativa privacy',
            'privacy.accepted' => 'Devi accettare l\'informativa privacy',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();

        try {
            // 1. Crea utente con credenziali temporanee (NON attivo)
            $password_temporanea = 'Prova' . rand(1000, 9999);

            $utente = Utente::create([
                'nome' => $request->nome,
                'cognome' => $request->cognome,
                'email' => $request->email,
                'password' => Hash::make($password_temporanea),
                'tipo_utente' => 'cliente',
                'attivo' => false, // ❌ Non attivo finché non diventa cliente effettiva
            ]);

            // 2. Crea cliente con tipo "prova"
            $cliente = Cliente::create([
                'utente_id' => $utente->id,
                'nome' => $request->nome,
                'cognome' => $request->cognome,
                'email' => $request->email,
                'telefono_mobile' => $request->telefono_mobile,
                'data_nascita' => $request->data_nascita ?? null,
                'sede_preferita_id' => $request->sede_preferita_id ?? null,
                'note_interne' => $request->note ?? '',
                'consenso_privacy' => true,
                'consenso_privacy_data' => now(),
                'tipo_cliente' => 'prova', // 🆕 Cliente di prova
                'stato_cliente' => 'prova', // Stato speciale
                'data_iscrizione' => now(),
            ]);

            // 3. Traccia evento analytics
            Analytics::traccia('form_submit', 'Giornata di Prova', 'cliente_prova_registrato', [
                'cliente_id' => $cliente->id,
                'email' => $cliente->email,
            ]);

            // 4. Invia notifica agli amministratori
            $this->notificaAmministratori($cliente);

            // 5. Invia email conferma
            $this->inviaConfermaRichiedente($request);

            DB::commit();

            return redirect()->route('giornata-prova.index')
                           ->with('success', 'Registrazione completata! Ti aspettiamo alla giornata di prova.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Errore registrazione cliente prova: ' . $e->getMessage());

            return back()->withInput()
                       ->with('error', 'Si è verificato un errore. Riprova o contattaci direttamente.');
        }
    }

    /**
     * Invia notifica agli amministratori (notifica DB)
     */
    protected function notificaAmministratori($cliente)
    {
        // Trova tutti gli admin
        $amministratori = Utente::where('tipo_utente', 'amministratore')->get();

        foreach ($amministratori as $admin) {
            Notifica::create([
                'utente_id' => $admin->id,
                'tipo' => 'cliente_prova',
                'titolo' => 'Nuova iscrizione Giornata di Prova',
                'messaggio' => "{$cliente->nome_completo} si è iscritta alla giornata di prova",
                'url' => route('admin.clienti.show', $cliente->id),
                'letta' => false,
            ]);
        }
    }

    /**
     * [ADMIN] Converti cliente di prova in cliente effettiva
     */
    public function convertiCliente($id)
    {
        $cliente = Cliente::findOrFail($id);

        // Verifica che sia un cliente di prova
        if ($cliente->tipo_cliente !== 'prova') {
            return back()->with('error', 'Questo cliente non è un cliente di prova.');
        }

        DB::beginTransaction();

        try {
            // 1. Aggiorna tipo e stato cliente
            $cliente->update([
                'tipo_cliente' => 'effettiva',
                'stato_cliente' => 'attivo',
            ]);

            // 2. Attiva l'utente
            $cliente->utente->update([
                'attivo' => true,
            ]);

            // 3. Log attività
            \Log::info("Cliente {$cliente->id} convertito da prova a effettiva");

            DB::commit();

            return back()->with('success', "Cliente {$cliente->nome_completo} convertita con successo in cliente effettiva!");

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Errore conversione cliente: ' . $e->getMessage());

            return back()->with('error', 'Errore durante la conversione del cliente.');
        }
    }

    /**
     * [ADMIN] Lista clienti di prova
     */
    public function listaClientiProva()
    {
        $clienti_prova = Cliente::where('tipo_cliente', 'prova')
                                ->with(['utente', 'sedePreferita'])
                                ->orderBy('created_at', 'desc')
                                ->paginate(20);

        $stats = [
            'totale' => Cliente::where('tipo_cliente', 'prova')->count(),
            'ultimi_7_giorni' => Cliente::where('tipo_cliente', 'prova')
                                        ->where('created_at', '>=', now()->subDays(7))
                                        ->count(),
            'da_convertire' => Cliente::where('tipo_cliente', 'prova')
                                     ->where('stato_cliente', 'prova')
                                     ->count(),
        ];

        return view('admin.clienti.clienti-prova', compact('clienti_prova', 'stats'));
    }
}
