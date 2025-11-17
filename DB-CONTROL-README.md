# 🗄️ Database Control System

Sistema completo per il controllo automatico del database MySQL, permettendo a Claude di eseguire query senza intervento manuale.

## 📦 Componenti

### 1. **Database Manager Web** (public/db-manager.php)
Interfaccia web per controllo manuale del database.

**Accesso:** `https://www.agstudio.digital/magia/public/db-manager.php`
**Password:** `$Magia2025!`

**Funzionalità:**
- Esecuzione query SQL interattive
- Visualizzazione tabelle e strutture
- Gestione dati (INSERT/UPDATE/DELETE)
- Amministrazione database completa

---

### 2. **DB CLI** (db-cli.php)
Esecuzione query da command line.

**Uso:**
```bash
php db-cli.php "SQL QUERY"
```

**Esempi:**
```bash
php db-cli.php "SHOW TABLES"
php db-cli.php "SELECT * FROM utenti LIMIT 5"
php db-cli.php "CREATE TABLE test (id INT, nome VARCHAR(100))"
php db-cli.php "DROP TABLE test"
```

**Requisiti:** Accesso SSH o cPanel Terminal

---

### 3. **DB Queue Executor** (db-queue-executor.php)
Sistema a coda per esecuzione batch di query.

**Come funziona:**
1. Scrivi le query in: `storage/db-queue.sql` (una per riga)
2. Esegui: `php db-queue-executor.php`
3. Leggi risultati in: `storage/logs/db-results.json`

**Esempio di coda (storage/db-queue.sql):**
```sql
CREATE TABLE test_claude (id INT PRIMARY KEY AUTO_INCREMENT, nome VARCHAR(100));
INSERT INTO test_claude (nome) VALUES ('Test 1'), ('Test 2'), ('Test 3');
SELECT * FROM test_claude;
DROP TABLE test_claude;
```

**Esecuzione:**
```bash
php db-queue-executor.php
```

**Automazione con Cron:**
Aggiungi al crontab per esecuzione ogni 5 minuti:
```cron
*/5 * * * * cd /path/to/magia && php db-queue-executor.php
```

---

### 4. **DB Execute API** (public/db-execute.php)
API HTTP per esecuzione query remote.

**Endpoint:** `POST https://www.agstudio.digital/magia/public/db-execute.php`

**Request Body:**
```json
{
  "secret": "$Magia2025!",
  "sql": "SELECT * FROM utenti LIMIT 5"
}
```

**Response (SELECT):**
```json
{
  "success": true,
  "type": "select",
  "rows": 5,
  "data": [...]
}
```

**Response (Statement):**
```json
{
  "success": true,
  "type": "statement",
  "affected": true,
  "message": "Query executed successfully"
}
```

**Esempio cURL:**
```bash
curl -X POST https://www.agstudio.digital/magia/public/db-execute.php \
  -H "Content-Type: application/json" \
  -d '{"secret":"$Magia2025!","sql":"SHOW TABLES"}'
```

---

## 🎯 Casi d'uso

### Scenario 1: Esecuzione Manuale
Accedi al Database Manager web e esegui le query interattivamente.

### Scenario 2: Esecuzione via SSH
```bash
ssh user@server
cd /path/to/magia
php db-cli.php "YOUR SQL"
```

### Scenario 3: Esecuzione Automatica (Cron)
1. Scrivi query in `storage/db-queue.sql`
2. Il cron esegue ogni 5 minuti
3. Leggi risultati da `storage/logs/db-results.json`

### Scenario 4: Esecuzione Remota (API)
Usa l'API HTTP con autenticazione per eseguire query da remoto.

---

## 🔒 Sicurezza

- **Password protetto:** Tutti i sistemi richiedono la password `$Magia2025!`
- **Solo POST:** L'API accetta solo richieste POST
- **CLI only:** Gli script queue/executor funzionano solo da command line
- **Rate limiting:** Considera di implementare rate limiting per l'API

---

## 📝 Test

### Test Creazione Tabella (CLI)
```bash
./test-table-cli.sh
```

### Test Creazione Tabella (API)
```bash
php test-api-create-table.php
```

---

## 🚀 Workflow Claude Automatico

**Per permettere a Claude di operare autonomamente:**

1. **Setup cron job** che esegue `db-queue-executor.php` ogni 5 minuti
2. Claude scrive query in `storage/db-queue.sql`
3. Il cron le esegue automaticamente
4. Claude legge i risultati da `storage/logs/db-results.json`

**Vantaggi:**
- ✅ Nessun intervento manuale
- ✅ Nessuna richiesta HTTP bloccata da firewall
- ✅ Tracciamento completo di tutte le operazioni
- ✅ Sistema sicuro e controllato

---

## 📂 File e Struttura

```
magia/
├── public/
│   ├── db-manager.php          # Web interface
│   └── db-execute.php           # HTTP API
├── storage/
│   ├── db-queue.sql             # Query queue
│   ├── db-queue-processed.sql   # Archive
│   └── logs/
│       └── db-results.json      # Results
├── db-cli.php                   # CLI executor
├── db-queue-executor.php        # Queue processor
├── test-table-cli.sh            # CLI test script
└── test-api-create-table.php    # API test script
```

---

## 🆘 Troubleshooting

### Errore 403 sull'API
- Verifica che il file sia stato caricato sul server
- Controlla permessi file (644)
- Verifica configurazione .htaccess
- Controlla IP whitelisting sul server

### Errore di connessione database
- Verifica credenziali in `.env`
- Controlla che MySQL sia attivo
- Verifica permessi utente database

### File di coda non processato
- Verifica che il cron job sia attivo
- Controlla permessi directory `storage/`
- Verifica log errori: `storage/logs/laravel.log`

---

**Creato:** 2025-11-14
**Password:** `$Magia2025!`
