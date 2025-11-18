<?php

namespace App\Services;

use App\Models\Notifica;
use App\Models\Utente;
use Illuminate\Support\Facades\Log;

/**
 * Service: Notifiche
 *
 * Gestisce la creazione e l'invio di notifiche agli utenti
 * Supporta: database, push browser (futuro), email (futuro)
 */
class NotificheService
{
    /**
     * Invia notifica a un utente
     */
    public function invia($utenteId, $tipo, $titolo, $corpo = null, $options = [])
    {
        try {
            // Crea notifica nel database
            $notifica = Notifica::creaPerUtente(
                $utenteId,
                $tipo,
                $titolo,
                $corpo,
                $options
            );

            // TODO: Inviare push browser se l'utente ha abilitato le notifiche
            // $this->inviaPushBrowser($notifica);

            Log::info('Notifica inviata', [
                'utente_id' => $utenteId,
                'tipo' => $tipo,
                'notifica_id' => $notifica->id,
            ]);

            return $notifica;

        } catch (\Exception $e) {
            Log::error('Errore invio notifica', [
                'utente_id' => $utenteId,
                'tipo' => $tipo,
                'errore' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Invia notifica a più utenti
     */
    public function inviaAMultipli(array $utentiIds, $tipo, $titolo, $corpo = null, $options = [])
    {
        $notifiche = [];

        foreach ($utentiIds as $utenteId) {
            try {
                $notifiche[] = $this->invia($utenteId, $tipo, $titolo, $corpo, $options);
            } catch (\Exception $e) {
                // Continua anche se una notifica fallisce
                continue;
            }
        }

        return $notifiche;
    }

    /**
     * Invia notifica a tutti gli utenti di un certo ruolo
     */
    public function inviaPerRuolo($ruolo, $tipo, $titolo, $corpo = null, $options = [])
    {
        $utenti = Utente::where('tipo', $ruolo)->pluck('id')->toArray();
        return $this->inviaAMultipli($utenti, $tipo, $titolo, $corpo, $options);
    }

    /**
     * Segna tutte le notifiche di un utente come lette
     */
    public function segnaTutteComeLette($utenteId)
    {
        return Notifica::where('utente_id', $utenteId)
            ->where('letta', false)
            ->update([
                'letta' => true,
                'letta_at' => now(),
            ]);
    }

    /**
     * Ottieni contatore notifiche non lette per utente
     */
    public function contaNonLette($utenteId)
    {
        return Notifica::where('utente_id', $utenteId)
            ->where('letta', false)
            ->count();
    }

    /**
     * Ottieni ultime notifiche per utente
     */
    public function ottieniRecenti($utenteId, $limit = 10)
    {
        return Notifica::where('utente_id', $utenteId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Pulisci notifiche vecchie (più di 30 giorni)
     */
    public function pulisciVecchie()
    {
        $deleted = Notifica::where('created_at', '<', now()->subDays(30))
            ->where('letta', true)
            ->delete();

        Log::info('Notifiche vecchie eliminate', ['count' => $deleted]);

        return $deleted;
    }

    // ============================================
    // NOTIFICHE SPECIFICHE PER TIPO
    // ============================================

    /**
     * Notifica nuova lezione
     */
    public function lezioneCreata($lezione)
    {
        // Notifica al professionista
        if ($lezione->professionista_id) {
            $utente = Utente::where('professionista_id', $lezione->professionista_id)->first();
            if ($utente) {
                $this->invia(
                    $utente->id,
                    'lezione',
                    'Nuova Lezione Assegnata',
                    "Ti è stata assegnata una lezione di {$lezione->tipologia} il {$lezione->data_ora->format('d/m/Y H:i')}",
                    [
                        'icona' => 'fa-calendar-plus',
                        'colore' => 'blue',
                        'link_url' => route('professionista.lezioni.show', $lezione->id),
                        'dati_extra' => ['lezione_id' => $lezione->id],
                    ]
                );
            }
        }

        // Notifica ai clienti prenotati
        foreach ($lezione->clienti as $cliente) {
            if ($cliente->utente) {
                $this->invia(
                    $cliente->utente->id,
                    'lezione',
                    'Lezione Confermata',
                    "La tua lezione di {$lezione->tipologia} è confermata per il {$lezione->data_ora->format('d/m/Y H:i')}",
                    [
                        'icona' => 'fa-check-circle',
                        'colore' => 'green',
                        'link_url' => route('cliente.calendario'),
                        'dati_extra' => ['lezione_id' => $lezione->id],
                    ]
                );
            }
        }
    }

    /**
     * Notifica nuovo messaggio
     */
    public function nuovoMessaggio($messaggio, $destinatarioId)
    {
        $mittente = $messaggio->mittente;

        $this->invia(
            $destinatarioId,
            'messaggio',
            'Nuovo Messaggio',
            "{$mittente->nome} {$mittente->cognome} ti ha inviato un messaggio",
            [
                'icona' => 'fa-comment',
                'colore' => 'green',
                'link_url' => route('messaging.show', $messaggio->conversazione_id),
                'dati_extra' => [
                    'conversazione_id' => $messaggio->conversazione_id,
                    'messaggio_id' => $messaggio->id,
                ],
            ]
        );
    }

    /**
     * Notifica pagamento approvato
     */
    public function pagamentoApprovato($utente, $importo)
    {
        $this->invia(
            $utente->id,
            'pagamento',
            'Pagamento Approvato',
            "Il tuo pagamento di €" . number_format($importo, 2, ',', '.') . " è stato approvato",
            [
                'icona' => 'fa-check-circle',
                'colore' => 'green',
                'link_url' => route('cliente.dashboard'),
            ]
        );
    }

    /**
     * Notifica compenso pagato (per professionisti)
     */
    public function compensoPagato($utenteId, $pagamento)
    {
        $this->invia(
            $utenteId,
            'compenso',
            'Compenso Pagato',
            "Hai ricevuto un compenso di €" . number_format($pagamento->importo_pagato, 2, ',', '.'),
            [
                'icona' => 'fa-euro-sign',
                'colore' => 'green',
                'link_url' => route('professionista.compensi.storico'),
                'dati_extra' => ['pagamento_id' => $pagamento->id],
            ]
        );
    }
}
