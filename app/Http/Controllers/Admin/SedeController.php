<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sede;
use App\Models\Utente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Controller: Gestione Sedi
 * Funzione: CRUD completo per le sedi fisiche del centro
 */
class SedeController extends Controller
{
    /**
     * Mostra l'elenco delle sedi
     */
    public function index(Request $request)
    {
        $query = Sede::withCount(['programmi', 'lezioni']);

        // Filtri
        if ($request->filled('attiva')) {
            $query->where('attiva', $request->attiva);
        }

        if ($request->filled('visibile')) {
            $query->where('visibile_pubblico', $request->visibile);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nome', 'like', "%{$search}%")
                  ->orWhere('indirizzo_citta', 'like', "%{$search}%")
                  ->orWhere('indirizzo_via', 'like', "%{$search}%");
            });
        }

        $sedi = $query->orderBy('ordine')->orderBy('nome')->paginate(20);

        // Statistiche
        $statistiche = [
            'totale' => Sede::count(),
            'attive' => Sede::where('attiva', true)->count(),
            'visibili' => Sede::where('visibile_pubblico', true)->count(),
            'principale' => Sede::where('sede_principale', true)->count(),
        ];

        return view('admin.sedi.index', compact('sedi', 'statistiche'));
    }

    /**
     * Mostra il form di creazione
     */
    public function create()
    {
        $professionisti = Utente::where('tipo_utente', 'professionista')->get();
        return view('admin.sedi.create', compact('professionisti'));
    }

    /**
     * Salva una nuova sede
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'descrizione' => 'nullable|string',
            'indirizzo_via' => 'required|string|max:255',
            'indirizzo_citta' => 'required|string|max:100',
            'indirizzo_provincia' => 'required|string|max:2',
            'indirizzo_cap' => 'required|string|max:10',
            'latitudine' => 'nullable|numeric',
            'longitudine' => 'nullable|numeric',
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'capienza_massima' => 'nullable|integer|min:1',
            'attiva' => 'boolean',
            'visibile_pubblico' => 'boolean',
            'sede_principale' => 'boolean',
            'ordine' => 'nullable|integer',
        ]);

        // Genera slug univoco
        $validated['slug'] = Str::slug($validated['nome']);

        // Assicura slug univoco
        $originalSlug = $validated['slug'];
        $counter = 1;
        while (Sede::where('slug', $validated['slug'])->exists()) {
            $validated['slug'] = $originalSlug . '-' . $counter;
            $counter++;
        }

        // Se questa è la sede principale, rimuovi il flag dalle altre
        if ($request->boolean('sede_principale')) {
            Sede::where('sede_principale', true)->update(['sede_principale' => false]);
        }

        $sede = Sede::create($validated);

        return redirect()->route('admin.sedi.show', $sede->id)
            ->with('success', 'Sede creata con successo!');
    }

    /**
     * Mostra il dettaglio di una sede
     */
    public function show($id)
    {
        $sede = Sede::with(['programmi', 'lezioni'])
            ->withCount(['programmi', 'lezioni'])
            ->findOrFail($id);

        // Statistiche della sede
        $statistiche = [
            'programmi_totali' => $sede->programmi_count,
            'programmi_attivi' => $sede->programmi()->where('attivo', true)->count(),
            'lezioni_totali' => $sede->lezioni_count,
            'lezioni_future' => $sede->lezioni()->where('data', '>=', now())->count(),
            'professionisti_assegnati' => 0, // TODO: implementare quando esiste tabella pivot
            'capienza_utilizzata' => 0, // TODO: calcolare in base a iscrizioni
        ];

        // Prossime lezioni in questa sede
        $prossimeLezioni = $sede->lezioni()
            ->with(['programma', 'professionista'])
            ->where('data', '>=', now())
            ->orderBy('data')
            ->orderBy('ora_inizio')
            ->limit(5)
            ->get();

        return view('admin.sedi.show', compact('sede', 'statistiche', 'prossimeLezioni'));
    }

    /**
     * Mostra il form di modifica
     */
    public function edit($id)
    {
        $sede = Sede::findOrFail($id);
        $professionisti = Utente::where('tipo_utente', 'professionista')->get();
        return view('admin.sedi.edit', compact('sede', 'professionisti'));
    }

    /**
     * Aggiorna una sede
     */
    public function update(Request $request, $id)
    {
        $sede = Sede::findOrFail($id);

        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'descrizione' => 'nullable|string',
            'indirizzo_via' => 'required|string|max:255',
            'indirizzo_citta' => 'required|string|max:100',
            'indirizzo_provincia' => 'required|string|max:2',
            'indirizzo_cap' => 'required|string|max:10',
            'latitudine' => 'nullable|numeric',
            'longitudine' => 'nullable|numeric',
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'capienza_massima' => 'nullable|integer|min:1',
            'attiva' => 'boolean',
            'visibile_pubblico' => 'boolean',
            'sede_principale' => 'boolean',
            'ordine' => 'nullable|integer',
        ]);

        // Se il nome è cambiato, rigenera lo slug
        if ($validated['nome'] !== $sede->nome) {
            $validated['slug'] = Str::slug($validated['nome']);

            // Assicura slug univoco (escludendo la sede corrente)
            $originalSlug = $validated['slug'];
            $counter = 1;
            while (Sede::where('slug', $validated['slug'])->where('id', '!=', $sede->id)->exists()) {
                $validated['slug'] = $originalSlug . '-' . $counter;
                $counter++;
            }
        }

        // Se questa diventa la sede principale, rimuovi il flag dalle altre
        if ($request->boolean('sede_principale') && !$sede->sede_principale) {
            Sede::where('sede_principale', true)->where('id', '!=', $sede->id)->update(['sede_principale' => false]);
        }

        $sede->update($validated);

        return redirect()->route('admin.sedi.show', $sede->id)
            ->with('success', 'Sede aggiornata con successo!');
    }

    /**
     * Elimina una sede (soft delete)
     */
    public function destroy($id)
    {
        $sede = Sede::findOrFail($id);

        // Verifica se ci sono programmi o lezioni associate
        if ($sede->programmi()->count() > 0 || $sede->lezioni()->count() > 0) {
            return redirect()->route('admin.sedi.index')
                ->with('error', 'Impossibile eliminare: la sede ha programmi o lezioni associate.');
        }

        $sede->delete();

        return redirect()->route('admin.sedi.index')
            ->with('success', 'Sede eliminata con successo!');
    }

    /**
     * Attiva/Disattiva una sede
     */
    public function toggleAttiva($id)
    {
        $sede = Sede::findOrFail($id);
        $sede->update(['attiva' => !$sede->attiva]);

        $stato = $sede->attiva ? 'attivata' : 'disattivata';
        return redirect()->route('admin.sedi.show', $sede->id)
            ->with('success', "Sede {$stato} con successo!");
    }

    /**
     * Imposta come sede principale
     */
    public function setPrincipale($id)
    {
        $sede = Sede::findOrFail($id);

        // Rimuovi il flag da tutte le altre sedi
        Sede::where('sede_principale', true)->update(['sede_principale' => false]);

        // Imposta questa come principale
        $sede->update(['sede_principale' => true]);

        return redirect()->route('admin.sedi.show', $sede->id)
            ->with('success', 'Sede impostata come principale!');
    }

    /**
     * Gestisce gli orari di apertura
     */
    public function orari($id)
    {
        $sede = Sede::findOrFail($id);

        $giorniSettimana = [
            'lunedi' => 'Lunedì',
            'martedi' => 'Martedì',
            'mercoledi' => 'Mercoledì',
            'giovedi' => 'Giovedì',
            'venerdi' => 'Venerdì',
            'sabato' => 'Sabato',
            'domenica' => 'Domenica',
        ];

        return view('admin.sedi.orari', compact('sede', 'giorniSettimana'));
    }

    /**
     * Salva gli orari di apertura
     */
    public function salvaOrari(Request $request, $id)
    {
        $sede = Sede::findOrFail($id);

        $orari = [];
        $giorni = ['lunedi', 'martedi', 'mercoledi', 'giovedi', 'venerdi', 'sabato', 'domenica'];

        foreach ($giorni as $giorno) {
            if ($request->has("aperto_{$giorno}")) {
                $orari[$giorno] = [
                    'aperto' => true,
                    'apertura' => $request->input("apertura_{$giorno}"),
                    'chiusura' => $request->input("chiusura_{$giorno}"),
                ];
            } else {
                $orari[$giorno] = ['aperto' => false];
            }
        }

        $sede->update(['orari_apertura' => $orari]);

        return redirect()->route('admin.sedi.show', $sede->id)
            ->with('success', 'Orari di apertura aggiornati!');
    }
}
