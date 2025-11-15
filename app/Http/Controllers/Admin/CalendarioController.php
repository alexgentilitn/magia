<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lezione;
use App\Models\Professionista;
use App\Models\Sede;
use App\Models\Cliente;
use App\Mail\ConfermaPrenotazioneMail;
use App\Mail\ReminderLezioneMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

/**
 * Controller: Gestione Calendario Visuale
 * Visualizzazione e gestione lezioni in formato calendario
 */
class CalendarioController extends Controller
{
    /**
     * Mostra il calendario visuale
     */
    public function index(Request $request)
    {
        // Ottieni professionisti per filtro
        $professionisti = Professionista::with('utente')
            ->where('stato', 'attivo')
            ->orderBy('cognome')
            ->orderBy('nome')
            ->get();

        // Ottieni sedi per filtro
        $sedi = Sede::where('attiva', true)
            ->orderBy('nome')
            ->get();

        return view('admin.calendario.index', compact('professionisti', 'sedi'));
    }

    /**
     * API: Ottieni eventi per il calendario (formato FullCalendar)
     */
    public function getEvents(Request $request)
    {
        $query = Lezione::with(['professionista', 'sede', 'programma'])
            ->where('visibile_calendario', true);

        // Filtro per date (richiesto da FullCalendar)
        if ($request->has('start') && $request->has('end')) {
            $start = Carbon::parse($request->start)->startOfDay();
            $end = Carbon::parse($request->end)->endOfDay();
            $query->whereBetween('data', [$start, $end]);
        }

        // Filtro per professionista
        if ($request->filled('professionista_id')) {
            $query->where('professionista_id', $request->professionista_id);
        }

        // Filtro per sede
        if ($request->filled('sede_id')) {
            $query->where('sede_id', $request->sede_id);
        }

        // Filtro per tipologia
        if ($request->filled('tipologia')) {
            $query->where('tipologia', $request->tipologia);
        }

        // Filtro per stato
        if ($request->filled('stato')) {
            $query->where('stato', $request->stato);
        }

        $lezioni = $query->orderBy('data')
            ->orderBy('ora_inizio')
            ->get();

        // Converti in formato FullCalendar
        $events = $lezioni->map(function ($lezione) {
            return [
                'id' => $lezione->id,
                'title' => $this->formatEventTitle($lezione),
                'start' => $lezione->data->format('Y-m-d') . 'T' . Carbon::parse($lezione->ora_inizio)->format('H:i:s'),
                'end' => $lezione->data->format('Y-m-d') . 'T' . Carbon::parse($lezione->ora_fine)->format('H:i:s'),
                'backgroundColor' => $this->getColorByType($lezione->tipologia),
                'borderColor' => $this->getBorderColorByStatus($lezione->stato),
                'textColor' => '#ffffff',
                'extendedProps' => [
                    'tipologia' => $lezione->tipologia,
                    'stato' => $lezione->stato,
                    'professionista' => $lezione->professionista ? $lezione->professionista->nome . ' ' . $lezione->professionista->cognome : 'Non assegnato',
                    'sede' => $lezione->sede ? $lezione->sede->nome : 'Non specificata',
                    'posti' => $lezione->posti_occupati . '/' . $lezione->posti_totali,
                    'descrizione' => $lezione->descrizione,
                    'programma' => $lezione->programma ? $lezione->programma->nome : null,
                ],
            ];
        });

        return response()->json($events);
    }

    /**
     * Formatta il titolo dell'evento per il calendario
     */
    private function formatEventTitle($lezione)
    {
        $title = $lezione->titolo;

        // Aggiungi posti se gruppo
        if ($lezione->tipologia === 'gruppo') {
            $title .= " ({$lezione->posti_occupati}/{$lezione->posti_totali})";
        }

        return $title;
    }

    /**
     * Ottieni colore in base alla tipologia
     */
    private function getColorByType($tipologia)
    {
        return match($tipologia) {
            'gruppo' => '#9c27b0',      // viola-magia
            'individuale' => '#e91e63', // fucsia-magia
            'online' => '#2196f3',      // blue
            'ibrida' => '#ff9800',      // orange
            default => '#757575',       // gray
        };
    }

    /**
     * Ottieni colore bordo in base allo stato
     */
    private function getBorderColorByStatus($stato)
    {
        return match($stato) {
            'programmata' => '#ffa726',   // orange
            'confermata' => '#66bb6a',    // green
            'in_corso' => '#42a5f5',      // blue
            'completata' => '#26a69a',    // teal
            'cancellata' => '#ef5350',    // red
            'rinviata' => '#ffa726',      // orange
            default => '#9e9e9e',         // gray
        };
    }

