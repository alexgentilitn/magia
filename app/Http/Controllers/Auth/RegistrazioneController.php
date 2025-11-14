<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Utente;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

/**
 * Controller: Registrazione
 * 
 * Funzione: Gestisce la registrazione di nuove clienti nel sistema
 * Crea contemporaneamente:
 * - Account Utente (tabella utenti)
 * - Profilo Cliente (tabella clienti)
 * 
 * Route associate:
 * - GET /registrazione → mostraRegistrazione()
 * - POST /registrazione → registraCliente()
 * 
 * Sicurezza:
 * - Validazione completa dati
 * - Hash password automatico
 * - Transazione DB (rollback se errore)
 * - Generazione automatica codici
 */
class RegistrazioneController extends Controller
{
    /**
     * Mostra il form di registrazione
     * 
     * Funzione: Visualizza la pagina per registrarsi come nuova cliente
     * Ritorna: View registrazione
     */
    public function mostraRegistrazione()
    {
        // Se l'utente è già autenticato, reindirizza
        if (Auth::check()) {
            return redirect()->route('cliente.dashboard');
        }

        return view('auth.registrazione');
    }

    /**
     * Registra una nuova cliente nel sistema
     * 
     * Funzione: Crea account utente + profilo cliente in un'unica transazione
     * Se tutto OK → login automatico e redirect a dashboard cliente
     * Se errore → rollback e mostra errori
     * 
     * Parametri Request (dati obbligatori minimi):
     * - nome: nome cliente
     * - cognome: cognome cliente
     * - email: email univoca
     * - password: password (min 8 caratteri)
     * - password_confirmation: conferma password
     * - codice_fiscale: codice fiscale italiano
     * - telefono_mobile: cellulare
     * - data_nascita: data di nascita
     * - indirizzo, citta, provincia, cap: dati residenza
     * - consenso_privacy: accettazione privacy (obbligatorio)
     * 
     * Ritorna: Redirect a dashboard cliente o back con errori
     */
    public function registraCliente(Request $request)
    {
        // Validazione dati input
        $dati_validati = $request->validate([
            // Dati account
            'email' => ['required', 'email', 'unique:utenti,email', 'unique:clienti,email'],
            'password' => ['required', 'confirmed', Password::min(8)],
            
            // Dati anagrafici obbligatori
            'nome' => ['required', 'string', 'max:100'],
            'cognome' => ['required', 'string', 'max:100'],
            'codice_fiscale' => ['required', 'string', 'size:16', 'unique:clienti,codice_fiscale'],
            'telefono_mobile' => ['required', 'string', 'max:20'],
            'data_nascita' => ['required', 'date', 'before:today'],
            
            // Indirizzo
            'indirizzo' => ['required', 'string', 'max:200'],
            'citta' => ['required', 'string', 'max:100'],
            'provincia' => ['required', 'string', 'size:2'],
            'cap' => ['required', 'string', 'size:5'],
            
            // Dati opzionali
            'telefono_fisso' => ['nullable', 'string', 'max:20'],
            'sesso' => ['nullable', 'in:F,M,Altro'],
            
            // Consensi obbligatori
            'consenso_privacy' => ['required', 'accepted'],
            
            // Consensi opzionali
            'consenso_marketing' => ['nullable', 'boolean'],
            'consenso_foto' => ['nullable', 'boolean'],
            
            // Referral opzionale
            'codice_invito' => ['nullable', 'string', 'exists:clienti,codice_referral'],
        ], [
            // Messaggi errore personalizzati
            'email.required' => 'L\'email è obbligatoria',
            'email.unique' => 'Questa email è già registrata',
            'password.required' => 'La password è obbligatoria',
            'password.min' => 'La password deve essere di almeno 8 caratteri',
            'password.confirmed' => 'Le password non coincidono',
            'codice_fiscale.required' => 'Il codice fiscale è obbligatorio',
            'codice_fiscale.size' => 'Il codice fiscale deve essere di 16 caratteri',
            'codice_fiscale.unique' => 'Questo codice fiscale è già registrato',
            'telefono_mobile.required' => 'Il numero di cellulare è obbligatorio',
            'data_nascita.required' => 'La data di nascita è obbligatoria',
            'data_nascita.before' => 'La data di nascita non può essere nel futuro',
            'consenso_privacy.required' => 'Devi accettare la privacy policy per registrarti',
            'consenso_privacy.accepted' => 'Devi accettare la privacy policy per registrarti',
            'provincia.size' => 'Inserisci la sigla della provincia (es: TN)',
            'cap.size' => 'Il CAP deve essere di 5 cifre',
        ]);

        // Gestione referral (se presente codice invito)
        $invitato_da_cliente_id = null;
        if ($request->filled('codice_invito')) {
            $cliente_invitante = Cliente::where('codice_referral', $request->codice_invito)->first();
            if ($cliente_invitante) {
                $invitato_da_cliente_id = $cliente_invitante->id;
            }
        }

        // Transazione database: crea utente E cliente insieme
        // Se fallisce uno, rollback di tutto
        DB::beginTransaction();
        
        try {
            // 1. Crea account Utente
            $utente = Utente::create([
                'email' => $dati_validati['email'],
                'password' => Hash::make($dati_validati['password']),
                'nome' => $dati_validati['nome'],
                'cognome' => $dati_validati['cognome'],
                'telefono' => $dati_validati['telefono_mobile'],
                'tipo_utente' => 'cliente',
                'attivo' => true,
                'email_verificata' => false,
            ]);

            // 2. Crea profilo Cliente collegato all'utente
            $cliente = Cliente::create([
                'utente_id' => $utente->id,
                'nome' => $dati_validati['nome'],
                'cognome' => $dati_validati['cognome'],
                'codice_fiscale' => strtoupper($dati_validati['codice_fiscale']),
                'email' => $dati_validati['email'],
                'telefono_mobile' => $dati_validati['telefono_mobile'],
                'telefono_fisso' => $dati_validati['telefono_fisso'] ?? null,
                'data_nascita' => $dati_validati['data_nascita'],
                'sesso' => $dati_validati['sesso'] ?? 'F',
                'indirizzo' => $dati_validati['indirizzo'],
                'citta' => $dati_validati['citta'],
                'provincia' => strtoupper($dati_validati['provincia']),
                'cap' => $dati_validati['cap'],
                'nazione' => 'Italia',
                'consenso_privacy' => true,
                'consenso_privacy_data' => now(),
                'consenso_marketing' => $request->boolean('consenso_marketing'),
                'consenso_foto' => $request->boolean('consenso_foto'),
                'stato_cliente' => 'attivo',
                'data_iscrizione' => now(),
                'invitato_da_cliente_id' => $invitato_da_cliente_id,
                // codice_cliente e codice_referral vengono generati automaticamente dal Model
            ]);

            // Commit transazione
            DB::commit();

            // Login automatico della nuova cliente
            Auth::login($utente);

            // TODO: Inviare email di benvenuto
            // Mail::to($utente->email)->send(new BenvenutoMail($cliente));

            // Redirect a dashboard cliente con messaggio successo
            return redirect()->route('cliente.dashboard')
                ->with('success', "Benvenuta {$cliente->nome}! La tua registrazione è completata.");

        } catch (\Exception $e) {
            // Rollback in caso di errore
            DB::rollBack();
            
            // Log dell'errore
            \Log::error('Errore registrazione cliente: ' . $e->getMessage());
            
            // Torna al form con errore generico
            return back()
                ->withInput($request->except('password', 'password_confirmation'))
                ->withErrors(['email' => 'Si è verificato un errore durante la registrazione. Riprova.']);
        }
    }

