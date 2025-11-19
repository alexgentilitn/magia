<?php

namespace App\Mail;

use App\Models\Utente;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Mailable: Conferma Registrazione Cliente
 *
 * Funzione: Email inviata al cliente dopo il completamento del pagamento
 * Contiene le credenziali di accesso e link per primo login
 */
class ConfermaRegistrazione extends Mailable
{
    use Queueable, SerializesModels;

    public $utente;

    /**
     * Create a new message instance.
     */
    public function __construct(Utente $utente)
    {
        $this->utente = $utente;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Benvenuta in MA.GIA DONNA - Account Attivato',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.conferma-registrazione',
            with: [
                'nome' => $this->utente->nome,
                'cognome' => $this->utente->cognome,
                'email' => $this->utente->email,
                'loginUrl' => route('cliente.login'),
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
