# 🧠 MEMORIA PROGETTO - MA.GIA DONNA

**Documento di Memoria Persistente per Claude Code**

Questo file contiene TUTTO il contesto necessario per continuare a lavorare su questo progetto in qualsiasi sessione futura di Claude Code.

---

## 📌 INFORMAZIONI ESSENZIALI

### Progetto
- **Nome:** MA.GIA DONNA - Web Application per Centro Wellness/Fitness
- **Tipo:** Applicazione Laravel per gestione clienti, lezioni, programmi, pagamenti
- **Cliente:** AGstudio Digital
- **Repository:** https://github.com/alexgentilitn/magia
- **Sito Produzione:** https://www.agstudio.digital/magia/public/

### Branch Strategy

⚠️ **ATTENZIONE - BRANCH POLICY CRITICA:**

**Branch di lavoro UFFICIALE:** `claude/Magia_Brench-01G4cTM33nQqZ3K3UtX3NGqm`

**TUTTE le modifiche vanno fatte SOLO ed ESCLUSIVAMENTE su questo branch!**

**Regole branch:**
- ✅ **Usare SEMPRE:** `claude/Magia_Brench-01G4cTM33nQqZ3K3UtX3NGqm`
- ❌ **NON pushare su:** `magia-brench` (errore 403 - branch di riferimento read-only)
- ❌ **NON pushare su:** `main` (produzione stabile)
- ⚠️ **Pattern obbligatorio:** Branch deve iniziare con `claude/` e terminare con session ID `-01G4cTM33nQqZ3K3UtX3NGqm`

**Motivo:** GitHub Actions è configurato per accettare push SOLO da branch `claude/**` che terminano con il session ID corretto. Qualsiasi push su altri branch fallirà con errore HTTP 403.

### Date Importanti
- **Setup iniziale:** 14 Novembre 2025
- **Ultimo aggiornamento:** 16 Novembre 2025
- **Sessione corrente:** review-technical-docs-01G4cTM33nQqZ3K3UtX3NGqm
- **Branch corrente:** claude/Magia_Brench-01G4cTM33nQqZ3K3UtX3NGqm

---

## 🛠️ STACK TECNOLOGICO

### Backend
- **Framework:** Laravel 10.x
- **PHP Version:** 8.4.14
- **Composer:** 2.8.12
- **Database:** MariaDB/MySQL 5.7.44-log-cll-lve

### Frontend
- **CSS Framework:** Tailwind CSS 3.x (via CDN)
- **JS Framework:** Alpine.js 3.x (via CDN)
- **Librerie UI:** SweetAlert2 11, Font Awesome 6.4.0
- **Build:** Nessun build process (tutto via CDN)

### Hosting
- **Provider:** Aruba Hosting Condiviso
- **Tipo:** Shared hosting SENZA accesso SSH
- **Server Web:** Apache/LiteSpeed
- **PHP Handler:** Probabilmente LSAPI o FastCGI

### Deploy e CI/CD

**Sistema:** GitHub Actions → Deploy automatico FTP
**Workflow File:** `.github/workflows/deploy.yml`

---

## 🚀 DEPLOY AUTOMATICO - GITHUB ACTIONS

### ⚠️ CONFIGURAZIONE CRITICA

**Branch abilitati per deploy automatico:**
```yaml
branches:
  - claude/fix-github-ftp-push-01QcEnaTAL1KMQVP9iPVzptc  # Branch precedente
  - claude/Magia_Brench-01G4cTM33nQqZ3K3UtX3NGqm        # Branch CORRENTE ⭐
```

**‼️ IMPORTANTE:**
- Solo i branch sopra elencati triggherano il deploy FTP automatico
- Push su altri branch NON verranno deployati su produzione
- Per aggiungere un nuovo branch, modificare `.github/workflows/deploy.yml`

### Come Funziona il Deploy

**Trigger:** Push su branch abilitati
**Tempo:** 2-5 minuti dall'ultimo push
**Destinazione:** FTP Aruba → `/www.agstudio.digital/magia/`

**Processo:**
1. Push codice su branch `claude/Magia_Brench-01G4cTM33nQqZ3K3UtX3NGqm`
2. GitHub Actions rileva il push
3. Workflow `.github/workflows/deploy.yml` parte
4. Checkout del codice
5. Upload FTP su Aruba (via `SamKirkland/FTP-Deploy-Action@v4.3.5`)
6. File disponibili su produzione

### File Esclusi dal Deploy

Il workflow esclude automaticamente:
```yaml
exclude: |
  **/.git*
  **/.git*/**
  **/node_modules/**
  .env                # ⚠️ Mai deployato (già presente su server)
  .env.example
  README.md
  .github/**
```

**⚠️ .env NON viene mai deployato:**
- Il file `.env` è già presente sul server (deployato manualmente UNA VOLTA)
- Modifiche al `.env` vanno fatte direttamente sul server via FTP
- NON committare mai `.env` nel repository

### Secrets GitHub Actions

**Configurati in:** `Settings → Secrets and variables → Actions`

Secrets necessari:
```
FTP_HOST      = ftp.agstudio.digital
FTP_USER      = agstudiodiital@agstudio.digital
FTP_PASSWORD  = (password FTP - vedi documentazione riservata)
FTP_PATH      = /www.agstudio.digital/magia
```

### Verifica Deploy

**Dopo ogni push, verifica che il deploy sia andato a buon fine:**

1. **GitHub Actions Dashboard:**
   ```
   https://github.com/alexgentilitn/magia/actions
   ```
   - Verifica che il workflow sia ✅ verde (success)
   - Se ❌ rosso (failed), leggi i log per vedere l'errore

2. **Timing:**
   - Deploy inizia entro 30 secondi dal push
   - Upload FTP: 2-4 minuti
   - File disponibili sul server: max 5 minuti

3. **Verifica File Deployati:**
   - Controlla che i file modificati siano presenti su produzione
   - Esempio: se hai creato `public/nuovo-file.php`, verifica:
     ```
     https://www.agstudio.digital/magia/public/nuovo-file.php
     ```

### Troubleshooting Deploy

**Problema: Workflow non parte dopo push**
- Verifica che il branch sia nella lista dei branch abilitati in `.github/workflows/deploy.yml`
- Controlla di aver fatto push sul branch corretto

**Problema: Workflow fallisce (❌ rosso)**
- Vai su https://github.com/alexgentilitn/magia/actions
- Clicca sul workflow fallito
- Leggi i log per identificare l'errore
- Errori comuni:
  - Credenziali FTP errate (controlla Secrets)
  - Permessi FTP insufficienti
  - Timeout connessione FTP

