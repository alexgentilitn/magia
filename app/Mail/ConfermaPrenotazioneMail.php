<?php

namespace App\Mail;

use App\Models\Lezione;
use App\Models\Cliente;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ConfermaPrenotazioneMail extends Mailable
{
    use Queueable, SerializesModels;

    public $lezione;
    public $cliente;
    public $listaAttesa;

    /**
     * Create a new message instance.
     */
    public function __construct(Lezione $lezione, Cliente $cliente, bool $listaAttesa = false)
    {
        $this->lezione = $lezione;
        $this->cliente = $cliente;
        $this->listaAttesa = $listaAttesa;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = $this->listaAttesa
            ? 'Aggiunto alla Lista d\'Attesa - MA.GIA DONNA'
            : 'Prenotazione Confermata - MA.GIA DONNA';

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.calendario.conferma-prenotazione',
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
