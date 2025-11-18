<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Model: Ricetta
 * Funzione: Gestisce ricette alimentari con distribuzione automatica programmabile
 */
class Ricetta extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'ricette';

    protected $fillable = [
        'titolo',
        'descrizione',
        'ingredienti',
        'procedimento',
        'tempo_preparazione',
        'difficolta',
        'calorie',
        'categoria',
        'immagine',
        'note',
        'ordine_pubblicazione',
        'data_pubblicazione',
        'pubblicata',
        'visibile_programmi', // JSON array di ID programmi
    ];

    protected $casts = [
        'data_pubblicazione' => 'date',
        'pubblicata' => 'boolean',
        'visibile_programmi' => 'array',
        'tempo_preparazione' => 'integer',
        'calorie' => 'integer',
    ];

    /**
     * Relazione: Programmi che vedono questa ricetta
     */
    public function programmi()
    {
        return $this->belongsToMany(Programma::class, 'programma_ricetta')
                    ->withTimestamps();
    }

    /**
     * Scope: Solo ricette pubblicate
     */
    public function scopePubblicate($query)
    {
        return $query->where('pubblicata', true)
                     ->whereNotNull('data_pubblicazione')
                     ->where('data_pubblicazione', '<=', now());
    }

    /**
     * Scope: Da pubblicare (future)
     */
    public function scopeDaPubblicare($query)
    {
        return $query->where('pubblicata', false)
                     ->orWhere(function($q) {
                         $q->where('data_pubblicazione', '>', now());
                     });
    }

    /**
     * Scope: Per categoria
     */
    public function scopePerCategoria($query, $categoria)
    {
        return $query->where('categoria', $categoria);
    }

    /**
     * Scope: Ordinate per pubblicazione
     */
    public function scopeOrdineDistribuzione($query)
    {
        return $query->orderBy('ordine_pubblicazione', 'asc')
                     ->orderBy('created_at', 'asc');
    }

    /**
     * Verifica se la ricetta è visibile per un programma specifico
     */
    public function isVisibilePerProgramma($programma_id)
    {
        if (!$this->visibile_programmi || empty($this->visibile_programmi)) {
            return true; // Visibile a tutti se non specificato
        }

        return in_array($programma_id, $this->visibile_programmi);
    }

    /**
     * Categorie ricette
     */
    public static function getCategorie()
    {
        return [
            'colazione' => 'Colazione',
            'pranzo' => 'Pranzo',
            'cena' => 'Cena',
            'spuntino' => 'Spuntino',
            'dolce' => 'Dolce',
            'snack_salutare' => 'Snack Salutare',
            'vegetariana' => 'Vegetariana',
            'vegana' => 'Vegana',
            'proteica' => 'Proteica',
            'detox' => 'Detox',
        ];
    }

    /**
     * Livelli difficoltà
     */
    public static function getDifficolta()
    {
        return [
            'facile' => 'Facile',
            'media' => 'Media',
            'difficile' => 'Difficile',
        ];
    }
}
