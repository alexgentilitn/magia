# Sistema Email SMTP - Guida Setup

## Panoramica

Il sistema email di AGstudio CRM supporta:
- ✉️ Configurazione SMTP personalizzabile
- 📧 Template HTML con editor WYSIWYG (TinyMCE)
- 🧪 Test invio email
- 🔄 Coda email con retry automatico
- 📝 Variabili dinamiche nei template

## Installazione PHPMailer

Il sistema richiede PHPMailer per l'invio delle email via SMTP.

### Opzione 1: Composer (Consigliato)

```bash
cd /home/user/ea
composer require phpmailer/phpmailer
```

### Opzione 2: Download Manuale

1. Scarica PHPMailer: https://github.com/PHPMailer/PHPMailer/releases
2. Estrai nella cartella `PHPMailer/` del progetto
3. Assicurati che esistano questi file:
   - `PHPMailer/PHPMailer.php`
   - `PHPMailer/SMTP.php`
   - `PHPMailer/Exception.php`

## Configurazione Database

1. Accedi a: `https://www.agstudio.digital/magia/update_database.php`
2. Esegui lo script per creare le tabelle:
   - `smtp_config` - Configurazione server SMTP
   - `email_templates` - Template HTML personalizzabili

## Configurazione SMTP

### 1. Accedi alla pagina di configurazione

`https://www.agstudio.digital/magia/smtp_settings.php` (Solo Admin)

### 2. Compila i parametri SMTP

**Per Gmail:**
- Server: `smtp.gmail.com`
- Porta: `587`
- Crittografia: `TLS`
- Username: La tua email Gmail
- Password: Devi usare una **App Password** (non la password Gmail normale)
  - Vai su: https://myaccount.google.com/apppasswords
  - Genera una App Password per "Mail"
  - Usa quella password nel campo SMTP Password

**Per Outlook/Office 365:**
- Server: `smtp.office365.com`
- Porta: `587`
- Crittografia: `TLS`
- Username: La tua email Outlook
- Password: Password del tuo account Microsoft

### 3. Test Configurazione

1. Inserisci un indirizzo email di test
2. Clicca "Invia Email di Test"
3. Controlla la casella di posta (anche spam)
4. Se ricevi l'email, la configurazione è corretta ✅

## Gestione Template Email

### Accesso Template

`https://www.agstudio.digital/magia/email_templates.php` (Solo Admin)

### Creazione Template

1. Clicca "Nuovo Template"
2. Compila i campi:
   - **Nome**: Identificatore univoco (es: `alert_scadenza`)
   - **Oggetto**: Oggetto dell'email (puoi usare variabili)
   - **Descrizione**: Breve descrizione del template
   - **Corpo HTML**: Contenuto HTML usando l'editor WYSIWYG

### Editor WYSIWYG (TinyMCE)

L'editor include:
- Formattazione testo (grassetto, corsivo, colori)
- Liste puntate e numerate
- Tabelle
- Immagini
- Link
- Codice HTML
- **Menu Variabili**: Inserimento veloce delle variabili

### Variabili Disponibili

Usa queste variabili nei template - verranno sostituite automaticamente:

- `{{titolo}}` - Titolo dell'alert/scadenza
- `{{corpo}}` - Corpo del messaggio
- `{{progetto}}` - Nome del progetto
- `{{cliente}}` - Nome del cliente
- `{{data_scadenza}}` - Data della scadenza
- `{{priorita}}` - Priorità (bassa/media/alta)
- `{{stato}}` - Stato (in_attesa/completata/cancellata)

### Template Predefinito

Il sistema include un template base `alert_scadenza` con:
- Header blu professionale
- Contenuto responsive
- Footer automatico
- Stili inline per compatibilità email

## Utilizzo nel Codice

### Invia Email con Template

```php
require_once 'send_email_helper.php';

$variabili = [
    'titolo' => 'Promemoria Scadenza',
    'corpo' => 'La scadenza del progetto X è prevista per domani',
    'progetto' => 'Sito Web ABC',
    'cliente' => 'Cliente SRL',
    'data_scadenza' => '15/01/2025'
];

$success = sendEmailWithTemplate(
    'cliente@example.com',
    'alert_scadenza',
    $variabili
);
```