**Problema: Deploy completato ma file non presente**
- Verifica che il file non sia nell'exclude list del workflow
- Controlla che il path FTP sia corretto (`FTP_PATH` secret)
- Verifica permessi file/cartelle sul server

### Deploy Manuale (Workflow Dispatch)

È possibile triggerare un deploy manuale:

1. Vai su: https://github.com/alexgentilitn/magia/actions
2. Seleziona workflow "Deploy to Aruba FTP"
3. Click "Run workflow"
4. Seleziona branch
5. Click "Run workflow"

**Uso:** Per ri-deployare senza fare un nuovo commit.

### Best Practices Deploy

✅ **DO:**
- Commit solo codice funzionante e testato
- Verificare sempre che il deploy sia completato con successo
- Testare modifiche su produzione dopo ogni deploy
- Usare messaggi di commit descrittivi

❌ **DON'T:**
- Pushare codice con errori syntax
- Committare `.env` o credenziali
- Fare push su branch non configurati
- Deployare senza verificare il risultato

---

## 🗄️ CONFIGURAZIONE DATABASE

### Credenziali Produzione
```
Host:     localhost
Porta:    3306
Database: agstudiodiital_magia
Username: agstudiodiital_agstudiomagia
Password: $Magia2015!
```

**⚠️ IMPORTANTE:**
- Usare `localhost` (NON `127.0.0.1`) → socket Unix più veloce
- Username COMPLETO è `agstudiodiital_agstudiomagia`
- Password contiene caratteri speciali: `$Magia2015!`

### Struttura Database

**Tabelle Principali:**
- `utenti` - Utenti sistema (admin, professionisti, clienti)
- `clienti` - Anagrafica clienti donne
- `ruoli` - Ruoli utenti (Super Admin, Admin, Professionista, Cliente)
- `permessi` - Permessi granulari
- `lezioni` - Lezioni/sessioni allenamento
- `programmi` - Programmi wellness (Balla & Snella, Alimentazione, ecc.)
- `pagamenti` - Gestione pagamenti clienti
- `sedes` - Sedi fisiche
- `professionisti` - Istruttori/coach
- `cliente_lezione` - Relazione molti-a-molti clienti-lezioni
- `cliente_programma` - Relazione molti-a-molti clienti-programmi
- `professionista_sede` - Relazione molti-a-molti professionisti-sedi
- `log_attivita` - Log azioni utenti
- `migrations` - Tabella migrazioni Laravel

### Migrations Eseguite
Tutte le migrations sono state eseguite. Flag: `storage/migrations_executed.flag`

### Database JSON Locale (File-Based) 🆕

**Implementato:** 14 Novembre 2025

Per gestire dati locali senza dipendere da MySQL, è stato implementato un database JSON file-based:

**Libreria:** `jajo/jsondb` - Database puro PHP senza SQLite
- **Repository:** https://github.com/donjajo/php-jsondb
- **Requisiti:** Solo PHP (no estensioni)
- **Directory:** `database/jsondb/`
- **File tabelle:** `database/jsondb/*.json`

**Helper creato:** `App\Helpers\JsonDatabase`
- Metodi: all(), where(), find(), insert(), update(), delete(), count()
- Pattern: `JsonDatabase::all('tabella')`

**Model creato:** `App\Models\ClienteJson`
- CRUD completo con ID automatici e timestamps
- Metodi: all(), find(), create(), update(), delete(), search()
- Pattern: `ClienteJson::create(['nome' => 'Maria', ...])`

**Test disponibili:**
- `test-jsondb.php` - Test base libreria
- `test-json-helper.php` - Test helper Laravel
- `test-cliente-json.php` - Test Model CRUD completo

**Documentazione:** Vedi `GUIDA PROGETTO/DATABASE_JSON_LOCALE.md`

**⚠️ Limitazioni:**
- Ottimale per dataset piccoli (<1000 record)
- No relazioni, no transazioni ACID
- Backup: copia manuale file .json

---

## 🔧 GESTIONE DATABASE - MODUS OPERANDI

### Sistema di Controllo Database via File PHP

**Implementato:** 14-15 Novembre 2025

Per permettere a Claude di gestire il database MySQL in modo autonomo senza accesso diretto, è stato implementato un sistema basato su file PHP eseguibili via HTTP.

### 📋 Scripts Disponibili

#### 1. **Database Manager Web** (`public/db-manager.php`)
Interfaccia web completa per gestione manuale database.

**URL:** `https://www.agstudio.digital/magia/public/db-manager.php`
**Password:** `$Magia2025!`

**Funzionalità:**
- Esecuzione query SQL interattive
- Visualizzazione tabelle e strutture
- Gestione dati (INSERT/UPDATE/DELETE)
- Amministrazione completa database

**Uso:** Per operazioni manuali o verifiche rapide.

---

#### 2. **Trova Tabelle Test** (`public/trova-tabelle-test.php`)
Identifica tabelle di test/temporanee nel database.

**URL:** `https://www.agstudio.digital/magia/public/trova-tabelle-test.php?secret=$Magia2025!`

**Output:**
- Lista completa tabelle (divise in PROD e TEST)
- Identifica tabelle con pattern: test*, temp*, tmp*, demo*, backup*
- Riepilogo: N tabelle produzione vs N tabelle test

**Uso:** Prima di fare pulizia database, per identificare cosa eliminare.

---

#### 3. **Elimina Tabelle Test** (`public/elimina-tabelle-test.php`)
Elimina solo le tabelle identificate come test.

**URL:** `https://www.agstudio.digital/magia/public/elimina-tabelle-test.php?secret=$Magia2025!`

**Funzionalità:**
- Verifica esistenza tabelle prima di eliminare
- Mostra struttura e count record
- Elimina solo tabelle nella whitelist
- Report dettagliato operazioni

**⚠️ Sicurezza:** Le tabelle da eliminare sono hardcoded nello script, non c'è rischio di eliminare tabelle di produzione per errore.

**Uso:** Dopo aver identificato tabelle test con lo script precedente.

---

#### 4. **Verifica Risultati Cron** (`public/check-results.php`)
Visualizza risultati ultime query eseguite (se usato con sistema queue).

**URL:** `https://www.agstudio.digital/magia/public/check-results.php?secret=$Magia2025!`

**Output:**
- Timestamp ultima esecuzione
- Riepilogo comandi eseguiti
- Dettaglio risultati per ogni query
- Stato queue (query in attesa)

---

#### 5. **Test Diagnostica Cron** (`public/test-cron.php`)
Verifica configurazione e funzionamento sistema.

**URL:** `https://www.agstudio.digital/magia/public/test-cron.php?secret=$Magia2025!`

