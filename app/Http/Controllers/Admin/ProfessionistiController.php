<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Professionista;
use App\Models\Utente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Mail\PasswordTemporaneaMail;

/**
 * Controller: Gestione Professionisti
 * CRUD completo per professionisti, istruttori, personal trainer
 */
class ProfessionistiController extends Controller
{
    /**
     * Mostra l'elenco dei professionisti
     */
    public function index(Request $request)
    {
        $query = Professionista::with('utente')->withCount(['lezioni', 'programmi']);

        // Filtri
        if ($request->filled('stato')) {
            $query->where('stato', $request->stato);
        }

        if ($request->filled('specializzazione')) {
            $query->whereJsonContains('specializzazioni', $request->specializzazione);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nome', 'like', "%{$search}%")
                  ->orWhere('cognome', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('codice_professionista', 'like', "%{$search}%");
            });
        }

        $professionisti = $query->orderBy('cognome')->orderBy('nome')->paginate(20);

        // Statistiche
        $statistiche = [
            'totale' => Professionista::count(),
            'pending' => Professionista::where('stato', 'pending')->count(),
            'attivi' => Professionista::where('stato', 'attivo')->count(),
            'visibili' => Professionista::where('visibile_pubblico', true)->count(),
            'con_certificazioni' => Professionista::whereNotNull('certificazioni')->count(),
        ];

