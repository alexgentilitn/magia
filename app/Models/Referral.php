<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Referral extends Model
{
    use HasFactory;

    protected $fillable = [
        'cliente_invitante_id',
        'cliente_invitato_id',
        'email_invitato',
        'codice_invito',
        'stato',
        'data_invito',
        'data_registrazione',
        'data_conversione',
        'sconto_invitante',
        'sconto_invitato',
        'sconto_applicato',
        'note'
    ];

    protected $casts = [
        'data_invito' => 'datetime',
        'data_registrazione' => 'datetime',
        'data_conversione' => 'datetime',
        'sconto_invitante' => 'decimal:2',
        'sconto_invitato' => 'decimal:2',
        'sconto_applicato' => 'boolean',
    ];

    /**
     * Relazioni
     */
    public function clienteInvitante()
    {
        return $this->belongsTo(Cliente::class, 'cliente_invitante_id');
    }

    public function clienteInvitato()
    {
        return $this->belongsTo(Cliente::class, 'cliente_invitato_id');
    }

    /**
     * Genera codice invito univoco
     */
    public static function generaCodiceInvito(): string
    {
        do {
            $codice = 'MAGIA-' . strtoupper(Str::random(8));
        } while (self::where('codice_invito', $codice)->exists());

        return $codice;
    }

    /**
     * Crea nuovo referral
     */
    public static function creaReferral(int $clienteId, string $emailInvitato, float $scontoInvitante = 10, float $scontoInvitato = 15): self
    {
        return self::create([
            'cliente_invitante_id' => $clienteId,
            'email_invitato' => $emailInvitato,
            'codice_invito' => self::generaCodiceInvito(),
            'stato' => 'pending',
            'data_invito' => now(),
            'sconto_invitante' => $scontoInvitante,
            'sconto_invitato' => $scontoInvitato,
        ]);
    }

    /**
     * Trova referral per codice
     */
    public static function findByCodice(string $codice): ?self
    {
        return self::where('codice_invito', $codice)->first();
    }

    /**
     * Marca come registrato
     */
    public function marcaRegistrato(int $clienteInvitatoId): bool
    {
        return $this->update([
            'cliente_invitato_id' => $clienteInvitatoId,
            'stato' => 'registrato',
            'data_registrazione' => now()
        ]);
    }

    /**
     * Marca come convertito (ha pagato)
     */
    public function marcaConvertito(): bool
    {
        return $this->update([
            'stato' => 'convertito',
            'data_conversione' => now(),
            'sconto_applicato' => true
        ]);
    }

    /**
     * Scopes
     */
    public function scopePending($query)
    {
        return $query->where('stato', 'pending');
    }

    public function scopeConvertiti($query)
    {
        return $query->where('stato', 'convertito');
    }
}
