<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Programma;
use App\Models\Sede;
use App\Models\Utente;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProgrammiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Programma::with(['sede', 'professionista']);

        // Filtro per tipologia
        if ($request->filled('tipologia')) {
            $query->where('tipologia', $request->tipologia);
        }

        // Filtro per livello
        if ($request->filled('livello')) {
            $query->where('livello', $request->livello);
        }

        // Filtro per sede
        if ($request->filled('sede_id')) {
            $query->where('sede_id', $request->sede_id);
        }

        // Filtro per professionista
        if ($request->filled('professionista_id')) {
            $query->where('professionista_id', $request->professionista_id);
        }

        // Filtro solo attivi
        if ($request->boolean('solo_attivi')) {
            $query->where('attivo', true);
        }

        // Filtro solo visibili
        if ($request->boolean('solo_visibili')) {
            $query->where('visibile_pubblico', true);
        }

        // Filtro solo in evidenza
        if ($request->boolean('solo_evidenza')) {
            $query->where('in_evidenza', true);
        }

        // Filtro programmi in promo
        if ($request->boolean('solo_promo')) {
            $query->whereNotNull('prezzo_promo')
                  ->where(function($q) {
                      $q->whereNull('promo_valida_da')
                        ->orWhere('promo_valida_da', '<=', now());
                  })
                  ->where(function($q) {
                      $q->whereNull('promo_valida_a')
                        ->orWhere('promo_valida_a', '>=', now());
                  });
        }

        // Ricerca per nome
        if ($request->filled('ricerca')) {
            $ricerca = $request->ricerca;
            $query->where(function($q) use ($ricerca) {
                $q->where('nome', 'like', "%{$ricerca}%")
                  ->orWhere('descrizione', 'like', "%{$ricerca}%")
                  ->orWhere('slug', 'like', "%{$ricerca}%");
            });
        }

        // Ordinamento
        $query->orderBy('ordine', 'asc')
              ->orderBy('nome', 'asc');

        $programmi = $query->paginate(15)->withQueryString();

        // Statistiche
        $statistiche = [
            'totali' => Programma::count(),
            'attivi' => Programma::where('attivo', true)->count(),
            'visibili' => Programma::where('visibile_pubblico', true)->count(),
            'in_promo' => Programma::whereNotNull('prezzo_promo')
                ->where(function($q) {
                    $q->whereNull('promo_valida_da')
                      ->orWhere('promo_valida_da', '<=', now());
                })
                ->where(function($q) {
                    $q->whereNull('promo_valida_a')
                      ->orWhere('promo_valida_a', '>=', now());
                })
                ->count(),
        ];

        // Dati per i filtri
        $sedi = Sede::orderBy('nome')->get();
        $professionisti = Utente::where('tipo_utente', 'professionista')->orderBy('nome')->get();

        return view('admin.programmi.index', compact('programmi', 'statistiche', 'sedi', 'professionisti'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $sedi = Sede::orderBy('nome')->get();
        $professionisti = Utente::where('tipo_utente', 'professionista')->orderBy('nome')->get();

        return view('admin.programmi.create', compact('sedi', 'professionisti'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:programmi,slug',
            'descrizione' => 'nullable|string',
            'descrizione_breve' => 'nullable|string|max:500',
            'tipologia' => 'required|in:fitness,danza,alimentazione,wellness,personalizzato,altro',
            'livello' => 'required|in:principiante,intermedio,avanzato,tutti',
            'durata_giorni' => 'nullable|integer|min:1',
            'durata_mesi' => 'nullable|integer|min:1',
            'lezioni_totali' => 'nullable|integer|min:1',
            'lezioni_settimana' => 'nullable|integer|min:1|max:7',
            'durata_singola_lezione' => 'nullable|integer|min:15',
            'prezzo_base' => 'required|numeric|min:0',
            'prezzo_promo' => 'nullable|numeric|min:0',
            'promo_valida_da' => 'nullable|date',
            'promo_valida_a' => 'nullable|date|after_or_equal:promo_valida_da',
            'prezzo_su_richiesta' => 'boolean',
            'posti_disponibili' => 'nullable|integer|min:1',
            'min_partecipanti' => 'nullable|integer|min:1',
            'max_partecipanti' => 'nullable|integer|min:1',
            'sede_id' => 'nullable|exists:sedi,id',
            'professionista_id' => 'nullable|exists:utenti,id',
            'data_inizio' => 'nullable|date',
            'data_fine' => 'nullable|date|after_or_equal:data_inizio',
            'sempre_disponibile' => 'boolean',
            'attivo' => 'boolean',
            'visibile_pubblico' => 'boolean',
            'in_evidenza' => 'boolean',
            'ordine' => 'nullable|integer|min:0',
        ]);

        // Genera slug se non fornito
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['nome']);

            // Assicura unicità
            $originalSlug = $validated['slug'];
            $counter = 1;
            while (Programma::where('slug', $validated['slug'])->exists()) {
                $validated['slug'] = $originalSlug . '-' . $counter;
                $counter++;
            }
        }

        // Converti boolean
        $validated['prezzo_su_richiesta'] = $request->boolean('prezzo_su_richiesta');
        $validated['sempre_disponibile'] = $request->boolean('sempre_disponibile');
        $validated['attivo'] = $request->boolean('attivo', true);
        $validated['visibile_pubblico'] = $request->boolean('visibile_pubblico', false);
        $validated['in_evidenza'] = $request->boolean('in_evidenza', false);

        // Inizializza posti_occupati a 0
        $validated['posti_occupati'] = 0;

        $programma = Programma::create($validated);

        return redirect()
            ->route('admin.programmi.show', $programma->id)
            ->with('success', 'Programma creato con successo!');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $programma = Programma::with(['sede', 'professionista', 'lezioni'])
            ->findOrFail($id);

        // Statistiche del programma
        $statistiche = [
            'iscritti_totali' => 0,
            'iscritti_attivi' => 0,
            'lezioni_totali' => $programma->lezioni->count(),
            'lezioni_future' => $programma->lezioni()->where('data', '>=', now())->count(),
            'posti_occupati' => $programma->posti_occupati,
            'posti_disponibili' => $programma->postiRimasti(),
        ];

        return view('admin.programmi.show', compact('programma', 'statistiche'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $programma = Programma::findOrFail($id);
        $sedi = Sede::orderBy('nome')->get();
        $professionisti = Utente::where('tipo_utente', 'professionista')->orderBy('nome')->get();

        return view('admin.programmi.edit', compact('programma', 'sedi', 'professionisti'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $programma = Programma::findOrFail($id);

        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:programmi,slug,' . $id,
            'descrizione' => 'nullable|string',
            'descrizione_breve' => 'nullable|string|max:500',
            'tipologia' => 'required|in:fitness,danza,alimentazione,wellness,personalizzato,altro',
            'livello' => 'required|in:principiante,intermedio,avanzato,tutti',
            'durata_giorni' => 'nullable|integer|min:1',
            'durata_mesi' => 'nullable|integer|min:1',
            'lezioni_totali' => 'nullable|integer|min:1',
            'lezioni_settimana' => 'nullable|integer|min:1|max:7',
            'durata_singola_lezione' => 'nullable|integer|min:15',
            'prezzo_base' => 'required|numeric|min:0',
            'prezzo_promo' => 'nullable|numeric|min:0',
            'promo_valida_da' => 'nullable|date',
            'promo_valida_a' => 'nullable|date|after_or_equal:promo_valida_da',
            'prezzo_su_richiesta' => 'boolean',
            'posti_disponibili' => 'nullable|integer|min:1',
            'min_partecipanti' => 'nullable|integer|min:1',
            'max_partecipanti' => 'nullable|integer|min:1',
            'sede_id' => 'nullable|exists:sedi,id',
            'professionista_id' => 'nullable|exists:utenti,id',
            'data_inizio' => 'nullable|date',
            'data_fine' => 'nullable|date|after_or_equal:data_inizio',
            'sempre_disponibile' => 'boolean',
            'attivo' => 'boolean',
            'visibile_pubblico' => 'boolean',
            'in_evidenza' => 'boolean',
            'ordine' => 'nullable|integer|min:0',
        ]);

        // Genera slug se non fornito
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['nome']);

            // Assicura unicità escludendo il programma corrente
            $originalSlug = $validated['slug'];
            $counter = 1;
            while (Programma::where('slug', $validated['slug'])->where('id', '!=', $id)->exists()) {
                $validated['slug'] = $originalSlug . '-' . $counter;
                $counter++;
            }
        }

        // Converti boolean
        $validated['prezzo_su_richiesta'] = $request->boolean('prezzo_su_richiesta');
        $validated['sempre_disponibile'] = $request->boolean('sempre_disponibile');
        $validated['attivo'] = $request->boolean('attivo');
        $validated['visibile_pubblico'] = $request->boolean('visibile_pubblico');
        $validated['in_evidenza'] = $request->boolean('in_evidenza');

        $programma->update($validated);

        return redirect()
            ->route('admin.programmi.show', $programma->id)
            ->with('success', 'Programma aggiornato con successo!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $programma = Programma::findOrFail($id);

        // Verifica che non ci siano iscritti attivi
        $iscrittiAttivi = $programma->clienti()->wherePivot('stato', 'attivo')->count();
        if ($iscrittiAttivi > 0) {
            return redirect()
                ->route('admin.programmi.index')
                ->with('error', 'Impossibile eliminare il programma: ci sono ancora ' . $iscrittiAttivi . ' iscritti attivi.');
        }

        // Verifica che non ci siano lezioni future
        $lezioniFuture = $programma->lezioni()->where('data', '>=', now())->count();
        if ($lezioniFuture > 0) {
            return redirect()
                ->route('admin.programmi.index')
                ->with('error', 'Impossibile eliminare il programma: ci sono ancora ' . $lezioniFuture . ' lezioni future programmate.');
        }

        $programma->delete();

        return redirect()
            ->route('admin.programmi.index')
            ->with('success', 'Programma eliminato con successo!');
    }

    /**
     * Cambia stato attivo del programma
     */
    public function cambiaStato(Request $request, $id)
    {
        $programma = Programma::findOrFail($id);

        $validated = $request->validate([
            'attivo' => 'required|boolean',
        ]);

        $programma->update(['attivo' => $validated['attivo']]);

        $messaggio = $validated['attivo']
            ? 'Programma attivato con successo!'
            : 'Programma disattivato con successo!';

        return redirect()
            ->back()
            ->with('success', $messaggio);
    }

    /**
     * Duplica un programma esistente
     */
    public function duplica($id)
    {
        $programmaOriginale = Programma::findOrFail($id);

        // Crea una copia del programma
        $nuovoProgramma = $programmaOriginale->replicate();
        $nuovoProgramma->nome = $programmaOriginale->nome . ' (Copia)';
        $nuovoProgramma->slug = Str::slug($nuovoProgramma->nome);

        // Assicura unicità slug
        $originalSlug = $nuovoProgramma->slug;
        $counter = 1;
        while (Programma::where('slug', $nuovoProgramma->slug)->exists()) {
            $nuovoProgramma->slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        $nuovoProgramma->posti_occupati = 0;
        $nuovoProgramma->attivo = false;
        $nuovoProgramma->visibile_pubblico = false;
        $nuovoProgramma->in_evidenza = false;
        $nuovoProgramma->save();

        return redirect()
            ->route('admin.programmi.edit', $nuovoProgramma->id)
            ->with('success', 'Programma duplicato con successo! Modifica i dettagli e attivalo quando sei pronto.');
    }
}
