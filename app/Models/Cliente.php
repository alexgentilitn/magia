<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

/**
 * Model: Cliente
 * Funzione: Gestisce l'anagrafica completa delle clienti
 */
class Cliente extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'clienti';

    protected $fillable = [
        'utente_id',
        'nome',
        'cognome',
        'codice_fiscale',
        'indirizzo_via',
        'indirizzo_citta',
        'indirizzo_provincia',
        'indirizzo_cap',
        'indirizzo_nazione',
        'telefono_fisso',
        'telefono_mobile',
        'email',
        'pec',
        'data_nascita',
        'sesso',
        'luogo_nascita',
        'peso_iniziale',
        'altezza',
        'circonferenza_vita',
        'circonferenza_fianchi',
        'circonferenza_braccia',
        'circonferenza_cosce',
        'massa_grassa',
        'massa_magra',
        'acqua_corporea',
        'metabolismo_basale',
        'obiettivi_personali',
        'note_mediche',
        'preferenze_alimentari',
        'farmaci_assunti',
        'certificato_medico_valido',
        'certificato_scadenza',
        'programma_attuale',
        'data_iscrizione',
        'inizio_programma',
        'fine_programma',
        'stato_programma',
        'dati_personalizzati',
        'gruppo_whatsapp',
        'sede_preferita_id',
        'consenso_privacy',
        'consenso_privacy_data',
        'consenso_marketing',
        'consenso_foto',
        'note_interne',
        'stato_cliente',
        'tipo_cliente', // 🆕 prova | effettiva
        'codice_cliente',
        'codice_referral',
        'invitato_da_cliente_id',
    ];

    protected $casts = [
        'data_nascita' => 'date',
        'data_iscrizione' => 'date',
        'inizio_programma' => 'date',
        'fine_programma' => 'date',
        'certificato_scadenza' => 'date',
        'certificato_medico_valido' => 'boolean',
        'consenso_privacy' => 'boolean',
        'consenso_marketing' => 'boolean',
        'consenso_foto' => 'boolean',
        'consenso_privacy_data' => 'datetime',
        'dati_personalizzati' => 'array',
        'peso_iniziale' => 'decimal:2',
        'altezza' => 'decimal:2',
        'circonferenza_vita' => 'decimal:2',
        'circonferenza_fianchi' => 'decimal:2',
        'circonferenza_braccia' => 'decimal:2',
        'circonferenza_cosce' => 'decimal:2',
        'massa_grassa' => 'decimal:2',
        'massa_magra' => 'decimal:2',
        'acqua_corporea' => 'decimal:2',
        'metabolismo_basale' => 'decimal:2',
    ];

    /**
     * Relazione: Cliente appartiene a un Utente
     */
    public function utente()
    {
        return $this->belongsTo(Utente::class, 'utente_id');
    }

    /**
     * Relazione: Sede preferita del cliente
     */
    public function sedePreferita()
    {
        return $this->belongsTo(Sede::class, 'sede_preferita_id');
    }

    /**
     * Relazione: Cliente invitato da altro Cliente (Referral)
     */
    public function invitatoDa()
    {
        return $this->belongsTo(Cliente::class, 'invitato_da_cliente_id');
    }

    /**
     * Relazione: Clienti invitati da questo Cliente
     */
    public function amicheInvitate()
    {
        return $this->hasMany(Cliente::class, 'invitato_da_cliente_id');
    }

    /**
     * Relazione: Documenti del Cliente
     */
    public function documenti()
    {
        return $this->hasMany(Documento::class);
    }

    /**
     * Relazione: Pagamenti del Cliente
     */
    public function pagamenti()
    {
        return $this->hasMany(Pagamento::class);
    }

    /**
     * Relazione: Programmi iscritti
     */
    public function programmi()
    {
        return $this->belongsToMany(Programma::class, 'cliente_programma')
                    ->withPivot('data_iscrizione', 'data_scadenza', 'stato')
                    ->withTimestamps();
    }

    /**
     * Relazione: Lezioni prenotate
     * NOTA: cliente_lezione.cliente_id riferisce utenti.id, non clienti.id
     * Questa relazione funziona solo se clienti.id == utenti.id (1:1)
     */
    public function lezioni()
    {
        return $this->belongsToMany(Lezione::class, 'cliente_lezione')
                    ->withPivot('stato', 'data_prenotazione', 'check_in', 'check_out', 'valutazione', 'feedback')
                    ->withTimestamps();
    }

    /**
     * Relazione: Storico parametri corporei
     */
    public function parametriCorporei()
    {
        return $this->hasMany(ParametroCorporeo::class)->orderBy('data_rilevazione', 'desc');
    }

    /**
     * Relazione: Ultimo parametro corporeo rilevato
     */
    public function ultimoParametro()
    {
        return $this->hasOne(ParametroCorporeo::class)->latestOfMany('data_rilevazione');
    }

    /**
     * Scope: Solo clienti attivi
     */
    public function scopeAttivi($query)
    {
        return $query->where('stato_cliente', 'attivo');
    }

    /**
     * Scope: Clienti per programma
     */
    public function scopePerProgramma($query, $programma)
    {
        return $query->where('programma_attuale', $programma)
                     ->where('stato_programma', 'attivo');
    }

    /**
     * Scope: Clienti con certificato medico scaduto
     */
    public function scopeCertificatoScaduto($query)
    {
        return $query->where('certificato_medico_valido', true)
                     ->where('certificato_scadenza', '<=', now()->addDays(30));
    }

    /**
     * Calcola l'età della cliente
     */
    public function getEtaAttribute()
    {
        if (!$this->data_nascita) {
            return null;
        }

        return Carbon::parse($this->data_nascita)->age;
    }

    /**
     * Ottiene il nome completo della cliente
     */
    public function getNomeCompletoAttribute()
    {
        return "{$this->nome} {$this->cognome}";
    }

    /**
     * Calcola l'IMC (Indice Massa Corporea) - ACCESSOR
     */
    public function getImcAttribute()
    {
        if (!$this->peso_iniziale || !$this->altezza) {
            return null;
        }

        $altezza_metri = $this->altezza / 100;
        $imc = $this->peso_iniziale / ($altezza_metri * $altezza_metri);

        return round($imc, 2);
    }

    /**
     * Calcola l'IMC (Indice Massa Corporea) - METODO
     * Funzione: Stesso calcolo dell'accessor ma come metodo chiamabile
     */
    public function calcolaImc()
    {
        if (!$this->peso_iniziale || !$this->altezza) {
            return null;
        }

        $altezza_metri = $this->altezza / 100;
        $imc = $this->peso_iniziale / ($altezza_metri * $altezza_metri);

        return round($imc, 2);
    }

    /**
     * Ottiene la categoria IMC
     */
    public function getCategoriaImcAttribute()
    {
        $imc = $this->imc;

        if (!$imc) {
            return 'Non disponibile';
        }

        if ($imc < 18.5) return 'Sottopeso';
        if ($imc < 25) return 'Normopeso';
        if ($imc < 30) return 'Sovrappeso';
        if ($imc < 35) return 'Obesità I grado';
        if ($imc < 40) return 'Obesità II grado';
        
        return 'Obesità III grado';
    }

    /**
     * Verifica se la cliente ha il consenso privacy
     */
    public function haConsensoPrivacy()
    {
        return $this->consenso_privacy;
    }

    /**
     * Verifica se il certificato medico è valido
     */
    public function haCertificatoValido()
    {
        if (!$this->certificato_medico_valido) {
            return false;
        }

        if (!$this->certificato_scadenza) {
            return false;
        }

        return $this->certificato_scadenza > now();
    }

    /**
     * Genera codice cliente univoco
     */
    public static function generaCodiceCliente()
    {
        $ultimo_cliente = self::orderBy('id', 'desc')->first();
        $prossimo_numero = $ultimo_cliente ? $ultimo_cliente->id + 1 : 1;
        
        return 'CL' . str_pad($prossimo_numero, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Genera codice referral univoco
     */
    public static function generaCodiceReferral()
    {
        do {
            $codice = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 8));
        } while (self::where('codice_referral', $codice)->exists());
        
        return $codice;
    }

    /**
     * Event: Eseguito prima di creare un nuovo cliente
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($cliente) {
            if (!$cliente->codice_cliente) {
                $cliente->codice_cliente = self::generaCodiceCliente();
            }
            
            if (!$cliente->codice_referral) {
                $cliente->codice_referral = self::generaCodiceReferral();
            }

            if (!$cliente->data_iscrizione) {
                $cliente->data_iscrizione = now();
            }
        });
    }
}