    /**
     * Verifica se un codice fiscale esiste già (AJAX)
     * 
     * Funzione: Endpoint per validazione in tempo reale del form
     * Ritorna: JSON con risultato
     */
    public function verificaCodiceFiscale(Request $request)
    {
        $codice_fiscale = strtoupper($request->input('codice_fiscale'));
        
        $esiste = Cliente::where('codice_fiscale', $codice_fiscale)->exists();
        
        return response()->json([
            'disponibile' => !$esiste,
            'messaggio' => $esiste ? 'Codice fiscale già registrato' : 'Codice fiscale disponibile'
        ]);
    }

    /**
     * Verifica se una email esiste già (AJAX)
     * 
     * Funzione: Endpoint per validazione in tempo reale del form
     * Ritorna: JSON con risultato
     */
    public function verificaEmail(Request $request)
    {
        $email = $request->input('email');
        
        $esiste = Utente::where('email', $email)->exists() || 
                  Cliente::where('email', $email)->exists();
        
        return response()->json([
            'disponibile' => !$esiste,
            'messaggio' => $esiste ? 'Email già registrata' : 'Email disponibile'
        ]);
    }

    /**
     * Verifica validità codice invito referral (AJAX)
     * 
     * Funzione: Controlla se il codice referral inserito esiste
     * Ritorna: JSON con risultato e nome dell'amica invitante
     */
    public function verificaCodiceInvito(Request $request)
    {
        $codice = strtoupper($request->input('codice_invito'));
        
        $cliente = Cliente::where('codice_referral', $codice)->first();
        
        if ($cliente) {
            return response()->json([
                'valido' => true,
                'messaggio' => "Codice valido! Sei stata invitata da {$cliente->nome} {$cliente->cognome}",
                'nome_invitante' => $cliente->nomeCompleto
            ]);
        }
        
        return response()->json([
            'valido' => false,
            'messaggio' => 'Codice invito non valido'
        ]);
    }
}
