<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use App\Models\Referral;
use App\Models\EmailTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

/**
 * Controller per Sistema Referral "Porta un'Amica" - Area Cliente
 */
class ReferralController extends Controller
{
    /**
     * Dashboard referral cliente
     */
    public function index()
    {
        $cliente = Auth::user()->cliente;

        if (!$cliente) {
            abort(403, 'Utente non autorizzato');
        }

        // Referral inviati dal cliente
        $referrals = Referral::where('cliente_invitante_id', $cliente->id)
            ->with('clienteInvitato')
            ->latest('data_invito')
            ->get();

        // Statistiche
        $statistiche = [
            'totale_inviti' => $referrals->count(),
            'amiche_registrate' => $referrals->whereIn('stato', ['registrato', 'convertito'])->count(),
            'amiche_convertite' => $referrals->where('stato', 'convertito')->count(),
            'in_attesa' => $referrals->where('stato', 'pending')->count(),
            'sconti_guadagnati' => $referrals->where('stato', 'convertito')->sum('sconto_invitante'),
            'sconti_disponibili' => $referrals->where('stato', 'convertito')
                ->where('sconto_applicato', false)
                ->sum('sconto_invitante'),
        ];

        // Codice referral personale (generato al volo, non salvato)
        $codicePersonale = 'MAGIA-' . strtoupper(substr($cliente->codice_cliente ?? $cliente->id, -6));

        return view('cliente.referral.index', compact('cliente', 'referrals', 'statistiche', 'codicePersonale'));
    }

    /**
     * Invia invito a un'amica
     */
    public function inviaInvito(Request $request)
    {
        $cliente = Auth::user()->cliente;

        if (!$cliente) {
            return response()->json([
                'success' => false,
                'message' => 'Cliente non trovato'
            ], 404);
        }

        $validated = $request->validate([
            'email_amica' => 'required|email',
            'nome_amica' => 'nullable|string|max:100',
            'messaggio_personale' => 'nullable|string|max:500',
        ]);

        try {
            // Verifica se email già invitata da questo cliente
            $esistente = Referral::where('cliente_invitante_id', $cliente->id)
                ->where('email_invitato', $validated['email_amica'])
                ->first();

            if ($esistente) {
                return response()->json([
                    'success' => false,
                    'message' => 'Hai già inviato un invito a questa email'
                ], 400);
            }

            // Crea referral (sconto invitante 10€, sconto invitato 15€)
            $referral = Referral::creaReferral(
                $cliente->id,
                $validated['email_amica'],
                10.00,
                15.00
            );

            // TODO: Invia email invito
            // $this->inviaEmailInvito($referral, $validated);

            return response()->json([
                'success' => true,
                'message' => 'Invito inviato con successo!',
                'referral' => [
                    'id' => $referral->id,
                    'codice_invito' => $referral->codice_invito,
                    'email' => $referral->email_invitato,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Errore durante l\'invio: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Invia promemoria per invito pending
     */
    public function inviaPromemoria($referralId)
    {
        $cliente = Auth::user()->cliente;

        if (!$cliente) {
            return response()->json([
                'success' => false,
                'message' => 'Cliente non trovato'
            ], 404);
        }

        try {
            $referral = Referral::where('id', $referralId)
                ->where('cliente_invitante_id', $cliente->id)
                ->where('stato', 'pending')
                ->firstOrFail();

            // TODO: Invia email promemoria
            // $this->inviaEmailPromemoria($referral);

            $referral->update([
                'note' => ($referral->note ?? '') . "\nPromemoria inviato: " . now()->format('d/m/Y H:i')
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Promemoria inviato con successo!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Errore: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Visualizza dettagli referral
     */
    public function show($id)
    {
        $cliente = Auth::user()->cliente;

        if (!$cliente) {
            abort(403, 'Utente non autorizzato');
        }

        $referral = Referral::where('id', $id)
            ->where('cliente_invitante_id', $cliente->id)
            ->with('clienteInvitato')
            ->firstOrFail();

        return view('cliente.referral.show', compact('referral'));
    }

    /**
     * Annulla invito pending
     */
    public function annullaInvito($id)
    {
        $cliente = Auth::user()->cliente;

        if (!$cliente) {
            return response()->json([
                'success' => false,
                'message' => 'Cliente non trovato'
            ], 404);
        }

        try {
            $referral = Referral::where('id', $id)
                ->where('cliente_invitante_id', $cliente->id)
                ->where('stato', 'pending')
                ->firstOrFail();

            $referral->delete();

            return response()->json([
                'success' => true,
                'message' => 'Invito annullato con successo'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Errore: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Condividi su social
     */
    public function condividi()
    {
        $cliente = Auth::user()->cliente;

        if (!$cliente) {
            abort(403, 'Utente non autorizzato');
        }

        $codicePersonale = 'MAGIA-' . strtoupper(substr($cliente->codice_cliente ?? $cliente->id, -6));
        $linkInvito = route('registrazione.cliente') . '?ref=' . $codicePersonale;

        $messaggioCondivisione = "🌟 Scopri MA.GIA DONNA! Un metodo innovativo per il tuo benessere. "
            . "Iscriviti con il mio codice {$codicePersonale} e ricevi 15€ di sconto! "
            . $linkInvito;

        return view('cliente.referral.condividi', compact('codicePersonale', 'linkInvito', 'messaggioCondivisione'));
    }

    /**
     * Helper: Invia email invito (da implementare)
     */
    private function inviaEmailInvito(Referral $referral, array $dati)
    {
        // TODO: Implementare invio email usando EmailTemplate
        /*
        $template = EmailTemplate::findByCodice('invito_amica');

        if ($template && $template->attivo) {
            $variabili = [
                'nome_invitante' => $referral->clienteInvitante->nome,
                'nome_amica' => $dati['nome_amica'] ?? 'Amica',
                'codice_invito' => $referral->codice_invito,
                'sconto_invitato' => number_format($referral->sconto_invitato, 2),
                'link_registrazione' => route('registrazione.cliente') . '?ref=' . $referral->codice_invito,
                'messaggio_personale' => $dati['messaggio_personale'] ?? '',
            ];

            $oggetto = $template->processaVariabili($template->oggetto, $variabili);
            $corpo = $template->processaVariabili($template->corpo_html, $variabili);

            Mail::send([], [], function($message) use ($referral, $oggetto, $corpo) {
                $message->to($referral->email_invitato)
                    ->subject($oggetto)
                    ->html($corpo);
            });
        }
        */
    }

    /**
     * Helper: Invia email promemoria
     */
    private function inviaEmailPromemoria(Referral $referral)
    {
        // TODO: Implementare invio email promemoria
    }
}
