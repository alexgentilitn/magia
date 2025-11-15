<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Model: SchedaAllenamento
 * Gestisce le schede di allenamento personalizzate create per ogni cliente
 */
class SchedaAllenamento extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'schede_allenamento';

    protected $fillable = [
        'cliente_id',
        'professionista_id',
        'nome_scheda',
        'descrizione',
        'obiettivi',
        'data_inizio',
        'data_fine',
        'durata_settimane',
        'note_generali',
        'note_alimentazione',
        'consigli_professionista',
        'stato',
        'inviata_email',
        'data_invio_email',
        'pdf_path',
    ];

    protected $casts = [
        'data_inizio' => 'date',
        'data_fine' => 'date',
        'durata_settimane' => 'integer',
        'inviata_email' => 'boolean',
        'data_invio_email' => 'datetime',
    ];

    /**
     * Relazione: Scheda appartiene a un Cliente
     */
    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    /**
     * Relazione: Scheda creata da un Professionista
     */
    public function professionista()
    {
        return $this->belongsTo(Utente::class, 'professionista_id');
    }

    /**
     * Relazione: Scheda ha molti Esercizi
     */
    public function esercizi()
    {
        return $this->hasMany(SchedaEsercizio::class, 'scheda_allenamento_id')->orderBy('giorno_settimana')->orderBy('ordine');
    }

    /**
     * Ottiene esercizi raggruppati per giorno
     */
    public function eserciziPerGiorno()
    {
        return $this->esercizi->groupBy('giorno_settimana');
    }

    /**
     * Scope: Solo schede attive
     */
    public function scopeAttive($query)
    {
        return $query->where('stato', 'attiva');
    }

    /**
     * Scope: Solo bozze
     */
    public function scopeBozze($query)
    {
        return $query->where('stato', 'bozza');
    }

    /**
     * Scope: Schede di un cliente specifico
     */
    public function scopePerCliente($query, $clienteId)
    {
        return $query->where('cliente_id', $clienteId);
    }

    /**
     * Verifica se la scheda è scaduta
     */
    public function isScaduta()
    {
        if (!$this->data_fine) {
            return false;
        }

        return now()->gt($this->data_fine);
    }

    /**
     * Verifica se la scheda è in corso
     */
    public function isInCorso()
    {
        if (!$this->data_inizio) {
            return false;
        }

        $now = now();

        if ($now->lt($this->data_inizio)) {
            return false;
        }

        if ($this->data_fine && $now->gt($this->data_fine)) {
            return false;
        }

        return true;
    }

    /**
     * Calcola il numero di esercizi totali
     */
    public function numeroEserciziTotali()
    {
        return $this->esercizi()->count();
    }

    /**
     * Calcola il numero di giorni di allenamento
     */
    public function numeroGiorniAllenamento()
    {
        return $this->esercizi()
            ->select('giorno_settimana')
            ->distinct()
            ->where('giorno_settimana', '!=', 'Riposo')
            ->count();
    }
}
