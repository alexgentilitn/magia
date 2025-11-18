<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model: ParametroCorporeo
 * Funzione: Gestisce lo storico dei parametri corporei del cliente nel tempo
 */
class ParametroCorporeo extends Model
{
    use HasFactory;

    protected $table = 'parametri_corporei';

    protected $fillable = [
        'cliente_id',
        'data_rilevazione',
        'peso',
        'altezza',
        'circonferenza_vita',
        'circonferenza_fianchi',
        'circonferenza_braccia',
        'circonferenza_cosce',
        'massa_grassa',
        'massa_magra',
        'acqua_corporea',
        'metabolismo_basale',
        'massa_ossea',
        'grasso_viscerale',
        'proteine',
        'eta_metabolica',
        'bmi',
        'note',
        'rilevato_da',
    ];

    protected $casts = [
        'data_rilevazione' => 'date',
        'peso' => 'decimal:2',
        'altezza' => 'decimal:2',
        'circonferenza_vita' => 'decimal:2',
        'circonferenza_fianchi' => 'decimal:2',
        'circonferenza_braccia' => 'decimal:2',
        'circonferenza_cosce' => 'decimal:2',
        'massa_grassa' => 'decimal:2',
        'massa_magra' => 'decimal:2',
        'acqua_corporea' => 'decimal:2',
        'metabolismo_basale' => 'decimal:2',
        'massa_ossea' => 'decimal:2',
        'grasso_viscerale' => 'decimal:2',
        'proteine' => 'decimal:2',
        'eta_metabolica' => 'integer',
        'bmi' => 'decimal:2',
    ];

    /**
     * Relazione: Parametro appartiene a un Cliente
     */
    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    /**
     * Calcola automaticamente il BMI se non fornito
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($parametro) {
            if (!$parametro->bmi && $parametro->peso && $parametro->altezza) {
                $altezza_metri = $parametro->altezza / 100;
                $parametro->bmi = round($parametro->peso / ($altezza_metri * $altezza_metri), 2);
            }

            if (!$parametro->data_rilevazione) {
                $parametro->data_rilevazione = now();
            }
        });
    }

    /**
     * Ottiene la categoria BMI
     */
    public function getCategoriaBmiAttribute()
    {
        if (!$this->bmi) {
            return 'Non disponibile';
        }

        if ($this->bmi < 18.5) return 'Sottopeso';
        if ($this->bmi < 25) return 'Normopeso';
        if ($this->bmi < 30) return 'Sovrappeso';
        if ($this->bmi < 35) return 'Obesità I grado';
        if ($this->bmi < 40) return 'Obesità II grado';
        
        return 'Obesità III grado';
    }

    /**
     * Scope: Parametri ordinati per data (più recenti prima)
     */
    public function scopeRecenti($query)
    {
        return $query->orderBy('data_rilevazione', 'desc');
    }

    /**
     * Scope: Parametri di un periodo specifico
     */
    public function scopeNelPeriodo($query, $data_inizio, $data_fine)
    {
        return $query->whereBetween('data_rilevazione', [$data_inizio, $data_fine]);
    }
}
