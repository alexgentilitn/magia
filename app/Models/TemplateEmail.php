<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Model: TemplateEmail
 * Funzione: Gestisce i template personalizzabili per le email automatiche
 *
 * Tipi template:
 * - benvenuto: Email di benvenuto dopo registrazione
 * - programma: Invio programma personalizzato alla cliente
 * - promemoria_24h: Promemoria lezione 24h prima
 * - promemoria_2h: Promemoria lezione 2h prima
 * - conferma_pagamento: Conferma ricezione pagamento
 * - scadenza_certificato: Allerta scadenza certificato medico
 * - auguri_compleanno: Auguri compleanno automatici
 * - newsletter: Template newsletter marketing
 * - custom: Template personalizzato
 */
class TemplateEmail extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'template_email';

    protected $fillable = [
        'tipo',
        'nome',
        'oggetto',
        'corpo_html',
        'corpo_text',
        'variabili_disponibili',
        'mittente_nome',
        'mittente_email',
        'attivo',
        'note',
        'invia_automaticamente',
    ];

    protected $casts = [
        'variabili_disponibili' => 'array',
        'attivo' => 'boolean',
        'invia_automaticamente' => 'boolean',
    ];

    /**
     * Scope: Solo template attivi
     */
    public function scopeAttivi($query)
    {
        return $query->where('attivo', true);
    }

    /**
     * Scope: Per tipo
     */
    public function scopePerTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    /**
     * Scope: Con invio automatico
     */
    public function scopeAutomatici($query)
    {
        return $query->where('invia_automaticamente', true);
    }

    /**
     * Sostituisce le variabili nel template
     *
     * @param array $variabili  Es: ['nome_cliente' => 'Maria', 'programma' => 'Balla & Snella']
     * @return array ['oggetto' => '...', 'corpo_html' => '...', 'corpo_text' => '...']
     */
    public function renderizza($variabili = [])
    {
        $oggetto = $this->oggetto;
        $corpo_html = $this->corpo_html;
        $corpo_text = $this->corpo_text;

        // Sostituisci variabili nell'oggetto
        foreach ($variabili as $chiave => $valore) {
            $placeholder = '{{' . $chiave . '}}';
            $oggetto = str_replace($placeholder, $valore, $oggetto);
            $corpo_html = str_replace($placeholder, $valore, $corpo_html);
            $corpo_text = str_replace($placeholder, $valore, $corpo_text);
        }

        return [
            'oggetto' => $oggetto,
            'corpo_html' => $corpo_html,
            'corpo_text' => $corpo_text,
        ];
    }

    /**
     * Tipi di template disponibili
     */
    public static function getTipiTemplate()
    {
        return [
            'benvenuto' => 'Email di Benvenuto',
            'programma' => 'Invio Programma Personalizzato',
            'promemoria_24h' => 'Promemoria Lezione (24h prima)',
            'promemoria_2h' => 'Promemoria Lezione (2h prima)',
            'conferma_pagamento' => 'Conferma Pagamento Ricevuto',
            'scadenza_certificato' => 'Scadenza Certificato Medico',
            'scadenza_programma' => 'Scadenza Programma',
            'auguri_compleanno' => 'Auguri Compleanno',
            'newsletter' => 'Newsletter Marketing',
            'invito_giornata_prova' => 'Invito Giornata di Prova',
            'reset_password' => 'Reset Password',
            'custom' => 'Template Personalizzato',
        ];
    }

    /**
     * Variabili disponibili per ciascun tipo
     */
    public static function getVariabiliPerTipo($tipo)
    {
        $variabili_comuni = [
            'nome_cliente',
            'cognome_cliente',
            'nome_completo',
            'email_cliente',
            'codice_cliente',
            'data_oggi',
            'anno_corrente',
        ];

        $variabili_specifiche = [
            'benvenuto' => ['username', 'password_temporanea', 'link_accesso'],
            'programma' => ['nome_programma', 'descrizione_programma', 'data_inizio', 'data_fine'],
            'promemoria_24h' => ['nome_lezione', 'data_lezione', 'ora_inizio', 'nome_sede', 'indirizzo_sede'],
            'promemoria_2h' => ['nome_lezione', 'data_lezione', 'ora_inizio', 'nome_sede', 'indirizzo_sede'],
            'conferma_pagamento' => ['importo', 'metodo_pagamento', 'data_pagamento', 'numero_ricevuta'],
            'scadenza_certificato' => ['data_scadenza', 'giorni_rimanenti'],
            'scadenza_programma' => ['nome_programma', 'data_scadenza', 'giorni_rimanenti'],
            'auguri_compleanno' => ['eta', 'codice_sconto'],
            'reset_password' => ['link_reset', 'scadenza_link'],
        ];

        $vars = $variabili_comuni;

        if (isset($variabili_specifiche[$tipo])) {
            $vars = array_merge($vars, $variabili_specifiche[$tipo]);
        }

        return $vars;
    }

    /**
     * Crea template di default per tipo
     */
    public static function creaTemplateDefault($tipo)
    {
        $templates_default = [
            'benvenuto' => [
                'nome' => 'Email di Benvenuto',
                'oggetto' => 'Benvenuta in MA.GIA DONNA - Balla & Snella!',
                'corpo_html' => '<h1>Benvenuta {{nome_cliente}}!</h1><p>Siamo felici di averti con noi in <strong>Balla & Snella</strong> by MA.GIA DONNA.</p><p>Le tue credenziali di accesso:<br><strong>Email:</strong> {{email_cliente}}<br><strong>Password temporanea:</strong> {{password_temporanea}}</p><p><a href="{{link_accesso}}">Accedi alla tua area riservata</a></p><p>Ti aspettiamo!<br>Il Team MA.GIA DONNA</p>',
                'corpo_text' => 'Benvenuta {{nome_cliente}}! Siamo felici di averti con noi. Email: {{email_cliente}}, Password: {{password_temporanea}}. Accedi: {{link_accesso}}',
                'variabili_disponibili' => self::getVariabiliPerTipo('benvenuto'),
                'invia_automaticamente' => true,
            ],
            'promemoria_24h' => [
                'nome' => 'Promemoria Lezione 24h',
                'oggetto' => 'Promemoria: {{nome_lezione}} domani alle {{ora_inizio}}',
                'corpo_html' => '<h2>Ciao {{nome_cliente}}!</h2><p>Ti ricordiamo che domani hai lezione:</p><p><strong>{{nome_lezione}}</strong><br>📅 {{data_lezione}}<br>🕐 {{ora_inizio}}<br>📍 {{nome_sede}} - {{indirizzo_sede}}</p><p>Ci vediamo lì!<br>Il Team MA.GIA DONNA</p>',
                'corpo_text' => 'Ciao {{nome_cliente}}! Domani hai lezione: {{nome_lezione}} alle {{ora_inizio}} presso {{nome_sede}}.',
                'variabili_disponibili' => self::getVariabiliPerTipo('promemoria_24h'),
                'invia_automaticamente' => false,
            ],
        ];

        if (isset($templates_default[$tipo])) {
            $data = $templates_default[$tipo];
            $data['tipo'] = $tipo;
            $data['mittente_nome'] = 'MA.GIA DONNA - Balla & Snella';
            $data['mittente_email'] = config('mail.from.address');
            $data['attivo'] = true;

            return self::create($data);
        }

        return null;
    }
}
