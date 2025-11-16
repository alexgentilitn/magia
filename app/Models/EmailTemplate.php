<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'codice',
        'nome',
        'oggetto',
        'corpo_html',
        'corpo_testo',
        'tipo',
        'variabili_disponibili',
        'attivo'
    ];

    protected $casts = [
        'variabili_disponibili' => 'array',
        'attivo' => 'boolean'
    ];

    /**
     * Trova template per codice
     */
    public static function findByCodice(string $codice): ?self
    {
        return self::where('codice', $codice)->where('attivo', true)->first();
    }

    /**
     * Processa template sostituendo variabili
     */
    public function processa(array $variabili = []): array
    {
        $oggetto = $this->oggetto;
        $corpo = $this->corpo_html;

        foreach ($variabili as $chiave => $valore) {
            $placeholder = '{{' . $chiave . '}}';
            $oggetto = str_replace($placeholder, $valore, $oggetto);
            $corpo = str_replace($placeholder, $valore, $corpo);
        }

        return [
            'oggetto' => $oggetto,
            'corpo_html' => $corpo,
            'corpo_testo' => strip_tags($corpo)
        ];
    }

    /**
     * Scope solo attivi
     */
    public function scopeAttivi($query)
    {
        return $query->where('attivo', true);
    }

    /**
     * Scope per tipo
     */
    public function scopeTipo($query, string $tipo)
    {
        return $query->where('tipo', $tipo);
    }
}
