# Istruzioni per Eseguire le Migrations Database

## Opzione 1: Script Interattivo (Consigliato)

Lo script interattivo mostra lo stato delle migrations e chiede conferma prima di procedere:

```bash
php run-migrations.php
```

Questo script:
- ✅ Mostra lo stato attuale delle migrations
- ✅ Elenca le modifiche che verranno applicate
- ✅ Chiede conferma prima di procedere
- ✅ Fornisce feedback dettagliato
- ✅ Suggerisce soluzioni in caso di errori

---

## Opzione 2: Script Rapido

Per eseguire direttamente senza conferma:

```bash
php migrate.php
```

Questo script esegue immediatamente le migrations senza chiedere conferma.

---

## Opzione 3: Comando Artisan (Standard Laravel)

Se preferisci usare il comando Laravel standard:

```bash
php artisan migrate
```

Oppure, per ambienti di produzione:

```bash
php artisan migrate --force
```

---

## Cosa Fanno le Migrations?

### Migration 1: `add_privacy_fields_to_clienti_table`
Aggiunge **10 campi** per la gestione GDPR:
- `privacy_accettata` + `privacy_accettata_at`
- `termini_accettati` + `termini_accettati_at`
- `marketing_accettato` + `marketing_accettato_at`
- `consenso_dati_sensibili` + `consenso_dati_sensibili_at`
- `ip_registrazione`
- `note_consensi`

### Migration 2: `add_custom_fields_to_clienti_table`
Aggiunge **25 campi** per anagrafica estesa:

**Dati Anagrafici:**
- `data_nascita`, `eta`, `sesso`

**Obiettivi:**
- `obiettivi_personali`, `livello_attivita`

**Dati Medici:**
- `note_mediche`, `allergie_intolleranze`, `patologie`, `farmaci_assunti`, `certificato_medico_presente`

**Alimentazione:**
- `preferenze_alimentari`, `regime_alimentare`, `cibi_da_evitare`

**Parametri Corporei:**
- `peso`, `altezza`, `bmi`

**Circonferenze:**
- `circonferenza_vita`, `circonferenza_fianchi`, `circonferenza_petto`
- `circonferenza_braccio_dx`, `circonferenza_braccio_sx`
- `circonferenza_coscia_dx`, `circonferenza_coscia_sx`

**Timestamp:**
- `ultima_pesata`, `ultima_misurazione`

**Note:**
- `note_professionista`

---

## Verifica Post-Esecuzione

Dopo aver eseguito le migrations, verifica che tutto sia andato a buon fine:

```bash
php artisan migrate:status
```

Dovresti vedere tutte le migrations con stato "Ran".

---

## Rollback (Se Necessario)

Se qualcosa va storto e vuoi annullare le ultime migrations:

```bash
php artisan migrate:rollback
```

Questo rimuoverà tutti i campi aggiunti dalle 2 nuove migrations.

---

## Backup Database (IMPORTANTE!)

⚠️ **Prima di eseguire in produzione**, crea sempre un backup del database:

```bash
# MySQL/MariaDB
mysqldump -u username -p database_name > backup_$(date +%Y%m%d_%H%M%S).sql

# Oppure usa il tuo pannello di hosting (phpMyAdmin, cPanel, etc.)
```

---

## Risoluzione Problemi

### Errore: "Access denied for user"
- Verifica le credenziali database in `.env`
- Controlla che l'utente abbia i permessi corretti

### Errore: "Database not found"
- Crea il database prima di eseguire le migrations
- Verifica il nome del database in `.env`

### Errore: "Syntax error or access violation: 1071"
- Probabilmente stai usando una versione vecchia di MySQL
- Verifica che MySQL sia >= 5.7 o MariaDB >= 10.2

### Errore: "SQLSTATE[HY000] [2002] Connection refused"
- Il server MySQL non è in esecuzione
- Verifica l'host e la porta in `.env`

---

## Supporto

Per problemi o domande, contatta il team di sviluppo.
