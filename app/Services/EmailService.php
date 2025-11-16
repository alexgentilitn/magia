<?php

namespace App\Services;

use App\Models\TemplateEmail;
use App\Models\Impostazione;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;

/**
 * Service: EmailService
 * Funzione: Gestisce l'invio di email utilizzando template e configurazione SMTP dinamica
 *
 * IMPORTANTE: Utilizza le impostazioni SMTP configurate nella pagina:
 * https://www.agstudio.digital/magia/public/admin/impostazioni/smtp
 */
class EmailService
{
    /**
     * Carica configurazione SMTP dal database
     */
    protected function caricaConfigurazioneSmtp()
    {
        try {
            $smtp_host = Impostazione::get('smtp_host');
            $smtp_port = Impostazione::get('smtp_port', 587);
            $smtp_username = Impostazione::get('smtp_username');
            $smtp_password = Impostazione::get('smtp_password');
            $smtp_encryption = Impostazione::get('smtp_encryption', 'tls');
            $smtp_from_address = Impostazione::get('smtp_from_address');
            $smtp_from_name = Impostazione::get('smtp_from_name', 'MA.GIA DONNA');

            // Applica configurazione runtime
            Config::set('mail.mailers.smtp.host', $smtp_host);
            Config::set('mail.mailers.smtp.port', $smtp_port);
            Config::set('mail.mailers.smtp.username', $smtp_username);
            Config::set('mail.mailers.smtp.password', $smtp_password);
            Config::set('mail.mailers.smtp.encryption', $smtp_encryption);
            Config::set('mail.from.address', $smtp_from_address);
            Config::set('mail.from.name', $smtp_from_name);

            return true;

        } catch (\Exception $e) {
            Log::error('Errore caricamento configurazione SMTP: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Invia email utilizzando un template
     *
     * @param string|int $tipo_template_o_id  Tipo del template (es: 'benvenuto') o ID del template
     * @param string $destinatario_email  Email destinatario
     * @param array $variabili  Variabili da sostituire nel template
     * @param array $allegati  Array di path file da allegare (opzionale)
     * @return bool  True se inviata con successo
     */
    public function inviaConTemplate($tipo_template_o_id, $destinatario_email, $variabili = [], $allegati = [])
    {
        try {
            // Carica configurazione SMTP
            if (!$this->caricaConfigurazioneSmtp()) {
                throw new \Exception('Configurazione SMTP non disponibile');
            }

            // Trova template: se è numero è ID, altrimenti è tipo
            if (is_numeric($tipo_template_o_id)) {
                $template = TemplateEmail::find($tipo_template_o_id);
            } else {
                $template = TemplateEmail::attivi()
                    ->perTipo($tipo_template_o_id)
                    ->first();
            }

            if (!$template) {
                throw new \Exception("Template email '{$tipo_template_o_id}' non trovato");
            }

            // Renderizza template con variabili
            $email_data = $template->renderizza($variabili);

            // Mittente personalizzato o default
            $from_email = $template->mittente_email ?: Config::get('mail.from.address');
            $from_name = $template->mittente_nome ?: Config::get('mail.from.name');

            // Invia email
            Mail::send([], [], function ($message) use ($email_data, $destinatario_email, $from_email, $from_name, $allegati) {
                $message->to($destinatario_email)
                        ->from($from_email, $from_name)
                        ->subject($email_data['oggetto'])
                        ->html($email_data['corpo_html'])
                        ->text($email_data['corpo_text']);

                // Aggiungi allegati se presenti
                foreach ($allegati as $allegato) {
                    if (file_exists($allegato)) {
                        $message->attach($allegato);
                    }
                }
            });

            Log::info("Email '{$tipo_template}' inviata a {$destinatario_email}");

            return true;

        } catch (\Exception $e) {
            Log::error("Errore invio email: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Invia email di benvenuto
     */
    public function inviaBenvenuto($cliente, $password_temporanea = null)
    {
        $variabili = [
            'nome_cliente' => $cliente->nome,
            'cognome_cliente' => $cliente->cognome,
            'nome_completo' => $cliente->nome_completo,
            'email_cliente' => $cliente->email,
            'codice_cliente' => $cliente->codice_cliente,
            'password_temporanea' => $password_temporanea ?: '****',
            'link_accesso' => route('cliente.login'),
            'username' => $cliente->email,
            'data_oggi' => now()->format('d/m/Y'),
            'anno_corrente' => now()->year,
        ];

        return $this->inviaConTemplate('benvenuto', $cliente->email, $variabili);
    }

    /**
     * Invia email con programma personalizzato
     */
    public function inviaProgramma($cliente, $programma)
    {
        $variabili = [
            'nome_cliente' => $cliente->nome,
            'cognome_cliente' => $cliente->cognome,
            'nome_completo' => $cliente->nome_completo,
            'email_cliente' => $cliente->email,
            'codice_cliente' => $cliente->codice_cliente,
            'nome_programma' => $programma->nome ?? 'Programma Personalizzato',
            'descrizione_programma' => $programma->descrizione ?? '',
            'data_inizio' => $cliente->inizio_programma ? $cliente->inizio_programma->format('d/m/Y') : '',
            'data_fine' => $cliente->fine_programma ? $cliente->fine_programma->format('d/m/Y') : '',
            'data_oggi' => now()->format('d/m/Y'),
            'anno_corrente' => now()->year,
        ];

        return $this->inviaConTemplate('programma', $cliente->email, $variabili);
    }

    /**
     * Invia promemoria lezione (24h prima)
     */
    public function inviaPromemoriaLezione($cliente, $lezione, $ore_prima = 24)
    {
        $tipo = $ore_prima == 24 ? 'promemoria_24h' : 'promemoria_2h';

        $variabili = [
            'nome_cliente' => $cliente->nome,
            'cognome_cliente' => $cliente->cognome,
            'nome_completo' => $cliente->nome_completo,
            'email_cliente' => $cliente->email,
            'nome_lezione' => $lezione->titolo,
            'data_lezione' => $lezione->data->format('d/m/Y'),
            'ora_inizio' => substr($lezione->ora_inizio, 0, 5),
            'nome_sede' => $lezione->sede->nome ?? '',
            'indirizzo_sede' => $lezione->sede->indirizzo_completo ?? '',
            'data_oggi' => now()->format('d/m/Y'),
            'anno_corrente' => now()->year,
        ];

        return $this->inviaConTemplate($tipo, $cliente->email, $variabili);
    }

    /**
     * Invia conferma pagamento
     */
    public function inviaConfermaPagamento($cliente, $pagamento)
    {
        $variabili = [
            'nome_cliente' => $cliente->nome,
            'cognome_cliente' => $cliente->cognome,
            'nome_completo' => $cliente->nome_completo,
            'email_cliente' => $cliente->email,
            'importo' => number_format($pagamento->importo, 2, ',', '.') . ' €',
            'metodo_pagamento' => ucfirst($pagamento->metodo_pagamento),
            'data_pagamento' => $pagamento->data_pagamento->format('d/m/Y'),
            'numero_ricevuta' => $pagamento->numero_ricevuta ?? $pagamento->id,
            'data_oggi' => now()->format('d/m/Y'),
            'anno_corrente' => now()->year,
        ];

        return $this->inviaConTemplate('conferma_pagamento', $cliente->email, $variabili);
    }

    /**
     * Invia allerta scadenza certificato medico
     */
    public function inviaAllertaCertificato($cliente)
    {
        if (!$cliente->certificato_scadenza) {
            return false;
        }

        $giorni_rimanenti = now()->diffInDays($cliente->certificato_scadenza);

        $variabili = [
            'nome_cliente' => $cliente->nome,
            'cognome_cliente' => $cliente->cognome,
            'nome_completo' => $cliente->nome_completo,
            'email_cliente' => $cliente->email,
            'data_scadenza' => $cliente->certificato_scadenza->format('d/m/Y'),
            'giorni_rimanenti' => $giorni_rimanenti,
            'data_oggi' => now()->format('d/m/Y'),
            'anno_corrente' => now()->year,
        ];

        return $this->inviaConTemplate('scadenza_certificato', $cliente->email, $variabili);
    }

    /**
     * Invia auguri compleanno
     */
    public function inviaAuguriCompleanno($cliente)
    {
        $variabili = [
            'nome_cliente' => $cliente->nome,
            'cognome_cliente' => $cliente->cognome,
            'nome_completo' => $cliente->nome_completo,
            'email_cliente' => $cliente->email,
            'eta' => $cliente->eta,
            'codice_sconto' => 'COMPLEANNO' . now()->year,
            'data_oggi' => now()->format('d/m/Y'),
            'anno_corrente' => now()->year,
        ];

        return $this->inviaConTemplate('auguri_compleanno', $cliente->email, $variabili);
    }

    /**
     * Invia email personalizzata
     */
    public function inviaEmail($destinatario, $oggetto, $corpo_html, $corpo_text = '', $allegati = [])
    {
        try {
            // Carica configurazione SMTP
            if (!$this->caricaConfigurazioneSmtp()) {
                throw new \Exception('Configurazione SMTP non disponibile');
            }

            $from_email = Config::get('mail.from.address');
            $from_name = Config::get('mail.from.name');

            Mail::send([], [], function ($message) use ($destinatario, $oggetto, $corpo_html, $corpo_text, $from_email, $from_name, $allegati) {
                $message->to($destinatario)
                        ->from($from_email, $from_name)
                        ->subject($oggetto)
                        ->html($corpo_html);

                if ($corpo_text) {
                    $message->text($corpo_text);
                }

                foreach ($allegati as $allegato) {
                    if (file_exists($allegato)) {
                        $message->attach($allegato);
                    }
                }
            });

            Log::info("Email personalizzata inviata a {$destinatario}");

            return true;

        } catch (\Exception $e) {
            Log::error("Errore invio email personalizzata: " . $e->getMessage());
            return false;
        }
    }
}
