<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model: LogAttivita
 * Funzione: Traccia tutte le azioni degli amministratori
 */
class LogAttivita extends Model
{
    protected $table = 'log_attivita';

    protected $fillable = [
        'utente_id',
        'azione',
        'descrizione',
        'ip_address',
        'user_agent',
        'dati_modificati',
    ];

    protected $casts = [
        'dati_modificati' => 'array',
    ];

    public function utente()
    {
        return $this->belongsTo(Utente::class);
    }

    public static function registra($azione, $descrizione, $dati = [])
    {
        self::create([
            'utente_id' => auth()->id(),
            'azione' => $azione,
            'descrizione' => $descrizione,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'dati_modificati' => $dati,
        ]);
    }
}
