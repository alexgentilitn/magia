<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model: Analytics
 * Funzione: Traccia comportamento utenti per statistiche e ottimizzazione UX
 */
class Analytics extends Model
{
    use HasFactory;

    protected $table = 'analytics';

    public $timestamps = true;

    protected $fillable = [
        'utente_id',
        'cliente_id',
        'tipo_evento', // page_view, click, form_submit, download, etc
        'pagina',
        'azione',
        'url',
        'ip_address',
        'user_agent',
        'dispositivo', // desktop, mobile, tablet
        'browser',
        'os',
        'tempo_permanenza', // secondi
        'referrer',
        'dati_extra', // JSON per dati aggiuntivi
    ];

    protected $casts = [
        'dati_extra' => 'array',
        'tempo_permanenza' => 'integer',
    ];

    /**
     * Relazione: Utente (se loggato)
     */
    public function utente()
    {
        return $this->belongsTo(Utente::class);
    }

    /**
     * Relazione: Cliente (se è un cliente)
     */
    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    /**
     * Scope: Eventi recenti
     */
    public function scopeRecenti($query, $giorni = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($giorni));
    }

    /**
     * Scope: Per tipo evento
     */
    public function scopePerTipo($query, $tipo)
    {
        return $query->where('tipo_evento', $tipo);
    }

    /**
     * Scope: Per pagina
     */
    public function scopePerPagina($query, $pagina)
    {
        return $query->where('pagina', $pagina);
    }

    /**
     * Scope: Da mobile
     */
    public function scopeMobile($query)
    {
        return $query->where('dispositivo', 'mobile');
    }

    /**
     * Scope: Da desktop
     */
    public function scopeDesktop($query)
    {
        return $query->where('dispositivo', 'desktop');
    }

    /**
     * Tipi di evento tracciabili
     */
    public static function getTipiEvento()
    {
        return [
            'page_view' => 'Visualizzazione Pagina',
            'click' => 'Click',
            'form_submit' => 'Invio Form',
            'download' => 'Download File',
            'upload' => 'Upload File',
            'login' => 'Login',
            'logout' => 'Logout',
            'registrazione' => 'Registrazione',
            'prenotazione' => 'Prenotazione Lezione',
            'pagamento' => 'Pagamento',
            'chat_message' => 'Messaggio Chat',
            'search' => 'Ricerca',
            'video_play' => 'Riproduzione Video',
            'error' => 'Errore',
        ];
    }

    /**
     * Traccia evento
     */
    public static function traccia($tipo_evento, $pagina, $azione = null, $dati_extra = [])
    {
        $request = request();

        // Detect dispositivo
        $user_agent = $request->userAgent();
        $dispositivo = 'desktop';
        if (preg_match('/mobile|android|iphone|ipad|phone/i', $user_agent)) {
            $dispositivo = 'mobile';
        } elseif (preg_match('/tablet|ipad/i', $user_agent)) {
            $dispositivo = 'tablet';
        }

        // Detect browser e OS (basic)
        $browser = 'unknown';
        if (preg_match('/chrome/i', $user_agent)) $browser = 'Chrome';
        elseif (preg_match('/firefox/i', $user_agent)) $browser = 'Firefox';
        elseif (preg_match('/safari/i', $user_agent)) $browser = 'Safari';
        elseif (preg_match('/edge/i', $user_agent)) $browser = 'Edge';

        $os = 'unknown';
        if (preg_match('/windows/i', $user_agent)) $os = 'Windows';
        elseif (preg_match('/mac/i', $user_agent)) $os = 'macOS';
        elseif (preg_match('/linux/i', $user_agent)) $os = 'Linux';
        elseif (preg_match('/android/i', $user_agent)) $os = 'Android';
        elseif (preg_match('/ios|iphone|ipad/i', $user_agent)) $os = 'iOS';

        return self::create([
            'utente_id' => auth()->id(),
            'cliente_id' => auth()->check() && auth()->user()->cliente ? auth()->user()->cliente->id : null,
            'tipo_evento' => $tipo_evento,
            'pagina' => $pagina,
            'azione' => $azione,
            'url' => $request->fullUrl(),
            'ip_address' => $request->ip(),
            'user_agent' => $user_agent,
            'dispositivo' => $dispositivo,
            'browser' => $browser,
            'os' => $os,
            'referrer' => $request->header('referer'),
            'dati_extra' => $dati_extra,
        ]);
    }
}