**Verifica:**
- File db-queue-run.php presente
- File db-queue.sql presente
- Directory storage/logs scrivibile
- Laravel bootstrap funzionante
- Connessione database OK

---

### 🎯 MODUS OPERANDI: Come Gestire il Database

**Principio fondamentale:**
> "Ogni volta che devi vedere/creare/modificare una tabella, crea un file PHP specifico per quella operazione."

#### Processo Standard

**Step 1: Analisi**
- Usa `trova-tabelle-test.php` per vedere stato attuale database
- Identifica quali tabelle esistono e quali sono di test

**Step 2: Creazione Script**
- Crea un nuovo file in `public/[operazione]-[descrizione].php`
- Includi sempre autenticazione con secret
- Bootstrap Laravel per usare DB facade
- Output testuale chiaro e formattato
- Gestione errori con try/catch

**Step 3: Deploy**
- Commit e push del nuovo script
- Attendi deploy GitHub Actions (30-60 secondi)

**Step 4: Esecuzione**
- Apri URL dello script via browser
- Copia output completo
- Verifica risultati

**Step 5: Verifica**
- Usa `trova-tabelle-test.php` o `db-manager.php` per confermare modifiche
- Documenta cosa è stato fatto

#### Template Script Database

```php
<?php
/**
 * NOME OPERAZIONE - Descrizione
 */

define('SECRET', '$Magia2025!');

if (!isset($_GET['secret']) || $_GET['secret'] !== SECRET) {
    http_response_code(401);
    die('❌ Unauthorized');
}

header('Content-Type: text/plain; charset=utf-8');

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "📝 TITOLO OPERAZIONE\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    // TUA LOGICA QUI
    $result = DB::select('SELECT * FROM tabella');

    echo "✅ Operazione completata\n";
    echo "Risultati: " . count($result) . "\n";

} catch (\Exception $e) {
    echo "❌ ERRORE: " . $e->getMessage() . "\n";
}
```

#### Esempi Pratici

**Esempio 1: Creare una nuova tabella**

File: `public/crea-tabella-nuova.php`

```php
DB::statement("
    CREATE TABLE nuova_tabella (
        id INT PRIMARY KEY AUTO_INCREMENT,
        nome VARCHAR(100),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )
");
echo "✅ Tabella creata\n";
```

**Esempio 2: Modificare struttura tabella**

File: `public/modifica-tabella-clienti.php`

```php
// Aggiungi colonna
DB::statement("ALTER TABLE clienti ADD COLUMN telefono_secondario VARCHAR(20)");
echo "✅ Colonna aggiunta\n";

// Verifica
$columns = DB::select("DESCRIBE clienti");
foreach ($columns as $col) {
    echo "- {$col->Field}\n";
}
```

**Esempio 3: Popolare dati di test**

File: `public/popola-dati-test.php`

```php
$data = [
    ['nome' => 'Test 1', 'email' => 'test1@example.com'],
    ['nome' => 'Test 2', 'email' => 'test2@example.com'],
];

foreach ($data as $record) {
    DB::table('utenti')->insert($record);
}

echo "✅ Inseriti " . count($data) . " record\n";
```

### ⚠️ Best Practices

**Sicurezza:**
- ✅ Sempre autenticazione con secret (`$Magia2025!`)
- ✅ Mai esporre credenziali database nell'output
- ✅ Validare input se lo script accetta parametri GET/POST
- ✅ Usare prepared statements per query dinamiche

**Organizzazione:**
- ✅ Nome file descrittivo: `[azione]-[cosa].php`
- ✅ Header chiaro con descrizione operazione
- ✅ Output formattato con emoji e separatori
- ✅ Try/catch per gestione errori

**Pulizia:**
- ✅ Dopo aver usato uno script, valuta se mantenerlo o eliminarlo
- ✅ Script di test possono essere eliminati dopo verifica
- ✅ Script di utility (trova-tabelle-test) vanno mantenuti

**Documentazione:**
- ✅ Aggiorna questo file dopo operazioni importanti
- ✅ Documenta modifiche struttura database
- ✅ Annota URL script creati e loro scopo

### 📊 Tabelle Database Produzione (Attuali)

**Totale:** 23 tabelle + 1 test (da eliminare: test_deployment)

**Tabelle Core:**
- `utenti` - Utenti sistema
- `clienti` - Anagrafica clienti
- `ruoli` - Ruoli utenti
- `permessi` - Permessi granulari

**Tabelle Funzionali:**
- `lezioni` - Lezioni/sessioni
- `programmi` - Programmi wellness
- `pagamenti` - Gestione pagamenti
- `sedi` / `sedes` - Sedi fisiche
- `professionisti` - Istruttori

**Tabelle Relazioni:**
- `cliente_lezione` - Molti-a-molti
- `cliente_programma` - Molti-a-molti
- `professionista_sede` - Molti-a-molti
- `ruolo_permesso` - Molti-a-molti
- `utente_permesso` - Molti-a-molti

**Tabelle Sistema:**
- `migrations` - Migrazioni Laravel
- `failed_jobs` - Job falliti
- `password_reset_tokens` - Reset password
- `personal_access_tokens` - API tokens
- `users` - Utenti Laravel (duplicato di utenti?)
- `log_attivita` - Log azioni
- `impostazioni` - Config app
- `impostazioni_sistema` - Config sistema

**Tabelle Test (da eliminare):**
- `test_deployment` ⚠️

### 🔄 Sistema Queue (Opzionale - Attualmente non attivo)

È stato implementato anche un sistema a queue per esecuzione batch di query SQL via cron, ma **attualmente non è in uso** per preferenza dell'utente.

**File disponibili ma non attivi:**
- `db-queue-executor.php` - CLI executor
- `public/db-queue-run.php` - HTTP endpoint per cron
- `storage/db-queue.sql` - File queue query

**Se si vuole riattivare:**
1. Configurare cron: `* * * * * curl -s "URL/db-queue-run.php?secret=$Magia2025!"`
2. Scrivere query in `storage/db-queue.sql`
3. Il cron esegue automaticamente ogni minuto
4. Risultati in `storage/logs/db-results.json`

**Motivo disattivazione:** L'utente preferisce approccio "un file PHP per operazione" più semplice e controllabile.

---

## 💾 SISTEMA BACKUP E RIPRISTINO

### Implementazione Backup Points (Codice + Database)

**Implementato:** 15 Novembre 2025
**Richiesta Utente:** Backup ogni ora di sviluppo per rollback sicuro

Il sistema permette di creare **Backup Points** completi che salvano lo stato di:
- ✅ **Codice** → Git tag su GitHub
- ✅ **Database** → Dump SQL locale in `storage/backups/`

