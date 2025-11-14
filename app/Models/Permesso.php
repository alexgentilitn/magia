<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model: Permesso
 * 
 * Funzione: Gestisce i permessi del sistema RBAC (Role-Based Access Control)
 * per controllare l'accesso alle funzionalità
 * Tabella: permessi
 * 
 * Categorie permessi:
 * - dashboard: Accesso dashboard
 * - clienti: Gestione clienti (visualizza, crea, modifica, elimina)
 * - programmi: Gestione programmi
 * - corsi: Gestione corsi e lezioni
 * - pagamenti: Gestione pagamenti
 * - sedi: Gestione sedi operative
 * - sistema: Gestione utenti e configurazioni
 * 
 * Relazioni:
 * - Molti a molti: Ruoli (un permesso può essere assegnato a più ruoli)
 * 
 * Funzionalità:
 * - Organizzazione per categoria
 * - Verifica assegnazione a ruoli
 * - Gestione stato attivo/inattivo
 */
class Permesso extends Model
{
    use HasFactory;

    /**
     * Nome della tabella nel database
     */
    protected $table = 'permessi';

    /**
     * Campi che possono essere assegnati in massa
     */
    protected $fillable = [
        'nome',
        'slug',
        'categoria',
        'descrizione',
        'attivo',
    ];

    /**
     * Cast automatico dei campi
     * 
     * Funzione: Converte automaticamente i campi nei tipi corretti
     */
    protected $casts = [
        'attivo' => 'boolean',
    ];

    /**
     * Relazione: Permesso appartiene a molti Ruoli (many-to-many)
     * 
     * Funzione: Lista dei ruoli che hanno questo permesso
     * Tabella pivot: ruolo_permesso
     * Ritorna: Collection di Ruoli
     */
    public function ruoli()
    {
        return $this->belongsToMany(
            Ruolo::class,
            'ruolo_permesso',
            'permesso_id',
            'ruolo_id'
        )->withTimestamps();
    }

    /**
     * Scope: Solo permessi attivi
     * 
     * Funzione: Filtra query per ottenere solo permessi attivi
     * Uso: Permesso::attivi()->get()
     */
    public function scopeAttivi($query)
    {
        return $query->where('attivo', true);
    }

    /**
     * Scope: Permessi per categoria
     * 
     * Funzione: Filtra permessi per una categoria specifica
     * Uso: Permesso::perCategoria('clienti')->get()
     */
    public function scopePerCategoria($query, $categoria)
    {
        return $query->where('categoria', $categoria);
    }

    /**
     * Scope: Permessi ordinati per categoria e nome
     * 
     * Funzione: Ordina i permessi per categoria alfabetica e poi per nome
     * Uso: Permesso::ordinati()->get()
     */
    public function scopeOrdinati($query)
    {
        return $query->orderBy('categoria', 'asc')
                     ->orderBy('nome', 'asc');
    }

    /**
     * Verifica se il permesso è assegnato a un ruolo specifico
     * 
     * Funzione: Controlla se questo permesso è collegato al ruolo specificato
     * Parametri:
     *   - $ruolo_slug: slug del ruolo (es: 'super-admin')
     * Ritorna: boolean (true se assegnato al ruolo)
     */
    public function isAssegnatoARuolo($ruolo_slug)
    {
        return $this->ruoli()
            ->where('slug', $ruolo_slug)
            ->exists();
    }

    /**
     * Ottiene tutti i ruoli che hanno questo permesso
     * 
     * Funzione: Ritorna la lista dei ruoli con questo permesso
     * Ritorna: Collection di Ruoli
     */
    public function getRuoliConPermesso()
    {
        return $this->ruoli()
            ->where('attivo', true)
            ->get();
    }

    /**
     * Conta i ruoli che hanno questo permesso
     * 
     * Funzione: Ritorna il numero di ruoli con questo permesso
     * Ritorna: integer
     */
    public function contaRuoli()
    {
        return $this->ruoli()->where('attivo', true)->count();
    }

    /**
     * Verifica se il permesso è critico
     * 
     * Funzione: I permessi della categoria 'sistema' sono considerati critici
     * perché riguardano la gestione di utenti e configurazioni
     * Ritorna: boolean
     */
    public function isCritico()
    {
        return $this->categoria === 'sistema';
    }

    /**
     * Ottiene tutti i permessi raggruppati per categoria
     * 
     * Funzione: Ritorna tutti i permessi organizzati per categoria
     * Uso statico: Permesso::tuttiPerCategoria()
     * Ritorna: Collection raggruppata
     */
    public static function tuttiPerCategoria()
    {
        return self::attivi()
            ->ordinati()
            ->get()
            ->groupBy('categoria');
    }

    /**
     * Ottiene le categorie disponibili
     * 
     * Funzione: Ritorna un array di tutte le categorie uniche
     * Ritorna: array di stringhe
     */
    public static function categorieDisponibili()
    {
        return self::attivi()
            ->distinct()
            ->pluck('categoria')
            ->sort()
            ->values()
            ->toArray();
    }

    /**
     * Ottiene il nome formattato della categoria
     * 
     * Funzione: Converte lo slug categoria in testo leggibile
     * Ritorna: stringa formattata
     */
    public function getCategoriaFormattataAttribute()
    {
        $categorie_map = [
            'dashboard' => 'Dashboard',
            'clienti' => 'Gestione Clienti',
            'programmi' => 'Gestione Programmi',
            'corsi' => 'Gestione Corsi',
            'pagamenti' => 'Gestione Pagamenti',
            'sedi' => 'Gestione Sedi',
            'sistema' => 'Configurazioni Sistema',
        ];

        return $categorie_map[$this->categoria] ?? ucfirst($this->categoria);
    }

    /**
     * Ottiene l'icona associata alla categoria
     * 
     * Funzione: Ritorna la classe icona per l'interfaccia grafica
     * Ritorna: stringa con classe CSS icona
     */
    public function getIconaCategoriaAttribute()
    {
        $icone_map = [
            'dashboard' => 'fa-chart-line',
            'clienti' => 'fa-users',
            'programmi' => 'fa-dumbbell',
            'corsi' => 'fa-calendar-alt',
            'pagamenti' => 'fa-credit-card',
            'sedi' => 'fa-map-marker-alt',
            'sistema' => 'fa-cog',
        ];

        return $icone_map[$this->categoria] ?? 'fa-circle';
    }

    /**
     * Crea permessi di base per una nuova categoria
     * 
     * Funzione: Genera automaticamente i permessi CRUD per una categoria
     * Parametri:
     *   - $categoria: nome categoria (es: 'eventi')
     *   - $nome_risorsa: nome risorsa al singolare (es: 'Evento')
     * Ritorna: array di Permessi creati
     */
    public static function creaPermessiCRUD($categoria, $nome_risorsa)
    {
        $permessi = [];
        $azioni = [
            'visualizza' => 'Visualizzare',
            'crea' => 'Creare',
            'modifica' => 'Modificare',
            'elimina' => 'Eliminare',
        ];

        foreach ($azioni as $azione => $verbo) {
            $permesso = self::create([
                'nome' => "{$verbo} {$nome_risorsa}",
                'slug' => "{$azione}-{$categoria}",
                'categoria' => $categoria,
                'descrizione' => "{$verbo} {$nome_risorsa} nel sistema",
                'attivo' => true,
            ]);
            
            $permessi[] = $permesso;
        }

        return $permessi;
    }
}
