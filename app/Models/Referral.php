<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model: Referral
 * Funzione: Gestisce il programma "Porta un Amico" con codici sconto
 */
class Referral extends Model
{
    use HasFactory;

    protected $table = 'referrals';

    protected $fillable = [
        'cliente_id', // Chi invita
        'cliente_invitato_id', // Chi è stato invitato
        'codice_referral', // Codice usato
        'stato', // in_attesa, iscritto, attivo, sconto_applicato
        'data_invito',
        'data_iscrizione',
        'data_attivazione',
        'sconto_tipo', // percentuale, fisso, lezione_gratis
        'sconto_valore',
        'sconto_applicato',
        'note',
    ];

    protected $casts = [
        'data_invito' => 'datetime',
        'data_iscrizione' => 'datetime',
        'data_attivazione' => 'datetime',
        'sconto_applicato' => 'boolean',
        'sconto_valore' => 'decimal:2',
    ];

    /**
     * Relazione: Cliente che invita
     */
    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    /**
     * Relazione: Cliente invitato
     */
    public function clienteInvitato()
    {
        return $this->belongsTo(Cliente::class, 'cliente_invitato_id');
    }

    /**
     * Scope: Referral attivi
     */
    public function scopeAttivi($query)
    {
        return $query->where('stato', 'attivo');
    }

    /**
     * Scope: In attesa iscrizione
     */
    public function scopeInAttesa($query)
    {
        return $query->where('stato', 'in_attesa');
    }

    /**
     * Scope: Con sconto non ancora applicato
     */
    public function scopeScontoNonApplicato($query)
    {
        return $query->where('sconto_applicato', false)
                     ->whereIn('stato', ['iscritto', 'attivo']);
    }

    /**
     * Stati referral
     */
    public static function getStati()
    {
        return [
            'in_attesa' => 'In Attesa',
            'iscritto' => 'Iscritto',
            'attivo' => 'Attivo',
            'sconto_applicato' => 'Sconto Applicato',
            'annullato' => 'Annullato',
        ];
    }

    /**
     * Tipi sconto
     */
    public static function getTipiSconto()
    {
        return [
            'percentuale' => 'Percentuale',
            'fisso' => 'Importo Fisso',
            'lezione_gratis' => 'Lezione Gratuita',
            'upgrade' => 'Upgrade Programma',
        ];
    }

    /**
     * Calcola valore sconto
     */
    public function calcolaScontoApplicabile($importo_base)
    {
        if ($this->sconto_tipo === 'percentuale') {
            return $importo_base * ($this->sconto_valore / 100);
        } elseif ($this->sconto_tipo === 'fisso') {
            return $this->sconto_valore;
        }

        return 0;
    }
}