### 🎯 Concetto: Backup Point

Un **Backup Point** è un punto di ripristino atomico che include:
1. **Tag Git** con nome `backup-YYYY-MM-DD_HH-MM-SS`
2. **Dump SQL** del database completo nello stesso momento
3. **Metadata** (tabelle, record count, dimensione file)

### 📋 Scripts Disponibili

#### 1. **Crea Backup Point** (`public/create-backup-point.php`) ⭐

**SCRIPT PRINCIPALE** - Usa sempre questo per creare backup completi.

**URL:** `https://www.agstudio.digital/magia/public/create-backup-point.php?secret=$Magia2025!`

**Cosa fa:**
1. Esporta tutte le tabelle del database in SQL
2. Salva in `storage/backups/db-backup-[timestamp].sql`
3. Fornisce i comandi Git per creare il tag
4. Fornisce le istruzioni per ripristino completo

**Output:**
```
📅 Timestamp: 15/11/2025 14:30:00
🏷️  Tag Git: backup-2025-11-15_14-30-00

Esportazione tabelle:
  [1/23] utenti... ✅ 150 record
  [2/23] clienti... ✅ 300 record
  ...

✅ Database esportato: 2.5 MB
   5000 record totali

COMANDI GIT:
git tag -a backup-2025-11-15_14-30-00 -m "Backup point: ..."
git push origin backup-2025-11-15_14-30-00
```

**Quando usare:**
- ✅ Ogni ora di sviluppo intenso
- ✅ Prima di modifiche rischiose (migrazioni, refactoring)
- ✅ Prima di merge/release importanti
- ✅ Fine giornata lavorativa

---

#### 2. **Backup Solo Database** (`public/backup-database.php`)

**URL:** `https://www.agstudio.digital/magia/public/backup-database.php?secret=$Magia2025!`

**Cosa fa:**
- Esporta solo il database (senza creare tag Git)
- Utile per backup rapidi senza committare codice

**Output:**
- File `storage/backups/db-backup-[timestamp].sql`
- Statistiche (tabelle, record, dimensione)

---

#### 3. **Ripristina Database** (`public/restore-database.php`)

**URL (Lista backups):**
`https://www.agstudio.digital/magia/public/restore-database.php?secret=$Magia2025!`

**URL (Ripristina specifico):**
`https://www.agstudio.digital/magia/public/restore-database.php?secret=$Magia2025!&file=db-backup-[timestamp].sql&confirm=YES`

**⚠️ ATTENZIONE:**
- Elimina TUTTE le tabelle attuali
- Sovrascrive TUTTI i dati
- NON reversibile

**Sicurezza:**
- Richiede parametro `confirm=YES` esplicito
- Mostra warning chiari prima dell'esecuzione
- Lista tutti i backup disponibili se nessun file specificato

**Output:**
```
⚠️  ATTENZIONE - OPERAZIONE PERICOLOSA!

Stai per ripristinare:
  📁 File: db-backup-2025-11-15_14-30-00.sql
  📅 Data backup: 15/11/2025 14:30:00
  💾 Dimensione: 2.5 MB

⚠️  Questa operazione:
  ❌ Eliminerà TUTTE le tabelle attuali
  ❌ Sovrascriverà TUTTI i dati
  ❌ NON può essere annullata
```

---

### 🔄 PROCEDURA: Creare un Backup Point Completo

**Step 1: Esegui lo script di backup**
```bash
# Via browser o curl
curl "https://www.agstudio.digital/magia/public/create-backup-point.php?secret=\$Magia2025!"
```

**Step 2: Copia i comandi Git dall'output**
```bash
git tag -a backup-2025-11-15_14-30-00 -m "Backup point: 2025-11-15_14-30-00 - 23 tables, 5000 records"
git push origin backup-2025-11-15_14-30-00
```

**Step 3: Verifica creazione**
```bash
# Lista tutti i backup points
git tag -l 'backup-*'

# Dettagli backup specifico
git show backup-2025-11-15_14-30-00
```

**✅ Backup Point creato!**
Ora hai un punto di ripristino completo:
- 🏷️ Tag Git: `backup-2025-11-15_14-30-00`
- 💾 Dump SQL: `storage/backups/db-backup-2025-11-15_14-30-00.sql`

---

### ♻️ PROCEDURA: Ripristinare da Backup Point

**Scenario:** Qualcosa è andato storto, devi tornare indietro.

**Step 1: Identifica il backup point da ripristinare**
```bash
# Lista tutti i backup disponibili
git tag -l 'backup-*'

# Output:
# backup-2025-11-15_10-00-00
# backup-2025-11-15_11-00-00
# backup-2025-11-15_14-30-00  ← Vuoi tornare qui
```

**Step 2: Ripristina il CODICE**
```bash
# Torna al codice del backup point
git checkout backup-2025-11-15_14-30-00

# Oppure crea un nuovo branch da quel punto
git checkout -b recovery-from-backup backup-2025-11-15_14-30-00
```

**Step 3: Ripristina il DATABASE**
```bash
# Via browser (ATTENZIONE: conferma richiesta!)
https://www.agstudio.digital/magia/public/restore-database.php?secret=$Magia2025!&file=db-backup-2025-11-15_14-30-00.sql&confirm=YES
```

**Step 4: Verifica applicazione**
- Controlla che l'app funzioni
- Verifica i dati nel database
- Testa le funzionalità critiche

**✅ Ripristino completato!**

---

### 📊 Gestione Backup

#### Lista Backup Points Disponibili

```bash
# Via Git
git tag -l 'backup-*'

# Via browser (per file SQL)
https://www.agstudio.digital/magia/public/restore-database.php?secret=$Magia2025!
```

#### Elimina Backup Vecchi

**Git tags:**
```bash
# Elimina tag locale
git tag -d backup-2025-11-15_10-00-00

# Elimina tag remoto
git push --delete origin backup-2025-11-15_10-00-00
```

**File SQL:**
```bash
# Via SSH (se disponibile)
rm storage/backups/db-backup-2025-11-15_10-00-00.sql

# Via FTP manualmente
```

#### Politica di Retention Suggerita

**Conserva:**
- ✅ Ultimo backup di ogni giornata → 7 giorni
- ✅ Backup settimanali → 4 settimane
- ✅ Backup mensili → 6 mesi
- ✅ Backup pre-release → per sempre

**Elimina:**
- ❌ Backup orari > 24 ore fa (tranne quello finale del giorno)
- ❌ Backup giornalieri > 7 giorni fa (tranne settimanali)

---

