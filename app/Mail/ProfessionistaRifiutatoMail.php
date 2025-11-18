<?php

namespace App\Mail;

use App\Models\Professionista;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Email: Notifica Rifiuto Professionista
 */
class ProfessionistaRifiutatoMail extends Mailable
{
    use Queueable, SerializesModels;

    public $professionista;
    public $motivo;

    public function __construct(Professionista $professionista, $motivo = null)
    {
        $this->professionista = $professionista;
        $this->motivo = $motivo;
    }

    public function build()
    {
        return $this->subject('Aggiornamento sulla tua richiesta di professionista')
                    ->view('emails.professionista-rifiutato');
    }
}
