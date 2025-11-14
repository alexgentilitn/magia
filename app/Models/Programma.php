<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Model: Programma
 * Funzione: Gestisce i programmi/corsi offerti (Balla & Snella, Alimentazione, ecc.)
 */
class Programma extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'programmi';

    protected $fillable = [
        'nome',
        'slug',
        'descrizione',
        'descrizione_breve',
        'tipologia',
        'discipline',
        'livello',
        'durata_giorni',
        'durata_mesi',
        'lezioni_totali',
        'lezioni_settimana',
        'durata_singola_lezione',
        'prezzo_base',
        'prezzo_promo',
        'promo_valida_da',
        'promo_valida_a',
        'prezzo_su_richiesta',
        'posti_disponibili',
        'posti_occupati',
        'min_partecipanti',
        'max_partecipanti',
        'sede_id',
        'professionista_id',
        'data_inizio',
        'data_fine',
        'sempre_disponibile',
        'obiettivi',
        'prerequisiti',
        'materiali_inclusi',
        'immagine_copertina',
        'galleria_immagini',
        'attivo',
        'visibile_pubblico',
        'in_evidenza',
        'ordine',
    ];

    protected $casts = [
        'discipline' => 'array',
        'obiettivi' => 'array',
        'prerequisiti' => 'array',
        'materiali_inclusi' => 'array',
        'galleria_immagini' => 'array',
        'promo_valida_da' => 'date',
        'promo_valida_a' => 'date',
        'data_inizio' => 'date',
        'data_fine' => 'date',
        'prezzo_base' => 'decimal:2',
        'prezzo_promo' => 'decimal:2',
        'prezzo_su_richiesta' => 'boolean',
        'sempre_disponibile' => 'boolean',
        'attivo' => 'boolean',
        'visibile_pubblico' => 'boolean',
        'in_evidenza' => 'boolean',
        'durata_giorni' => 'integer',
        'durata_mesi' => 'integer',
        'lezioni_totali' => 'integer',
        'lezioni_settimana' => 'integer',
        'durata_singola_lezione' => 'integer',
        'posti_disponibili' => 'integer',
        'posti_occupati' => 'integer',
        'min_partecipanti' => 'integer',
        'max_partecipanti' => 'integer',
        'ordine' => 'integer',
    ];

    /**
     * Relazione: Programma appartiene a una Sede
     */
    public function sede()
    {
        return $this->belongsTo(Sede::class, 'sede_id');
    }

    /**
     * Relazione: Programma ha un Professionista responsabile
     */
    public function professionista()
    {
        return $this->belongsTo(Utente::class, 'professionista_id');
    }

    /**
     * Relazione: Programma ha molte Lezioni
     */
    public function lezioni()
    {
        return $this->hasMany(Lezione::class, 'programma_id');
    }

    /**
     * Relazione: Programma ha molti Clienti iscritti
     */
    public function clienti()
    {
        return $this->belongsToMany(Utente::class, 'cliente_programma', 'programma_id', 'cliente_id')
            ->withPivot('data_iscrizione', 'data_inizio', 'data_fine', 'stato', 'prezzo_pagato', 'completamento')
            ->withTimestamps();
    }

    /**
     * Scope: Solo programmi attivi
     */
    public function scopeAttivi($query)
    {
        return $query->where('attivo', true);
    }

    /**
     * Scope: Solo programmi visibili al pubblico
     */
    public function scopeVisibili($query)
    {
        return $query->where('visibile_pubblico', true);
    }

    /**
     * Scope: Solo programmi in evidenza
     */
    public function scopeInEvidenza($query)
    {
        return $query->where('in_evidenza', true);
    }

    /**
     * Verifica se il programma è in promo
     */
    public function isInPromo()
    {
        if (!$this->prezzo_promo) {
            return false;
        }

        $oggi = now();

        if ($this->promo_valida_da && $oggi->lt($this->promo_valida_da)) {
            return false;
        }

        if ($this->promo_valida_a && $oggi->gt($this->promo_valida_a)) {
            return false;
        }

        return true;
    }

    /**
     * Ottiene il prezzo attuale (promo se attiva, altrimenti base)
     */
    public function getPrezzoAttualeAttribute()
    {
        if ($this->isInPromo()) {
            return $this->prezzo_promo;
        }

        return $this->prezzo_base;
    }

    /**
     * Calcola lo sconto percentuale
     */
    public function getScontoPercentualeAttribute()
    {
        if (!$this->isInPromo() || $this->prezzo_base == 0) {
            return 0;
        }

        return round((($this->prezzo_base - $this->prezzo_promo) / $this->prezzo_base) * 100);
    }

    /**
     * Verifica se ci sono posti disponibili
     */
    public function hasPostiDisponibili()
    {
        if ($this->sempre_disponibile) {
            return true;
        }

        if (!$this->posti_disponibili) {
            return true; // Illimitati se null
        }

        return $this->posti_occupati < $this->posti_disponibili;
    }

    /**
     * Calcola i posti disponibili
     */
    public function postiRimasti()
    {
        if ($this->sempre_disponibile || !$this->posti_disponibili) {
            return null; // Illimitati
        }

        return max(0, $this->posti_disponibili - $this->posti_occupati);
    }
}
