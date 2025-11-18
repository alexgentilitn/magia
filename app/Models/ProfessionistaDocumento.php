<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model: Documento Professionista
 *
 * Gestisce documenti e allegati del professionista:
 * - Certificati e attestati
 * - Curriculum Vitae
 * - Carta d'identità / Documenti
 * - Altri allegati
 */
class ProfessionistaDocumento extends Model
{
    protected $table = 'professionista_documenti';

    protected $fillable = [
        'professionista_id',
        'nome_file',
        'path',
        'tipo_documento',
        'mime_type',
        'dimensione',
        'descrizione',
        'data_scadenza',
        'scaduto',
        'verificato',
        'verificato_at',
        'verificato_da',
    ];

    protected $casts = [
        'data_scadenza' => 'date',
        'scaduto' => 'boolean',
        'verificato' => 'boolean',
        'verificato_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relazione: Documento appartiene a Professionista
     */
    public function professionista(): BelongsTo
    {
        return $this->belongsTo(Professionista::class, 'professionista_id');
    }

    /**
     * Relazione: Documento verificato da Utente (Admin)
     */
    public function verificatoDa(): BelongsTo
    {
        return $this->belongsTo(Utente::class, 'verificato_da');
    }

    /**
     * Accessor: Badge tipo documento con colore
     */
    public function getBadgeTipoAttribute(): string
    {
        return match($this->tipo_documento) {
            'certificato' => 'bg-purple-100 text-purple-800',
            'cv' => 'bg-blue-100 text-blue-800',
            'carta_identita' => 'bg-green-100 text-green-800',
            'contratto' => 'bg-yellow-100 text-yellow-800',
            'altro' => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * Accessor: Nome tipo documento leggibile
     */
    public function getTipoDocumentoLeggibileAttribute(): string
    {
        return match($this->tipo_documento) {
            'certificato' => 'Certificato/Attestato',
            'cv' => 'Curriculum Vitae',
            'carta_identita' => 'Documento d\'Identità',
            'contratto' => 'Contratto',
            'altro' => 'Altro Documento',
            default => 'Documento',
        };
    }

    /**
     * Accessor: Dimensione formattata leggibile
     */
    public function getDimensioneFormattataAttribute(): string
    {
        if (!$this->dimensione) {
            return 'N/A';
        }

        $bytes = $this->dimensione;

        if ($bytes < 1024) {
            return $bytes . ' B';
        } elseif ($bytes < 1048576) {
            return round($bytes / 1024, 2) . ' KB';
        } else {
            return round($bytes / 1048576, 2) . ' MB';
        }
    }

    /**
     * Accessor: Icona Font Awesome per tipo file
     */
    public function getIconaFileAttribute(): string
    {
        $extension = pathinfo($this->nome_file, PATHINFO_EXTENSION);

        return match(strtolower($extension)) {
            'pdf' => 'fa-file-pdf text-red-600',
            'doc', 'docx' => 'fa-file-word text-blue-600',
            'xls', 'xlsx' => 'fa-file-excel text-green-600',
            'jpg', 'jpeg', 'png', 'gif' => 'fa-file-image text-purple-600',
            'zip', 'rar', '7z' => 'fa-file-archive text-orange-600',
            default => 'fa-file text-gray-600',
        };
    }

    /**
     * Scope: Solo documenti verificati
     */
    public function scopeVerificati($query)
    {
        return $query->where('verificato', true);
    }

    /**
     * Scope: Solo documenti non scaduti
     */
    public function scopeNonScaduti($query)
    {
        return $query->where('scaduto', false);
    }

    /**
     * Scope: Documenti in scadenza entro N giorni
     */
    public function scopeInScadenza($query, $giorni = 30)
    {
        return $query->whereNotNull('data_scadenza')
                     ->where('data_scadenza', '>=', now())
                     ->where('data_scadenza', '<=', now()->addDays($giorni))
                     ->where('scaduto', false);
    }

    /**
     * Helper: Verifica se il documento è scaduto
     */
    public function isScaduto(): bool
    {
        if (!$this->data_scadenza) {
            return false;
        }

        return $this->data_scadenza->isPast();
    }

    /**
     * Helper: Giorni alla scadenza
     */
    public function giorniAllaScadenza(): ?int
    {
        if (!$this->data_scadenza) {
            return null;
        }

        return now()->diffInDays($this->data_scadenza, false);
    }
}
