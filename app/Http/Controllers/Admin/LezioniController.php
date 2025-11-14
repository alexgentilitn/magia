<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lezione;
use App\Models\Programma;
use App\Models\Sede;
use App\Models\Utente;
use App\Models\ImpostazioneSistema;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

/**
 * Controller: Lezioni
 * Funzione: Gestisce le lezioni/corsi del centro fitness
 */
class LezioniController extends Controller
{
    /**
     * Ottieni le impostazioni di sistema con fallback ai valori hardcoded
     * Questo permette al sistema di funzionare anche se la migrazione non è stata eseguita
     */
    private function getImpostazioniSistema()
    {
        $impostazioni = [
            'tipologie' => [],
            'stati' => [],
            'frequenze' => []
        ];

        try {
            // Controlla se la tabella esiste
            if (Schema::hasTable('impostazioni_sistema')) {
                // Usa le impostazioni dal database
                $impostazioni['tipologie'] = ImpostazioneSistema::arraySelect('tipologia_lezione');
                $impostazioni['stati'] = ImpostazioneSistema::arraySelect('stato_lezione');
                $impostazioni['frequenze'] = ImpostazioneSistema::arraySelect('frequenza_ricorrenza');
            } else {
                // Fallback ai valori hardcoded
                throw new \Exception('Tabella non esistente');
            }
        } catch (\Exception $e) {
            // Fallback ai valori hardcoded se la tabella non esiste o c'è un errore
            $impostazioni['tipologie'] = [
                'individuale' => 'Lezione Individuale',
                'gruppo' => 'Lezione di Gruppo',
                'online' => 'Lezione Online',
                'ibrida' => 'Lezione Ibrida (Presenza + Online)',
            ];
            $impostazioni['stati'] = [
                'programmata' => 'Programmata',
                'confermata' => 'Confermata',
                'in_corso' => 'In Corso',
                'completata' => 'Completata',
                'cancellata' => 'Cancellata',
                'rinviata' => 'Rinviata',
            ];
            $impostazioni['frequenze'] = [
                'giornaliera' => 'Ogni giorno',
                'settimanale' => 'Ogni settimana',
                'bisettimanale' => 'Ogni 2 settimane',
                'mensile' => 'Ogni mese',
            ];
        }

        return $impostazioni;
    }
    /**
     * Mostra la lista delle lezioni
     */
    public function index(Request $request)
    {
        try {
            $query = Lezione::with(['programma', 'sede', 'professionista']);

            // Filtro per data
            if ($request->filled('data_da')) {
                $query->where('data', '>=', $request->data_da);
            }
            if ($request->filled('data_a')) {
                $query->where('data', '<=', $request->data_a);
            }

            // Filtro per stato
            if ($request->filled('stato')) {
                $query->where('stato', $request->stato);
            }

            // Filtro per programma
            if ($request->filled('programma_id')) {
                $query->where('programma_id', $request->programma_id);
            }

            // Filtro per sede
            if ($request->filled('sede_id')) {
                $query->where('sede_id', $request->sede_id);
            }

            // Filtro per professionista
            if ($request->filled('professionista_id')) {
                $query->where('professionista_id', $request->professionista_id);
            }

            // Filtro per tipologia
            if ($request->filled('tipologia')) {
                $query->where('tipologia', $request->tipologia);
            }

            // Filtro: solo future
            if ($request->filled('solo_future') && $request->solo_future == '1') {
                $query->future();
            }

            // Filtro: solo passate
            if ($request->filled('solo_passate') && $request->solo_passate == '1') {
                $query->passate();
            }

            // Filtro: solo oggi
            if ($request->filled('solo_oggi') && $request->solo_oggi == '1') {
                $query->oggi();
            }

            // Ordinamento
            $query->orderBy('data', 'desc')->orderBy('ora_inizio', 'desc');

            // Paginazione
            $lezioni = $query->paginate(20);

            // Dati per i filtri
            $programmi = Programma::attivi()->orderBy('nome')->get();
            $sedi = Sede::attive()->orderBy('nome')->get();
            $professionisti = Utente::where('tipo_utente', 'professionista')
                ->orWhere('tipo_utente', 'amministratore')
                ->orderBy('nome')
                ->get();

            // Statistiche rapide
            $statistiche = [
                'totali' => Lezione::count(),
                'oggi' => Lezione::oggi()->count(),
                'future' => Lezione::future()->count(),
                'completate' => Lezione::conStato('completata')->count(),
            ];

            return view('admin.lezioni.index', compact(
                'lezioni',
                'programmi',
                'sedi',
                'professionisti',
                'statistiche'
            ));

        } catch (\Exception $e) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Errore nel caricamento delle lezioni: ' . $e->getMessage());
        }
    }

    /**
     * Mostra il form per creare una nuova lezione
     */
    public function create()
    {
        try {
            $programmi = Programma::attivi()->orderBy('nome')->get();
            $sedi = Sede::attive()->orderBy('nome')->get();
            $professionisti = Utente::where('tipo_utente', 'professionista')
                ->orWhere('tipo_utente', 'amministratore')
                ->orderBy('nome')
                ->get();

            // Ottieni le impostazioni di sistema
            $impostazioni = $this->getImpostazioniSistema();

            return view('admin.lezioni.create', compact(
                'programmi',
                'sedi',
                'professionisti',
                'impostazioni'
            ));

        } catch (\Exception $e) {
            return redirect()->route('admin.lezioni.index')
                ->with('error', 'Errore nel caricamento del form: ' . $e->getMessage());
        }
    }

    /**
     * Salva una nuova lezione
     */
    public function store(Request $request)
    {
        try {
            // Validazione
            $validated = $request->validate([
                'titolo' => 'required|string|max:200',
                'descrizione' => 'nullable|string',
                'tipologia' => 'required|in:individuale,gruppo,online,ibrida',
                'programma_id' => 'nullable|exists:programmi,id',
                'sede_id' => 'nullable|exists:sedes,id',
                'professionista_id' => 'required|exists:utenti,id',
                'data' => 'required|date',
                'ora_inizio' => 'required',
                'ora_fine' => 'required',
                'durata_minuti' => 'nullable|integer|min:15|max:480',
                'posti_totali' => 'required|integer|min:1|max:100',
                'stato' => 'required|in:programmata,confermata,in_corso,completata,cancellata,rinviata',
                'ricorrente' => 'boolean',
                'frequenza_ricorrenza' => 'nullable|in:giornaliera,settimanale,bisettimanale,mensile',
                'fine_ricorrenza' => 'nullable|date|after:data',
                'materiale_necessario' => 'nullable|array',
                'note_interne' => 'nullable|string',
                'note_pubbliche' => 'nullable|string',
                'link_online' => 'nullable|url|max:255',
                'password_online' => 'nullable|string|max:255',
            ]);

            // Calcola durata se non fornita
            if (!isset($validated['durata_minuti']) || !$validated['durata_minuti']) {
                $inizio = Carbon::parse($validated['ora_inizio']);
                $fine = Carbon::parse($validated['ora_fine']);
                $validated['durata_minuti'] = $fine->diffInMinutes($inizio);
            }

            // Assicura che visibile_calendario sia true se non specificato
            if (!isset($validated['visibile_calendario'])) {
                $validated['visibile_calendario'] = true;
            }

            // Crea la lezione
            $lezione = Lezione::create($validated);

            // Se ricorrente, crea le lezioni successive
            if ($request->ricorrente && $request->fine_ricorrenza && $request->frequenza_ricorrenza) {
                $this->creaLezioniRicorrenti($lezione, $request);
            }

            // Restituisce JSON per richieste AJAX, altrimenti redirect
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Lezione creata con successo!',
                    'lezione' => $lezione
                ], 201);
            }

            return redirect()->route('admin.lezioni.show', $lezione->id)
                ->with('success', 'Lezione creata con successo!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Errori di validazione
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Errore di validazione',
                    'errors' => $e->errors()
                ], 422);
            }

            return redirect()->back()
                ->withInput()
                ->withErrors($e->errors());

        } catch (\Exception $e) {
            // Altri errori
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Errore nella creazione della lezione: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', 'Errore nella creazione della lezione: ' . $e->getMessage());
        }
    }

    /**
     * Mostra il dettaglio di una lezione
     */
    public function show($id)
    {
        try {
            $lezione = Lezione::with([
                'programma',
                'sede',
                'professionista',
                'clienti' => function($query) {
                    $query->orderBy('cliente_lezione.data_prenotazione', 'desc');
                }
            ])->findOrFail($id);

            return view('admin.lezioni.show', compact('lezione'));

        } catch (\Exception $e) {
            return redirect()->route('admin.lezioni.index')
                ->with('error', 'Lezione non trovata.');
        }
    }

    /**
     * Mostra il form per modificare una lezione
     */
    public function edit($id)
    {
        try {
            $lezione = Lezione::with(['lezionePadre', 'lezioniFiglie'])->findOrFail($id);

            $programmi = Programma::attivi()->orderBy('nome')->get();
            $sedi = Sede::attive()->orderBy('nome')->get();
            $professionisti = Utente::where('tipo_utente', 'professionista')
                ->orWhere('tipo_utente', 'amministratore')
                ->orderBy('nome')
                ->get();

            // Ottieni le impostazioni di sistema
            $impostazioni = $this->getImpostazioniSistema();

            return view('admin.lezioni.edit', compact(
                'lezione',
                'programmi',
                'sedi',
                'professionisti',
                'impostazioni'
            ));

        } catch (\Exception $e) {
            return redirect()->route('admin.lezioni.index')
                ->with('error', 'Lezione non trovata.');
        }
    }

    /**
     * Aggiorna una lezione
     */
    public function update(Request $request, $id)
    {
        try {
            $lezione = Lezione::findOrFail($id);

            // Validazione
            $validated = $request->validate([
                'titolo' => 'required|string|max:200',
                'descrizione' => 'nullable|string',
                'tipologia' => 'required|in:individuale,gruppo,online,ibrida',
                'programma_id' => 'nullable|exists:programmi,id',
                'sede_id' => 'nullable|exists:sedes,id',
                'professionista_id' => 'required|exists:utenti,id',
                'data' => 'required|date',
                'ora_inizio' => 'required',
                'ora_fine' => 'required',
                'durata_minuti' => 'required|integer|min:15|max:480',
                'posti_totali' => 'required|integer|min:1|max:100',
                'stato' => 'required|in:programmata,confermata,in_corso,completata,cancellata,rinviata',
                'materiale_necessario' => 'nullable|array',
                'note_interne' => 'nullable|string',
                'note_pubbliche' => 'nullable|string',
                'link_online' => 'nullable|url|max:255',
                'password_online' => 'nullable|string|max:255',
                'applica_modifiche' => 'nullable|in:solo_questa,serie_completa,questa_e_future',
                'return' => 'nullable|string|in:calendario',
            ]);

            $applicaModifiche = $request->input('applica_modifiche', 'solo_questa');
            $lezioniAggiornate = 0;

            // Determina quali lezioni aggiornare
            $lezioniDaAggiornare = collect([$lezione]);

            if ($applicaModifiche === 'serie_completa' && $lezione->lezione_padre_id) {
                // Aggiorna tutta la serie (padre + tutte le figlie)
                $lezionePadre = $lezione->lezionePadre;
                $lezioniDaAggiornare = collect([$lezionePadre])->merge($lezionePadre->lezioniFiglie);
            } elseif ($applicaModifiche === 'questa_e_future' && !$lezione->lezione_padre_id) {
                // Aggiorna questa e tutte le figlie future
                $lezioniDaAggiornare = collect([$lezione])->merge($lezione->lezioniFiglie);
            }

            // Prepara i dati da aggiornare (escludi data e applica_modifiche)
            $updateData = collect($validated)->except(['data', 'applica_modifiche'])->toArray();

            // Aggiorna tutte le lezioni selezionate
            foreach ($lezioniDaAggiornare as $lezioneDaAggiornare) {
                $lezioneDaAggiornare->update($updateData);
                $lezioniAggiornate++;
            }

            $messaggio = $lezioniAggiornate === 1
                ? 'Lezione aggiornata con successo!'
                : "Aggiornate $lezioniAggiornate lezioni della serie ricorrente!";

            // Gestisci il redirect in base a dove provieni
            if ($request->input('return') === 'calendario') {
                return redirect()->route('admin.calendario.index')
                    ->with('success', $messaggio);
            }

            return redirect()->route('admin.lezioni.show', $lezione->id)
                ->with('success', $messaggio);

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Errore nell\'aggiornamento della lezione: ' . $e->getMessage());
        }
    }

    /**
     * Elimina una lezione (soft delete)
     */
    public function destroy($id)
    {
        try {
            $lezione = Lezione::findOrFail($id);

            // Verifica se ci sono clienti prenotati
            if ($lezione->posti_occupati > 0) {
                return redirect()->back()
                    ->with('warning', 'Impossibile eliminare la lezione: ci sono ' . $lezione->posti_occupati . ' clienti prenotati. Cambia lo stato in "cancellata" invece.');
            }

            $lezione->delete();

            return redirect()->route('admin.lezioni.index')
                ->with('success', 'Lezione eliminata con successo!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Errore nell\'eliminazione della lezione: ' . $e->getMessage());
        }
    }

    /**
     * Cambia lo stato di una lezione
     */
    public function cambiaStato(Request $request, $id)
    {
        try {
            $lezione = Lezione::findOrFail($id);

            $request->validate([
                'stato' => 'required|in:programmata,confermata,in_corso,completata,cancellata,rinviata'
            ]);

            $lezione->update(['stato' => $request->stato]);

            return redirect()->back()
                ->with('success', 'Stato lezione aggiornato a: ' . ucfirst($request->stato));

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Errore nel cambio stato: ' . $e->getMessage());
        }
    }

    /**
     * Crea lezioni ricorrenti
     */
    private function creaLezioniRicorrenti($lezionePadre, $request)
    {
        $dataInizio = Carbon::parse($request->data);
        $dataFine = Carbon::parse($request->fine_ricorrenza);
        $frequenza = $request->frequenza_ricorrenza;

        $dataCorrente = clone $dataInizio;
        $lezioniCreate = 0;
        $maxLezioni = 52; // Limite massimo per sicurezza

        while ($dataCorrente->lte($dataFine) && $lezioniCreate < $maxLezioni) {
            // Salta la prima (è la lezione padre)
            switch ($frequenza) {
                case 'giornaliera':
                    $dataCorrente->addDay();
                    break;
                case 'settimanale':
                    $dataCorrente->addWeek();
                    break;
                case 'bisettimanale':
                    $dataCorrente->addWeeks(2);
                    break;
                case 'mensile':
                    $dataCorrente->addMonth();
                    break;
            }

            if ($dataCorrente->lte($dataFine)) {
                // Crea lezione figlia
                $nuovaLezione = $lezionePadre->replicate();
                $nuovaLezione->data = $dataCorrente->toDateString();
                $nuovaLezione->lezione_padre_id = $lezionePadre->id;
                $nuovaLezione->posti_occupati = 0;
                $nuovaLezione->visibile_calendario = true;
                $nuovaLezione->save();

                $lezioniCreate++;
            }
        }

        return $lezioniCreate;
    }

    /**
     * Gestisce le prenotazioni (check-in/check-out)
     */
    public function gestionePrenotazioni($id)
    {
        try {
            $lezione = Lezione::with([
                'clienti' => function($query) {
                    $query->orderBy('cliente_lezione.data_prenotazione', 'asc');
                },
                'clienti.cliente',
                'professionista',
                'sede',
                'programma'
            ])->findOrFail($id);

            return view('admin.lezioni.prenotazioni', compact('lezione'));

        } catch (\Exception $e) {
            return redirect()->route('admin.lezioni.index')
                ->with('error', 'Lezione non trovata.');
        }
    }

    /**
     * Check-in cliente
     */
    public function checkIn(Request $request, $lezioneId, $clienteId)
    {
        try {
            $lezione = Lezione::findOrFail($lezioneId);

            // Aggiorna pivot table
            $lezione->clienti()->updateExistingPivot($clienteId, [
                'check_in' => now(),
                'stato' => 'presente'
            ]);

            return redirect()->back()
                ->with('success', 'Check-in effettuato!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Errore nel check-in: ' . $e->getMessage());
        }
    }

    /**
     * Check-out cliente
     */
    public function checkOut(Request $request, $lezioneId, $clienteId)
    {
        try {
            $lezione = Lezione::findOrFail($lezioneId);

            // Aggiorna pivot table
            $lezione->clienti()->updateExistingPivot($clienteId, [
                'check_out' => now()
            ]);

            return redirect()->back()
                ->with('success', 'Check-out effettuato!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Errore nel check-out: ' . $e->getMessage());
        }
    }

    /**
     * Aggiungi prenotazione
     */
    public function aggiungiPrenotazione(Request $request, $lezioneId)
    {
        try {
            $lezione = Lezione::findOrFail($lezioneId);

            // Validazione
            $request->validate([
                'cliente_id' => 'required|exists:utenti,id'
            ]);

            // Verifica posti disponibili
            if ($lezione->postiDisponibili() <= 0) {
                return redirect()->back()
                    ->with('error', 'Nessun posto disponibile per questa lezione.');
            }

            // Verifica se già prenotato
            if ($lezione->clienti()->where('utenti.id', $request->cliente_id)->exists()) {
                return redirect()->back()
                    ->with('error', 'Questo cliente ha già una prenotazione per questa lezione.');
            }

            // Aggiungi prenotazione
            $lezione->clienti()->attach($request->cliente_id, [
                'data_prenotazione' => now(),
                'stato' => 'prenotato'
            ]);

            // Aggiorna contatore posti occupati
            $lezione->increment('posti_occupati');

            return redirect()->back()
                ->with('success', 'Prenotazione aggiunta con successo!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Errore nell\'aggiunta della prenotazione: ' . $e->getMessage());
        }
    }

    /**
     * Rimuovi prenotazione
     */
    public function rimuoviPrenotazione($lezioneId, $clienteId)
    {
        try {
            $lezione = Lezione::findOrFail($lezioneId);

            // Verifica se la prenotazione esiste
            if (!$lezione->clienti()->where('utenti.id', $clienteId)->exists()) {
                return redirect()->back()
                    ->with('error', 'Prenotazione non trovata.');
            }

            // Rimuovi prenotazione
            $lezione->clienti()->detach($clienteId);

            // Aggiorna contatore posti occupati
            if ($lezione->posti_occupati > 0) {
                $lezione->decrement('posti_occupati');
            }

            return redirect()->back()
                ->with('success', 'Prenotazione rimossa con successo!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Errore nella rimozione della prenotazione: ' . $e->getMessage());
        }
    }

    /**
     * Segna come assente
     */
    public function segnaAssente($lezioneId, $clienteId)
    {
        try {
            $lezione = Lezione::findOrFail($lezioneId);

            // Aggiorna pivot table
            $lezione->clienti()->updateExistingPivot($clienteId, [
                'stato' => 'assente'
            ]);

            return redirect()->back()
                ->with('success', 'Cliente segnato come assente.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Errore: ' . $e->getMessage());
        }
    }

    /**
     * Annulla assenza (riporta a prenotato)
     */
    public function annullaAssenza($lezioneId, $clienteId)
    {
        try {
            $lezione = Lezione::findOrFail($lezioneId);

            // Aggiorna pivot table riportando a prenotato
            $lezione->clienti()->updateExistingPivot($clienteId, [
                'stato' => 'prenotato'
            ]);

            return redirect()->back()
                ->with('success', 'Assenza annullata. Cliente riportato a prenotato.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Errore: ' . $e->getMessage());
        }
    }

    /**
     * Mostra form per modifica multipla lezioni
     */
    public function editMultiple(Request $request)
    {
        try {
            // Ottieni gli ID delle lezioni da modificare
            $ids = explode(',', $request->get('ids', ''));
            $ids = array_filter($ids); // Rimuovi valori vuoti

            if (empty($ids)) {
                return redirect()->route('admin.lezioni.index')
                    ->with('error', 'Nessuna lezione selezionata.');
            }

            // Carica le lezioni selezionate
            $lezioni = Lezione::with(['programma', 'sede', 'professionista'])
                ->whereIn('id', $ids)
                ->orderBy('data')
                ->orderBy('ora_inizio')
                ->get();

            if ($lezioni->isEmpty()) {
                return redirect()->route('admin.lezioni.index')
                    ->with('error', 'Lezioni non trovate.');
            }

            // Dati per i dropdown
            $programmi = Programma::attivi()->orderBy('nome')->get();
            $sedi = Sede::attive()->orderBy('nome')->get();
            $professionisti = Utente::where('tipo_utente', 'professionista')
                ->orWhere('tipo_utente', 'amministratore')
                ->orderBy('nome')
                ->get();

            // Ottieni le impostazioni di sistema
            $impostazioni = $this->getImpostazioniSistema();

            return view('admin.lezioni.edit-multiple', compact(
                'lezioni',
                'programmi',
                'sedi',
                'professionisti',
                'impostazioni'
            ));

        } catch (\Exception $e) {
            return redirect()->route('admin.lezioni.index')
                ->with('error', 'Errore nel caricamento: ' . $e->getMessage());
        }
    }

    /**
     * Aggiorna multiple lezioni
     */
    public function updateMultiple(Request $request)
    {
        try {
            // Validazione
            $validated = $request->validate([
                'lezione_ids' => 'required|array',
                'lezione_ids.*' => 'exists:lezioni,id',
                'tipologia' => 'nullable|in:individuale,gruppo,online,ibrida',
                'stato' => 'nullable|in:programmata,confermata,in_corso,completata,cancellata,rinviata',
                'professionista_id' => 'nullable|exists:utenti,id',
                'sede_id' => 'nullable|exists:sedes,id',
                'programma_id' => 'nullable|exists:programmi,id',
                'durata_minuti' => 'nullable|integer|min:15|max:480',
                'posti_totali' => 'nullable|integer|min:1|max:100',
                'descrizione' => 'nullable|string',
                'note_pubbliche' => 'nullable|string',
                'note_interne' => 'nullable|string',
                'link_online' => 'nullable|url|max:255',
                'password_online' => 'nullable|string|max:255',
            ]);

            $lezioneIds = $validated['lezione_ids'];
            unset($validated['lezione_ids']);

            // Prepara i dati da aggiornare (solo campi compilati)
            $updateData = [];
            foreach ($validated as $campo => $valore) {
                if ($valore !== null && $valore !== '') {
                    $updateData[$campo] = $valore;
                }
            }

            if (empty($updateData)) {
                return redirect()->route('admin.lezioni.index')
                    ->with('warning', 'Nessun campo è stato modificato.');
            }

            // Aggiorna tutte le lezioni selezionate
            $aggiornate = Lezione::whereIn('id', $lezioneIds)->update($updateData);

            $campiModificati = array_keys($updateData);
            $campiModificatiStr = implode(', ', $campiModificati);

            return redirect()->route('admin.lezioni.index')
                ->with('success', "✅ Aggiornate $aggiornate lezioni con successo!

                Campi modificati: $campiModificatiStr");

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Errore nell\'aggiornamento: ' . $e->getMessage());
        }
    }
}
