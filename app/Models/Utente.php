<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Model: Utente
 * 
 * Funzione: Gestisce gli utenti del sistema (amministratori, professionisti, clienti)
 * Tabella: utenti
 * 
 * Relazioni:
 * - Appartiene a: Ruolo (ruolo_id)
 * - Ha uno: Cliente (se tipo_utente = 'cliente')
 * - Ha uno: Professionista (se tipo_utente = 'professionista')
 * 
 * Funzionalità:
 * - Autenticazione Laravel (login/logout/remember)
 * - Hash automatico password
 * - Verifica permessi tramite ruolo
 * - Soft delete per non perdere dati storici
 */
class Utente extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * Nome della tabella nel database
     */
    protected $table = 'utenti';

    /**
     * Campi che possono essere assegnati in massa
     */
    protected $fillable = [
        'email',
        'password',
        'nome',
        'cognome',
        'telefono',
        'tipo_utente',
        'ruolo_id',
        'attivo',
        'email_verificata',
        'email_verificata_il',
        'ultimo_accesso',
        'ultimo_ip',
    ];

    /**
     * Campi nascosti quando il model viene serializzato (JSON)
     * La password non deve mai essere visibile nelle API
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Cast automatico dei campi
     * 
     * Funzione: Converte automaticamente i campi nei tipi corretti
     */
    protected $casts = [
        'email_verificata' => 'boolean',
        'attivo' => 'boolean',
        'email_verificata_il' => 'datetime',
        'ultimo_accesso' => 'datetime',
        'email_verified_at' => 'datetime',
        'password' => 'hashed', // Laravel 10+ cripta automaticamente
    ];

    /**
     * Relazione: Utente appartiene a un Ruolo
     * 
     * Funzione: Collega l'utente al suo ruolo (Super Admin, Moderatore, ecc.)
     * Ritorna: Oggetto Ruolo
     */
    public function ruolo()
    {
        return $this->belongsTo(Ruolo::class, 'ruolo_id');
    }

    /**
     * Relazione: Utente ha un profilo Cliente
     * 
     * Funzione: Se tipo_utente = 'cliente', collega al profilo cliente
     * Ritorna: Oggetto Cliente o null
     */
    public function cliente()
    {
        return $this->hasOne(Cliente::class, 'utente_id');
    }

    /**
     * Scope: Solo utenti attivi
     * 
     * Funzione: Filtra query per ottenere solo utenti attivi
     * Uso: Utente::attivi()->get()
     */
    public function scopeAttivi($query)
    {
        return $query->where('attivo', true);
    }

    /**
     * Scope: Solo amministratori
     * 
     * Funzione: Filtra query per ottenere solo amministratori
     * Uso: Utente::amministratori()->get()
     */
    public function scopeAmministratori($query)
    {
        return $query->where('tipo_utente', 'amministratore');
    }

    /**
     * Scope: Solo clienti
     * 
     * Funzione: Filtra query per ottenere solo clienti
     * Uso: Utente::clienti()->get()
     */
    public function scopeClienti($query)
    {
        return $query->where('tipo_utente', 'cliente');
    }

    /**
     * Verifica se l'utente ha un permesso specifico
     * 
     * Funzione: Controlla se il ruolo dell'utente ha il permesso richiesto
     * Parametri:
     *   - $permesso_slug: slug del permesso (es: 'gestione-clienti')
     * Ritorna: true se ha il permesso, false altrimenti
     */
    public function haPermesso($permesso_slug)
    {
        if (!$this->ruolo) {
            return false;
        }

        return $this->ruolo->permessi()
            ->where('slug', $permesso_slug)
            ->where('attivo', true)
            ->exists();
    }

    /**
     * Verifica se l'utente ha uno dei permessi specificati
     * 
     * Funzione: Controlla se ha almeno uno dei permessi nella lista
     * Parametri:
     *   - $permessi: array di slug permessi
     * Ritorna: true se ha almeno un permesso, false altrimenti
     */
    public function haAlmenoUnPermesso(array $permessi)
    {
        if (!$this->ruolo) {
            return false;
        }

        return $this->ruolo->permessi()
            ->whereIn('slug', $permessi)
            ->where('attivo', true)
            ->exists();
    }

    /**
     * Verifica se l'utente è Super Admin
     * 
     * Funzione: Controlla se il ruolo è Super Admin
     * Ritorna: true se è Super Admin, false altrimenti
     */
    public function isSuperAdmin()
    {
        return $this->ruolo && $this->ruolo->slug === 'super-admin';
    }

    /**
     * Verifica se l'utente è Moderatore
     * 
     * Funzione: Controlla se il ruolo è Moderatore
     * Ritorna: true se è Moderatore, false altrimenti
     */
    public function isModeratore()
    {
        return $this->ruolo && $this->ruolo->slug === 'moderatore';
    }

    /**
     * Verifica se l'utente è Cliente
     * 
     * Funzione: Controlla se il tipo_utente è 'cliente'
     * Ritorna: true se è Cliente, false altrimenti
     */
    public function isCliente()
    {
        return $this->tipo_utente === 'cliente';
    }

    /**
     * Ottiene il nome completo dell'utente
     * 
     * Funzione: Concatena nome e cognome
     * Ritorna: stringa "Nome Cognome"
     */
    public function getNomeCompletoAttribute()
    {
        return "{$this->nome} {$this->cognome}";
    }

    /**
     * Aggiorna data e IP dell'ultimo accesso
     * 
     * Funzione: Registra quando l'utente fa login
     * Parametri:
     *   - $ip: indirizzo IP dell'utente
     */
    public function aggiornaUltimoAccesso($ip = null)
    {
        $this->ultimo_accesso = now();
        $this->ultimo_ip = $ip ?? request()->ip();
        $this->save();
    }

    /**
     * Verifica se l'email è stata verificata
     * 
     * Funzione: Controlla se l'utente ha confermato la sua email
     * Ritorna: true se verificata, false altrimenti
     */
    public function hasVerifiedEmail()
    {
        return $this->email_verificata;
    }

    /**
     * Marca l'email come verificata
     * 
     * Funzione: Imposta email_verificata a true e registra la data
     */
    public function markEmailAsVerified()
    {
        $this->email_verificata = true;
        $this->email_verificata_il = now();
        $this->save();
    }
}