        return view('admin.professionisti.index', compact('professionisti', 'statistiche'));
    }

    /**
     * Mostra il form di creazione
     */
    public function create()
    {
        return view('admin.professionisti.create');
    }

    /**
     * Salva un nuovo professionista
     */
    public function store(Request $request)
    {
        try {
            \Log::info('Inizio creazione professionista', $request->all());

            $validated = $request->validate([
                'nome' => 'required|string|max:100',
                'cognome' => 'required|string|max:100',
                'email' => 'required|email|unique:utenti,email',
                'telefono_mobile' => 'nullable|string|max:20',
                'titolo_professionale' => 'nullable|string|max:100',
                'bio' => 'nullable|string',
                'anni_esperienza' => 'nullable|integer|min:0',
                'tariffa_oraria' => 'nullable|numeric|min:0',
                'tariffa_lezione_gruppo' => 'nullable|numeric|min:0',
                'tariffa_lezione_privata' => 'nullable|numeric|min:0',
                'stato' => 'required|in:pending,attivo,sospeso,inattivo',
            ]);

            \Log::info('Validazione OK', $validated);

            // Genera password temporanea
            $passwordTemporanea = 'temp' . rand(1000, 9999);
            $scadenza = now()->addHours(24);

            // Crea utente base
            $utente = Utente::create([
                'nome' => $validated['nome'],
                'cognome' => $validated['cognome'],
                'email' => $validated['email'],
                'password' => Hash::make($passwordTemporanea),
                'password_temp_expires_at' => $scadenza,
                'deve_cambiare_password' => true,
                'tipo_utente' => 'professionista',
                'telefono' => $validated['telefono_mobile'] ?? null,
                'attivo' => true,
            ]);

            \Log::info('Utente creato', ['id' => $utente->id]);

            // Crea profilo professionista
            $dataProfessionista = $validated;
            unset($dataProfessionista['email']); // Email è solo in utenti, non in professionisti
            $dataProfessionista['utente_id'] = $utente->id;
            $dataProfessionista['codice_professionista'] = Professionista::generaCodiceProfessionista();

            \Log::info('Dati professionista preparati', $dataProfessionista);

            $professionista = Professionista::create($dataProfessionista);

            \Log::info('Professionista creato', ['id' => $professionista->id]);

            // Invia email con credenziali
            try {
                // Applica configurazioni SMTP dal database
                \App\Models\Impostazione::applySmtpConfig();

                Mail::to($utente->email)->send(new PasswordTemporaneaMail(
                    $professionista,
                    $passwordTemporanea,
                    $scadenza
                ));
                $emailMessage = ' Email inviata con successo a ' . $utente->email;
                \Log::info('Email inviata', ['email' => $utente->email]);
            } catch (\Exception $e) {
                $emailMessage = ' ATTENZIONE: Email non inviata. Comunica manualmente: ' . $passwordTemporanea . ' - Errore: ' . $e->getMessage();
                \Log::error('Errore invio email', ['error' => $e->getMessage()]);
            }

            return redirect()->route('admin.professionisti.show', $professionista->id)
                ->with('success', 'Professionista creato con successo! Password temporanea: ' . $passwordTemporanea . '. ' . $emailMessage);

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Errori di validazione - Laravel li gestisce automaticamente
            \Log::warning('Errore validazione', ['errors' => $e->errors()]);
            throw $e;
        } catch (\Exception $e) {
            // Errore generico
            \Log::error('Errore creazione professionista', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Errore durante la creazione del professionista: ' . $e->getMessage());
        }
    }

    /**
     * Mostra il dettaglio di un professionista
     */
    public function show($id)
    {
        $professionista = Professionista::with(['utente', 'lezioni', 'programmi'])
            ->withCount(['lezioni', 'programmi'])
            ->findOrFail($id);

        // Statistiche professionista
        $statistiche = [
            'lezioni_totali' => $professionista->lezioni_count,
            'lezioni_future' => $professionista->lezioni()->where('data', '>=', now())->count(),
            'programmi_totali' => $professionista->programmi_count,
            'programmi_attivi' => $professionista->programmi()->where('attivo', true)->count(),
            'certificazioni_valide' => $professionista->hasCertificazioniValide(),
            'certificazioni_scadenza' => $professionista->getCertificazioniInScadenza()->count(),
        ];

        // Prossime lezioni
        $prossimeLezioni = $professionista->lezioni()
            ->with(['programma', 'sede'])
            ->where('data', '>=', now())
            ->orderBy('data')
            ->orderBy('ora_inizio')
            ->limit(5)
            ->get();

        return view('admin.professionisti.show', compact('professionista', 'statistiche', 'prossimeLezioni'));
    }

    /**
     * Mostra il form di modifica
     */
    public function edit($id)
    {
        $professionista = Professionista::with('utente')->findOrFail($id);
        return view('admin.professionisti.edit', compact('professionista'));
    }

    /**
     * Aggiorna un professionista
     */
    public function update(Request $request, $id)
    {
        $professionista = Professionista::findOrFail($id);

        $validated = $request->validate([
            'nome' => 'required|string|max:100',
            'cognome' => 'required|string|max:100',
            'email' => 'required|email|unique:utenti,email,' . $professionista->utente_id,
            'telefono_mobile' => 'nullable|string|max:20',
            'titolo_professionale' => 'nullable|string|max:100',
            'bio' => 'nullable|string',
            'anni_esperienza' => 'nullable|integer|min:0',
            'tariffa_oraria' => 'nullable|numeric|min:0',
            'tariffa_lezione_gruppo' => 'nullable|numeric|min:0',
            'tariffa_lezione_privata' => 'nullable|numeric|min:0',
            'stato' => 'required|in:pending,attivo,sospeso,inattivo',
            'visibile_pubblico' => 'boolean',
            'specializzazioni' => 'nullable|array',
            'specializzazioni.*' => 'string|max:100',
            'qualifiche' => 'nullable|array',
            'qualifiche.*' => 'string|max:200',
        ]);

        // Aggiorna utente base
        $professionista->utente->update([
            'nome' => $validated['nome'],
            'cognome' => $validated['cognome'],
            'email' => $validated['email'],
            'telefono' => $validated['telefono_mobile'] ?? null,
        ]);

        // Aggiorna professionista (rimuovi email)
        $dataProfessionista = $validated;
        unset($dataProfessionista['email']); // Email è solo in utenti, non in professionisti
        $professionista->update($dataProfessionista);

        return redirect()->route('admin.professionisti.show', $professionista->id)
            ->with('success', 'Professionista aggiornato con successo!');
    }

    /**
     * Elimina un professionista (soft delete)
     */
    public function destroy($id)
    {
        $professionista = Professionista::findOrFail($id);

        // Verifica se ha lezioni o programmi attivi
        $lezioniAttive = $professionista->lezioni()->where('data', '>=', now())->count();
        $programmiAttivi = $professionista->programmi()->where('attivo', true)->count();

        if ($lezioniAttive > 0 || $programmiAttivi > 0) {
            return redirect()->route('admin.professionisti.index')
                ->with('error', 'Impossibile eliminare: il professionista ha lezioni o programmi attivi.');
        }

        // Soft delete utente e professionista
        $professionista->utente->delete();
        $professionista->delete();

        return redirect()->route('admin.professionisti.index')
            ->with('success', 'Professionista eliminato con successo!');
    }

    /**
     * Cambia stato professionista
     */
    public function cambiaStato(Request $request, $id)
    {
        $professionista = Professionista::findOrFail($id);

        $validated = $request->validate([
            'stato' => 'required|in:pending,attivo,sospeso,inattivo',
        ]);

        $professionista->update(['stato' => $validated['stato']]);

        return redirect()->route('admin.professionisti.show', $professionista->id)
            ->with('success', 'Stato professionista aggiornato!');
    }

    /**
     * Approva un professionista in stato pending
     */
    public function approva($id)
    {
        $professionista = Professionista::findOrFail($id);

        if ($professionista->stato !== 'pending') {
            return redirect()->route('admin.professionisti.show', $professionista->id)
                ->with('error', 'Solo i professionisti in stato pending possono essere approvati.');
        }

        $professionista->update(['stato' => 'attivo']);

        // Invia email di approvazione
        try {
            \App\Models\Impostazione::applySmtpConfig();
            Mail::to($professionista->utente->email)->send(new \App\Mail\ProfessionistaApprovatoMail($professionista));
            $emailMessage = 'Email di notifica inviata.';
        } catch (\Exception $e) {
            $emailMessage = 'Email non inviata: ' . $e->getMessage();
        }

        return redirect()->route('admin.professionisti.show', $professionista->id)
            ->with('success', "Professionista approvato con successo! {$emailMessage}");
    }

    /**
     * Rifiuta un professionista in stato pending
     */
    public function rifiuta(Request $request, $id)
    {
        $professionista = Professionista::findOrFail($id);

        if ($professionista->stato !== 'pending') {
            return redirect()->route('admin.professionisti.show', $professionista->id)
                ->with('error', 'Solo i professionisti in stato pending possono essere rifiutati.');
        }

        $validated = $request->validate([
            'motivo_rifiuto' => 'nullable|string|max:500',
        ]);

        // Cambia stato a inattivo
        $professionista->update(['stato' => 'inattivo']);

        // Invia email di rifiuto con motivo
        try {
            \App\Models\Impostazione::applySmtpConfig();
            Mail::to($professionista->utente->email)->send(
                new \App\Mail\ProfessionistaRifiutatoMail($professionista, $validated['motivo_rifiuto'] ?? null)
            );
            $emailMessage = 'Email di notifica inviata.';
        } catch (\Exception $e) {
            $emailMessage = 'Email non inviata: ' . $e->getMessage();
        }

        return redirect()->route('admin.professionisti.index')
            ->with('success', "Professionista rifiutato. {$emailMessage}");
    }

    /**
     * Gestione certificazioni
     */
    public function certificazioni($id)
    {
        $professionista = Professionista::findOrFail($id);
        return view('admin.professionisti.certificazioni', compact('professionista'));
    }

    /**
     * Salva certificazioni
     */
    public function salvaCertificazioni(Request $request, $id)
    {
        $professionista = Professionista::findOrFail($id);

        $certificazioni = [];
        if ($request->has('certificazione_nome')) {
            foreach ($request->certificazione_nome as $index => $nome) {
                if ($nome) {
                    $certificazioni[] = [
                        'nome' => $nome,
                        'ente' => $request->certificazione_ente[$index] ?? null,
                        'data_conseguimento' => $request->certificazione_data[$index] ?? null,
                        'scadenza' => $request->certificazione_scadenza[$index] ?? null,
                    ];
                }
            }
        }

        $professionista->update(['certificazioni' => $certificazioni]);

        return redirect()->route('admin.professionisti.show', $professionista->id)
            ->with('success', 'Certificazioni aggiornate!');
    }

    /**
     * Gestione disponibilità settimanale
     */
    public function disponibilita($id)
    {
        $professionista = Professionista::findOrFail($id);

        $giorniSettimana = [
            'lunedi' => 'Lunedì',
            'martedi' => 'Martedì',
            'mercoledi' => 'Mercoledì',
            'giovedi' => 'Giovedì',
            'venerdi' => 'Venerdì',
            'sabato' => 'Sabato',
            'domenica' => 'Domenica',
        ];

        return view('admin.professionisti.disponibilita', compact('professionista', 'giorniSettimana'));
    }

    /**
     * Salva disponibilità
     */
    public function salvaDisponibilita(Request $request, $id)
    {
        $professionista = Professionista::findOrFail($id);

        $disponibilita = [];
        $giorni = ['lunedi', 'martedi', 'mercoledi', 'giovedi', 'venerdi', 'sabato', 'domenica'];

        foreach ($giorni as $giorno) {
            if ($request->has("disponibile_{$giorno}")) {
                $disponibilita[$giorno] = [
                    'disponibile' => true,
                    'dalle' => $request->input("dalle_{$giorno}"),
                    'alle' => $request->input("alle_{$giorno}"),
                ];
            } else {
                $disponibilita[$giorno] = ['disponibile' => false];
            }
        }

        $professionista->update(['disponibilita_settimanale' => $disponibilita]);

        return redirect()->route('admin.professionisti.show', $professionista->id)
            ->with('success', 'Disponibilità aggiornata!');
    }

    /**
     * Upload foto profilo
     */
    public function uploadFoto(Request $request, $id)
    {
        $professionista = Professionista::findOrFail($id);

        $request->validate([
            'foto_profilo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        try {
            // Elimina foto vecchia se esiste
            if ($professionista->foto_profilo && \Storage::disk('public')->exists($professionista->foto_profilo)) {
                \Storage::disk('public')->delete($professionista->foto_profilo);
            }

            // Salva nuova foto
            $fileName = 'professionisti/' . time() . '_' . $professionista->id . '.' . $request->foto_profilo->extension();
            $path = $request->foto_profilo->storeAs('professionisti', basename($fileName), 'public');

            $professionista->update(['foto_profilo' => $path]);

            return redirect()->route('admin.professionisti.show', $professionista->id)
                ->with('success', 'Foto profilo caricata con successo!');
        } catch (\Exception $e) {
            return redirect()->route('admin.professionisti.show', $professionista->id)
                ->with('error', 'Errore durante il caricamento della foto: ' . $e->getMessage());
        }
    }

    /**
     * Elimina foto profilo
     */
    public function eliminaFoto($id)
    {
        $professionista = Professionista::findOrFail($id);

        try {
            if ($professionista->foto_profilo && \Storage::disk('public')->exists($professionista->foto_profilo)) {
                \Storage::disk('public')->delete($professionista->foto_profilo);
            }

            $professionista->update(['foto_profilo' => null]);

            return redirect()->route('admin.professionisti.show', $professionista->id)
                ->with('success', 'Foto profilo eliminata con successo!');
        } catch (\Exception $e) {
            return redirect()->route('admin.professionisti.show', $professionista->id)
                ->with('error', 'Errore durante l\'eliminazione della foto: ' . $e->getMessage());
        }
    }

    /**
     * Reset password professionista
     */
    public function resetPassword($id)
    {
        $professionista = Professionista::findOrFail($id);

        // Genera nuova password temporanea
        $nuovaPassword = 'temp' . rand(1000, 9999);
        $scadenza = now()->addHours(24);

        $professionista->utente->update([
            'password' => Hash::make($nuovaPassword),
            'password_temp_expires_at' => $scadenza,
            'deve_cambiare_password' => true,
        ]);

        // Invia email con nuova password
        try {
            // Applica configurazioni SMTP dal database
            \App\Models\Impostazione::applySmtpConfig();

            Mail::to($professionista->utente->email)->send(new PasswordTemporaneaMail(
                $professionista,
                $nuovaPassword,
                $scadenza
            ));
            $emailMessage = ' Email inviata con successo a ' . $professionista->utente->email;
        } catch (\Exception $e) {
            $emailMessage = ' ATTENZIONE: Email non inviata. Comunica manualmente la password: ' . $nuovaPassword . ' - Errore: ' . $e->getMessage();
        }

        return redirect()->route('admin.professionisti.show', $professionista->id)
            ->with('success', "Password resettata! Nuova password temporanea: {$nuovaPassword}. {$emailMessage}");
    }
}