### 🛡️ Sicurezza e Best Practices

**File SQL:**
- ⚠️ NON sono versionati su Git (troppo grandi)
- ⚠️ Rimangono solo sul server in `storage/backups/`
- ⚠️ NON fanno backup automatico esterno
- ✅ Hanno .gitignore per evitare commit accidentali

**Backup Esterno (CONSIGLIATO):**
Per vera sicurezza, scaricare periodicamente i dump SQL:
1. Via FTP da `storage/backups/`
2. Su cloud storage (Dropbox, Google Drive)
3. Su backup service professionale

**Password Protected:**
- ✅ Tutti gli script richiedono `secret=$Magia2025!`
- ✅ Restore richiede conferma esplicita `confirm=YES`
- ✅ Nessun parametro di default pericoloso

**Logging:**
- ✅ Output completo di ogni operazione
- ✅ Statistiche dettagliate (tabelle, record, errori)
- ✅ Timestamp su ogni file/tag

---

### 📝 Template Commit Message per Backup

Quando crei un backup point manualmente (oltre allo script):

```
🔖 Backup point: [YYYY-MM-DD HH:mm]

Fase: [Descrizione fase sviluppo]
Funzionalità: [Cosa è stato implementato]

Database:
- Tabelle: [N]
- Record: [N]
- Dimensione: [N MB]

Codice:
- Files modificati: [N]
- Linee aggiunte: [+N]
- Linee rimosse: [-N]

Note: [Eventuali annotazioni]
```

---

### 🎯 Esempio Workflow Completo

**Scenario:** Implementazione nuova feature "Calendario Drag & Drop"

**10:00 - Inizio sviluppo**
```bash
# Backup point iniziale
curl "https://www.agstudio.digital/magia/public/create-backup-point.php?secret=\$Magia2025!"
git tag -a backup-2025-11-15_10-00-00 -m "Backup: Inizio implementazione drag&drop calendario"
git push origin backup-2025-11-15_10-00-00
```

**11:30 - Primo checkpoint**
```bash
# Backup intermedio
curl "https://www.agstudio.digital/magia/public/create-backup-point.php?secret=\$Magia2025!"
git tag -a backup-2025-11-15_11-30-00 -m "Backup: Backend drag&drop completato"
git push origin backup-2025-11-15_11-30-00
```

**14:00 - Feature completa**
```bash
# Backup finale
curl "https://www.agstudio.digital/magia/public/create-backup-point.php?secret=\$Magia2025!"
git tag -a backup-2025-11-15_14-00-00 -m "Backup: Drag&drop calendario COMPLETATO"
git push origin backup-2025-11-15_14-00-00
```

**14:15 - PROBLEMA! Qualcosa si è rotto**
```bash
# Ripristino all'ultimo backup funzionante
git checkout backup-2025-11-15_11-30-00

# Ripristina database
curl "https://www.agstudio.digital/magia/public/restore-database.php?secret=\$Magia2025!&file=db-backup-2025-11-15_11-30-00.sql&confirm=YES"

# ✅ Tutto torna a funzionare!
```

---

### ⚙️ File e Directory

```
magia/
├── storage/
│   └── backups/              # Dump SQL (gitignored)
│       ├── .gitignore
│       ├── db-backup-2025-11-15_10-00-00.sql
│       ├── db-backup-2025-11-15_11-00-00.sql
│       └── db-backup-2025-11-15_14-00-00.sql
│
└── public/
    ├── backup-database.php          # Backup solo DB
    ├── restore-database.php         # Ripristino DB
    └── create-backup-point.php      # ⭐ Crea backup point completo
```

---

### 📌 Promemoria per Claude

**OGNI ORA di sviluppo:**
```bash
1. curl create-backup-point.php
2. Copia comandi Git dall'output
3. Esegui git tag + git push
4. Annota nel commit il tag creato
```

**PRIMA di operazioni rischiose:**
- ✅ Migrazioni database
- ✅ Refactoring massiccio
- ✅ Modifiche strutturali
- ✅ Merge/rebase complessi
- ✅ Deploy in produzione

**Backup Point = Salvavita!** 🛟

---

## ⚙️ CONFIGURAZIONE AMBIENTE (.env)

### File .env Produzione

**Percorso sul server:** `/public_html/magia/.env`

**Configurazione corrente:**
```env
APP_NAME="MA.GIA DONNA"
APP_ENV=production
APP_KEY=base64:Jss+hm6Bdc2PuHAWtNffsQlLTLntHLxUDA/jccAD2QI=
APP_DEBUG=false
APP_URL=https://www.agstudio.digital/magia

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=agstudiodiital_magia
DB_USERNAME=agstudiodiital_agstudiomagia
DB_PASSWORD=$Magia2015!
```

**⚠️ NOTA CRITICA:**
- Il file `.env` è nel `.gitignore` (NON viene versionato per sicurezza)
- `.env` è stato deployato UNA VOLTA manualmente sul server
- Modifiche future al `.env` vanno fatte DIRETTAMENTE sul server via FTP
- `.env` è ESCLUSO dal deploy automatico FTP (vedi workflow)

---

## 🚀 DEPLOY AUTOMATICO FTP

### GitHub Actions Workflow

**File:** `.github/workflows/deploy.yml`

**Trigger:**
- Push su `main`
- Push su qualsiasi branch `claude/**`
- Esecuzione manuale (workflow_dispatch)

**Action usata:** `SamKirkland/FTP-Deploy-Action@v4.3.5`

### Secrets GitHub Actions

**Percorso configurazione:** Repository → Settings → Secrets and Variables → Actions

**Secrets configurati:**

| Nome | Valore | Descrizione |
|------|--------|-------------|
| `FTP_HOST` | `ftp.agstudio.digital` | Host FTP Aruba |
| `FTP_USER` | `agstudiodiital` | Username FTP |
| `FTP_PASSWORD` | `[nascosta]` | Password FTP Aruba |
| `FTP_PATH` | `/public_html/magia` | Percorso directory server |

### File Esclusi dal Deploy
```
**/.git*
**/node_modules/**
.env                  ← ESCLUSO per sicurezza
.env.example
README.md
.github/**
```

### Processo Deploy
1. Developer fa push su branch `claude/**`
2. GitHub Actions si attiva automaticamente
3. Workflow esegue checkout del codice
4. FTP-Deploy-Action carica file su Aruba
5. File vengono sincronizzati in `/public_html/magia/`
6. Tempo medio: 2-5 minuti

---

## 📍 PERCORSI PROGETTO

### ⚠️ PERCORSI CORRETTI - INFORMAZIONI CRITICHE

