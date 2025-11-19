<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Utente;
use Illuminate\Support\Facades\Mail;
use App\Mail\SegnalazioneStatoCambiatoMail;

/**
 * Model: Segnalazione
 * Gestisce le segnalazioni di bug/suggerimenti da parte degli utenti
 */
class Segnalazione extends Model
{
    use HasFactory;

    protected $table = 'segnalazioni';

    protected $fillable = [
        'utente_id',
        'titolo',
        'descrizione',
        'tipo',
        'priorita',
        'stato',
        'risposta',
        'email_notifica',
        'risolto_da',
        'risolto_il',
    ];

    protected $casts = [
        'risolto_il' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ============================================
    // RELAZIONI
    // ============================================

    /**
     * Utente che ha creato la segnalazione
     */
    public function utente()
    {
        return $this->belongsTo(Utente::class, 'utente_id');
    }

    /**
     * Utente che ha risolto la segnalazione
     */
    public function risolutore()
    {
        return $this->belongsTo(Utente::class, 'risolto_da');
    }

    // ============================================
    // SCOPES
    // ============================================

    /**
     * Scope: Solo segnalazioni aperte
     */
    public function scopeAperte($query)
    {
        return $query->where('stato', 'aperta');
    }

    /**
     * Scope: Solo segnalazioni in lavorazione
     */
    public function scopeInLavorazione($query)
    {
        return $query->where('stato', 'in_lavorazione');
    }

    /**
     * Scope: Solo segnalazioni risolte
     */
    public function scopeRisolte($query)
    {
        return $query->where('stato', 'risolta');
    }

    /**
     * Scope: Per tipo
     */
    public function scopeTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    /**
     * Scope: Per priorità
     */
    public function scopePriorita($query, $priorita)
    {
        return $query->where('priorita', $priorita);
    }

    /**
     * Scope: Ordina per più recenti
     */
    public function scopeRecenti($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    // ============================================
    // ACCESSORS & MUTATORS
    // ============================================

    /**
     * Badge colore per stato
     */
    public function getStatoBadgeAttribute()
    {
        return match($this->stato) {
            'aperta' => 'bg-red-100 text-red-800',
            'in_lavorazione' => 'bg-yellow-100 text-yellow-800',
            'risolta' => 'bg-green-100 text-green-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * Badge colore per priorità
     */
    public function getPrioritaBadgeAttribute()
    {
        return match($this->priorita) {
            'alta' => 'bg-red-100 text-red-800',
            'media' => 'bg-yellow-100 text-yellow-800',
            'bassa' => 'bg-blue-100 text-blue-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * Icona per tipo
     */
    public function getTipoIconaAttribute()
    {
        return match($this->tipo) {
            'bug' => 'fa-bug',
            'suggerimento' => 'fa-lightbulb',
            'altro' => 'fa-question-circle',
            default => 'fa-circle',
        };
    }

    /**
     * Etichetta tipo leggibile
     */
    public function getTipoLabelAttribute()
    {
        return match($this->tipo) {
            'bug' => 'Bug',
            'suggerimento' => 'Suggerimento',
            'altro' => 'Altro',
            default => ucfirst($this->tipo),
        };
    }

    /**
     * Etichetta stato leggibile
     */
    public function getStatoLabelAttribute()
    {
        return match($this->stato) {
            'aperta' => 'Aperta',
            'in_lavorazione' => 'In Lavorazione',
            'risolta' => 'Risolta',
            default => ucfirst($this->stato),
        };
    }

    /**
     * Etichetta priorità leggibile
     */
    public function getPrioritaLabelAttribute()
    {
        return match($this->priorita) {
            'alta' => 'Alta',
            'media' => 'Media',
            'bassa' => 'Bassa',
            default => ucfirst($this->priorita),
        };
    }

    // ============================================
    // METODI HELPER
    // ============================================

    /**
     * Verifica se la segnalazione è aperta
     */
    public function isAperta()
    {
        return $this->stato === 'aperta';
    }

    /**
     * Verifica se la segnalazione è in lavorazione
     */
    public function isInLavorazione()
    {
        return $this->stato === 'in_lavorazione';
    }

    /**
     * Verifica se la segnalazione è risolta
     */
    public function isRisolta()
    {
        return $this->stato === 'risolta';
    }

    /**
     * Cambia lo stato della segnalazione e invia notifica email
     */
    public function cambiaStato($nuovoStato, $risposta = null, $risolto_da = null)
    {
        $vecchioStato = $this->stato;

        $this->stato = $nuovoStato;

        if ($risposta) {
            $this->risposta = $risposta;
        }

        if ($nuovoStato === 'risolta' && $risolto_da) {
            $this->risolto_da = $risolto_da;
            $this->risolto_il = now();
        }

        $this->save();

        // Invia email notifica se presente email_notifica
        if ($this->email_notifica && $vecchioStato !== $nuovoStato) {
            $this->inviaNotificaCambioStato($vecchioStato, $nuovoStato);
        }

        return true;
    }

    /**
     * Invia email di notifica cambio stato
     */
    protected function inviaNotificaCambioStato($vecchioStato, $nuovoStato)
    {
        try {
            // Applica configurazione SMTP
            if (class_exists(\App\Models\Impostazione::class)) {
                \App\Models\Impostazione::applySmtpConfig();
            }

            Mail::to($this->email_notifica)->send(
                new SegnalazioneStatoCambiatoMail($this, $vecchioStato, $nuovoStato)
            );

            \Log::info("Email notifica inviata per segnalazione #{$this->id} a {$this->email_notifica}");

        } catch (\Exception $e) {
            \Log::error("Errore invio email notifica segnalazione #{$this->id}: " . $e->getMessage());
        }
    }

    /**
     * Invia email di notifica nuova segnalazione
     * Invia sempre copia BCC a support@agstudio.digital (sviluppatore)
     */
    public function inviaNotificaCreazione()
    {
        try {
            // Applica configurazione SMTP
            if (class_exists(\App\Models\Impostazione::class)) {
                \App\Models\Impostazione::applySmtpConfig();
            }

            $mail = new \App\Mail\NuovaSegnalazioneMail($this);

            // Se c'è email di notifica, invia a quella + BCC sviluppatore
            // Altrimenti invia solo allo sviluppatore
            if ($this->email_notifica) {
                Mail::to($this->email_notifica)
                    ->bcc('support@agstudio.digital') // Copia nascosta allo sviluppatore
                    ->send($mail);

                \Log::info("Email notifica nuova segnalazione inviata per #{$this->id} a {$this->email_notifica} (BCC: support@agstudio.digital)");
            } else {
                // Se non c'è email notifica, invia solo allo sviluppatore
                Mail::to('support@agstudio.digital')
                    ->send($mail);

                \Log::info("Email notifica nuova segnalazione inviata per #{$this->id} a support@agstudio.digital");
            }

            return true;

        } catch (\Exception $e) {
            \Log::error("Errore invio email nuova segnalazione #{$this->id}: " . $e->getMessage());
            return false;
        }
    }
}
