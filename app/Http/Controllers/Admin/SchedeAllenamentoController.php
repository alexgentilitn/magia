<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchedaAllenamento;
use App\Models\SchedaEsercizio;
use App\Models\Cliente;
use App\Models\Utente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class SchedeAllenamentoController extends Controller
{
    /**
     * Mostra elenco di tutte le schede
     */
    public function index(Request $request)
    {
        $query = SchedaAllenamento::with(['cliente', 'professionista']);

        // Filtro per cliente
        if ($request->filled('cliente_id')) {
            $query->where('cliente_id', $request->cliente_id);
        }

        // Filtro per stato
        if ($request->filled('stato')) {
            $query->where('stato', $request->stato);
        }

        // Filtro per professionista
        if ($request->filled('professionista_id')) {
            $query->where('professionista_id', $request->professionista_id);
        }

        // Ordinamento
        $query->orderBy('created_at', 'desc');

        $schede = $query->paginate(20);
        $clienti = Cliente::attivi()->orderBy('cognome')->get();
        $professionisti = Utente::where('ruolo', 'admin')->orWhere('ruolo', 'professionista')->get();

        return view('admin.schede.index', compact('schede', 'clienti', 'professionisti'));
    }

    /**
     * Mostra form per creare nuova scheda
     */
    public function create(Request $request)
    {
        $clienti = Cliente::attivi()->orderBy('cognome')->get();
        $cliente_selezionata = null;

        // Se viene passato un cliente_id, pre-selezionalo
        if ($request->filled('cliente_id')) {
            $cliente_selezionata = Cliente::find($request->cliente_id);
        }

        $giorni_settimana = ['Lunedì', 'Martedì', 'Mercoledì', 'Giovedì', 'Venerdì', 'Sabato', 'Domenica'];

        return view('admin.schede.create', compact('clienti', 'cliente_selezionata', 'giorni_settimana'));
    }

    /**
     * Salva nuova scheda nel database
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'cliente_id' => ['required', 'exists:clienti,id'],
            'nome_scheda' => ['required', 'string', 'max:255'],
            'descrizione' => ['nullable', 'string'],
            'obiettivi' => ['nullable', 'string'],
            'data_inizio' => ['nullable', 'date'],
            'data_fine' => ['nullable', 'date', 'after_or_equal:data_inizio'],
            'durata_settimane' => ['nullable', 'integer', 'min:1', 'max:52'],
            'note_generali' => ['nullable', 'string'],
            'note_alimentazione' => ['nullable', 'string'],
            'consigli_professionista' => ['nullable', 'string'],
            'stato' => ['required', 'in:bozza,attiva'],

            // Esercizi (array)
            'esercizi' => ['nullable', 'array'],
            'esercizi.*.giorno_settimana' => ['required', 'string'],
            'esercizi.*.ordine' => ['required', 'integer'],
            'esercizi.*.nome_esercizio' => ['required', 'string', 'max:255'],
            'esercizi.*.descrizione' => ['nullable', 'string'],
            'esercizi.*.serie' => ['nullable', 'integer', 'min:1'],
            'esercizi.*.ripetizioni' => ['nullable', 'string'],
            'esercizi.*.recupero_secondi' => ['nullable', 'integer', 'min:0'],
            'esercizi.*.peso_suggerito' => ['nullable', 'string'],
            'esercizi.*.categoria' => ['nullable', 'in:forza,cardio,stretching,mobilità,altro'],
            'esercizi.*.note_esecuzione' => ['nullable', 'string'],
        ]);

        DB::beginTransaction();

        try {
            // Crea la scheda
            $scheda = SchedaAllenamento::create([
                'cliente_id' => $validated['cliente_id'],
                'professionista_id' => Auth::id(),
                'nome_scheda' => $validated['nome_scheda'],
                'descrizione' => $validated['descrizione'] ?? null,
                'obiettivi' => $validated['obiettivi'] ?? null,
                'data_inizio' => $validated['data_inizio'] ?? null,
                'data_fine' => $validated['data_fine'] ?? null,
                'durata_settimane' => $validated['durata_settimane'] ?? null,
                'note_generali' => $validated['note_generali'] ?? null,
                'note_alimentazione' => $validated['note_alimentazione'] ?? null,
                'consigli_professionista' => $validated['consigli_professionista'] ?? null,
                'stato' => $validated['stato'],
            ]);

            // Aggiungi esercizi se presenti
            if (!empty($validated['esercizi'])) {
                foreach ($validated['esercizi'] as $esercizio_data) {
                    SchedaEsercizio::create([
                        'scheda_allenamento_id' => $scheda->id,
                        'giorno_settimana' => $esercizio_data['giorno_settimana'],
                        'ordine' => $esercizio_data['ordine'],
                        'nome_esercizio' => $esercizio_data['nome_esercizio'],
                        'descrizione' => $esercizio_data['descrizione'] ?? null,
                        'serie' => $esercizio_data['serie'] ?? null,
                        'ripetizioni' => $esercizio_data['ripetizioni'] ?? null,
                        'recupero_secondi' => $esercizio_data['recupero_secondi'] ?? null,
                        'peso_suggerito' => $esercizio_data['peso_suggerito'] ?? null,
                        'categoria' => $esercizio_data['categoria'] ?? 'forza',
                        'note_esecuzione' => $esercizio_data['note_esecuzione'] ?? null,
                    ]);
                }
            }

            DB::commit();

            return redirect()
                ->route('admin.schede.show', $scheda)
                ->with('success', 'Scheda di allenamento creata con successo!');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', 'Errore durante la creazione della scheda: ' . $e->getMessage());
        }
    }

    /**
     * Mostra dettaglio scheda
     */
    public function show(SchedaAllenamento $scheda)
    {
        $scheda->load(['cliente', 'professionista', 'esercizi']);
        $esercizi_per_giorno = $scheda->eserciziPerGiorno();

        return view('admin.schede.show', compact('scheda', 'esercizi_per_giorno'));
    }

    /**
     * Mostra form per modificare scheda
     */
    public function edit(SchedaAllenamento $scheda)
    {
        $scheda->load(['cliente', 'esercizi']);
        $clienti = Cliente::attivi()->orderBy('cognome')->get();
        $giorni_settimana = ['Lunedì', 'Martedì', 'Mercoledì', 'Giovedì', 'Venerdì', 'Sabato', 'Domenica'];

        return view('admin.schede.edit', compact('scheda', 'clienti', 'giorni_settimana'));
    }

    /**
     * Aggiorna scheda esistente
     */
    public function update(Request $request, SchedaAllenamento $scheda)
    {
        $validated = $request->validate([
            'nome_scheda' => ['required', 'string', 'max:255'],
            'descrizione' => ['nullable', 'string'],
            'obiettivi' => ['nullable', 'string'],
            'data_inizio' => ['nullable', 'date'],
            'data_fine' => ['nullable', 'date', 'after_or_equal:data_inizio'],
            'durata_settimane' => ['nullable', 'integer', 'min:1', 'max:52'],
            'note_generali' => ['nullable', 'string'],
            'note_alimentazione' => ['nullable', 'string'],
            'consigli_professionista' => ['nullable', 'string'],
            'stato' => ['required', 'in:bozza,attiva,completata,archiviata'],

            // Esercizi (array)
            'esercizi' => ['nullable', 'array'],
            'esercizi.*.id' => ['nullable', 'exists:scheda_esercizi,id'],
            'esercizi.*.giorno_settimana' => ['required', 'string'],
            'esercizi.*.ordine' => ['required', 'integer'],
            'esercizi.*.nome_esercizio' => ['required', 'string', 'max:255'],
            'esercizi.*.descrizione' => ['nullable', 'string'],
            'esercizi.*.serie' => ['nullable', 'integer', 'min:1'],
            'esercizi.*.ripetizioni' => ['nullable', 'string'],
            'esercizi.*.recupero_secondi' => ['nullable', 'integer', 'min:0'],
            'esercizi.*.peso_suggerito' => ['nullable', 'string'],
            'esercizi.*.categoria' => ['nullable', 'in:forza,cardio,stretching,mobilità,altro'],
            'esercizi.*.note_esecuzione' => ['nullable', 'string'],
        ]);

        DB::beginTransaction();

        try {
            // Aggiorna la scheda
            $scheda->update([
                'nome_scheda' => $validated['nome_scheda'],
                'descrizione' => $validated['descrizione'] ?? null,
                'obiettivi' => $validated['obiettivi'] ?? null,
                'data_inizio' => $validated['data_inizio'] ?? null,
                'data_fine' => $validated['data_fine'] ?? null,
                'durata_settimane' => $validated['durata_settimane'] ?? null,
                'note_generali' => $validated['note_generali'] ?? null,
                'note_alimentazione' => $validated['note_alimentazione'] ?? null,
                'consigli_professionista' => $validated['consigli_professionista'] ?? null,
                'stato' => $validated['stato'],
            ]);

            // Gestisci esercizi
            if (isset($validated['esercizi'])) {
                $esercizi_ids_mantenuti = [];

                foreach ($validated['esercizi'] as $esercizio_data) {
                    if (!empty($esercizio_data['id'])) {
                        // Aggiorna esercizio esistente
                        $esercizio = SchedaEsercizio::find($esercizio_data['id']);
                        if ($esercizio && $esercizio->scheda_allenamento_id === $scheda->id) {
                            $esercizio->update([
                                'giorno_settimana' => $esercizio_data['giorno_settimana'],
                                'ordine' => $esercizio_data['ordine'],
                                'nome_esercizio' => $esercizio_data['nome_esercizio'],
                                'descrizione' => $esercizio_data['descrizione'] ?? null,
                                'serie' => $esercizio_data['serie'] ?? null,
                                'ripetizioni' => $esercizio_data['ripetizioni'] ?? null,
                                'recupero_secondi' => $esercizio_data['recupero_secondi'] ?? null,
                                'peso_suggerito' => $esercizio_data['peso_suggerito'] ?? null,
                                'categoria' => $esercizio_data['categoria'] ?? 'forza',
                                'note_esecuzione' => $esercizio_data['note_esecuzione'] ?? null,
                            ]);
                            $esercizi_ids_mantenuti[] = $esercizio->id;
                        }
                    } else {
                        // Crea nuovo esercizio
                        $nuovo_esercizio = SchedaEsercizio::create([
                            'scheda_allenamento_id' => $scheda->id,
                            'giorno_settimana' => $esercizio_data['giorno_settimana'],
                            'ordine' => $esercizio_data['ordine'],
                            'nome_esercizio' => $esercizio_data['nome_esercizio'],
                            'descrizione' => $esercizio_data['descrizione'] ?? null,
                            'serie' => $esercizio_data['serie'] ?? null,
                            'ripetizioni' => $esercizio_data['ripetizioni'] ?? null,
                            'recupero_secondi' => $esercizio_data['recupero_secondi'] ?? null,
                            'peso_suggerito' => $esercizio_data['peso_suggerito'] ?? null,
                            'categoria' => $esercizio_data['categoria'] ?? 'forza',
                            'note_esecuzione' => $esercizio_data['note_esecuzione'] ?? null,
                        ]);
                        $esercizi_ids_mantenuti[] = $nuovo_esercizio->id;
                    }
                }

                // Elimina esercizi rimossi
                $scheda->esercizi()->whereNotIn('id', $esercizi_ids_mantenuti)->delete();
            } else {
                // Se non ci sono esercizi, elimina tutti
                $scheda->esercizi()->delete();
            }

            DB::commit();

            return redirect()
                ->route('admin.schede.show', $scheda)
                ->with('success', 'Scheda di allenamento aggiornata con successo!');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', 'Errore durante l\'aggiornamento della scheda: ' . $e->getMessage());
        }
    }

    /**
     * Elimina scheda
     */
    public function destroy(SchedaAllenamento $scheda)
    {
        try {
            // Elimina PDF se esiste
            if ($scheda->pdf_path && Storage::exists($scheda->pdf_path)) {
                Storage::delete($scheda->pdf_path);
            }

            $scheda->delete();

            return redirect()
                ->route('admin.schede.index')
                ->with('success', 'Scheda di allenamento eliminata con successo!');

        } catch (\Exception $e) {
            return back()
                ->with('error', 'Errore durante l\'eliminazione della scheda: ' . $e->getMessage());
        }
    }

    /**
     * Duplica una scheda esistente
     */
    public function duplica(SchedaAllenamento $scheda)
    {
        DB::beginTransaction();

        try {
            // Crea nuova scheda con dati simili
            $nuova_scheda = $scheda->replicate();
            $nuova_scheda->nome_scheda = $scheda->nome_scheda . ' (Copia)';
            $nuova_scheda->stato = 'bozza';
            $nuova_scheda->inviata_email = false;
            $nuova_scheda->data_invio_email = null;
            $nuova_scheda->pdf_path = null;
            $nuova_scheda->save();

            // Duplica tutti gli esercizi
            foreach ($scheda->esercizi as $esercizio) {
                $nuovo_esercizio = $esercizio->replicate();
                $nuovo_esercizio->scheda_allenamento_id = $nuova_scheda->id;
                $nuovo_esercizio->save();
            }

            DB::commit();

            return redirect()
                ->route('admin.schede.edit', $nuova_scheda)
                ->with('success', 'Scheda duplicata con successo! Puoi modificarla e personalizzarla.');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->with('error', 'Errore durante la duplicazione della scheda: ' . $e->getMessage());
        }
    }

    /**
     * Cambia stato della scheda
     */
    public function cambiaStato(Request $request, SchedaAllenamento $scheda)
    {
        $validated = $request->validate([
            'nuovo_stato' => ['required', 'in:bozza,attiva,completata,archiviata'],
        ]);

        try {
            $scheda->update([
                'stato' => $validated['nuovo_stato'],
            ]);

            return back()
                ->with('success', 'Stato della scheda aggiornato a: ' . ucfirst($validated['nuovo_stato']));

        } catch (\Exception $e) {
            return back()
                ->with('error', 'Errore durante il cambio di stato: ' . $e->getMessage());
        }
    }

    /**
     * Genera PDF della scheda (placeholder - da implementare con libreria PDF)
     */
    public function generaPDF(SchedaAllenamento $scheda)
    {
        // TODO: Implementare generazione PDF con Dompdf o TCPDF
        // Per ora ritorna messaggio

        return back()
            ->with('info', 'Funzione generazione PDF in fase di implementazione.');
    }

    /**
     * Invia scheda via email alla cliente (placeholder)
     */
    public function inviaEmail(SchedaAllenamento $scheda)
    {
        // TODO: Implementare invio email con PDF allegato
        // Per ora ritorna messaggio

        return back()
            ->with('info', 'Funzione invio email in fase di implementazione.');
    }
}