**Ambiente di Sviluppo (Docker/Container):**
```
/home/user/magia/
```

**Ambiente di Produzione (Server Aruba):**
```
/home/agstudiodiital/public_html/magia/
```

**⚠️ IMPORTANTE:**
- Il progetto NON si trova in `/var/www/html/magia` sul server di produzione
- Il percorso corretto su Aruba è `/home/agstudiodiital/public_html/magia/`
- Accessibile via FTP: `ftp.agstudio.digital` → `/public_html/magia/`
- URL pubblico: `https://www.agstudio.digital/magia/public/`

### Directory Critiche del Progetto

**Storage Backups (CORRETTA):**
```
storage/app/backups/          # Directory CORRETTA per backup database
```

**⚠️ Errore Comune:**
```
storage/backups/              # SBAGLIATO - directory esistente ma non usata dal codice
```

**Il BackupService Laravel usa `storage_path('app/backups')` che punta a `storage/app/backups/`**

### Comando mysqldump sul Server Produzione

**Path mysqldump:** `/bin/mysqldump`

**Verifica disponibilità:**
```bash
which mysqldump
# Output: /bin/mysqldump
```

**Versione installata:** MySQL dump 8.0.43

---

## 📁 STRUTTURA PROGETTO

### Directory Laravel Standard
```
/magia/
├── app/                    # Controllers, Models, Middleware
│   ├── Console/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── Admin/     # Controllers area admin
│   │   └── Middleware/
│   ├── Mail/
│   ├── Models/
│   └── Services/
│       └── BackupService.php  # Servizio backup database
├── bootstrap/
│   └── cache/             # Cache Laravel (permessi 755)
├── config/                # Configurazioni Laravel
├── database/
│   ├── migrations/        # Migrations database
│   └── seeders/
├── public/                # Web root (1.1 MB)
│   ├── index.php         # Entry point Laravel
│   ├── diagnose.php      # Script diagnostica errori
│   └── clear-cache.php   # Script pulizia cache
├── resources/
│   └── views/
│       ├── admin/        # Views area amministratore
│       │   └── super-admin/  # Views super admin (backup, cache, ecc.)
│       ├── emails/       # Template email
│       └── layouts/      # Layout Blade
├── routes/
│   └── web.php           # Route web
├── storage/              # Storage Laravel (permessi 755)
│   ├── app/
│   │   ├── backups/      # ⭐ Directory backup database (CORRETTA)
│   │   └── public/
│   ├── framework/
│   │   ├── cache/
│   │   ├── sessions/
│   │   └── views/
│   └── logs/
│       └── laravel.log   # Log applicazione
├── vendor/               # Dipendenze Composer (58 MB, 8107 file)
├── .env                  # Configurazione ambiente (NON versionato)
├── .gitignore
├── artisan              # CLI Laravel
├── composer.json
└── composer.lock
```

### File Speciali Creati
- `public/diagnose.php` - Diagnostica errori 500
- `public/clear-cache.php` - Pulizia cache Laravel
- `ENV_TEMPLATE_PRODUCTION.txt` - Template .env
- `CLAUDE_MEMORY.md` - Questo file
- `test.txt` - File test deploy FTP

---

## 🔧 CONFIGURAZIONE SPECIALE HOSTING CONDIVISO

### Problema: Vendor/ Versionato

**Situazione:** Su hosting condiviso Aruba NON c'è accesso SSH, quindi NON si può eseguire `composer install`.

**Soluzione applicata:**
1. Rimosso `/vendor` dal `.gitignore`
2. Committato vendor/ nel repository (58 MB, 8107 file)
3. Deploy automatico carica vendor/ sul server

**⚠️ Nota:** Normalmente `vendor/` NON si committa, ma è l'unica soluzione per hosting senza SSH.

### Permessi Cartelle

**Permessi richiesti:**
- `storage/` → **755** o **777**
- `bootstrap/cache/` → **755** o **777**
- `.env` → **644**

**Come impostare via FTP:**
- Tasto destro → Permessi file → Valore numerico

---

## 🏗️ ARCHITETTURA APPLICAZIONE

### Area Amministratore

**Controllers implementati:**
- `DashboardController` - Dashboard admin
- `ClientiController` - CRUD clienti (COMPLETO)
- `LezioniController` - Gestione lezioni
- `ProgrammiController` - Gestione programmi
- `PagamentiController` - Gestione pagamenti
- `SedeController` - Gestione sedi
- `ProfessionistiController` - Gestione professionisti
- `ImpostazioniController` - Impostazioni SMTP
- `ProfiloController` - Profilo utente

**Models implementati:**
- `Utente` - Con relazioni ruoli e permessi
- `Cliente` - Anagrafica clienti
- `Lezione` - Lezioni/corsi
- `Programma` - Programmi wellness
- `Pagamento` - Pagamenti
- `Sede` - Sedi fisiche
- `Professionista` - Istruttori
- `Impostazione` - Configurazioni app
- `ImpostazioneSistema` - Configurazioni sistema

### Autenticazione

**Middleware:**
- `auth` - Verifica autenticazione
- `tipo_utente` - Verifica ruolo utente

**Route Login:**
```
/admin/login
```

**Metodo hasRole():**
Implementato nel modello `Utente` per verificare ruoli.

---

## ⚠️ PROBLEMI RISOLTI

### 1. Errore vendor/composer/autoload_real.php (RISOLTO)

**Errore:**
```
Failed to open stream: vendor/composer/../symfony/deprecation-contracts/function.php
```

**Causa:** Cartella `vendor/` non presente sul server (esclusa da git).

**Soluzione:**
1. Rimosso `/vendor` da `.gitignore`
2. Committato vendor/ (commit: `6f79508`)
3. Deployato su server FTP

### 2. Errore 500 - File .env Mancante (RISOLTO)

**Causa:** File `.env` non presente sul server (escluso da deploy FTP).

**Soluzione:**
1. Temporaneamente rimosso `.env` da exclude nel workflow
2. Deployato `.env` sul server (commit: `3976595`)
3. Ripristinato exclude per sicurezza (commit: `baeddc0`)

### 3. File .env non in Sync (RISOLTO)

**Problema:** `.env` è nel `.gitignore` ma anche versionato (causa confusione).

**Stato attuale:**
- `.env` è nel repository git (tracciato)
- `.env` è nel `.gitignore` (per future modifiche)
- `.env` è ESCLUSO dal workflow deploy
- Modifiche future: solo via FTP direttamente sul server

### 4. Errore Backup Database - Codice Errore 1 (RISOLTO)

**Data risoluzione:** 17 Novembre 2025

