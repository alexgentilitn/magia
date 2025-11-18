<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Model: Pagamento Professionista
 *
 * Funzione: Gestisce i pagamenti effettivi ai professionisti
 * Distingue tra compenso maturato (da lezioni) e compenso pagato (bonifici effettivi)
 *
 * Tabella: pagamenti_professionisti
 */
class PagamentoProfessionista extends Model
{
    use HasFactory;

    protected $table = 'pagamenti_professionisti';

    protected $fillable = [
        'professionista_id',
        'utente_id',
        'periodo_da',
        'periodo_a',
        'importo_maturato',
        'importo_pagato',
        'ritenuta_fiscale',
        'importo_netto',
        'metodo_pagamento',
        'data_pagamento',
        'numero_bonifico',
        'iban',
        'note',
        'stato',
        'ricevuta_path',
        'pagato_da',
    ];

    protected $casts = [
        'periodo_da' => 'date',
        'periodo_a' => 'date',
        'data_pagamento' => 'datetime',
        'importo_maturato' => 'decimal:2',
        'importo_pagato' => 'decimal:2',
        'ritenuta_fiscale' => 'decimal:2',
        'importo_netto' => 'decimal:2',
    ];

    /**
     * Relazione con Utente (professionista)
     */
    public function professionista()
    {
        return $this->belongsTo(Utente::class, 'utente_id');
    }

    /**
     * Relazione con Professionista (dettagli)
     */
    public function professionistaDettagli()
    {
        return $this->belongsTo(Professionista::class, 'professionista_id');
    }

    /**
     * Amministratore che ha registrato il pagamento
     */
    public function amministratore()
    {
        return $this->belongsTo(Utente::class, 'pagato_da');
    }

    /**
     * Scope: Pagamenti completati
     */
    public function scopeCompletati($query)
    {
        return $query->where('stato', 'completato');
    }

    /**
     * Scope: Pagamenti in attesa
     */
    public function scopeInAttesa($query)
    {
        return $query->where('stato', 'in_attesa');
    }

    /**
     * Scope: Pagamenti per periodo
     */
    public function scopePerPeriodo($query, $da, $a)
    {
        return $query->whereBetween('periodo_da', [$da, $a])
                    ->orWhereBetween('periodo_a', [$da, $a]);
    }

    /**
     * Calcola ritenuta fiscale (20% default per collaboratori)
     */
    public function calcolaRitenuta()
    {
        return $this->importo_maturato * 0.20;
    }

    /**
     * Calcola importo netto
     */
    public function calcolaImportoNetto()
    {
        return $this->importo_maturato - ($this->ritenuta_fiscale ?? $this->calcolaRitenuta());
    }

    /**
     * Badge stato
     */
    public function getBadgeStatoAttribute()
    {
        return match($this->stato) {
            'completato' => '<span class="badge bg-success">Pagato</span>',
            'in_attesa' => '<span class="badge bg-warning">In Attesa</span>',
            'annullato' => '<span class="badge bg-danger">Annullato</span>',
            default => '<span class="badge bg-secondary">Sconosciuto</span>',
        };
    }

    /**
     * Descrizione periodo
     */
    public function getDescrizionePeriodoAttribute()
    {
        return $this->periodo_da->format('d/m/Y') . ' - ' . $this->periodo_a->format('d/m/Y');
    }
}
