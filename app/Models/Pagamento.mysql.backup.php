<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Model: Pagamento
 * Funzione: Gestisce i pagamenti dei clienti per programmi e lezioni
 */
class Pagamento extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pagamenti';

    protected $fillable = [
        'cliente_id',
        'programma_id',
        'lezione_id',
        'importo',
        'importo_pagato',
        'importo_residuo',
        'tipo',
        'metodo',
        'stato',
        'data_emissione',
        'data_scadenza',
        'data_pagamento',
        'numero_fattura',
        'riferimento_transazione',
        'note',
        'fatturato',
        'inviato_promemoria',
    ];

    protected $casts = [
        'importo' => 'decimal:2',
        'importo_pagato' => 'decimal:2',
        'importo_residuo' => 'decimal:2',
        'data_emissione' => 'date',
        'data_scadenza' => 'date',
        'data_pagamento' => 'date',
        'fatturato' => 'boolean',
        'inviato_promemoria' => 'boolean',
    ];

    /**
     * Relazione: Pagamento appartiene a un Cliente (Utente)
     */
    public function cliente()
    {
        return $this->belongsTo(Utente::class, 'cliente_id');
    }

    /**
     * Relazione: Pagamento può essere collegato a un Programma
     */
    public function programma()
    {
        return $this->belongsTo(Programma::class, 'programma_id');
    }

    /**
     * Relazione: Pagamento può essere collegato a una Lezione
     */
    public function lezione()
    {
        return $this->belongsTo(Lezione::class, 'lezione_id');
    }

    /**
     * Scope: Solo pagamenti in attesa
     */
    public function scopeInAttesa($query)
    {
        return $query->where('stato', 'in_attesa');
    }

    /**
     * Scope: Solo pagamenti completati
     */
    public function scopeCompletati($query)
    {
        return $query->where('stato', 'completato');
    }

    /**
     * Scope: Solo pagamenti scaduti
     */
    public function scopeScaduti($query)
    {
        return $query->where('stato', 'scaduto')
                     ->orWhere(function($q) {
                         $q->where('stato', 'in_attesa')
                           ->where('data_scadenza', '<', now());
                     });
    }

    /**
     * Scope: Pagamenti per tipo
     */
    public function scopePerTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    /**
     * Scope: Pagamenti per metodo
     */
    public function scopePerMetodo($query, $metodo)
    {
        return $query->where('metodo', $metodo);
    }

    /**
     * Verifica se il pagamento è scaduto
     */
    public function isScaduto()
    {
        if (!$this->data_scadenza) {
            return false;
        }

        return now()->gt($this->data_scadenza) && $this->stato !== 'completato';
    }

    /**
     * Verifica se il pagamento è parziale
     */
    public function isParziale()
    {
        return $this->importo_pagato > 0 && $this->importo_pagato < $this->importo;
    }

    /**
     * Verifica se il pagamento è completo
     */
    public function isCompleto()
    {
        return $this->importo_pagato >= $this->importo;
    }

    /**
     * Calcola la percentuale pagata
     */
    public function percentualePagata()
    {
        if ($this->importo == 0) {
            return 0;
        }

        return round(($this->importo_pagato / $this->importo) * 100, 2);
    }

    /**
     * Ottiene il badge CSS per lo stato
     */
    public function getBadgeStatoAttribute()
    {
        return match($this->stato) {
            'in_attesa' => 'bg-yellow-100 text-yellow-800',
            'parziale' => 'bg-blue-100 text-blue-800',
            'completato' => 'bg-green-100 text-green-800',
            'scaduto' => 'bg-red-100 text-red-800',
            'rimborsato' => 'bg-purple-100 text-purple-800',
            'cancellato' => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * Ottiene il badge CSS per il metodo
     */
    public function getBadgeMetodoAttribute()
    {
        return match($this->metodo) {
            'contanti' => 'bg-green-50 text-green-700',
            'bonifico' => 'bg-blue-50 text-blue-700',
            'carta', 'pos' => 'bg-purple-50 text-purple-700',
            'paypal', 'satispay' => 'bg-indigo-50 text-indigo-700',
            default => 'bg-gray-50 text-gray-700',
        };
    }

    /**
     * Ottiene l'icona per il metodo di pagamento
     */
    public function getIconaMetodoAttribute()
    {
        return match($this->metodo) {
            'contanti' => 'fa-money-bill-wave',
            'bonifico' => 'fa-university',
            'carta', 'pos' => 'fa-credit-card',
            'paypal' => 'fa-paypal',
            'satispay' => 'fa-mobile-alt',
            default => 'fa-wallet',
        };
    }

    /**
     * Ottiene la descrizione dell'oggetto del pagamento
     */
    public function getDescrizioneOggettoAttribute()
    {
        if ($this->programma) {
            return "Programma: {$this->programma->nome}";
        }

        if ($this->lezione) {
            return "Lezione: {$this->lezione->titolo}";
        }

        return ucfirst($this->tipo);
    }

    /**
     * Genera il numero fattura automaticamente
     */
    public static function generaNumeroFattura()
    {
        $anno = now()->year;
        $ultimoNumero = static::whereYear('created_at', $anno)
                             ->whereNotNull('numero_fattura')
                             ->count();

        return sprintf('FAT-%d-%04d', $anno, $ultimoNumero + 1);
    }
}
