<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

/**
 * Model: Lezione
 * Funzione: Gestisce le lezioni/corsi del centro fitness
 */
class Lezione extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'lezioni';

    protected $fillable = [
        'titolo',
        'descrizione',
        'tipologia',
        'disciplina',
        'livello',
        'programma_id',
        'sede_id',
        'professionista_id',
        'data',
        'ora_inizio',
        'ora_fine',
        'durata_minuti',
        'posti_totali',
        'posti_occupati',
        'posti_lista_attesa',
        'stato',
        'ricorrente',
        'frequenza_ricorrenza',
        'fine_ricorrenza',
        'lezione_padre_id',
        'materiale_necessario',
        'note_interne',
        'note_pubbliche',
        'link_online',
        'password_online',
        'prenotazione_obbligatoria',
        'max_cancellazioni_ore',
        'lista_attesa_attiva',
        'visibile_calendario',
        'invia_reminder',
    ];

    protected $casts = [
        'data' => 'date',
        'ora_inizio' => 'datetime',
        'ora_fine' => 'datetime',
        'fine_ricorrenza' => 'date',
        'ricorrente' => 'boolean',
        'prenotazione_obbligatoria' => 'boolean',
        'lista_attesa_attiva' => 'boolean',
        'visibile_calendario' => 'boolean',
        'invia_reminder' => 'boolean',
        'materiale_necessario' => 'array',
        'durata_minuti' => 'integer',
        'posti_totali' => 'integer',
        'posti_occupati' => 'integer',
        'posti_lista_attesa' => 'integer',
        'max_cancellazioni_ore' => 'integer',
    ];

    /**
     * Relazione: Lezione appartiene a un Programma
     */
    public function programma()
    {
        return $this->belongsTo(Programma::class, 'programma_id');
    }

    /**
     * Relazione: Lezione appartiene a una Sede
     */
    public function sede()
    {
        return $this->belongsTo(Sede::class, 'sede_id');
    }

    /**
     * Relazione: Lezione ha un Professionista (istruttore)
     */
    public function professionista()
    {
        return $this->belongsTo(Utente::class, 'professionista_id');
    }

    /**
     * Relazione: Lezione ha molti Clienti (partecipanti)
     */
    public function clienti()
    {
        return $this->belongsToMany(Utente::class, 'cliente_lezione', 'lezione_id', 'cliente_id')
            ->withPivot('stato', 'data_prenotazione', 'check_in', 'check_out', 'valutazione', 'feedback')
            ->withTimestamps();
    }

    /**
     * Alias per clienti - iscrizioni
     */
    public function iscrizioni()
    {
        return $this->clienti();
    }

    /**
     * Relazione: Lezione padre (per lezioni ricorrenti)
     */
    public function lezionePadre()
    {
        return $this->belongsTo(Lezione::class, 'lezione_padre_id');
    }

    /**
     * Relazione: Lezioni figlie (se questa è una lezione padre ricorrente)
     */
    public function lezioniFiglie()
    {
        return $this->hasMany(Lezione::class, 'lezione_padre_id');
    }

    /**
     * Scope: Solo lezioni future
     */
    public function scopeFuture($query)
    {
        return $query->where('data', '>=', now()->toDateString())
            ->whereNotIn('stato', ['cancellata', 'completata']);
    }

    /**
     * Scope: Solo lezioni passate
     */
    public function scopePassate($query)
    {
        return $query->where('data', '<', now()->toDateString())
            ->orWhere('stato', 'completata');
    }

    /**
     * Scope: Solo lezioni di oggi
     */
    public function scopeOggi($query)
    {
        return $query->whereDate('data', now()->toDateString());
    }

    /**
     * Scope: Lezioni per stato
     */
    public function scopeConStato($query, $stato)
    {
        return $query->where('stato', $stato);
    }

    /**
     * Scope: Lezioni con posti disponibili
     */
    public function scopeConPostiDisponibili($query)
    {
        return $query->whereColumn('posti_occupati', '<', 'posti_totali');
    }

    /**
     * Verifica se la lezione è al completo
     */
    public function isAlCompleto()
    {
        return $this->posti_occupati >= $this->posti_totali;
    }

    /**
     * Calcola i posti disponibili
     */
    public function postiDisponibili()
    {
        return max(0, $this->posti_totali - $this->posti_occupati);
    }

    /**
     * Verifica se la lezione è prenotabile
     */
    public function isPrenotabile()
    {
        // Non prenotabile se cancellata, completata o al completo
        if (in_array($this->stato, ['cancellata', 'completata'])) {
            return false;
        }

        // Non prenotabile se passata
        if ($this->data < now()->toDateString()) {
            return false;
        }

        // Non prenotabile se al completo e senza lista attesa attiva
        if ($this->isAlCompleto() && !$this->lista_attesa_attiva) {
            return false;
        }

        return true;
    }

    /**
     * Ottiene la data e ora completa come stringa
     */
    public function getDataOraCompletaAttribute()
    {
        return $this->data->format('d/m/Y') . ' ' . Carbon::parse($this->ora_inizio)->format('H:i');
    }

    /**
     * Ottiene il range orario formattato
     */
    public function getRangeOrarioAttribute()
    {
        return Carbon::parse($this->ora_inizio)->format('H:i') . ' - ' . Carbon::parse($this->ora_fine)->format('H:i');
    }

    /**
     * Ottiene il badge di stato colorato
     */
    public function getBadgeStatoAttribute()
    {
        $colori = [
            'programmata' => 'bg-blue-100 text-blue-800',
            'confermata' => 'bg-green-100 text-green-800',
            'in_corso' => 'bg-yellow-100 text-yellow-800',
            'completata' => 'bg-gray-100 text-gray-800',
            'cancellata' => 'bg-red-100 text-red-800',
            'rinviata' => 'bg-orange-100 text-orange-800',
        ];

        return $colori[$this->stato] ?? 'bg-gray-100 text-gray-800';
    }

    /**
     * Ottiene la percentuale di occupazione
     */
    public function getPercentualeOccupazioneAttribute()
    {
        if ($this->posti_totali == 0) {
            return 0;
        }

        return round(($this->posti_occupati / $this->posti_totali) * 100);
    }

    /**
     * Verifica se la lezione è online
     */
    public function isOnline()
    {
        return in_array($this->tipologia, ['online', 'ibrida']);
    }

    /**
     * Ottiene il link meeting online se disponibile
     */
    public function getLinkMeetingAttribute()
    {
        if ($this->isOnline() && $this->link_online) {
            return $this->link_online;
        }

        return null;
    }

    // ===================================================================
    // GESTIONE RICORRENZE
    // ===================================================================

    /**
     * Verifica se questa lezione fa parte di una serie ricorrente
     */
    public function isLezioneRicorrente()
    {
        return $this->ricorrente || $this->lezione_padre_id !== null;
    }

    /**
     * Verifica se questa è la lezione padre (master) di una serie ricorrente
     */
    public function isLezionePadre()
    {
        return $this->ricorrente && $this->lezione_padre_id === null;
    }

    /**
     * Calcola la prossima data in base alla frequenza di ricorrenza
     */
    public function calcolaDataProssimaOccorrenza(Carbon $dataCorrente)
    {
        switch ($this->frequenza_ricorrenza) {
            case 'giornaliera':
                return $dataCorrente->copy()->addDay();

            case 'settimanale':
                return $dataCorrente->copy()->addWeek();

            case 'bisettimanale':
                return $dataCorrente->copy()->addWeeks(2);

            case 'mensile':
                return $dataCorrente->copy()->addMonth();

            default:
                return null;
        }
    }

    /**
     * Genera tutte le occorrenze ricorrenti di questa lezione
     * Ritorna il numero di lezioni create
     */
    public function generaOccorrenze()
    {
        // Solo la lezione padre può generare occorrenze
        if (!$this->ricorrente || $this->lezione_padre_id !== null) {
            return 0;
        }

        // Verifica che ci sia una frequenza e una data di fine
        if (!$this->frequenza_ricorrenza || !$this->fine_ricorrenza) {
            return 0;
        }

        $occorrenzeCreate = 0;
        $dataCorrente = Carbon::parse($this->data);
        $dataFine = Carbon::parse($this->fine_ricorrenza);

        // Genera occorrenze fino alla data di fine
        while (true) {
            $dataProssima = $this->calcolaDataProssimaOccorrenza($dataCorrente);

            if (!$dataProssima || $dataProssima->gt($dataFine)) {
                break;
            }

            // Crea la lezione figlia
            $this->creaOccorrenzaFiglia($dataProssima);
            $occorrenzeCreate++;

            $dataCorrente = $dataProssima;
        }

        return $occorrenzeCreate;
    }

    /**
     * Crea una singola occorrenza figlia
     */
    protected function creaOccorrenzaFiglia(Carbon $data)
    {
        $attributi = $this->toArray();

        // Rimuovi campi che non devono essere duplicati
        unset($attributi['id']);
        unset($attributi['created_at']);
        unset($attributi['updated_at']);
        unset($attributi['deleted_at']);

        // Imposta la nuova data e collega alla lezione padre
        $attributi['data'] = $data->toDateString();
        $attributi['lezione_padre_id'] = $this->id;
        $attributi['ricorrente'] = false; // Le figlie non sono a loro volta ricorrenti
        $attributi['posti_occupati'] = 0; // Reset posti

        return static::create($attributi);
    }

    /**
     * Elimina tutte le occorrenze future di questa serie ricorrente
     */
    public function eliminaOccorrenzeFuture($includiPadre = false)
    {
        $lezionePadreId = $this->isLezionePadre() ? $this->id : $this->lezione_padre_id;

        if (!$lezionePadreId) {
            return 0;
        }

        $query = static::where('lezione_padre_id', $lezionePadreId)
            ->where('data', '>=', now()->toDateString());

        $count = $query->count();
        $query->delete();

        // Se richiesto, elimina anche la lezione padre
        if ($includiPadre && $this->isLezionePadre()) {
            $this->delete();
            $count++;
        }

        return $count;
    }

    /**
     * Aggiorna tutte le occorrenze future di questa serie
     * Utile per modificare professionista, sede, ecc. su tutta la serie
     */
    public function aggiornaOccorrenzeFuture(array $attributi)
    {
        $lezionePadreId = $this->isLezionePadre() ? $this->id : $this->lezione_padre_id;

        if (!$lezionePadreId) {
            return 0;
        }

        // Non permettere la modifica di alcuni campi critici
        $campiNonModificabili = ['id', 'data', 'lezione_padre_id', 'posti_occupati', 'created_at', 'updated_at'];
        foreach ($campiNonModificabili as $campo) {
            unset($attributi[$campo]);
        }

        return static::where('lezione_padre_id', $lezionePadreId)
            ->where('data', '>', now()->toDateString())
            ->update($attributi);
    }
}
