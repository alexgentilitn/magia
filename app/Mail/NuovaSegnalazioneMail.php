<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Segnalazione;

/**
 * Mailable: Notifica Nuova Segnalazione
 * Inviata quando viene creata una nuova segnalazione
 */
class NuovaSegnalazioneMail extends Mailable
{
    use Queueable, SerializesModels;

    public $segnalazione;

    /**
     * Create a new message instance.
     */
    public function __construct(Segnalazione $segnalazione)
    {
        $this->segnalazione = $segnalazione;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $tipoLabel = match($this->segnalazione->tipo) {
            'bug' => 'Bug',
            'suggerimento' => 'Suggerimento',
            'altro' => 'Altro',
            default => ucfirst($this->segnalazione->tipo),
        };

        return new Envelope(
            subject: "Nuova Segnalazione #{$this->segnalazione->id} - {$tipoLabel}: {$this->segnalazione->titolo}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.nuova-segnalazione',
            with: [
                'segnalazione' => $this->segnalazione,
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
