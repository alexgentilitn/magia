<?php

namespace App\Mail;

use App\Models\Professionista;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Email: Notifica Approvazione Professionista
 */
class ProfessionistaApprovatoMail extends Mailable
{
    use Queueable, SerializesModels;

    public $professionista;

    public function __construct(Professionista $professionista)
    {
        $this->professionista = $professionista;
    }

    public function build()
    {
        return $this->subject('Il tuo profilo professionista è stato approvato!')
                    ->view('emails.professionista-approvato');
    }
}
