<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Segnalazione;

/**
 * Mailable: Notifica Cambio Stato Segnalazione
 * Inviata quando lo stato di una segnalazione cambia
 */
class SegnalazioneStatoCambiatoMail extends Mailable
{
    use Queueable, SerializesModels;

    public $segnalazione;
    public $vecchioStato;
    public $nuovoStato;

    /**
     * Create a new message instance.
     */
    public function __construct(Segnalazione $segnalazione, $vecchioStato, $nuovoStato)
    {
        $this->segnalazione = $segnalazione;
        $this->vecchioStato = $vecchioStato;
        $this->nuovoStato = $nuovoStato;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $statoLabel = match($this->nuovoStato) {
            'aperta' => 'Aperta',
            'in_lavorazione' => 'In Lavorazione',
            'risolta' => 'Risolta',
            default => ucfirst($this->nuovoStato),
        };

        return new Envelope(
            subject: "Segnalazione #{$this->segnalazione->id} - Stato: {$statoLabel}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.segnalazione-stato-cambiato',
            with: [
                'segnalazione' => $this->segnalazione,
                'vecchioStato' => $this->vecchioStato,
                'nuovoStato' => $this->nuovoStato,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
