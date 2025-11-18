<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model: Notifica
 *
 * Rappresenta una notifica per un utente
 * Supporta vari tipi: lezioni, messaggi, pagamenti, etc.
 */
class Notifica extends Model
{
    use HasFactory;

    protected $table = 'notifiche';

    protected $fillable = [
        'utente_id',
        'tipo',
        'titolo',
        'corpo',
        'icona',
        'colore',
        'link_url',
        'dati_extra',
        'letta',
        'letta_at',
    ];

    protected $casts = [
        'letta' => 'boolean',
        'letta_at' => 'datetime',
        'dati_extra' => 'array',
    ];

    /**
     * Destinatario della notifica
     */
    public function utente(): BelongsTo
    {
        return $this->belongsTo(Utente::class);
    }

    /**
     * Scope: Solo notifiche non lette
     */
    public function scopeNonLette($query)
    {
        return $query->where('letta', false);
    }

    /**
     * Scope: Solo notifiche lette
     */
    public function scopeLette($query)
    {
        return $query->where('letta', true);
    }

    /**
     * Scope: Per tipo specifico
     */
    public function scopePerTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    /**
     * Scope: Delle ultime 24 ore
     */
    public function scopeRecenti($query)
    {
        return $query->where('created_at', '>=', now()->subDay());
    }

    /**
     * Segna come letta
     */
    public function segnaComeLetta()
    {
        if (!$this->letta) {
            $this->update([
                'letta' => true,
                'letta_at' => now(),
            ]);
        }
    }

    /**
     * Crea notifica per utente
     */
    public static function creaPerUtente($utenteId, $tipo, $titolo, $corpo = null, $options = [])
    {
        return static::create(array_merge([
            'utente_id' => $utenteId,
            'tipo' => $tipo,
            'titolo' => $titolo,
            'corpo' => $corpo,
        ], $options));
    }

    /**
     * Crea notifica nuova lezione
     */
    public static function nuovaLezione($utenteId, $lezione)
    {
        return static::creaPerUtente(
            $utenteId,
            'lezione',
            'Nuova Lezione Prenotata',
            "Hai una lezione di {$lezione->tipologia} il {$lezione->data_ora->format('d/m/Y')} alle {$lezione->data_ora->format('H:i')}",
            [
                'icona' => 'fa-calendar',
                'colore' => 'blue',
                'link_url' => route('admin.lezioni.show', $lezione->id),
                'dati_extra' => ['lezione_id' => $lezione->id],
            ]
        );
    }

    /**
     * Crea notifica nuovo messaggio
     */
    public static function nuovoMessaggio($utenteId, $mittente, $conversazione)
    {
        return static::creaPerUtente(
            $utenteId,
            'messaggio',
            'Nuovo Messaggio',
            "{$mittente->nome} {$mittente->cognome} ti ha inviato un messaggio",
            [
                'icona' => 'fa-envelope',
                'colore' => 'green',
                'link_url' => route('messaging.show', $conversazione->id),
                'dati_extra' => [
                    'conversazione_id' => $conversazione->id,
                    'mittente_id' => $mittente->id,
                ],
            ]
        );
    }

    /**
     * Crea notifica pagamento
     */
    public static function pagamentoRicevuto($utenteId, $importo)
    {
        return static::creaPerUtente(
            $utenteId,
            'pagamento',
            'Pagamento Ricevuto',
            "Hai ricevuto un pagamento di €" . number_format($importo, 2, ',', '.'),
            [
                'icona' => 'fa-euro-sign',
                'colore' => 'green',
                'link_url' => route('professionista.compensi.index'),
            ]
        );
    }
}
