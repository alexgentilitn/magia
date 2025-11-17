<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model: Ruolo
 * 
 * Funzione: Gestisce i ruoli del sistema RBAC (Role-Based Access Control)
 * per controllare i permessi degli utenti
 * Tabella: ruoli
 * 
 * Ruoli predefiniti:
 * - Super Admin (livello 1): Accesso totale
 * - Moderatore (livello 2): Gestione clienti e contenuti
 * - Collaboratore (livello 3): Accesso limitato
 * 
 * Relazioni:
 * - Ha molti: Utenti (un ruolo può essere assegnato a più utenti)
 * - Molti a molti: Permessi (un ruolo ha molti permessi)
 * 
 * Funzionalità:
 * - Verifica permessi
 * - Gestione livelli gerarchici
 * - Assegnazione dinamica permessi
 */
class Ruolo extends Model
{
    use HasFactory;

    /**
     * Nome della tabella nel database
     */
    protected $table = 'ruoli';

    /**
     * Campi che possono essere assegnati in massa
     */
    protected $fillable = [
        'nome',
        'slug',
        'descrizione',
        'livello',
        'attivo',
    ];

    /**
     * Cast automatico dei campi
     * 
     * Funzione: Converte automaticamente i campi nei tipi corretti
     */
    protected $casts = [
        'attivo' => 'boolean',
        'livello' => 'integer',
    ];

    /**
     * Relazione: Ruolo ha molti Utenti
     * 
     * Funzione: Lista di tutti gli utenti con questo ruolo
     * Ritorna: Collection di Utenti
     */
    public function utenti()
    {
        return $this->hasMany(Utente::class, 'ruolo_id');
    }

    /**
     * Relazione: Ruolo ha molti Permessi (many-to-many)
     * 
     * Funzione: Lista dei permessi associati a questo ruolo
     * Tabella pivot: ruolo_permesso
     * Ritorna: Collection di Permessi
     */
    public function permessi()
    {
        return $this->belongsToMany(
            Permesso::class,
            'ruolo_permesso',
            'ruolo_id',
            'permesso_id'
        )->withTimestamps();
    }

    /**
     * Scope: Solo ruoli attivi
     * 
     * Funzione: Filtra query per ottenere solo ruoli attivi
     * Uso: Ruolo::attivi()->get()
     */
    public function scopeAttivi($query)
    {
        return $query->where('attivo', true);
    }

    /**
     * Scope: Ruoli ordinati per livello
     * 
     * Funzione: Ordina i ruoli dal più alto (1) al più basso
     * Uso: Ruolo::perLivello()->get()
     */
    public function scopePerLivello($query)
    {
        return $query->orderBy('livello', 'asc');
    }

    /**
     * Verifica se il ruolo ha un permesso specifico
     * 
     * Funzione: Controlla se il ruolo ha il permesso tramite slug
     * Parametri:
     *   - $permesso_slug: slug del permesso (es: 'gestione-clienti')
     * Ritorna: boolean (true se ha il permesso)
     */
    public function haPermesso($permesso_slug)
    {
        return $this->permessi()
            ->where('slug', $permesso_slug)
            ->where('attivo', true)
            ->exists();
    }

    /**
     * Verifica se il ruolo ha tutti i permessi specificati
     * 
     * Funzione: Controlla se ha TUTTI i permessi nella lista
     * Parametri:
     *   - $permessi: array di slug permessi
     * Ritorna: boolean (true se ha tutti i permessi)
     */
    public function haTuttiIPermessi(array $permessi)
    {
        $count_richiesti = count($permessi);
        $count_presenti = $this->permessi()
            ->whereIn('slug', $permessi)
            ->where('attivo', true)
            ->count();

        return $count_richiesti === $count_presenti;
    }

    /**
     * Verifica se il ruolo ha almeno uno dei permessi specificati
     * 
     * Funzione: Controlla se ha ALMENO UNO dei permessi nella lista
     * Parametri:
     *   - $permessi: array di slug permessi
     * Ritorna: boolean (true se ha almeno un permesso)
     */
    public function haAlmenoUnPermesso(array $permessi)
    {
        return $this->permessi()
            ->whereIn('slug', $permessi)
            ->where('attivo', true)
            ->exists();
    }

    /**
     * Assegna un permesso al ruolo
     * 
     * Funzione: Collega un permesso a questo ruolo
     * Parametri:
     *   - $permesso: oggetto Permesso o ID permesso
     * Ritorna: void
     */
    public function assegnaPermesso($permesso)
    {
        $permesso_id = is_object($permesso) ? $permesso->id : $permesso;
        
        if (!$this->permessi()->where('permesso_id', $permesso_id)->exists()) {
            $this->permessi()->attach($permesso_id);
        }
    }

    /**
     * Rimuove un permesso dal ruolo
     * 
     * Funzione: Scollega un permesso da questo ruolo
     * Parametri:
     *   - $permesso: oggetto Permesso o ID permesso
     * Ritorna: void
     */
    public function rimuoviPermesso($permesso)
    {
        $permesso_id = is_object($permesso) ? $permesso->id : $permesso;
        $this->permessi()->detach($permesso_id);
    }

    /**
     * Sincronizza i permessi del ruolo
     * 
     * Funzione: Sostituisce tutti i permessi con una nuova lista
     * Parametri:
     *   - $permessi: array di ID permessi
     * Ritorna: void
     */
    public function sincronizzaPermessi(array $permessi)
    {
        $this->permessi()->sync($permessi);
    }

    /**
     * Verifica se il ruolo è Super Admin
     * 
     * Funzione: Controlla se il ruolo è Super Admin tramite slug
     * Ritorna: boolean
     */
    public function isSuperAdmin()
    {
        return $this->slug === 'super-admin';
    }

    /**
     * Verifica se il ruolo è Moderatore
     * 
     * Funzione: Controlla se il ruolo è Moderatore tramite slug
     * Ritorna: boolean
     */
    public function isModeratore()
    {
        return $this->slug === 'moderatore';
    }

    /**
     * Verifica se il ruolo è Collaboratore
     * 
     * Funzione: Controlla se il ruolo è Collaboratore tramite slug
     * Ritorna: boolean
     */
    public function isCollaboratore()
    {
        return $this->slug === 'collaboratore';
    }

    /**
     * Conta gli utenti con questo ruolo
     * 
     * Funzione: Ritorna il numero di utenti che hanno questo ruolo
     * Ritorna: integer
     */
    public function contaUtenti()
    {
        return $this->utenti()->count();
    }

    /**
     * Conta i permessi assegnati a questo ruolo
     * 
     * Funzione: Ritorna il numero di permessi collegati
     * Ritorna: integer
     */
    public function contaPermessi()
    {
        return $this->permessi()->where('attivo', true)->count();
    }

    /**
     * Ottiene i permessi raggruppati per categoria
     * 
     * Funzione: Ritorna i permessi organizzati per categoria
     * Ritorna: Collection raggruppata per 'categoria'
     */
    public function permessiPerCategoria()
    {
        return $this->permessi()
            ->where('attivo', true)
            ->get()
            ->groupBy('categoria');
    }

    /**
     * Verifica se il ruolo può gestire un altro ruolo
     * 
     * Funzione: Controlla la gerarchia (livello inferiore = più potente)
     * Un ruolo può gestire solo ruoli con livello superiore
     * Parametri:
     *   - $altro_ruolo: oggetto Ruolo da verificare
     * Ritorna: boolean
     */
    public function puoGestire(Ruolo $altro_ruolo)
    {
        return $this->livello < $altro_ruolo->livello;
    }
}