**Errore:**
```
Errore durante il backup. Codice errore: 1. Dettagli: Nessun output
```

**Errore visibile su:**
`https://www.agstudio.digital/magia/public/admin/super-admin`
Quando si clicca su "Crea backup ora"

**Causa Principale:** Directory `storage/app/backups/` non esistente sul server di produzione.

**Causa Secondaria:** `mysqldump` non installato nell'ambiente di sviluppo locale (ma già presente in produzione).

**Analisi Problema:**
1. Il `BackupService.php` usa `storage_path('app/backups')` che punta a `storage/app/backups/`
2. Sul server esisteva solo `storage/backups/` (directory vuota, non utilizzata)
3. Laravel tentava di scrivere in `storage/app/backups/` che non esisteva
4. Il comando `mysqldump` restituiva exit code 1 (errore generico)

**Soluzione applicata sul server di produzione:**

**Step 1: Verifica percorso progetto**
```bash
cd /home/agstudiodiital/public_html/magia
```

**Step 2: Creazione directory corretta**
```bash
mkdir -p storage/app/backups
chmod 775 storage/app/backups
```

**Step 3: Test mysqldump manuale**
```bash
mysqldump --user='agstudiodiital_agstudiomagia' \
  --password='$Magia2015!' \
  --host='localhost' \
  --port=3306 \
  agstudiodiital_magia > /tmp/test_backup.sql

# Exit code: 0 (successo)
# File creato: 97K
```

**Step 4: Verifica funzionalità**
- Test su interfaccia web: ✅ Funzionante
- Backup creato con successo in `storage/app/backups/`

**Configurazione Finale:**
```
Directory: storage/app/backups/
Permessi: 775 (rwxrwxr-x)
Owner: agstudiodiital
Group: agstudiodiital
```

**mysqldump Configurazione:**
```
Path: /bin/mysqldump
Versione: MySQL dump 8.0.43
Host DB: localhost (socket Unix, non TCP/IP)
Database: agstudiodiital_magia
```

**⚠️ Note Importanti:**
- La directory `storage/backups/` esiste ma NON è usata dal codice Laravel
- BackupService usa sempre `storage_path('app/backups')`
- I backup sono salvati come: `backup_YYYY-MM-DD_HH-MM-SS.sql`
- Retention policy: mantiene ultimi 30 backup (`BackupService::MAX_BACKUPS`)

**Verifica Funzionamento:**
1. Accedi a: `https://www.agstudio.digital/magia/public/admin/super-admin`
2. Sezione "Backup Database"
3. Click su "Crea backup ora"
4. ✅ Messaggio: "Backup creato con successo: backup_YYYY-MM-DD_HH-MM-SS.sql (XX KB)"

**Files coinvolti nella risoluzione:**
- `app/Services/BackupService.php` - Servizio backup (già corretto)
- `app/Http/Controllers/Admin/SuperAdminController.php` - Controller (già corretto)
- `storage/app/backups/` - Directory creata manualmente sul server

---

## 📊 STATO IMPLEMENTAZIONE

### ✅ Funzionalità Completate
- Autenticazione e autorizzazione
- Dashboard amministratore
- Gestione clienti (CRUD completo)
- Sistema ruoli e permessi
- Models e migrations database
- Deploy automatico FTP
- Script diagnostica errori

### 🔄 Funzionalità Parziali
- Gestione lezioni (controller esistente, da testare)
- Gestione programmi (controller esistente, da testare)
- Gestione pagamenti (controller esistente, da testare)
- Log attività (tabella esiste, interfaccia mancante)

### ❌ Funzionalità Mancanti
- Calendario interattivo lezioni
- Report e statistiche avanzate
- Sistema referral completo
- Newsletter/comunicazioni
- Area cliente (frontend pubblico)

---

## 🔐 SICUREZZA

### Credenziali Sensibili

**NON committare mai:**
- File `.env` completo
- Password database
- Secrets GitHub Actions
- Credenziali FTP

### File Protetti
- `.env` → nel `.gitignore` e escluso da deploy
- `vendor/` → Versionato per necessità hosting (eccezione)
- `.git/` → Mai deployato

### Best Practices
- `APP_DEBUG=false` in produzione
- `APP_ENV=production` sul server
- `LOG_LEVEL=error` (solo errori critici)
- HTTPS obbligatorio (già configurato)

---

## 🚨 TROUBLESHOOTING

### Script Diagnostica

**URL:** `https://www.agstudio.digital/magia/public/diagnose.php`

**Cosa verifica:**
- Esistenza file `.env`
- APP_KEY configurata
- Permessi storage/ e bootstrap/cache/
- Autoload vendor/
- Log errori Laravel

### Script Pulizia Cache

**URL:** `https://www.agstudio.digital/magia/public/clear-cache.php`

**Cosa pulisce:**
- Cache config
- Cache routes
- Cache views
- Cache framework

### Comandi Laravel (se SSH disponibile)

```bash
# Pulisci cache
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Verifica database
php artisan migrate:status

# Genera nuova APP_KEY
php artisan key:generate
```

**⚠️ Nota:** SSH NON disponibile su Aruba, usare script PHP

---

## 🔄 WORKFLOW SVILUPPO

### ⚠️ REGOLA FONDAMENTALE

**Usa SEMPRE e SOLO il branch ufficiale:** `claude/Magia_Brench-01G4cTM33nQqZ3K3UtX3NGqm`

❌ NON creare nuovi branch!
❌ NON pushare su `magia-brench` (errore 403)
✅ Lavora SEMPRE su `claude/Magia_Brench-01G4cTM33nQqZ3K3UtX3NGqm`

### Per Nuove Funzionalità

1. **Assicurati di essere sul branch corretto:**
   ```bash
   git checkout claude/Magia_Brench-01G4cTM33nQqZ3K3UtX3NGqm
   git pull origin claude/Magia_Brench-01G4cTM33nQqZ3K3UtX3NGqm
   ```

2. **Sviluppa le modifiche:**
   - Modifica codice
   - Testa localmente (se possibile)

3. **Commit e push:**
   ```bash
   git add .
   git commit -m "Descrizione modifiche in italiano"
   git push origin claude/Magia_Brench-01G4cTM33nQqZ3K3UtX3NGqm
   ```

4. **Deploy automatico:**
   - GitHub Actions si attiva automaticamente
   - File caricati su FTP in 2-5 minuti
   - Verifica su: https://www.agstudio.digital/magia/public/

5. **Test su produzione:**
   - Testa funzionalità
   - Usa `diagnose.php` se errori
   - Usa `clear-cache.php` se necessario

### Per Modifiche .env

**NON modificare via git!**

