<?php

namespace App\Mail;

use App\Models\Utente;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Email: Reset Password
 * Inviata agli admin/professionisti per recuperare la password
 */
class ResetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public $utente;
    public $token;
    public $resetUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(Utente $utente, $token)
    {
        $this->utente = $utente;
        $this->token = $token;
        $this->resetUrl = url(route('admin.password.reset', ['token' => $token], false)) . '?email=' . urlencode($utente->email);
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Recupero Password - MA.GIA DONNA')
                    ->view('emails.reset-password');
    }
}
