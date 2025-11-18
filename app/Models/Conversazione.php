<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model: Conversazione
 *
 * Rappresenta una conversazione chat tra utenti
 * Può essere privata (1-1) o di gruppo (più utenti)
 */
class Conversazione extends Model
{
    use HasFactory;

    protected $table = 'conversazioni';

    protected $fillable = [
        'titolo',
        'tipo',
        'ultimo_messaggio_at',
    ];

    protected $casts = [
        'ultimo_messaggio_at' => 'datetime',
    ];

    /**
     * Utenti partecipanti alla conversazione
     */
    public function utenti(): BelongsToMany
    {
        return $this->belongsToMany(Utente::class, 'conversazione_utente')
            ->withPivot(['ultimo_accesso', 'messaggi_non_letti'])
            ->withTimestamps();
    }

    /**
     * Messaggi della conversazione
     */
    public function messaggi(): HasMany
    {
        return $this->hasMany(Messaggio::class)->orderBy('created_at', 'asc');
    }

    /**
     * Ultimo messaggio
     */
    public function ultimoMessaggio()
    {
        return $this->hasOne(Messaggio::class)->latestOfMany();
    }

    /**
     * Trova o crea conversazione privata tra due utenti
     */
    public static function trovaOCreaPrivata($utenteId1, $utenteId2)
    {
        // Cerca conversazione esistente tra questi due utenti
        $conversazione = static::whereHas('utenti', function ($query) use ($utenteId1) {
            $query->where('utente_id', $utenteId1);
        })->whereHas('utenti', function ($query) use ($utenteId2) {
            $query->where('utente_id', $utenteId2);
        })->where('tipo', 'privata')
            ->first();

        if ($conversazione) {
            return $conversazione;
        }

        // Crea nuova conversazione
        $conversazione = static::create([
            'tipo' => 'privata',
        ]);

        $conversazione->utenti()->attach([$utenteId1, $utenteId2]);

        return $conversazione;
    }

    /**
     * Segna come letta per un utente specifico
     */
    public function segnaComeLetta($utenteId)
    {
        $this->utenti()->updateExistingPivot($utenteId, [
            'ultimo_accesso' => now(),
            'messaggi_non_letti' => 0,
        ]);
    }

    /**
     * Incrementa contatore non letti per tutti eccetto mittente
     */
    public function incrementaNonLetti($mittenteId)
    {
        foreach ($this->utenti as $utente) {
            if ($utente->id !== $mittenteId) {
                $this->utenti()->updateExistingPivot($utente->id, [
                    'messaggi_non_letti' => \DB::raw('messaggi_non_letti + 1'),
                ]);
            }
        }
    }
}
