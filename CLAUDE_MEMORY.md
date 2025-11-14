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
- **Branch main:** `main` - Produzione stabile (non usato attualmente)
- **Branch sviluppo:** `claude/confirm-status-01NaRPJZBUHxak94aM2zKA1u` - Branch corrente di lavoro
- **Pattern branch:** `claude/[descrizione]-[session-id]` per nuovi branch

### Date Importanti
- **Setup iniziale:** 14 Novembre 2025
- **Ultimo aggiornamento:** 14 Novembre 2025
- **Sessione corrente:** confirm-status-01NaRPJZBUHxak94aM2zKA1u

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
│   └── Models/
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
│       ├── emails/       # Template email
│       └── layouts/      # Layout Blade
├── routes/
│   └── web.php           # Route web
├── storage/              # Storage Laravel (permessi 755)
│   ├── app/
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

### Per Nuove Funzionalità

1. **Crea nuovo branch:**
   ```bash
   git checkout -b claude/nome-feature-[session-id]
   ```

2. **Sviluppa in locale:**
   - Modifica codice
   - Testa localmente (se possibile)

3. **Commit e push:**
   ```bash
   git add .
   git commit -m "Descrizione modifiche"
   git push -u origin claude/nome-feature-[session-id]
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
2. **Verifica branch corrente:** `git branch`
3. **Pull ultime modifiche:** `git pull origin [branch-name]`
4. **Controlla GitHub Actions:** Vedi se deploy in corso
5. **Testa sito produzione:** Verifica che funzioni
6. **Usa diagnose.php:** Se ci sono problemi

### Se Ti Blocchi

1. Controlla `diagnose.php` per errori
2. Verifica log in `storage/logs/laravel.log` sul server
3. Controlla GitHub Actions per deploy falliti
4. Verifica secrets GitHub configurati
5. Controlla che `.env` esista sul server

### Convenzioni da Seguire

- Branch: sempre `claude/[descrizione]-[session-id]`
- Commit: messaggi descrittivi in italiano
- Deploy: attendi sempre che sia verde prima di testare
- .env: MAI modificare via git, solo FTP diretto
- vendor/: È versionato, accettalo (necessità hosting)

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

**📅 Ultimo aggiornamento:** 14 Novembre 2025
**✍️ Creato da:** Claude Code - Sessione confirm-status-01NaRPJZBUHxak94aM2zKA1u
**🔄 Versione:** 1.0

---

**💬 Messaggio per la prossima sessione:**

Ciao Claude del futuro! 👋

Hai ereditato un progetto Laravel funzionante su hosting condiviso Aruba.
Tutto è documentato in questo file. Leggilo con attenzione prima di fare qualsiasi modifica.

Il deploy è automatico via GitHub Actions, ma ricorda che `.env` va modificato manualmente sul server.

`vendor/` è versionato (stranezza necessaria per hosting senza SSH - accettalo).

Se vedi errori, usa `diagnose.php` - è il tuo migliore amico.

Buon lavoro! 🚀

---
