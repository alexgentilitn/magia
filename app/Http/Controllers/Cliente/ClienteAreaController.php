<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Cliente;
use App\Models\Lezione;
use App\Models\Programma;
use App\Models\Pagamento;
use App\Models\Prenotazione;

/**
 * Controller Area Privata Cliente
 *
 * Gestisce tutte le funzionalità dell'area riservata cliente:
 * - Dashboard personale
 * - Profilo e parametri corporei
 * - Prenotazioni lezioni
 * - Storico pagamenti
 * - Download materiali
 * - Upload documenti
 */
class ClienteAreaController extends Controller
{
    /**
     * Verifica che l'utente sia un cliente autenticato
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('tipo_utente:cliente');
    }

    /**
     * Dashboard Cliente
     *
     * Mostra panoramica personalizzata con:
     * - KPI personali
     * - Prossime lezioni
     * - Statistiche presenze
     * - Stato programma
     */
    public function dashboard()
    {
        $cliente = Auth::user();

        // KPI Statistiche
        $stats = [
            'lezioni_prenotate' => $cliente->prenotazioni()->where('stato', 'confermata')->count(),
            'presenze_totali' => $cliente->prenotazioni()->where('presenza_confermata', true)->count(),
            'programma_attivo' => $cliente->programma_id ? true : false,
            'giorni_attivita' => $cliente->created_at->diffInDays(now()),
        ];

        // Prossime lezioni (max 5)
        $prossimiLezioni = $cliente->prenotazioni()
            ->with('lezione.sede', 'lezione.professionista')
            ->whereHas('lezione', function ($query) {
                $query->where('data', '>=', now())
                    ->where('data', '<=', now()->addDays(7));
            })
            ->where('stato', 'confermata')
            ->orderBy('id', 'desc')
            ->limit(5)
            ->get();

        // Programma attivo
        $programma = $cliente->programma;

        // Ultimi pagamenti (max 3)
        $ultimiPagamenti = $cliente->pagamenti()
            ->orderBy('data_pagamento', 'desc')
            ->limit(3)
            ->get();

        // Parametri corporei recenti (peso, altezza, etc)
        $parametriRecenti = $cliente->parametri_corporei ?? null;

        return view('cliente.dashboard', compact(
            'cliente',
            'stats',
            'prossimiLezioni',
            'programma',
            'ultimiPagamenti',
            'parametriRecenti'
        ));
    }

    /**
     * Profilo Cliente
     *
     * Visualizzazione e modifica dati personali
     */
    public function profilo()
    {
        $cliente = Auth::user();

        return view('cliente.profilo', compact('cliente'));
    }

