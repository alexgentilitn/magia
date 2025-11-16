<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Model: Professionista
 * Gestisce i dettagli dei professionisti (istruttori, personal trainer, nutrizionisti)
 */
class Professionista extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'professionisti';

    protected $fillable = [
        'utente_id',
        'nome',
        'cognome',
        'codice_fiscale',
        'data_nascita',
        'sesso',
        'titolo_professionale',
        'specializzazioni',
        'bio',
        'anni_esperienza',
        'partita_iva',
        'certificazioni',
        'qualifiche',
        'telefono_mobile',
        // 'email', // Email è solo nella tabella utenti, non in professionisti
        'indirizzo',
        'citta',
        'provincia',
        'cap',
        'tariffa_oraria',
        'tariffa_lezione_gruppo',
        'tariffa_lezione_privata',
        'disponibilita_settimanale',
        'disponibile_da',
        'disponibile_fino',
        'foto_profilo',
        'galleria_foto',
        'video_presentazione',
        'sito_web',
        'instagram',
        'facebook',
        'linkedin',
        'tiktok',
        'lingue_parlate',
        'tipo_contratto',
        'data_assunzione',
        'note_interne',
        'visibile_pubblico',
        'stato',
        'codice_professionista',
        'valutazione_media',
        'numero_recensioni',
        'lezioni_completate',
        'clienti_seguiti',
    ];

    protected $casts = [
        'specializzazioni' => 'array',
        'certificazioni' => 'array',
        'qualifiche' => 'array',
        'disponibilita_settimanale' => 'array',
        'galleria_foto' => 'array',
        'lingue_parlate' => 'array',
        'data_nascita' => 'date',
        'disponibile_da' => 'date',
        'disponibile_fino' => 'date',
        'data_assunzione' => 'date',
        'tariffa_oraria' => 'decimal:2',
        'tariffa_lezione_gruppo' => 'decimal:2',
        'tariffa_lezione_privata' => 'decimal:2',
        'valutazione_media' => 'decimal:2',
        'visibile_pubblico' => 'boolean',
        'anni_esperienza' => 'integer',
        'numero_recensioni' => 'integer',
        'lezioni_completate' => 'integer',
        'clienti_seguiti' => 'integer',
    ];

    /**
     * Relazione: Professionista appartiene a un Utente
     */
    public function utente()
    {
        return $this->belongsTo(Utente::class, 'utente_id');
    }

    /**
     * Relazione: Professionista ha molte Lezioni
     */
    public function lezioni()
    {
        return $this->hasMany(Lezione::class, 'professionista_id', 'utente_id');
    }

    /**
     * Relazione: Professionista ha molti Programmi
     */
    public function programmi()
    {
        return $this->hasMany(Programma::class, 'professionista_id', 'utente_id');
    }

    /**
     * Accessor: Email dal relazione utente
     * Permette di usare $professionista->email invece di $professionista->utente->email
     */
    public function getEmailAttribute()
    {
        return $this->utente?->email;
    }

    /**
     * Scope: Solo professionisti attivi
     */
    public function scopeAttivi($query)
    {
        return $query->where('stato', 'attivo');
    }

    /**
     * Scope: Solo professionisti visibili al pubblico
     */
    public function scopeVisibili($query)
    {
        return $query->where('visibile_pubblico', true)->where('stato', 'attivo');
    }

    /**
     * Accessor: Nome completo
     */
    public function getNomeCompletoAttribute()
    {
        return "{$this->nome} {$this->cognome}";
    }

    /**
     * Accessor: Badge stato con colore
     */
    public function getBadgeStatoAttribute()
    {
        return match($this->stato) {
            'attivo' => 'bg-green-100 text-green-800',
            'sospeso' => 'bg-yellow-100 text-yellow-800',
            'inattivo' => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * Helper: Età del professionista
     */
    public function getEta()
    {
        return $this->data_nascita ? $this->data_nascita->age : null;
    }

    /**
     * Helper: Verifica se ha certificazioni valide
     */
    public function hasCertificazioniValide()
    {
        if (!$this->certificazioni) {
            return false;
        }

        $oggi = now();
        foreach ($this->certificazioni as $cert) {
            if (isset($cert['scadenza'])) {
                $scadenza = \Carbon\Carbon::parse($cert['scadenza']);
                if ($scadenza->lt($oggi)) {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * Helper: Certificazioni in scadenza (entro 30 giorni)
     */
    public function getCertificazioniInScadenza()
    {
        if (!$this->certificazioni) {
            return collect();
        }

        $oggi = now();
        $limite = now()->addDays(30);

        return collect($this->certificazioni)->filter(function($cert) use ($oggi, $limite) {
            if (!isset($cert['scadenza'])) {
                return false;
            }
            $scadenza = \Carbon\Carbon::parse($cert['scadenza']);
            return $scadenza->between($oggi, $limite);
        });
    }

    /**
     * Helper: Genera codice professionista univoco
     */
    public static function generaCodiceProfessionista()
    {
        $anno = now()->year;

        // Trova l'ultimo numero utilizzato per l'anno corrente
        $ultimoCodice = static::where('codice_professionista', 'like', "PROF-{$anno}-%")
            ->orderByRaw('CAST(SUBSTRING(codice_professionista, -4) AS UNSIGNED) DESC')
            ->value('codice_professionista');

        if ($ultimoCodice) {
            // Estrai il numero dall'ultimo codice e incrementalo
            preg_match('/PROF-\d{4}-(\d{4})/', $ultimoCodice, $matches);
            $ultimoNumero = isset($matches[1]) ? (int)$matches[1] : 0;
            $nuovoNumero = $ultimoNumero + 1;
        } else {
            // Primo professionista dell'anno
            $nuovoNumero = 1;
        }

        // Genera il codice e verifica che non esista già (sicurezza extra)
        do {
            $codice = sprintf('PROF-%d-%04d', $anno, $nuovoNumero);
            $esiste = static::where('codice_professionista', $codice)->exists();
            if ($esiste) {
                $nuovoNumero++;
            }
        } while ($esiste);

        return $codice;
    }

    /**
     * Accessor: URL foto profilo o placeholder
     */
    public function getFotoProfiloUrlAttribute()
    {
        if ($this->foto_profilo) {
            return asset('storage/' . $this->foto_profilo);
        }
        // Placeholder generico
        return asset('images/placeholder-avatar.png');
    }

    /**
     * Helper: Verifica disponibilità in un giorno specifico
     */
    public function isDisponibileGiorno($giorno)
    {
        if (!$this->disponibilita_settimanale) {
            return false;
        }

        $giornoLower = strtolower($giorno);
        return isset($this->disponibilita_settimanale[$giornoLower]) &&
               $this->disponibilita_settimanale[$giornoLower]['disponibile'] ?? false;
    }
}
