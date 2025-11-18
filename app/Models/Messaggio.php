<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model: Messaggio
 *
 * Rappresenta un singolo messaggio in una conversazione
 * Supporta testo, immagini e allegati
 */
class Messaggio extends Model
{
    use HasFactory;

    protected $table = 'messaggi';

    protected $fillable = [
        'conversazione_id',
        'mittente_id',
        'corpo',
        'tipo',
        'allegato_path',
        'allegato_nome',
        'letto',
        'letto_at',
    ];

    protected $casts = [
        'letto' => 'boolean',
        'letto_at' => 'datetime',
    ];

    /**
     * Conversazione a cui appartiene il messaggio
     */
    public function conversazione(): BelongsTo
    {
        return $this->belongsTo(Conversazione::class);
    }

    /**
     * Mittente del messaggio
     */
    public function mittente(): BelongsTo
    {
        return $this->belongsTo(Utente::class, 'mittente_id');
    }

    /**
     * Scope: Solo messaggi non letti
     */
    public function scopeNonLetti($query)
    {
        return $query->where('letto', false);
    }

    /**
     * Scope: Solo messaggi di oggi
     */
    public function scopeOggi($query)
    {
        return $query->whereDate('created_at', today());
    }

    /**
     * Segna come letto
     */
    public function segnaComeLetto()
    {
        if (!$this->letto) {
            $this->update([
                'letto' => true,
                'letto_at' => now(),
            ]);
        }
    }

    /**
     * Verifica se ha allegato
     */
    public function hasAllegato(): bool
    {
        return !empty($this->allegato_path);
    }

    /**
     * URL allegato completo
     */
    public function allegatoUrl(): ?string
    {
        if (!$this->hasAllegato()) {
            return null;
        }

        return asset('storage/' . $this->allegato_path);
    }
}
