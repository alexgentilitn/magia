<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Professionista;

class PasswordTemporaneaMail extends Mailable
{
    use Queueable, SerializesModels;

    public $professionista;
    public $passwordTemporanea;
    public $scadenza;
    public $loginUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(Professionista $professionista, string $passwordTemporanea, $scadenza)
    {
        $this->professionista = $professionista;
        $this->passwordTemporanea = $passwordTemporanea;
        $this->scadenza = $scadenza;
        $this->loginUrl = url('/admin/login');
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Password Temporanea - MA.GIA DONNA',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.password-temporanea',
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