    /**
     * Mostra dettagli lezione in modal
     */
    public function show($id)
    {
        $lezione = Lezione::with([
            'professionista',
            'sede',
            'programma',
            'clienti' => function($query) {
                $query->withPivot('stato', 'check_in', 'check_out', 'created_at');
            }
        ])->findOrFail($id);

        return response()->json([
            'lezione' => $lezione,
            'html' => view('admin.calendario.partials.modal-dettagli', compact('lezione'))->render()
        ]);
    }

    /**
     * Prenota un cliente a una lezione
     */
    public function prenota(Request $request, $id)
    {
        $request->validate([
            'cliente_id' => 'required|exists:clienti,id'
        ]);

        $lezione = Lezione::with(['professionista', 'sede'])->findOrFail($id);
        $clienteId = $request->cliente_id;
        $cliente = Cliente::findOrFail($clienteId);

        // Verifica se il cliente è già prenotato
        if ($lezione->clienti()->where('cliente_id', $clienteId)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Il cliente è già prenotato per questa lezione.'
            ], 422);
        }

        // Verifica posti disponibili
        $postiDisponibili = $lezione->posti_totali - $lezione->posti_occupati;

        if ($postiDisponibili <= 0) {
            // Nessun posto disponibile - aggiungi a lista d'attesa
            $lezione->clienti()->attach($clienteId, [
                'stato' => 'lista_attesa',
                'data_prenotazione' => now()->format('Y-m-d'),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Invia email di conferma lista d'attesa
            try {
                Mail::to($cliente->email)->send(new ConfermaPrenotazioneMail($lezione, $cliente, true));
            } catch (\Exception $e) {
                \Log::error('Errore invio email lista attesa: ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'lista_attesa' => true,
                'message' => 'Nessun posto disponibile. Cliente aggiunto alla lista d\'attesa.',
                'posti_occupati' => $lezione->posti_occupati,
                'posti_totali' => $lezione->posti_totali
            ]);
        }

        // Prenota il cliente
        $lezione->clienti()->attach($clienteId, [
            'stato' => 'prenotato',
            'data_prenotazione' => now()->format('Y-m-d'),
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Incrementa posti occupati
        $lezione->increment('posti_occupati');

        // Invia email di conferma prenotazione
        try {
            Mail::to($cliente->email)->send(new ConfermaPrenotazioneMail($lezione, $cliente, false));
        } catch (\Exception $e) {
            \Log::error('Errore invio email conferma prenotazione: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'Prenotazione effettuata con successo!',
            'posti_occupati' => $lezione->posti_occupati,
            'posti_totali' => $lezione->posti_totali
        ]);
    }

    /**
     * Invia reminder email a tutti i clienti prenotati per una lezione
     */
    public function inviaReminder($id)
    {
        $lezione = Lezione::with(['professionista', 'sede'])->findOrFail($id);

        // Ottieni tutti i clienti con stato 'prenotato' (non lista d'attesa)
        $clienti = $lezione->clienti()
            ->wherePivot('stato', 'prenotato')
            ->get();

        if ($clienti->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Nessun cliente prenotato per questa lezione.'
            ], 422);
        }

        $inviati = 0;
        $errori = 0;

        foreach ($clienti as $cliente) {
            try {
                Mail::to($cliente->email)->send(new ReminderLezioneMail($lezione, $cliente));
                $inviati++;
            } catch (\Exception $e) {
                \Log::error("Errore invio reminder a {$cliente->email}: " . $e->getMessage());
                $errori++;
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Reminder inviati: {$inviati} successi, {$errori} errori.",
            'inviati' => $inviati,
            'errori' => $errori
        ]);
    }

    /**
     * Annulla prenotazione di un cliente
     */
    public function annullaPrenotazione($lezioneId, $clienteId)
    {
        $lezione = Lezione::findOrFail($lezioneId);

        // Verifica se il cliente è prenotato
        $prenotazione = $lezione->clienti()
            ->where('cliente_id', $clienteId)
            ->first();

        if (!$prenotazione) {
            return response()->json([
                'success' => false,
                'message' => 'Prenotazione non trovata.'
            ], 404);
        }

        $statoPrenotazione = $prenotazione->pivot->stato;

        // Rimuovi prenotazione
        $lezione->clienti()->detach($clienteId);

        // Se era una prenotazione confermata/prenotata, decrementa posti occupati
        if (in_array($statoPrenotazione, ['prenotato', 'confermato'])) {
            $lezione->decrement('posti_occupati');

            // Controlla se c'è qualcuno in lista d'attesa
            $primoInAttesa = $lezione->clienti()
                ->wherePivot('stato', 'lista_attesa')
                ->orderBy('cliente_lezione.created_at')
                ->first();

            if ($primoInAttesa) {
                // Promuovi da lista d'attesa a prenotato
                $lezione->clienti()->updateExistingPivot($primoInAttesa->id, [
                    'stato' => 'prenotato',
                    'updated_at' => now()
                ]);

                $lezione->increment('posti_occupati');

                return response()->json([
                    'success' => true,
                    'message' => 'Prenotazione annullata. Un cliente dalla lista d\'attesa è stato promosso.',
                    'posti_occupati' => $lezione->posti_occupati,
                    'posti_totali' => $lezione->posti_totali,
                    'promosso' => [
                        'id' => $primoInAttesa->id,
                        'nome' => $primoInAttesa->nome . ' ' . $primoInAttesa->cognome
                    ]
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Prenotazione annullata con successo.',
            'posti_occupati' => $lezione->posti_occupati,
            'posti_totali' => $lezione->posti_totali
        ]);
    }

    /**
     * Modifica durata lezione (resize calendario)
     */
    public function resize(Request $request, $id)
    {
        $request->validate([
            'start' => 'required|date',
            'end' => 'required|date|after:start'
        ]);

        $lezione = Lezione::with(['professionista', 'sede'])->findOrFail($id);

        try {
            // Parse nuove date/orari
            $nuovaDataInizio = Carbon::parse($request->start);
            $nuovaDataFine = Carbon::parse($request->end);

            // Resize può cambiare sia inizio che fine
            $nuovaOraInizio = $nuovaDataInizio->format('H:i:s');
            $nuovaOraFine = $nuovaDataFine->format('H:i:s');
            $data = $nuovaDataInizio->format('Y-m-d');

            // Calcola durata in minuti
            $durata = $nuovaDataInizio->diffInMinutes($nuovaDataFine);

            if ($durata < 15) {
                return response()->json([
                    'success' => false,
                    'message' => 'La durata minima della lezione è di 15 minuti.'
                ], 422);
            }

            if ($durata > 240) {
                return response()->json([
                    'success' => false,
                    'message' => 'La durata massima della lezione è di 4 ore.'
                ], 422);
            }

            // Verifica conflitti con stesso professionista
            $conflittoProfessionista = Lezione::where('id', '!=', $id)
                ->where('professionista_id', $lezione->professionista_id)
                ->where('data', $data)
                ->where(function($query) use ($nuovaOraInizio, $nuovaOraFine) {
                    $query->whereBetween('ora_inizio', [$nuovaOraInizio, $nuovaOraFine])
                          ->orWhereBetween('ora_fine', [$nuovaOraInizio, $nuovaOraFine])
                          ->orWhere(function($q) use ($nuovaOraInizio, $nuovaOraFine) {
                              $q->where('ora_inizio', '<=', $nuovaOraInizio)
                                ->where('ora_fine', '>=', $nuovaOraFine);
                          });
                })
                ->exists();

            if ($conflittoProfessionista) {
                return response()->json([
                    'success' => false,
                    'message' => 'Conflitto di orario: il professionista ha già un\'altra lezione in questo orario.'
                ], 422);
            }

            // Verifica conflitti con stessa sede
            $conflittoSede = Lezione::where('id', '!=', $id)
                ->where('sede_id', $lezione->sede_id)
                ->where('data', $data)
                ->where(function($query) use ($nuovaOraInizio, $nuovaOraFine) {
                    $query->whereBetween('ora_inizio', [$nuovaOraInizio, $nuovaOraFine])
                          ->orWhereBetween('ora_fine', [$nuovaOraInizio, $nuovaOraFine])
                          ->orWhere(function($q) use ($nuovaOraInizio, $nuovaOraFine) {
                              $q->where('ora_inizio', '<=', $nuovaOraInizio)
                                ->where('ora_fine', '>=', $nuovaOraFine);
                          });
                })
                ->exists();

            if ($conflittoSede) {
                return response()->json([
                    'success' => false,
                    'message' => 'Conflitto di orario: la sede è già occupata in questo orario.'
                ], 422);
            }

            // Aggiorna durata lezione
            $lezione->update([
                'ora_inizio' => $nuovaOraInizio,
                'ora_fine' => $nuovaOraFine
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Durata lezione modificata con successo!',
                'lezione' => [
                    'id' => $lezione->id,
                    'ora_inizio' => Carbon::parse($lezione->ora_inizio)->format('H:i'),
                    'ora_fine' => Carbon::parse($lezione->ora_fine)->format('H:i'),
                    'durata_minuti' => $durata
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Errore durante la modifica: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Sposta lezione (drag & drop calendario)
     */
    public function move(Request $request, $id)
    {
        $request->validate([
            'start' => 'required|date',
            'end' => 'required|date|after:start'
        ]);

        $lezione = Lezione::with(['professionista', 'sede'])->findOrFail($id);

        try {
            // Parse nuove date/orari
            $nuovaData = Carbon::parse($request->start);
            $nuovaDataFine = Carbon::parse($request->end);

            $nuovaOraInizio = $nuovaData->format('H:i:s');
            $nuovaOraFine = $nuovaDataFine->format('H:i:s');

            // Verifica conflitti con stesso professionista
            $conflittoProfessionista = Lezione::where('id', '!=', $id)
                ->where('professionista_id', $lezione->professionista_id)
                ->where('data', $nuovaData->format('Y-m-d'))
                ->where(function($query) use ($nuovaOraInizio, $nuovaOraFine) {
                    $query->whereBetween('ora_inizio', [$nuovaOraInizio, $nuovaOraFine])
                          ->orWhereBetween('ora_fine', [$nuovaOraInizio, $nuovaOraFine])
                          ->orWhere(function($q) use ($nuovaOraInizio, $nuovaOraFine) {
                              $q->where('ora_inizio', '<=', $nuovaOraInizio)
                                ->where('ora_fine', '>=', $nuovaOraFine);
                          });
                })
                ->exists();

            if ($conflittoProfessionista) {
                return response()->json([
                    'success' => false,
                    'message' => 'Conflitto di orario: il professionista ha già un\'altra lezione in questo orario.'
                ], 422);
            }

            // Verifica conflitti con stessa sede
            $conflittoSede = Lezione::where('id', '!=', $id)
                ->where('sede_id', $lezione->sede_id)
                ->where('data', $nuovaData->format('Y-m-d'))
                ->where(function($query) use ($nuovaOraInizio, $nuovaOraFine) {
                    $query->whereBetween('ora_inizio', [$nuovaOraInizio, $nuovaOraFine])
                          ->orWhereBetween('ora_fine', [$nuovaOraInizio, $nuovaOraFine])
                          ->orWhere(function($q) use ($nuovaOraInizio, $nuovaOraFine) {
                              $q->where('ora_inizio', '<=', $nuovaOraInizio)
                                ->where('ora_fine', '>=', $nuovaOraFine);
                          });
                })
                ->exists();

            if ($conflittoSede) {
                return response()->json([
                    'success' => false,
                    'message' => 'Conflitto di orario: la sede è già occupata in questo orario.'
                ], 422);
            }

            // Aggiorna lezione
            $lezione->update([
                'data' => $nuovaData->format('Y-m-d'),
                'ora_inizio' => $nuovaOraInizio,
                'ora_fine' => $nuovaOraFine
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Lezione spostata con successo!',
                'lezione' => [
                    'id' => $lezione->id,
                    'data' => $lezione->data->format('d/m/Y'),
                    'ora_inizio' => Carbon::parse($lezione->ora_inizio)->format('H:i'),
                    'ora_fine' => Carbon::parse($lezione->ora_fine)->format('H:i')
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Errore durante lo spostamento: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Elimina lezione
     */
    public function destroy(Request $request, $id)
    {
        try {
            $lezione = Lezione::findOrFail($id);
            $eliminaTutteSerie = $request->input('elimina_serie', false);

            // Controlla se ci sono prenotazioni
            $prenotazioni = $lezione->clienti()->count();

            // Se è una lezione ricorrente e si vuole eliminare tutta la serie
            if ($eliminaTutteSerie && $lezione->isLezioneRicorrente()) {
                $count = $lezione->eliminaOccorrenzeFuture($lezione->isLezionePadre());

                $message = "Eliminate {$count} occorrenze della serie ricorrente.";
                if ($prenotazioni > 0) {
                    $message .= " Le prenotazioni associate sono state cancellate.";
                }
            } else {
                // Elimina solo questa lezione
                $lezione->delete();

                $message = $prenotazioni > 0
                    ? "Lezione eliminata con successo. {$prenotazioni} prenotazioni sono state cancellate."
                    : "Lezione eliminata con successo.";
            }

            return response()->json([
                'success' => true,
                'message' => $message
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Errore durante l\'eliminazione: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check-in cliente
     */
    public function checkIn($lezioneId, $clienteId)
    {
        try {
            $lezione = Lezione::findOrFail($lezioneId);

            // Verifica che il cliente sia prenotato
            $prenotazione = $lezione->clienti()->where('cliente_id', $clienteId)->first();

            if (!$prenotazione) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cliente non prenotato per questa lezione.'
                ], 404);
            }

            // Aggiorna stato e check-in
            $lezione->clienti()->updateExistingPivot($clienteId, [
                'stato' => 'presente',
                'check_in' => now(),
                'updated_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Check-in effettuato con successo!',
                'check_in' => now()->format('H:i')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Errore durante il check-in: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check-out cliente
     */
    public function checkOut($lezioneId, $clienteId)
    {
        try {
            $lezione = Lezione::findOrFail($lezioneId);

            // Verifica che il cliente sia presente
            $prenotazione = $lezione->clienti()
                ->where('cliente_id', $clienteId)
                ->wherePivot('stato', 'presente')
                ->first();

            if (!$prenotazione) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cliente non presente o non ha effettuato il check-in.'
                ], 404);
            }

            // Aggiorna check-out
            $lezione->clienti()->updateExistingPivot($clienteId, [
                'check_out' => now(),
                'updated_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Check-out effettuato con successo!',
                'check_out' => now()->format('H:i')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Errore durante il check-out: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Segna cliente come assente
     */
    public function segnaAssente($lezioneId, $clienteId)
    {
        try {
            $lezione = Lezione::findOrFail($lezioneId);

            // Verifica che il cliente sia prenotato
            $prenotazione = $lezione->clienti()->where('cliente_id', $clienteId)->first();

            if (!$prenotazione) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cliente non prenotato per questa lezione.'
                ], 404);
            }

            // Aggiorna stato
            $lezione->clienti()->updateExistingPivot($clienteId, [
                'stato' => 'assente',
                'updated_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Cliente segnato come assente.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Errore: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Annulla assenza cliente
     */
    public function annullaAssenza($lezioneId, $clienteId)
    {
        try {
            $lezione = Lezione::findOrFail($lezioneId);

            // Verifica che il cliente sia assente
            $prenotazione = $lezione->clienti()
                ->where('cliente_id', $clienteId)
                ->wherePivot('stato', 'assente')
                ->first();

            if (!$prenotazione) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cliente non segnato come assente.'
                ], 404);
            }

            // Ripristina stato prenotato
            $lezione->clienti()->updateExistingPivot($clienteId, [
                'stato' => 'prenotato',
                'updated_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Assenza annullata. Cliente ripristinato.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Errore: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export calendario in PDF
     */
    public function exportPdf(Request $request)
    {
        // Parametri mese/anno (default: mese corrente)
        $mese = $request->get('mese', now()->month);
        $anno = $request->get('anno', now()->year);

        // Crea date inizio e fine mese
        $dataInizio = Carbon::create($anno, $mese, 1)->startOfMonth();
        $dataFine = Carbon::create($anno, $mese, 1)->endOfMonth();

        // Recupera lezioni del mese
        $lezioni = Lezione::with(['professionista.utente', 'sede', 'programma', 'clienti'])
            ->where('visibile_calendario', true)
            ->whereBetween('data', [$dataInizio, $dataFine])
            ->orderBy('data')
            ->orderBy('ora_inizio')
            ->get();

        // Raggruppa lezioni per giorno
        $lezioniPerGiorno = $lezioni->groupBy(function($lezione) {
            return $lezione->data->format('Y-m-d');
        });

        // Statistiche mese
        $statistiche = [
            'totale_lezioni' => $lezioni->count(),
            'totale_partecipanti' => $lezioni->sum(function($lezione) {
                return $lezione->clienti->count();
            }),
            'posti_disponibili' => $lezioni->sum('posti_totali'),
            'posti_occupati' => $lezioni->sum('posti_occupati'),
        ];

        // Genera PDF
        $pdf = \PDF::loadView('admin.calendario.pdf.mensile', [
            'lezioni' => $lezioni,
            'lezioniPerGiorno' => $lezioniPerGiorno,
            'statistiche' => $statistiche,
            'mese' => $mese,
            'anno' => $anno,
            'dataInizio' => $dataInizio,
            'dataFine' => $dataFine,
            'nomeMese' => $dataInizio->locale('it')->isoFormat('MMMM YYYY'),
        ]);

        $nomeFile = "calendario-{$anno}-{$mese}.pdf";

        return $pdf->download($nomeFile);
    }
}