1. Connetti via FTP a `ftp.agstudio.digital`
2. Vai in `/public_html/magia/`
3. Modifica `.env` direttamente
4. Salva
5. Pulisci cache con `clear-cache.php`

---

## 📞 INFORMAZIONI DI CONTATTO

### Repository e Hosting

- **GitHub Repo:** https://github.com/alexgentilitn/magia
- **GitHub Actions:** https://github.com/alexgentilitn/magia/actions
- **Sito Produzione:** https://www.agstudio.digital/magia/public/
- **Login Admin:** https://www.agstudio.digital/magia/public/admin/login

### FTP Aruba

- **Host:** ftp.agstudio.digital
- **User:** agstudiodiital
- **Path:** /public_html/magia/

---

## 📝 COMMIT IMPORTANTI

### Deploy Iniziale
- `baff857` - Configura GitHub Actions per deploy automatico FTP
- `7dd4157` - Riattiva deploy con tutti i secrets configurati
- `6f79508` - Aggiungi vendor/ per deploy su hosting condiviso

### Fix Errori
- `a7dfd53` - Deploy file .env configurato per produzione
- `3976595` - Deploy .env: rimuovi temporaneamente esclusione
- `baeddc0` - Ripristina esclusione .env nel workflow

### Diagnostica
- `cb6ae00` - Aggiungi script diagnostica per errore 500

---

## 🎯 PROSSIMI PASSI SUGGERITI

### Priorità Alta
1. Testare funzionalità lezioni esistenti
2. Testare funzionalità programmi esistenti
3. Testare funzionalità pagamenti esistenti
4. Verificare tutte le route admin

### Priorità Media
5. Implementare calendario interattivo lezioni
6. Creare dashboard con più statistiche
7. Sistema prenotazioni lezioni
8. Report e export dati

### Priorità Bassa
9. Sistema referral completo
10. Newsletter e comunicazioni
11. Area cliente frontend
12. Mobile app (futuro)

---

## 💡 NOTE IMPORTANTI PER SESSIONI FUTURE

### Quando Riprendi il Progetto

1. **Leggi questo file COMPLETO** prima di iniziare
2. **⚠️ VERIFICA BRANCH:** Assicurati di essere su `claude/Magia_Brench-01G4cTM33nQqZ3K3UtX3NGqm`
   ```bash
   git checkout claude/Magia_Brench-01G4cTM33nQqZ3K3UtX3NGqm
   ```
3. **Pull ultime modifiche:**
   ```bash
   git pull origin claude/Magia_Brench-01G4cTM33nQqZ3K3UtX3NGqm
   ```
4. **Controlla GitHub Actions:** Vedi se deploy in corso
5. **Testa sito produzione:** Verifica che funzioni
6. **Usa diagnose.php:** Se ci sono problemi

### Se Ti Blocchi

1. Controlla `diagnose.php` per errori
2. Verifica log in `storage/logs/laravel.log` sul server
3. Controlla GitHub Actions per deploy falliti
4. Verifica secrets GitHub configurati
5. Controlla che `.env` esista sul server
6. **Se errore 403 su push:** Verifica di essere su `claude/Magia_Brench-01G4cTM33nQqZ3K3UtX3NGqm`

### Convenzioni da Seguire - CRITICHE!

- **Branch:** SEMPRE e SOLO `claude/Magia_Brench-01G4cTM33nQqZ3K3UtX3NGqm`
- ❌ **NON creare nuovi branch!**
- ❌ **NON pushare su `magia-brench`** (errore 403)
- ✅ **Commit:** Messaggi descrittivi in italiano
- ✅ **Deploy:** Attendi sempre GitHub Actions verde prima di testare
- ✅ **.env:** MAI modificare via git, solo FTP diretto
- ✅ **vendor/:** È versionato, accettalo (necessità hosting)

---

## 📚 DOCUMENTAZIONE CORRELATA

**File nel repository:**
- `GUIDA PROGETTO/CONFIGURAZIONE_TECNICA.md` - Setup tecnico dettagliato
- `GUIDA PROGETTO/STATO_IMPLEMENTAZIONE_ADMIN.md` - Stato funzionalità admin
- `ENV_TEMPLATE_PRODUCTION.txt` - Template .env produzione
- `README.md` - Documentazione Laravel standard

**Documentazione esterna:**
- Laravel 10: https://laravel.com/docs/10.x
- GitHub Actions: https://docs.github.com/en/actions
- FTP-Deploy-Action: https://github.com/SamKirkland/FTP-Deploy-Action

---

**📅 Ultimo aggiornamento:** 17 Novembre 2025
**✍️ Creato da:** Claude Code - Sessione review-technical-docs-01G4cTM33nQqZ3K3UtX3NGqm
**🔄 Versione:** 4.1 - Aggiornato percorsi progetto e risoluzione problema backup

**🆕 Novità versione 4.1:**
- ✅ Documentati percorsi corretti: sviluppo `/home/user/magia` e produzione `/home/agstudiodiital/public_html/magia`
- ✅ Documentata directory backup corretta: `storage/app/backups/` (NON `storage/backups/`)
- ✅ Risolto problema "Errore backup. Codice errore: 1" (directory mancante)
- ✅ Configurazione mysqldump documentata: `/bin/mysqldump` su server produzione
- ✅ Procedura completa per risoluzione problemi backup su server Aruba

---

**💬 Messaggio per la prossima sessione:**

Ciao Claude del futuro! 👋

Hai ereditato un progetto Laravel funzionante su hosting condiviso Aruba.
Tutto è documentato in questo file. Leggilo con attenzione prima di fare qualsiasi modifica.

**⚠️ PRIMA COSA - BRANCH POLICY CRITICA:**

**USA SEMPRE E SOLO QUESTO BRANCH:**
```
claude/Magia_Brench-01G4cTM33nQqZ3K3UtX3NGqm
```

❌ **NON creare nuovi branch!**
❌ **NON pushare su `magia-brench`** (errore 403)
✅ **Lavora SEMPRE su `claude/Magia_Brench-01G4cTM33nQqZ3K3UtX3NGqm`**

Se provi a pushare su altri branch, riceverai errore HTTP 403. GitHub Actions accetta SOLO branch `claude/**` che terminano con il session ID corretto.

**Altre cose importanti:**

- Il deploy è automatico via GitHub Actions (2-5 minuti)
- `.env` va modificato manualmente sul server (NON via git)
- `vendor/` è versionato (necessario per hosting senza SSH)
- Usa `diagnose.php` per errori
- Crea backup point ogni ora con `create-backup-point.php`

**Backup Point = Salvavita!** 🛟

Buon lavoro! 🚀

---
