<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Segnalazione;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

/**
 * Controller: Gestione Segnalazioni
 * Sistema di segnalazione bug/suggerimenti per comunicazione team
 */
class SegnalazioniController extends Controller
{
    /**
     * Salva nuova segnalazione
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'titolo' => 'required|string|max:255',
            'descrizione' => 'required|string',
            'tipo' => 'required|in:bug,suggerimento,altro',
            'priorita' => 'required|in:bassa,media,alta',
            'email_notifica' => 'nullable|email|max:255',
        ], [
            'titolo.required' => 'Il titolo è obbligatorio',
            'titolo.max' => 'Il titolo non può superare 255 caratteri',
            'descrizione.required' => 'La descrizione è obbligatoria',
            'tipo.required' => 'Il tipo è obbligatorio',
            'tipo.in' => 'Il tipo selezionato non è valido',
            'priorita.required' => 'La priorità è obbligatoria',
            'priorita.in' => 'La priorità selezionata non è valida',
            'email_notifica.email' => 'L\'email deve essere valida',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $data = $validator->validated();
            $data['utente_id'] = Auth::id();
            $data['stato'] = 'aperta';

            $segnalazione = Segnalazione::create($data);

            \Log::info("Nuova segnalazione creata: #{$segnalazione->id} - {$segnalazione->titolo}");

            // Invia email di notifica se presente email_notifica
            $emailInviata = $segnalazione->inviaNotificaCreazione();

            $messaggioSuccesso = 'Segnalazione inviata con successo! Ti aggiorneremo sullo stato.';
            if ($emailInviata) {
                $messaggioSuccesso .= ' Email di conferma inviata.';
            }

            return redirect()->route('admin.impostazioni.index')
                ->with('success', $messaggioSuccesso)
                ->with('active_tab', 'segnalazioni');

        } catch (\Exception $e) {
            \Log::error('Errore salvataggio segnalazione: ' . $e->getMessage());

            return redirect()->back()
                ->withInput()
                ->with('error', 'Errore durante il salvataggio: ' . $e->getMessage());
        }
    }

    /**
     * Mostra dettaglio segnalazione
     */
    public function show(Segnalazione $segnalazione)
    {
        // Controlla permessi: solo amministratori (super admin) o proprietario
        $isAdmin = Auth::user()->tipo_utente === 'amministratore';
        $isOwner = $segnalazione->utente_id === Auth::id();

        if (!$isAdmin && !$isOwner) {
            abort(403, 'Non hai i permessi per visualizzare questa segnalazione');
        }

        $segnalazione->load(['utente', 'risolutore']);

        $isSuperAdmin = $isAdmin; // Gli amministratori possono gestire le segnalazioni

        return view('admin.segnalazioni.show', compact('segnalazione', 'isSuperAdmin'));
    }

    /**
     * Aggiorna stato e risposta segnalazione (solo Amministratori)
     */
    public function update(Request $request, Segnalazione $segnalazione)
    {
        // Solo amministratori possono aggiornare
        if (Auth::user()->tipo_utente !== 'amministratore') {
            abort(403, 'Solo gli Amministratori possono aggiornare le segnalazioni');
        }

        $validator = Validator::make($request->all(), [
            'stato' => 'required|in:aperta,in_lavorazione,risolta',
            'risposta' => 'nullable|string',
        ], [
            'stato.required' => 'Lo stato è obbligatorio',
            'stato.in' => 'Lo stato selezionato non è valido',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $nuovoStato = $request->stato;
            $risposta = $request->risposta;

            // Usa il metodo cambiaStato del model che gestisce anche l'invio email
            $segnalazione->cambiaStato(
                $nuovoStato,
                $risposta,
                Auth::id()
            );

            \Log::info("Segnalazione #{$segnalazione->id} aggiornata a stato: {$nuovoStato}");

            return redirect()->route('admin.impostazioni.segnalazioni.show', $segnalazione)
                ->with('success', 'Segnalazione aggiornata con successo!' .
                    ($segnalazione->email_notifica ? ' Email di notifica inviata.' : ''));

        } catch (\Exception $e) {
            \Log::error('Errore aggiornamento segnalazione: ' . $e->getMessage());

            return redirect()->route('admin.impostazioni.segnalazioni.show', $segnalazione)
                ->withInput()
                ->with('error', 'Errore durante l\'aggiornamento: ' . $e->getMessage());
        }
    }

    /**
     * Elimina segnalazione (solo Amministratori)
     */
    public function destroy(Segnalazione $segnalazione)
    {
        // Solo amministratori possono eliminare
        if (Auth::user()->tipo_utente !== 'amministratore') {
            abort(403, 'Solo gli Amministratori possono eliminare le segnalazioni');
        }

        try {
            $id = $segnalazione->id;
            $titolo = $segnalazione->titolo;

            $segnalazione->delete();

            \Log::info("Segnalazione #{$id} eliminata: {$titolo}");

            return redirect()->route('admin.impostazioni.index')
                ->with('success', 'Segnalazione eliminata con successo.')
                ->with('active_tab', 'segnalazioni');

        } catch (\Exception $e) {
            \Log::error('Errore eliminazione segnalazione: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Errore durante l\'eliminazione: ' . $e->getMessage());
        }
    }
}
