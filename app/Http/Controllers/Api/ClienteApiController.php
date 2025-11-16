<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\Utente;
use App\Models\Lezione;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * API Controller per App Mobile Clienti
 *
 * @group API Cliente
 */
class ClienteApiController extends Controller
{
    /**
     * Login cliente e genera token API
     *
     * @bodyParam email string required Email del cliente
     * @bodyParam password string required Password
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'device_name' => 'string|nullable',
        ]);

        $utente = Utente::where('email', $request->email)
            ->where('tipo_utente', 'cliente')
            ->first();

        if (!$utente || !Hash::check($request->password, $utente->password)) {
            throw ValidationException::withMessages([
                'email' => ['Credenziali non valide'],
            ]);
        }

        // Genera token Sanctum
        $token = $utente->createToken($request->device_name ?? 'mobile-app')->plainTextToken;

        return response()->json([
            'success' => true,
            'data' => [
                'token' => $token,
                'utente' => [
                    'id' => $utente->id,
                    'email' => $utente->email,
                    'tipo_utente' => $utente->tipo_utente,
                ],
                'cliente' => $utente->cliente ? [
                    'id' => $utente->cliente->id,
                    'nome' => $utente->cliente->nome,
                    'cognome' => $utente->cliente->cognome,
                    'codice_cliente' => $utente->cliente->codice_cliente,
                ] : null
            ]
        ]);
    }

    /**
     * Logout (revoca token)
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout effettuato con successo'
        ]);
    }

    /**
     * Profilo cliente autenticato
     */
    public function profilo(Request $request)
    {
        $utente = $request->user();
        $cliente = $utente->cliente;

        if (!$cliente) {
            return response()->json([
                'success' => false,
                'message' => 'Cliente non trovato'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $cliente->id,
                'codice_cliente' => $cliente->codice_cliente,
                'nome' => $cliente->nome,
                'cognome' => $cliente->cognome,
                'email' => $cliente->email,
                'telefono' => $cliente->telefono_mobile,
                'data_nascita' => $cliente->data_nascita ? $cliente->data_nascita->format('Y-m-d') : null,
                'peso' => $cliente->peso_iniziale,
                'altezza' => $cliente->altezza,
                'imc' => $cliente->imc,
                'obiettivo' => $cliente->obiettivo_primario,
            ]
        ]);
    }

    /**
     * Aggiorna profilo cliente
     */
    public function aggiornaProfilo(Request $request)
    {
        $utente = $request->user();
        $cliente = $utente->cliente;

        if (!$cliente) {
            return response()->json([
                'success' => false,
                'message' => 'Cliente non trovato'
            ], 404);
        }

        $validated = $request->validate([
            'telefono' => 'nullable|string',
            'peso' => 'nullable|numeric',
            'altezza' => 'nullable|integer',
        ]);

        $cliente->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Profilo aggiornato con successo',
            'data' => $cliente->fresh()
        ]);
    }

    /**
     * Lista lezioni disponibili
     */
    public function lezioni(Request $request)
    {
        $query = Lezione::with(['programma', 'sede', 'professionista'])
            ->where('data', '>=', now())
            ->orderBy('data')
            ->orderBy('ora_inizio');

        // Filtri
        if ($request->has('programma_id')) {
            $query->where('programma_id', $request->programma_id);
        }

        if ($request->has('sede_id')) {
            $query->where('sede_id', $request->sede_id);
        }

        $lezioni = $query->paginate(50);

        return response()->json([
            'success' => true,
            'data' => $lezioni->items(),
            'pagination' => [
                'current_page' => $lezioni->currentPage(),
                'total_pages' => $lezioni->lastPage(),
                'per_page' => $lezioni->perPage(),
                'total' => $lezioni->total(),
            ]
        ]);
    }

    /**
     * Prenota lezione
     */
    public function prenotaLezione(Request $request, $lezioneId)
    {
        $cliente = $request->user()->cliente;

        if (!$cliente) {
            return response()->json([
                'success' => false,
                'message' => 'Cliente non trovato'
            ], 404);
        }

        $lezione = Lezione::findOrFail($lezioneId);

        // Verifica posti disponibili
        if ($lezione->posti_occupati >= $lezione->posti_totali) {
            return response()->json([
                'success' => false,
                'message' => 'Lezione al completo'
            ], 400);
        }

        // Verifica se già prenotato
        if ($lezione->clienti()->where('cliente_id', $cliente->id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Hai già prenotato questa lezione'
            ], 400);
        }

        // Prenota
        $lezione->clienti()->attach($cliente->id, [
            'stato' => 'prenotato',
            'data_prenotazione' => now(),
        ]);

        $lezione->increment('posti_occupati');

        return response()->json([
            'success' => true,
            'message' => 'Lezione prenotata con successo',
            'data' => $lezione->fresh()
        ]);
    }

    /**
     * Le mie prenotazioni
     */
    public function miePrenotazioni(Request $request)
    {
        $cliente = $request->user()->cliente;

        if (!$cliente) {
            return response()->json([
                'success' => false,
                'message' => 'Cliente non trovato'
            ], 404);
        }

        $prenotazioni = $cliente->lezioniPrenotate()
            ->with(['programma', 'sede', 'professionista'])
            ->where('data', '>=', now())
            ->orderBy('data')
            ->orderBy('ora_inizio')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $prenotazioni
        ]);
    }

    /**
     * Annulla prenotazione
     */
    public function annullaPrenotazione(Request $request, $lezioneId)
    {
        $cliente = $request->user()->cliente;

        if (!$cliente) {
            return response()->json([
                'success' => false,
                'message' => 'Cliente non trovato'
            ], 404);
        }

        $lezione = Lezione::findOrFail($lezioneId);

        // Verifica se prenotato
        if (!$lezione->clienti()->where('cliente_id', $cliente->id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Prenotazione non trovata'
            ], 404);
        }

        // Annulla
        $lezione->clienti()->detach($cliente->id);
        $lezione->decrement('posti_occupati');

        return response()->json([
            'success' => true,
            'message' => 'Prenotazione annullata con successo'
        ]);
    }
}
