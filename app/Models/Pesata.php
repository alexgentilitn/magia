<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model: Pesata
 * Gestisce lo storico delle pesate con tutti i parametri corporei
 */
class Pesata extends Model
{
    protected $table = 'pesate';

    protected $fillable = [
        'cliente_id',
        'sede',
        'peso',
        'bmi',
        'peso_corporeo_senza_grassi',
        'muscolo_scheletrico',
        'grasso_corporeo',
        'grasso_sottocutaneo',
        'grasso_viscerale',
        'acqua_corporea',
        'massa_muscolare',
        'massa_ossea',
        'proteine',
        'bmr',
        'eta_metabolica',
        'data_rilevazione',
        'note',
    ];

    protected $casts = [
        'peso' => 'decimal:2',
        'bmi' => 'decimal:2',
        'peso_corporeo_senza_grassi' => 'decimal:2',
        'muscolo_scheletrico' => 'decimal:2',
        'grasso_corporeo' => 'decimal:2',
        'grasso_sottocutaneo' => 'decimal:2',
        'grasso_viscerale' => 'integer',
        'acqua_corporea' => 'decimal:2',
        'massa_muscolare' => 'decimal:2',
        'massa_ossea' => 'decimal:2',
        'proteine' => 'decimal:2',
        'bmr' => 'integer',
        'eta_metabolica' => 'integer',
        'data_rilevazione' => 'date',
    ];

    /**
     * Relazione con Cliente
     */
    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    /**
     * Scope: Pesate recenti
     */
    public function scopeRecenti($query, int $limit = 10)
    {
        return $query->orderBy('data_rilevazione', 'desc')
                     ->orderBy('created_at', 'desc')
                     ->limit($limit);
    }

    /**
     * Scope: Pesate per cliente
     */
    public function scopePerCliente($query, int $cliente_id)
    {
        return $query->where('cliente_id', $cliente_id);
    }

    /**
     * Scope: Pesate per sede
     */
    public function scopePerSede($query, string $sede)
    {
        return $query->where('sede', $sede);
    }

    /**
     * Scope: Pesate in un periodo
     */
    public function scopePeriodo($query, string $data_inizio, string $data_fine)
    {
        return $query->whereBetween('data_rilevazione', [$data_inizio, $data_fine]);
    }

    /**
     * Ottieni statistiche per un cliente
     */
    public static function getStatistiche(int $cliente_id): array
    {
        $pesate = self::where('cliente_id', $cliente_id)
                     ->orderBy('data_rilevazione', 'asc')
                     ->get();

        if ($pesate->isEmpty()) {
            return [
                'totale_pesate' => 0,
                'peso_iniziale' => null,
                'peso_attuale' => null,
                'variazione_peso' => null,
                'bmi_iniziale' => null,
                'bmi_attuale' => null,
                'variazione_bmi' => null,
            ];
        }

        $prima = $pesate->first();
        $ultima = $pesate->last();

        return [
            'totale_pesate' => $pesate->count(),
            'peso_iniziale' => $prima->peso,
            'peso_attuale' => $ultima->peso,
            'variazione_peso' => $ultima->peso - $prima->peso,
            'bmi_iniziale' => $prima->bmi,
            'bmi_attuale' => $ultima->bmi,
            'variazione_bmi' => $ultima->bmi - $prima->bmi,
            'grasso_iniziale' => $prima->grasso_corporeo,
            'grasso_attuale' => $ultima->grasso_corporeo,
            'variazione_grasso' => $ultima->grasso_corporeo - $prima->grasso_corporeo,
        ];
    }

    /**
     * Ottieni dati per grafici
     */
    public static function getDatiGrafici(int $cliente_id): array
    {
        $pesate = self::where('cliente_id', $cliente_id)
                     ->orderBy('data_rilevazione', 'asc')
                     ->get();

        $date = [];
        $peso = [];
        $bmi = [];
        $grasso = [];
        $acqua = [];
        $muscolo = [];

        foreach ($pesate as $pesata) {
            $date[] = $pesata->data_rilevazione->format('d/m/Y');
            $peso[] = (float) $pesata->peso;
            $bmi[] = (float) $pesata->bmi;
            $grasso[] = (float) $pesata->grasso_corporeo;
            $acqua[] = (float) $pesata->acqua_corporea;
            $muscolo[] = (float) $pesata->muscolo_scheletrico;
        }

        return [
            'date' => $date,
            'peso' => $peso,
            'bmi' => $bmi,
            'grasso_corporeo' => $grasso,
            'acqua_corporea' => $acqua,
            'muscolo_scheletrico' => $muscolo,
        ];
    }

    /**
     * Formatta il peso per la visualizzazione
     */
    public function getPesoFormattato(): string
    {
        return number_format($this->peso, 2, ',', '.') . ' kg';
    }

    /**
     * Formatta il BMI per la visualizzazione
     */
    public function getBmiFormattato(): string
    {
        return number_format($this->bmi, 1, ',', '.');
    }

    /**
     * Ottieni categoria BMI
     */
    public function getCategoriaBmi(): string
    {
        $bmi = (float) $this->bmi;

        if ($bmi < 18.5) return 'Sottopeso';
        if ($bmi < 25) return 'Normopeso';
        if ($bmi < 30) return 'Sovrappeso';
        if ($bmi < 35) return 'Obesità I grado';
        if ($bmi < 40) return 'Obesità II grado';
        return 'Obesità III grado';
    }

    /**
     * Ottieni colore categoria BMI
     */
    public function getColoreBmi(): string
    {
        $bmi = (float) $this->bmi;

        if ($bmi < 18.5) return 'text-blue-600';
        if ($bmi < 25) return 'text-green-600';
        if ($bmi < 30) return 'text-yellow-600';
        return 'text-red-600';
    }
}