### Invia Email Semplice (senza template)

```php
require_once 'send_email_helper.php';

$html = '<h1>Ciao!</h1><p>Questo è un messaggio di test.</p>';

$success = sendSimpleEmail(
    'destinatario@example.com',
    'Oggetto Email',
    $html
);
```

### Aggiungi Email alla Coda

Se PHPMailer non è disponibile o SMTP fallisce, le email vengono automaticamente aggiunte alla coda:

```php
queueEmail(
    'destinatario@example.com',
    'Oggetto',
    'Corpo HTML',
    'tipo_alert'
);
```

### Processa Coda Email

Esegui periodicamente (via cron o manualmente):

```php
require_once 'send_email_helper.php';
$result = processEmailQueue(10); // Invia max 10 email dalla coda

echo "Inviate: {$result['sent']}\n";
echo "Fallite: {$result['failed']}\n";
```

## Sistema Alert Automatici

Il file `check_alerts.php` è configurato per inviare automaticamente email per:

1. **Scadenze Imminenti** - X giorni prima della scadenza
2. **Scadenze Scadute** - Dopo la data di scadenza
3. **Progetti in Ritardo** - Progetti oltre la data di consegna

### Configurazione Cron

Aggiungi al crontab per esecuzione automatica ogni ora:

```bash
0 * * * * /usr/bin/php /percorso/completo/check_alerts.php
```

Oppure ogni 30 minuti:

```bash
*/30 * * * * /usr/bin/php /percorso/completo/check_alerts.php
```

## Troubleshooting

### Email non arrivano

1. **Verifica SMTP è attivo**: Vai su `smtp_settings.php`, assicurati che "Abilita invio email" sia spuntato
2. **Test configurazione**: Usa il pulsante "Invia Email di Test"
3. **Controlla spam**: Le prime email potrebbero finire in spam
4. **Verifica credenziali**: Ricontrolla username, password e porta
5. **Gmail**: Assicurati di usare App Password, non la password normale
6. **Log errori**: Controlla `logs/alerts.log` per messaggi di errore

### PHPMailer non trovato

```bash
# Via Composer
composer require phpmailer/phpmailer

# Oppure download manuale
wget https://github.com/PHPMailer/PHPMailer/archive/refs/tags/v6.8.1.zip
unzip v6.8.1.zip
mv PHPMailer-6.8.1/src PHPMailer
```

### Email in coda non vengono inviate

Le email in coda richiedono esecuzione manuale di `processEmailQueue()` o uno script cron dedicato.

Crea file `process_queue.php`:

```php
<?php
require_once 'config.php';
require_once 'send_email_helper.php';

$result = processEmailQueue(50);
echo "✓ Processate: {$result['sent']} inviate, {$result['failed']} fallite\n";
?>
```

Aggiungi al cron:

```bash
*/15 * * * * /usr/bin/php /path/to/process_queue.php
```

## Best Practices

1. **Test prima di produzione**: Usa sempre il test email prima di attivare alert automatici
2. **Monitoraggio**: Controlla regolarmente `email_queue` per email in coda
3. **Template puliti**: Usa HTML semplice e inline CSS per massima compatibilità
4. **Variabili**: Documenta sempre le variabili disponibili nei template
5. **Retry limit**: Email con 3+ tentativi falliti vanno verificate manualmente

## Sicurezza

- ⚠️ **Non committare credenziali SMTP** in git
- ⚠️ Password SMTP sono salvate in chiaro nel database - proteggi il DB
- ⚠️ Usa HTTPS per accedere alle pagine di configurazione
- ⚠️ Solo account Admin possono modificare SMTP e template

## Supporto

Per problemi o domande:
- Controlla i log: `logs/alerts.log`
- Verifica database: tabelle `smtp_config`, `email_templates`, `email_queue`
- Test SMTP esterno: https://www.gmass.co/smtp-test
