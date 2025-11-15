<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model: SchedaEsercizio
 * Gestisce i singoli esercizi di una scheda di allenamento
 */
class SchedaEsercizio extends Model
{
    use HasFactory;

    protected $table = 'scheda_esercizi';

    protected $fillable = [
        'scheda_allenamento_id',
        'giorno_settimana',
        'ordine',
        'nome_esercizio',
        'descrizione',
        'istruzioni_esecuzione',
        'serie',
        'ripetizioni',
        'recupero_secondi',
        'peso_suggerito',
        'durata_minuti',
        'intensita',
        'note_esecuzione',
        'varianti',
        'video_url',
        'immagine_url',
        'categoria',
        'muscoli_target',
    ];

    protected $casts = [
        'serie' => 'integer',
        'recupero_secondi' => 'integer',
        'durata_minuti' => 'integer',
        'ordine' => 'integer',
        'muscoli_target' => 'array',
    ];

    /**
     * Relazione: Esercizio appartiene a una Scheda
     */
    public function schedaAllenamento()
    {
        return $this->belongsTo(SchedaAllenamento::class, 'scheda_allenamento_id');
    }

    /**
     * Formatta il recupero in formato leggibile
     */
    public function getRecuperoFormattato()
    {
        if (!$this->recupero_secondi) {
            return '-';
        }

        if ($this->recupero_secondi < 60) {
            return $this->recupero_secondi . ' sec';
        }

        $minuti = floor($this->recupero_secondi / 60);
        $secondi = $this->recupero_secondi % 60;

        if ($secondi == 0) {
            return $minuti . ' min';
        }

        return $minuti . ' min ' . $secondi . ' sec';
    }

    /**
     * Formatta serie e ripetizioni
     */
    public function getSerieRipetizioniFormattato()
    {
        if (!$this->serie && !$this->ripetizioni) {
            return '-';
        }

        if ($this->serie && $this->ripetizioni) {
            return $this->serie . ' x ' . $this->ripetizioni;
        }

        if ($this->serie) {
            return $this->serie . ' serie';
        }

        return $this->ripetizioni . ' rip';
    }

    /**
     * Scope: Esercizi di un giorno specifico
     */
    public function scopePerGiorno($query, $giorno)
    {
        return $query->where('giorno_settimana', $giorno)->orderBy('ordine');
    }

    /**
     * Scope: Esercizi per categoria
     */
    public function scopePerCategoria($query, $categoria)
    {
        return $query->where('categoria', $categoria);
    }
}
