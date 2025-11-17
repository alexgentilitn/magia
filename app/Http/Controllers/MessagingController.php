<?php

namespace App\Http\Controllers;

use App\Models\Conversazione;
use App\Models\Messaggio;
use App\Services\NotificheService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

/**
 * Controller: Messaging (Chat)
 *
 * Gestisce il sistema di messaggistica interna
 * Accessibile da: Admin, Professionisti, Clienti
 */
class MessagingController extends Controller
{
    protected $notificheService;

    public function __construct(NotificheService $notificheService)
    {
        $this->middleware('auth');
        $this->notificheService = $notificheService;
    }

    /**
     * Lista conversazioni dell'utente corrente
     */
    public function index()
    {
        $utente = Auth::user();

        $conversazioni = $utente->conversazioni()
            ->with(['utenti', 'ultimoMessaggio.mittente'])
            ->orderBy('ultimo_messaggio_at', 'desc')
            ->get();

        // Contatori
        $totaleConversazioni = $conversazioni->count();
        $messaggiNonLetti = $conversazioni->sum(function ($conv) use ($utente) {
            return $conv->utenti()->where('utente_id', $utente->id)->first()->pivot->messaggi_non_letti;
        });

        return view('messaging.index', compact('conversazioni', 'totaleConversazioni', 'messaggiNonLetti'));
    }

    /**
     * Visualizza conversazione specifica
     */
    public function show($id)
    {
        $utente = Auth::user();

        $conversazione = Conversazione::with(['utenti', 'messaggi.mittente'])
            ->findOrFail($id);

        // Verifica che l'utente faccia parte della conversazione
        if (!$conversazione->utenti->contains($utente->id)) {
            abort(403, 'Non autorizzato ad accedere a questa conversazione');
        }

        // Segna come letta
        $conversazione->segnaComeLetta($utente->id);

        // Altri partecipanti
        $altriUtenti = $conversazione->utenti->where('id', '!=', $utente->id);

        return view('messaging.show', compact('conversazione', 'altriUtenti'));
    }

    /**
     * Crea nuova conversazione
     */
    public function create(Request $request)
    {
        // Lista utenti per iniziare conversazione
        $utente = Auth::user();

        // Utenti disponibili (escluso l'utente corrente)
        $utenti = \App\Models\Utente::where('id', '!=', $utente->id)
            ->where('stato', 'attivo')
            ->orderBy('nome')
            ->orderBy('cognome')
            ->get();

        return view('messaging.create', compact('utenti'));
    }

    /**
     * Salva nuova conversazione
     */
    public function store(Request $request)
    {
        $request->validate([
            'destinatario_id' => 'required|exists:utenti,id',
            'messaggio' => 'required|string|max:5000',
        ]);

        $utente = Auth::user();

        // Trova o crea conversazione privata
        $conversazione = Conversazione::trovaOCreaPrivata($utente->id, $request->destinatario_id);

        // Crea messaggio
        $messaggio = Messaggio::create([
            'conversazione_id' => $conversazione->id,
            'mittente_id' => $utente->id,
            'corpo' => $request->messaggio,
            'tipo' => 'testo',
        ]);

        // Aggiorna timestamp conversazione
        $conversazione->update(['ultimo_messaggio_at' => now()]);

        // Incrementa contatore non letti
        $conversazione->incrementaNonLetti($utente->id);

        // Invia notifica al destinatario
        $this->notificheService->nuovoMessaggio($messaggio, $request->destinatario_id);

        return redirect()->route('messaging.show', $conversazione->id)
            ->with('success', 'Messaggio inviato!');
    }

    /**
     * Invia messaggio in conversazione esistente
     */
    public function sendMessage(Request $request, $conversazioneId)
    {
        $request->validate([
            'messaggio' => 'required_without:allegato|string|max:5000',
            'allegato' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,pdf,doc,docx',
        ]);

        $utente = Auth::user();
        $conversazione = Conversazione::findOrFail($conversazioneId);

        // Verifica autorizzazione
        if (!$conversazione->utenti->contains($utente->id)) {
            abort(403);
        }

        $datiMessaggio = [
            'conversazione_id' => $conversazione->id,
            'mittente_id' => $utente->id,
            'corpo' => $request->messaggio ?? '',
            'tipo' => 'testo',
        ];

        // Gestione allegato
        if ($request->hasFile('allegato')) {
            $file = $request->file('allegato');
            $path = $file->store('messaggi/allegati', 'private');

            $datiMessaggio['tipo'] = str_starts_with($file->getMimeType(), 'image/') ? 'immagine' : 'file';
            $datiMessaggio['allegato_path'] = $path;
            $datiMessaggio['allegato_nome'] = $file->getClientOriginalName();
        }

        // Crea messaggio
        $messaggio = Messaggio::create($datiMessaggio);

        // Aggiorna conversazione
        $conversazione->update(['ultimo_messaggio_at' => now()]);
        $conversazione->incrementaNonLetti($utente->id);

        // Invia notifiche agli altri partecipanti
        foreach ($conversazione->utenti->where('id', '!=', $utente->id) as $destinatario) {
            $this->notificheService->nuovoMessaggio($messaggio, $destinatario->id);
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'messaggio' => $messaggio->load('mittente'),
            ]);
        }

        return redirect()->route('messaging.show', $conversazione->id);
    }

    /**
     * API: Ottieni nuovi messaggi (polling)
     */
    public function getNewMessages(Request $request, $conversazioneId)
    {
        $ultimoId = $request->get('ultimo_id', 0);

        $messaggi = Messaggio::where('conversazione_id', $conversazioneId)
            ->where('id', '>', $ultimoId)
            ->with('mittente')
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json([
            'messaggi' => $messaggi,
            'count' => $messaggi->count(),
        ]);
    }

    /**
     * Segna messaggio come letto
     */
    public function markAsRead($conversazioneId)
    {
        $utente = Auth::user();
        $conversazione = Conversazione::findOrFail($conversazioneId);

        if ($conversazione->utenti->contains($utente->id)) {
            $conversazione->segnaComeLetta($utente->id);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Elimina conversazione (soft)
     */
    public function destroy($id)
    {
        // Per ora non implementiamo l'eliminazione
        // In futuro: rimuovi utente dalla conversazione
        return redirect()->route('messaging.index')
            ->with('info', 'Funzione non ancora implementata');
    }
}
