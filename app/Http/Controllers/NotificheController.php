<?php

namespace App\Http\Controllers;

use App\Models\Notifica;
use App\Services\NotificheService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Controller: Notifiche
 *
 * Gestisce centro notifiche utente
 * Accessibile da tutti gli utenti autenticati
 */
class NotificheController extends Controller
{
    protected $notificheService;

    public function __construct(NotificheService $notificheService)
    {
        $this->middleware('auth');
        $this->notificheService = $notificheService;
    }

    /**
     * Centro notifiche
     */
    public function index()
    {
        $utente = Auth::user();

        $notifiche = Notifica::where('utente_id', $utente->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $nonLette = $this->notificheService->contaNonLette($utente->id);

        return view('notifiche.index', compact('notifiche', 'nonLette'));
    }

    /**
     * API: Ottieni notifiche recenti (per dropdown)
     */
    public function getRecenti()
    {
        $utente = Auth::user();

        $notifiche = $this->notificheService->ottieniRecenti($utente->id, 10);
        $count = $this->notificheService->contaNonLette($utente->id);

        return response()->json([
            'notifiche' => $notifiche,
            'count' => $count,
        ]);
    }

    /**
     * Segna notifica come letta
     */
    public function markAsRead($id)
    {
        $notifica = Notifica::where('id', $id)
            ->where('utente_id', Auth::id())
            ->firstOrFail();

        $notifica->segnaComeLetta();

        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back();
    }

    /**
     * Segna tutte come lette
     */
    public function markAllAsRead()
    {
        $this->notificheService->segnaTutteComeLette(Auth::id());

        return response()->json(['success' => true]);
    }

    /**
     * Elimina notifica
     */
    public function destroy($id)
    {
        Notifica::where('id', $id)
            ->where('utente_id', Auth::id())
            ->delete();

        return redirect()->back()->with('success', 'Notifica eliminata');
    }
}
