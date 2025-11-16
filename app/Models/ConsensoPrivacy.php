<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConsensoPrivacy extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'consensi_privacy';

    protected $fillable = [
        'cliente_id',
        'tipo_consenso',
        'consenso_dato',
        'data_consenso',
        'data_revoca',
        'ip_address',
        'user_agent',
        'stato',
        'note',
        'versione_policy'
    ];

    protected $casts = [
        'consenso_dato' => 'boolean',
        'data_consenso' => 'datetime',
        'data_revoca' => 'datetime',
    ];

    /**
     * Relazione con Cliente
     */
    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    /**
     * Scope per consensi attivi
     */
    public function scopeAttivi($query)
    {
        return $query->where('stato', 'attivo')->where('consenso_dato', true);
    }

    /**
     * Scope per tipo di consenso
     */
    public function scopeTipo($query, string $tipo)
    {
        return $query->where('tipo_consenso', $tipo);
    }

    /**
     * Verifica se un consenso specifico è stato dato
     */
    public static function haConsenso(int $clienteId, string $tipo): bool
    {
        return self::where('cliente_id', $clienteId)
            ->where('tipo_consenso', $tipo)
            ->where('consenso_dato', true)
            ->where('stato', 'attivo')
            ->exists();
    }

    /**
     * Registra un nuovo consenso
     */
    public static function registraConsenso(int $clienteId, string $tipo, bool $consenso, ?string $versione = null): self
    {
        // Revoca eventuali consensi precedenti dello stesso tipo
        self::where('cliente_id', $clienteId)
            ->where('tipo_consenso', $tipo)
            ->where('stato', 'attivo')
            ->update([
                'stato' => 'modificato',
                'data_revoca' => now()
            ]);

        // Crea nuovo consenso
        return self::create([
            'cliente_id' => $clienteId,
            'tipo_consenso' => $tipo,
            'consenso_dato' => $consenso,
            'data_consenso' => now(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'stato' => 'attivo',
            'versione_policy' => $versione ?? config('app.privacy_version', '1.0')
        ]);
    }

    /**
     * Revoca un consenso
     */
    public function revoca(?string $motivazione = null): bool
    {
        return $this->update([
            'stato' => 'revocato',
            'data_revoca' => now(),
            'note' => $motivazione
        ]);
    }

    /**
     * Verifica se il consenso è scaduto (più di 2 anni)
     */
    public function isScaduto(): bool
    {
        return $this->data_consenso && $this->data_consenso->diffInYears(now()) >= 2;
    }

    /**
     * Etichetta tipo consenso in italiano
     */
    public function getTipoConsensoLabelAttribute(): string
    {
        $labels = [
            'privacy_policy' => 'Privacy Policy',
            'trattamento_dati' => 'Trattamento Dati Personali',
            'marketing' => 'Marketing e Newsletter',
            'profilazione' => 'Profilazione',
            'cookie_tecnici' => 'Cookie Tecnici',
            'cookie_analytics' => 'Cookie Analytics',
            'cookie_marketing' => 'Cookie Marketing',
            'terze_parti' => 'Condivisione con Terze Parti'
        ];

        return $labels[$this->tipo_consenso] ?? $this->tipo_consenso;
    }

    /**
     * Badge colore stato
     */
    public function getStatoBadgeAttribute(): string
    {
        $badges = [
            'attivo' => 'success',
            'revocato' => 'danger',
            'modificato' => 'warning',
            'scaduto' => 'secondary'
        ];

        return $badges[$this->stato] ?? 'secondary';
    }
}
