<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

/**
 * Model: Documento
 * Funzione: Gestisce upload e archiviazione documenti clienti
 *
 * Tipi documento supportati:
 * - certificato_medico
 * - documento_identita
 * - consenso_privacy
 * - consenso_trattamento
 * - altro
 */
class Documento extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'documenti';

    protected $fillable = [
        'cliente_id',
        'caricato_da_utente_id',
        'tipo_documento',
        'nome_file',
        'nome_originale',
        'path',
        'mime_type',
        'dimensione',
        'descrizione',
        'data_scadenza',
        'stato',
        'note',
        'visibile_cliente',
    ];

    protected $casts = [
        'data_scadenza' => 'date',
        'visibile_cliente' => 'boolean',
        'dimensione' => 'integer',
    ];

    /**
     * Relazione: Documento appartiene a un Cliente
     */
    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    /**
     * Relazione: Documento caricato da Utente (admin o cliente stesso)
     */
    public function caricatoDa()
    {
        return $this->belongsTo(Utente::class, 'caricato_da_utente_id');
    }

    /**
     * Scope: Solo documenti validi (non scaduti)
     */
    public function scopeValidi($query)
    {
        return $query->where(function($q) {
            $q->whereNull('data_scadenza')
              ->orWhere('data_scadenza', '>', now());
        });
    }

    /**
     * Scope: Documenti scaduti
     */
    public function scopeScaduti($query)
    {
        return $query->whereNotNull('data_scadenza')
                     ->where('data_scadenza', '<=', now());
    }

    /**
     * Scope: Documenti in scadenza (prossimi 30 giorni)
     */
    public function scopeInScadenza($query, $giorni = 30)
    {
        return $query->whereNotNull('data_scadenza')
                     ->whereBetween('data_scadenza', [now(), now()->addDays($giorni)]);
    }

    /**
     * Scope: Per tipo documento
     */
    public function scopePerTipo($query, $tipo)
    {
        return $query->where('tipo_documento', $tipo);
    }

    /**
     * Ottiene l'URL completo del documento
     */
    public function getUrlAttribute()
    {
        return Storage::url($this->path);
    }

    /**
     * Verifica se il documento è scaduto
     */
    public function isScaduto()
    {
        if (!$this->data_scadenza) {
            return false;
        }

        return $this->data_scadenza < now();
    }

    /**
     * Verifica se il documento è in scadenza (prossimi 30 giorni)
     */
    public function isInScadenza($giorni = 30)
    {
        if (!$this->data_scadenza) {
            return false;
        }

        return $this->data_scadenza <= now()->addDays($giorni) && !$this->isScaduto();
    }

    /**
     * Ottiene dimensione formattata human-readable
     */
    public function getDimensioneFormattataAttribute()
    {
        $bytes = $this->dimensione;

        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }

    /**
     * Ottiene icona in base al tipo MIME
     */
    public function getIconaAttribute()
    {
        $mime = $this->mime_type;

        if (str_contains($mime, 'pdf')) return 'fa-file-pdf text-red-600';
        if (str_contains($mime, 'word')) return 'fa-file-word text-blue-600';
        if (str_contains($mime, 'image')) return 'fa-file-image text-green-600';
        if (str_contains($mime, 'excel') || str_contains($mime, 'spreadsheet')) return 'fa-file-excel text-green-700';

        return 'fa-file text-gray-600';
    }

    /**
     * Elimina il file fisico quando viene eliminato il record
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($documento) {
            // Elimina il file fisico se esiste
            if ($documento->path && Storage::exists($documento->path)) {
                Storage::delete($documento->path);
            }
        });
    }

    /**
     * Tipi di documento consentiti
     */
    public static function getTipiDocumento()
    {
        return [
            'certificato_medico' => 'Certificato Medico',
            'documento_identita' => 'Documento di Identità',
            'consenso_privacy' => 'Consenso Privacy',
            'consenso_trattamento' => 'Consenso Trattamento Dati',
            'consenso_foto' => 'Consenso Uso Immagini',
            'prescrizione_medica' => 'Prescrizione Medica',
            'referto_medico' => 'Referto Medico',
            'contratto' => 'Contratto',
            'ricevuta_pagamento' => 'Ricevuta Pagamento',
            'altro' => 'Altro',
        ];
    }

    /**
     * Stati documento
     */
    public static function getStati()
    {
        return [
            'valido' => 'Valido',
            'scaduto' => 'Scaduto',
            'in_verifica' => 'In Verifica',
            'rifiutato' => 'Rifiutato',
        ];
    }
}
