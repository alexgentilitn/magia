<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * Model: ImpostazioneSistema
 * Gestisce valori configurabili del sistema (tipologie, stati, ecc.)
 */
class ImpostazioneSistema extends Model
{
    use HasFactory;

    protected $table = 'impostazioni_sistema';

    protected $fillable = [
        'categoria',
        'chiave',
        'etichetta',
        'descrizione',
        'colore',
        'icona',
        'ordinamento',
        'attivo',
        'di_sistema',
    ];

    protected $casts = [
        'attivo' => 'boolean',
        'di_sistema' => 'boolean',
        'ordinamento' => 'integer',
    ];

    /**
     * Scope: Solo impostazioni attive
     */
    public function scopeAttive($query)
    {
        return $query->where('attivo', true);
    }

    /**
     * Scope: Per categoria
     */
    public function scopeCategoria($query, $categoria)
    {
        return $query->where('categoria', $categoria);
    }

    /**
     * Scope: Ordinate
     */
    public function scopeOrdinate($query)
    {
        return $query->orderBy('ordinamento')->orderBy('etichetta');
    }

    /**
     * Helper: Ottieni tutte le impostazioni di una categoria (con cache)
     */
    public static function perCategoria($categoria, $soloAttive = true)
    {
        $cacheKey = "impostazioni_{$categoria}_" . ($soloAttive ? 'attive' : 'tutte');

        return Cache::remember($cacheKey, 3600, function () use ($categoria, $soloAttive) {
            $query = self::categoria($categoria)->ordinate();

            if ($soloAttive) {
                $query->attive();
            }

            return $query->get();
        });
    }

    /**
     * Helper: Ottieni array chiave => etichetta per select
     */
    public static function arraySelect($categoria, $soloAttive = true)
    {
        return self::perCategoria($categoria, $soloAttive)->pluck('etichetta', 'chiave')->toArray();
    }

    /**
     * Helper: Pulisci cache
     */
    public static function pulisciCache($categoria = null)
    {
        if ($categoria) {
            Cache::forget("impostazioni_{$categoria}_attive");
            Cache::forget("impostazioni_{$categoria}_tutte");
        } else {
            Cache::flush();
        }
    }

    /**
     * Event: Pulisci cache quando si salva/elimina
     */
    protected static function boot()
    {
        parent::boot();

        static::saved(function ($impostazione) {
            self::pulisciCache($impostazione->categoria);
        });

        static::deleted(function ($impostazione) {
            self::pulisciCache($impostazione->categoria);
        });
    }
}
