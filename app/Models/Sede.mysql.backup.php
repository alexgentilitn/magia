<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Model: Sede
 * Funzione: Gestisce le sedi fisiche del centro fitness
 */
class Sede extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'sedi';

    protected $fillable = [
        'nome',
        'slug',
        'descrizione',
        'indirizzo_via',
        'indirizzo_citta',
        'indirizzo_provincia',
        'indirizzo_cap',
        'latitudine',
        'longitudine',
        'telefono',
        'email',
        'orari_apertura',
        'note_accesso',
        'tipologie_corsi',
        'capienza_massima',
        'immagine_copertina',
        'galleria_immagini',
        'attiva',
        'visibile_pubblico',
        'sede_principale',
        'ordine',
    ];

    protected $casts = [
        'orari_apertura' => 'array',
        'tipologie_corsi' => 'array',
        'galleria_immagini' => 'array',
        'latitudine' => 'decimal:8',
        'longitudine' => 'decimal:8',
        'attiva' => 'boolean',
        'visibile_pubblico' => 'boolean',
        'sede_principale' => 'boolean',
        'capienza_massima' => 'integer',
        'ordine' => 'integer',
    ];

    /**
     * Relazione: Sede ha molti Programmi
     */
    public function programmi()
    {
        return $this->hasMany(Programma::class, 'sede_id');
    }

    /**
     * Relazione: Sede ha molte Lezioni
     */
    public function lezioni()
    {
        return $this->hasMany(Lezione::class, 'sede_id');
    }

    /**
     * Relazione: Sede ha molti Professionisti assegnati
     */
    public function professionisti()
    {
        return $this->belongsToMany(Utente::class, 'professionista_sede', 'sede_id', 'professionista_id')
            ->withPivot('ruolo_nella_sede', 'data_assegnazione')
            ->withTimestamps();
    }

    /**
     * Scope: Solo sedi attive
     */
    public function scopeAttive($query)
    {
        return $query->where('attiva', true);
    }

    /**
     * Scope: Solo sedi visibili
     */
    public function scopeVisibili($query)
    {
        return $query->where('visibile_pubblico', true);
    }

    /**
     * Scope: Sede principale
     */
    public function scopePrincipale($query)
    {
        return $query->where('sede_principale', true);
    }

    /**
     * Ottiene l'indirizzo completo
     */
    public function getIndirizzoCompletoAttribute()
    {
        return "{$this->indirizzo_via}, {$this->indirizzo_cap} {$this->indirizzo_citta} ({$this->indirizzo_provincia})";
    }

    /**
     * Genera link Google Maps
     */
    public function getLinkMapsAttribute()
    {
        if ($this->latitudine && $this->longitudine) {
            return "https://www.google.com/maps?q={$this->latitudine},{$this->longitudine}";
        }

        $indirizzo = urlencode($this->indirizzo_completo);
        return "https://www.google.com/maps/search/?api=1&query={$indirizzo}";
    }

    /**
     * Verifica se la sede è aperta oggi
     */
    public function isApertaOggi()
    {
        if (!$this->orari_apertura) {
            return true; // Assume sempre aperta se non specificato
        }

        $giornoSettimana = now()->locale('it')->dayName;
        $giornoKey = strtolower($giornoSettimana);

        return isset($this->orari_apertura[$giornoKey]) && $this->orari_apertura[$giornoKey];
    }
}
