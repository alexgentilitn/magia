<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EmailTemplate;

class EmailTemplatesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $templates = [
            [
                'nome' => 'Benvenuto Nuovo Cliente',
                'codice' => 'benvenuto_cliente',
                'tipo' => 'sistema',
                'descrizione' => 'Email di benvenuto inviata alla registrazione di un nuovo cliente',
                'oggetto' => 'Benvenuta in MA.GIA DONNA, {{nome_cliente}}!',
                'corpo_html' => '
                    <h1 style="color: #7B2869;">Benvenuta {{nome_cliente}}!</h1>
                    <p>Siamo felici di averti con noi in MA.GIA DONNA.</p>
                    <p>Il tuo percorso di benessere inizia qui. Accedi alla tua area personale per:</p>
                    <ul>
                        <li>Visualizzare i tuoi programmi</li>
                        <li>Prenotare le lezioni</li>
                        <li>Monitorare i tuoi progressi</li>
                    </ul>
                    <p><a href="{{link_area_cliente}}" style="background: linear-gradient(to right, #7B2869, #E91E8C); color: white; padding: 12px 30px; text-decoration: none; border-radius: 8px; display: inline-block;">Accedi alla Tua Area</a></p>
                    <p>A presto,<br><strong>Il Team MA.GIA DONNA</strong></p>
                ',
                'corpo_testo' => 'Benvenuta {{nome_cliente}}! Siamo felici di averti con noi in MA.GIA DONNA. Accedi alla tua area personale: {{link_area_cliente}}',
                'attivo' => true,
            ],
            [
                'nome' => 'Invito Porta un\'Amica',
                'codice' => 'invito_amica',
                'tipo' => 'referral',
                'descrizione' => 'Email di invito inviata alle amiche tramite sistema referral',
                'oggetto' => '{{nome_invitante}} ti invita a scoprire MA.GIA DONNA - 15€ di sconto!',
                'corpo_html' => '
                    <h1 style="color: #7B2869;">Ciao {{nome_amica}}!</h1>
                    <p><strong>{{nome_invitante}}</strong> pensa che MA.GIA DONNA sia perfetto per te!</p>
                    {{messaggio_personale}}
                    <div style="background: linear-gradient(to right, #7B2869, #E91E8C); color: white; padding: 20px; border-radius: 12px; text-align: center; margin: 20px 0;">
                        <h2 style="color: white; margin: 0;">Ricevi 15€ di Sconto!</h2>
                        <p style="font-size: 24px; font-weight: bold; margin: 10px 0;">Codice: {{codice_invito}}</p>
                    </div>
                    <p><a href="{{link_registrazione}}" style="background: #E91E8C; color: white; padding: 12px 30px; text-decoration: none; border-radius: 8px; display: inline-block;">Registrati Ora</a></p>
                    <p style="font-size: 12px; color: #666;">Offerta valida per nuovi clienti. Lo sconto verrà applicato al primo acquisto.</p>
                ',
                'corpo_testo' => 'Ciao {{nome_amica}}! {{nome_invitante}} ti invita a scoprire MA.GIA DONNA. Usa il codice {{codice_invito}} e ricevi 15€ di sconto! Registrati qui: {{link_registrazione}}',
                'attivo' => true,
            ],
            [
                'nome' => 'Conferma Prenotazione Lezione',
                'codice' => 'conferma_prenotazione',
                'tipo' => 'prenotazioni',
                'descrizione' => 'Email di conferma inviata dopo la prenotazione di una lezione',
                'oggetto' => 'Prenotazione Confermata - {{titolo_lezione}}',
                'corpo_html' => '
                    <h1 style="color: #7B2869;">Prenotazione Confermata!</h1>
                    <p>Ciao <strong>{{nome_cliente}}</strong>,</p>
                    <p>La tua prenotazione è stata confermata con successo.</p>
                    <div style="background: #f5f5f5; padding: 20px; border-radius: 8px; margin: 20px 0;">
                        <h3 style="margin-top: 0;">{{titolo_lezione}}</h3>
                        <p><strong>Data:</strong> {{data_lezione}}</p>
                        <p><strong>Orario:</strong> {{ora_inizio}} - {{ora_fine}}</p>
                        <p><strong>Sede:</strong> {{nome_sede}}</p>
                        <p><strong>Indirizzo:</strong> {{indirizzo_sede}}</p>
                        <p><strong>Istruttore:</strong> {{nome_professionista}}</p>
                    </div>
                    <p><strong>Cosa portare:</strong></p>
                    <ul>
                        <li>Abbigliamento comodo</li>
                        <li>Tappetino (se disponibile)</li>
                        <li>Bottiglia d\'acqua</li>
                    </ul>
                    <p>Ti aspettiamo!<br><strong>Il Team MA.GIA DONNA</strong></p>
                ',
                'corpo_testo' => 'Prenotazione confermata! {{titolo_lezione}} - {{data_lezione}} ore {{ora_inizio}} presso {{nome_sede}}.',
                'attivo' => true,
            ],
            [
                'nome' => 'Reminder Lezione (24h prima)',
                'codice' => 'reminder_lezione',
                'tipo' => 'prenotazioni',
                'descrizione' => 'Promemoria inviato 24 ore prima della lezione',
                'oggetto' => 'Reminder: {{titolo_lezione}} domani alle {{ora_inizio}}',
                'corpo_html' => '
                    <h1 style="color: #7B2869;">Ci vediamo domani!</h1>
                    <p>Ciao <strong>{{nome_cliente}}</strong>,</p>
                    <p>Ti ricordiamo la tua lezione prenotata per domani:</p>
                    <div style="background: linear-gradient(to right, #7B2869, #E91E8C); color: white; padding: 20px; border-radius: 12px; margin: 20px 0;">
                        <h2 style="color: white; margin: 0;">{{titolo_lezione}}</h2>
                        <p style="font-size: 18px; margin: 10px 0;">{{data_lezione}} • {{ora_inizio}}</p>
                        <p style="margin: 0;">{{nome_sede}}</p>
                    </div>
                    <p><strong>Note importanti:</strong></p>
                    <ul>
                        <li>Arriva 10 minuti prima per il check-in</li>
                        <li>Porta abbigliamento comodo</li>
                        <li>In caso di impedimento, disdici per tempo</li>
                    </ul>
                    <p>A domani!<br><strong>Il Team MA.GIA DONNA</strong></p>
                ',
                'corpo_testo' => 'Reminder: {{titolo_lezione}} domani {{data_lezione}} alle {{ora_inizio}} presso {{nome_sede}}. Ti aspettiamo!',
                'attivo' => true,
            ],
            [
                'nome' => 'Password Temporanea',
                'codice' => 'password_temporanea',
                'tipo' => 'sistema',
                'descrizione' => 'Email con credenziali di accesso temporanee',
                'oggetto' => 'Le tue credenziali di accesso - MA.GIA DONNA',
                'corpo_html' => '
                    <h1 style="color: #7B2869;">Benvenuta {{nome_cliente}}!</h1>
                    <p>Il tuo account è stato creato con successo.</p>
                    <p>Ecco le tue credenziali di accesso temporanee:</p>
                    <div style="background: #f5f5f5; padding: 20px; border-radius: 8px; margin: 20px 0;">
                        <p><strong>Email:</strong> {{email}}</p>
                        <p><strong>Password Temporanea:</strong> <span style="font-family: monospace; font-size: 16px; background: #fff; padding: 5px 10px; border-radius: 4px;">{{password_temp}}</span></p>
                    </div>
                    <p style="color: #d33; font-weight: bold;">⚠️ IMPORTANTE: Al primo accesso ti verrà chiesto di modificare la password.</p>
                    <p><a href="{{link_login}}" style="background: linear-gradient(to right, #7B2869, #E91E8C); color: white; padding: 12px 30px; text-decoration: none; border-radius: 8px; display: inline-block;">Accedi Ora</a></p>
                    <p style="font-size: 12px; color: #666;">Per motivi di sicurezza, questa password è valida solo per 7 giorni.</p>
                ',
                'corpo_testo' => 'Benvenuta {{nome_cliente}}! Email: {{email}} - Password temporanea: {{password_temp}} - Accedi qui: {{link_login}}',
                'attivo' => true,
            ],
            [
                'nome' => 'Certificato Medico in Scadenza',
                'codice' => 'certificato_scadenza',
                'tipo' => 'promemoria',
                'descrizione' => 'Promemoria per rinnovo certificato medico',
                'oggetto' => 'Rinnova il tuo Certificato Medico',
                'corpo_html' => '
                    <h1 style="color: #7B2869;">Promemoria Certificato Medico</h1>
                    <p>Ciao <strong>{{nome_cliente}}</strong>,</p>
                    <p>Il tuo certificato medico scadrà il <strong>{{data_scadenza}}</strong>.</p>
                    <p>Per continuare a partecipare alle lezioni, è necessario rinnovarlo.</p>
                    <div style="background: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 20px 0;">
                        <p style="margin: 0;"><strong>⚠️ Scadenza:</strong> {{data_scadenza}}</p>
                    </div>
                    <p>Puoi caricare il nuovo certificato dalla tua area personale.</p>
                    <p><a href="{{link_area_cliente}}" style="background: #E91E8C; color: white; padding: 12px 30px; text-decoration: none; border-radius: 8px; display: inline-block;">Carica Certificato</a></p>
                    <p>Grazie,<br><strong>Il Team MA.GIA DONNA</strong></p>
                ',
                'corpo_testo' => 'Promemoria: Il tuo certificato medico scade il {{data_scadenza}}. Caricalo dalla tua area personale: {{link_area_cliente}}',
                'attivo' => true,
            ],
            [
                'nome' => 'Riepilogo Mensile',
                'codice' => 'riepilogo_mensile',
                'tipo' => 'report',
                'descrizione' => 'Riepilogo mensile attività cliente',
                'oggetto' => 'Il tuo Riepilogo {{mese}} - MA.GIA DONNA',
                'corpo_html' => '
                    <h1 style="color: #7B2869;">Riepilogo {{mese}}</h1>
                    <p>Ciao <strong>{{nome_cliente}}</strong>,</p>
                    <p>Ecco il riepilogo delle tue attività del mese:</p>
                    <div style="background: #f5f5f5; padding: 20px; border-radius: 8px; margin: 20px 0;">
                        <h3>Le tue statistiche:</h3>
                        <p>📅 <strong>Lezioni frequentate:</strong> {{lezioni_frequentate}}</p>
                        <p>🔥 <strong>Ore totali:</strong> {{ore_totali}}</p>
                        <p>📊 <strong>Peso iniziale:</strong> {{peso_iniziale}} kg</p>
                        <p>📊 <strong>Peso attuale:</strong> {{peso_attuale}} kg</p>
                        <p>✨ <strong>Variazione:</strong> {{variazione_peso}} kg</p>
                    </div>
                    <p><strong>Continua così!</strong> Ogni progresso è un passo verso il tuo benessere.</p>
                    <p><a href="{{link_area_cliente}}" style="background: linear-gradient(to right, #7B2869, #E91E8C); color: white; padding: 12px 30px; text-decoration: none; border-radius: 8px; display: inline-block;">Vedi Dettagli</a></p>
                    <p>Grazie per essere parte della famiglia MA.GIA DONNA!<br><strong>Il Team</strong></p>
                ',
                'corpo_testo' => 'Riepilogo {{mese}}: {{lezioni_frequentate}} lezioni frequentate, {{ore_totali}} ore totali. Continua così!',
                'attivo' => true,
            ],
        ];

        foreach ($templates as $template) {
            EmailTemplate::updateOrCreate(
                ['codice' => $template['codice']],
                $template
            );
        }

        $this->command->info('✅ Email templates creati con successo!');
    }
}
