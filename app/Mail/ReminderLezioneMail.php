<?php

namespace App\Mail;

use App\Models\Lezione;
use App\Models\Cliente;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReminderLezioneMail extends Mailable
{
    use Queueable, SerializesModels;

    public $lezione;
    public $cliente;

    /**
     * Create a new message instance.
     */
    public function __construct(Lezione $lezione, Cliente $cliente)
    {
        $this->lezione = $lezione;
        $this->cliente = $cliente;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '⏰ Promemoria Lezione Domani - MA.GIA DONNA',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.calendario.reminder-lezione',
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