    /**
     * Aggiorna Profilo
     */
    public function aggiornaProfilo(Request $request)
    {
        $cliente = Auth::user();

        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'cognome' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $cliente->id,
            'telefono' => 'nullable|string|max:20',
            'indirizzo' => 'nullable|string|max:255',
            'citta' => 'nullable|string|max:100',
            'cap' => 'nullable|string|max:10',
            'foto_profilo' => 'nullable|image|max:2048', // Max 2MB
        ]);

        // Upload foto profilo
        if ($request->hasFile('foto_profilo')) {
            $path = $request->file('foto_profilo')->store('profili', 'public');
            $validated['foto_profilo'] = $path;
        }

        $cliente->update($validated);

        return redirect()->route('cliente.profilo')
            ->with('success', 'Profilo aggiornato con successo!');
    }

    /**
     * Parametri Corporei
     *
     * Gestione peso, altezza, impedenziometria
     */
    public function parametri()
    {
        $cliente = Auth::user();

        // Storico parametri
        $storicoParametri = DB::table('parametri_corporei')
            ->where('cliente_id', $cliente->id)
            ->orderBy('data_rilevazione', 'desc')
            ->get();

        return view('cliente.parametri', compact('cliente', 'storicoParametri'));
    }

    /**
     * Salva Parametri Corporei
     */
    public function salvaParametri(Request $request)
    {
        $cliente = Auth::user();

        $validated = $request->validate([
            'peso' => 'required|numeric|min:0',
            'altezza' => 'nullable|numeric|min:0',
            'circonferenza_vita' => 'nullable|numeric',
            'circonferenza_fianchi' => 'nullable|numeric',
            'circonferenza_braccia' => 'nullable|numeric',
            'massa_grassa' => 'nullable|numeric',
            'massa_magra' => 'nullable|numeric',
            'acqua_corporea' => 'nullable|numeric',
            'note' => 'nullable|string',
        ]);

        DB::table('parametri_corporei')->insert([
            'cliente_id' => $cliente->id,
            'data_rilevazione' => now(),
            ...$validated,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('cliente.parametri')
            ->with('success', 'Parametri corporei salvati con successo!');
    }

    /**
     * Prenotazioni Lezioni
     *
     * Visualizza tutte le prenotazioni e permette nuove prenotazioni
     */
    public function prenotazioni()
    {
        $cliente = Auth::user();

        // Prenotazioni attive
        $prenotazioniAttive = $cliente->prenotazioni()
            ->with('lezione.sede', 'lezione.professionista')
            ->whereHas('lezione', function ($query) {
                $query->where('data', '>=', now());
            })
            ->orderBy('id', 'desc')
            ->get();

        // Storico prenotazioni
        $storicoPrenotazioni = $cliente->prenotazioni()
            ->with('lezione.sede', 'lezione.professionista')
            ->whereHas('lezione', function ($query) {
                $query->where('data', '<', now());
            })
            ->orderBy('id', 'desc')
            ->paginate(20);

        return view('cliente.prenotazioni', compact(
            'cliente',
            'prenotazioniAttive',
            'storicoPrenotazioni'
        ));
    }

    /**
     * Prenota Lezione
     */
    public function prenotaLezione(Request $request, $lezioneId)
    {
        $cliente = Auth::user();
        $lezione = Lezione::findOrFail($lezioneId);

        // Verifica disponibilità posti
        if ($lezione->posti_disponibili <= $lezione->prenotazioni()->count()) {
            return back()->with('error', 'Lezione al completo!');
        }

        // Verifica se già prenotata
        if ($cliente->prenotazioni()->where('lezione_id', $lezioneId)->exists()) {
            return back()->with('warning', 'Hai già prenotato questa lezione.');
        }

        // Crea prenotazione
        $cliente->prenotazioni()->create([
            'lezione_id' => $lezioneId,
            'stato' => 'confermata',
            'data_prenotazione' => now(),
        ]);

        return redirect()->route('cliente.prenotazioni')
            ->with('success', 'Lezione prenotata con successo!');
    }

    /**
     * Cancella Prenotazione
     */
    public function cancellaPrenotazione($prenotazioneId)
    {
        $cliente = Auth::user();

        $prenotazione = $cliente->prenotazioni()->findOrFail($prenotazioneId);

        // Verifica se la lezione è ancora cancellabile (es. almeno 24h prima)
        if ($prenotazione->lezione->data < now()->addDay()) {
            return back()->with('error', 'Non è possibile cancellare la prenotazione con meno di 24 ore di anticipo.');
        }

        $prenotazione->update(['stato' => 'cancellata']);

        return back()->with('success', 'Prenotazione cancellata.');
    }

    /**
     * Storico Pagamenti
     */
    public function pagamenti()
    {
        $cliente = Auth::user();

        $pagamenti = $cliente->pagamenti()
            ->orderBy('data_pagamento', 'desc')
            ->paginate(20);

        $totale_pagato = $cliente->pagamenti()->sum('importo');

        return view('cliente.pagamenti', compact('cliente', 'pagamenti', 'totale_pagato'));
    }

    /**
     * Download Materiali
     *
     * Schede allenamento, ricette, programmi
     */
    public function materiali()
    {
        $cliente = Auth::user();

        // Schede allenamento
        $schede = $cliente->schedeallenamento()
            ->orderBy('created_at', 'desc')
            ->get();

        // Documenti caricati dall'admin per il cliente
        $documenti = DB::table('documenti_cliente')
            ->where('cliente_id', $cliente->id)
            ->orderBy('created_at', 'desc')
            ->get();

        // Ricette disponibili (se programma attivo)
        $ricette = [];
        if ($cliente->programma_id) {
            $ricette = DB::table('ricette')
                ->where('programma_id', $cliente->programma_id)
                ->orderBy('ordine', 'asc')
                ->get();
        }

        return view('cliente.materiali', compact('cliente', 'schede', 'documenti', 'ricette'));
    }

    /**
     * Upload Documenti
     *
     * Permette al cliente di caricare certificati medici, etc.
     */
    public function documenti()
    {
        $cliente = Auth::user();

        $documentiCaricati = DB::table('documenti_caricati_cliente')
            ->where('cliente_id', $cliente->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('cliente.documenti', compact('cliente', 'documentiCaricati'));
    }

    /**
     * Carica Documento
     */
    public function caricaDocumento(Request $request)
    {
        $cliente = Auth::user();

        $validated = $request->validate([
            'tipo_documento' => 'required|in:certificato_medico,documento_identita,altro',
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120', // Max 5MB
            'note' => 'nullable|string|max:500',
        ]);

        $path = $request->file('file')->store('documenti_clienti', 'public');

        DB::table('documenti_caricati_cliente')->insert([
            'cliente_id' => $cliente->id,
            'tipo_documento' => $validated['tipo_documento'],
            'file_path' => $path,
            'file_name' => $request->file('file')->getClientOriginalName(),
            'note' => $validated['note'] ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('cliente.documenti')
            ->with('success', 'Documento caricato con successo!');
    }
}
