# 🔧 CONFIGURAZIONE TECNICA - magia

**Documento di riferimento completo per riconfigurare il progetto da zero**

*Ultimo aggiornamento: 12 Novembre 2025*

---

## 📋 INDICE

1. [Configurazione Database](#-configurazione-database)
2. [Configurazione Laravel (.env)](#-configurazione-laravel-env)
3. [Git e Branch Strategy](#-git-e-branch-strategy)
4. [Deploy Automatico FTP](#-deploy-automatico-ftp)
5. [Riconfigurazione da Zero](#-riconfigurazione-da-zero)
6. [Troubleshooting](#-troubleshooting)

---

## 🗄️ CONFIGURAZIONE DATABASE

### Tipo Database
- **Sistema:** MariaDB / MySQL
- **Versione:** 5.7.44-log-cll-lve (verificato su produzione)
- **Charset:** utf8mb4
- **Collation:** utf8mb4_unicode_ci

### Credenziali Produzione

```
Host:     localhost
Porta:    3306
Database: agstudiodiital_magia
Username: agstudiodiital_agstudiomagia
Password: $Magia2015!
```

### Importante
- ⚠️ **NON usare `127.0.0.1`** → Usare `localhost` (socket Unix più veloce)
- ⚠️ Username completo è `agstudiodiital_agstudiomagia` (non solo `agstudio`)
- ✅ Il database contiene tutte le tabelle Laravel migrate

### File SQL Dump
- **Backup Database:** `agstudiodiital_magia.sql` (53KB) nella root del progetto
- Contiene struttura completa con tabelle:
  - `cliente_lezione`
  - `utenti`
  - `migrations`
  - E altre tabelle Laravel

---

## ⚙️ CONFIGURAZIONE LARAVEL (.env)

### File .env Completo

**IMPORTANTE:** Il file `.env` NON è versionato su Git (è nel `.gitignore` per sicurezza).
Deve essere creato manualmente sul server di produzione.

```env
APP_NAME="AGstudio CRM"
APP_ENV=production
APP_KEY=base64:Jss+hm6Bdc2PuHAWtNffsQlLTLntHLxUDA/jccAD2QI=
APP_DEBUG=false
APP_URL=https://www.agstudio.digital/magia/website-agstudio

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=agstudiodiital_magia
DB_USERNAME=agstudiodiital_agstudiomagia
DB_PASSWORD=$Magia2015!

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MEMCACHED_HOST=127.0.0.1

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_HOST=
PUSHER_PORT=443
PUSHER_SCHEME=https
PUSHER_APP_CLUSTER=mt1

VITE_APP_NAME="${APP_NAME}"
VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_HOST="${PUSHER_HOST}"
VITE_PUSHER_PORT="${PUSHER_PORT}"
VITE_PUSHER_SCHEME="${PUSHER_SCHEME}"
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"
```

### Valori Critici da NON Modificare

| Variabile | Valore | Perché è Importante |
|-----------|--------|---------------------|
| `APP_KEY` | `base64:Jss+hm6Bdc2PuHAWtNffsQlLTLntHLxUDA/jccAD2QI=` | Usato per encryption. Cambiarlo invalida sessioni e dati criptati |
| `APP_ENV` | `production` | Disabilita debug e ottimizza performance |
| `APP_DEBUG` | `false` | In produzione deve essere false per sicurezza |
| `DB_HOST` | `localhost` | NON usare 127.0.0.1 |
| `DB_PASSWORD` | `$Magia2015!` | La password contiene caratteri speciali, usare le virgolette se necessario |

### Generare una Nuova APP_KEY (solo se necessario)

```bash
php artisan key:generate
```

⚠️ **ATTENZIONE:** Cambiare APP_KEY invalida:
- Tutte le sessioni utente
- Tutti i dati criptati nel database
- Token CSRF

---

## 🌳 GIT E BRANCH STRATEGY

### Repository GitHub
- **URL:** https://github.com/alexgentilitn/magia
- **Owner:** alexgentilitn
- **Visibilità:** Privato

### Branch Attuale

```
Branch Principale: main
Branch di Sviluppo: claude/review-repository-011CV4kgUwYkENQMuapteRn9
```

### Strategia Branch

```
main (produzione stabile)
  │
  ├─── claude/review-repository-011CV4kgUwYkENQMuapteRn9 (sviluppo attivo)
  │
  └─── claude/explore-project-files-011CV4hXmGD7qUSnErTFMhv7 (esplorazione)
```

### Pattern Naming Branch

- **main** → Versione stabile in produzione
- **claude/*** → Branch di sviluppo automatici (creati da Claude Code)
  - Formato: `claude/[descrizione]-[session-id]`
  - Esempio: `claude/review-repository-011CV4kgUwYkENQMuapteRn9`

### Come Funziona il Push

1. **Commit Locale:**
   ```bash
   git add .
   git commit -m "Descrizione modifiche"
   ```

2. **Push su GitHub:**
   ```bash
   git push -u origin claude/review-repository-011CV4kgUwYkENQMuapteRn9
   ```

3. **GitHub riceve i file** → Repository aggiornato

4. **GitHub Actions si attiva automaticamente** → Workflow `deploy.yml` parte

5. **Deploy su FTP** → File caricati su server Aruba

6. **Sito live aggiornato** → https://www.agstudio.digital/magia/website-agstudio/

### Restrizioni Push

⚠️ **IMPORTANTE:** Non è possibile fare push diretto su `main` dal sistema Claude Code per sicurezza.

**Soluzione:** Usare Pull Request
1. Sviluppo su branch `claude/*`
2. Push su GitHub
3. Creare Pull Request da GitHub web interface
4. Merge manuale su `main`

---

## 🚀 DEPLOY AUTOMATICO FTP

### Sistema di Deploy

**Tecnologia:** GitHub Actions + FTP-Deploy-Action

**File Configurazione:** `.github/workflows/deploy.yml`

### Workflow Deploy

```yaml
name: Deploy to Aruba FTP

on:
  push:
    branches:
      - main
      - 'claude/**'  # ← Aggiunto il 12/11/2025 per deploy automatico da branch claude
  workflow_dispatch:

jobs:
  deploy:
    name: Deploy Files to Aruba
    runs-on: ubuntu-latest

    steps:
      - name: 📥 Checkout del codice
        uses: actions/checkout@v4

      - name: 🚀 Deploy via FTP
        uses: SamKirkland/FTP-Deploy-Action@v4.3.5
        with:
          server: ${{ secrets.FTP_HOST }}
          username: ${{ secrets.FTP_USER }}
          password: ${{ secrets.FTP_PASSWORD }}
          server-dir: ${{ secrets.FTP_PATH }}/

          exclude: |
            **/.git*
            **/.git*/**
            **/node_modules/**
            .env
            .env.example
            README.md
            .github/**

          dry-run: false
```

### Trigger Deploy

Il deploy si attiva automaticamente quando:
1. ✅ Push su branch `main`
2. ✅ Push su qualsiasi branch `claude/**`
3. ✅ Esecuzione manuale da GitHub Actions (workflow_dispatch)

### File Esclusi dal Deploy

**NON vengono caricati sul server:**
- `.git/` e `.github/` → File di versioning
- `node_modules/` → Dipendenze npm (troppo pesanti)
- `.env` → Configurazione locale (contiene credenziali)
- `.env.example` → Template non necessario in produzione
- `README.md` → Documentazione sviluppatore

### Secrets GitHub Configurati

**Percorso:** GitHub Repository → Settings → Secrets and Variables → Actions

| Secret | Descrizione | Esempio |
|--------|-------------|---------|
| `FTP_HOST` | Host FTP Aruba | `ftp.agstudio.digital` |
| `FTP_USER` | Username FTP | `magia@agstudio.digital` |
| `FTP_PASSWORD` | Password FTP | `$magia2025!` |
| `FTP_PATH` | Percorso directory server | `/magia/website-agstudio` |

⚠️ **IMPORTANTE:** Questi secrets sono configurati SOLO su GitHub, non nel codice.

### Monitorare il Deploy

1. Vai su: https://github.com/alexgentilitn/magia/actions
2. Vedrai l'ultimo workflow "Deploy to Aruba FTP"
3. Clicca per vedere i dettagli:
   - ✅ Verde = Deploy riuscito
   - ❌ Rosso = Deploy fallito
   - 🟡 Giallo = In corso

### Tempo di Deploy

- **Durata media:** 2-5 minuti
- **Dipende da:** Numero di file modificati e velocità FTP

---

## 🔄 RICONFIGURAZIONE DA ZERO

### Scenario: Devi riconfigurare il progetto su un nuovo server o dopo una perdita di configurazione

### Step 1: Clone Repository

```bash
git clone https://github.com/alexgentilitn/magia.git
cd magia
```

### Step 2: Checkout Branch Corretto

```bash
# Verifica branch disponibili
git branch -a

# Passa al branch di sviluppo
git checkout claude/review-repository-011CV4kgUwYkENQMuapteRn9

# Oppure crea un nuovo branch
git checkout -b claude/nuova-feature-[session-id]
```

### Step 3: Installa Dipendenze Composer

```bash
composer install --no-dev --optimize-autoloader
```

**Flags spiegati:**
- `--no-dev` → Non installa dipendenze di sviluppo (PHPUnit, ecc.)
- `--optimize-autoloader` → Ottimizza l'autoloader per produzione

### Step 4: Crea File .env

```bash
# Copia il template
cp .env.example .env

# Edita il file con le credenziali corrette
nano .env
```

**Valori da configurare:**
```env
DB_HOST=localhost
DB_DATABASE=agstudiodiital_magia
DB_USERNAME=agstudiodiital_agstudiomagia
DB_PASSWORD=$Magia2015!

APP_URL=https://www.agstudio.digital/magia/website-agstudio
```

### Step 5: Genera APP_KEY

```bash
php artisan key:generate
```

Questo aggiorna automaticamente il file `.env` con una nuova chiave sicura.

### Step 6: Configura Permessi

```bash
# Permessi cartelle storage e cache
chmod -R 755 storage bootstrap/cache

# Se sei su server condiviso con www-data
chown -R www-data:www-data storage bootstrap/cache

# Permessi file .env
chmod 644 .env
```

### Step 7: Ottimizza per Produzione

```bash
# Cache configurazioni
php artisan config:cache

# Cache rotte
php artisan route:cache

# Cache viste
php artisan view:cache
```

### Step 8: Verifica Database

```bash
# Verifica connessione e mostra tabelle
php artisan migrate:status

# Se necessario, esegui migrazioni
php artisan migrate --force
```

### Step 9: Test Configurazione

Visita questi URL per verificare:
1. **Homepage:** https://www.agstudio.digital/magia/website-agstudio/
2. **File test connessione:** https://www.agstudio.digital/magia/website-agstudio/index.php

### Step 10: Configura GitHub Actions (se necessario)

**Su GitHub:**
1. Vai su: Repository → Settings → Secrets and Variables → Actions
2. Aggiungi questi secrets:
   - `FTP_HOST`
   - `FTP_USER`
   - `FTP_PASSWORD`
   - `FTP_PATH`

**Il workflow è già configurato** nel file `.github/workflows/deploy.yml`

---

## 🐛 TROUBLESHOOTING

### Errore: "SQLiteDatabaseDoesNotExistException"

**Causa:** Laravel sta usando configurazione cache vecchia che punta a SQLite

**Soluzione:**
```bash
# Pulisci tutte le cache
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Elimina file cache manualmente
rm -f bootstrap/cache/config.php
rm -f bootstrap/cache/routes-v7.php
rm -f bootstrap/cache/services.php
```

### Errore: "Access denied for user 'agstudio'@'localhost'"

**Causa:** Credenziali database errate nel file `.env`

**Soluzione:**
1. Verifica credenziali nel cPanel → MySQL Databases
2. Username corretto è: `agstudiodiital_agstudiomagia` (non `agstudio`)
3. Host deve essere `localhost` (non `127.0.0.1`)
4. Aggiorna `.env` e pulisci cache

### Errore: "The stream or file could not be opened"

**Causa:** Permessi file storage/logs non corretti

**Soluzione:**
```bash
chmod -R 755 storage
chmod -R 755 bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### Deploy GitHub Actions Fallisce

**Causa:** Secrets FTP non configurati o errati

**Soluzione:**
1. Verifica secrets su GitHub: Settings → Secrets and Variables → Actions
2. Controlla che tutti e 4 i secrets esistano:
   - `FTP_HOST`
   - `FTP_USER`
   - `FTP_PASSWORD`
   - `FTP_PATH`
3. Verifica credenziali FTP con FileZilla o client FTP

### File .env Non Viene Letto

**Causa:** File .env corrotto o encoding sbagliato

**Soluzione:**
```bash
# Verifica encoding (deve essere UTF-8)
file -i .env

# Riconverti se necessario
iconv -f ISO-8859-1 -t UTF-8 .env -o .env

# Verifica contenuto
cat .env | grep DB_

# Verifica permessi
ls -la .env  # Deve essere 644 e leggibile
```

### Cache Non Si Pulisce

**Causa:** File cache bloccati o permessi errati

**Soluzione:**
```bash
# Forza eliminazione cache
rm -rf storage/framework/cache/*
rm -rf storage/framework/views/*
rm -rf bootstrap/cache/*

# Ricrea cartelle
mkdir -p storage/framework/{cache,views,sessions}
chmod -R 755 storage/framework
```

### Push Git Fallisce con Errore 403

**Causa:** Tentativo di push diretto su branch `main` (non permesso da Claude Code)

**Soluzione:**
1. Lavora sempre su branch `claude/*`
2. Fai push su quel branch
3. Usa Pull Request per merge su `main`

---

## 📊 CHECKLIST MANUTENZIONE

### Giornaliera
- [ ] Verifica che il sito sia online e funzionante
- [ ] Controlla log Laravel in `storage/logs/laravel.log`

### Settimanale
- [ ] Verifica deploy GitHub Actions (tab Actions su GitHub)
- [ ] Backup database con `mysqldump`
- [ ] Controlla spazio disco sul server

### Mensile
- [ ] Aggiorna dipendenze Composer: `composer update`
- [ ] Verifica vulnerabilità: `composer audit`
- [ ] Rivedi e aggiorna questa documentazione se necessario

### Annuale
- [ ] Rinnova certificato SSL (se gestito manualmente)
- [ ] Cambia password database
- [ ] Rivedi e rigenera APP_KEY (con piano migrazione)

---

## 📞 CONTATTI E RISORSE

### Repository
- **GitHub:** https://github.com/alexgentilitn/magia
- **Actions:** https://github.com/alexgentilitn/magia/actions

### Server Produzione
- **URL:** https://www.agstudio.digital/magia/website-agstudio/
- **Server:** Aruba Hosting
- **Account FTP:** magia@agstudio.digital

### Documentazione Utile
- **Laravel 10:** https://laravel.com/docs/10.x
- **GitHub Actions:** https://docs.github.com/en/actions
- **FTP-Deploy-Action:** https://github.com/SamKirkland/FTP-Deploy-Action

---

## 🔒 SICUREZZA

### Credenziali da NON Condividere MAI

- ❌ Password database
- ❌ APP_KEY Laravel
- ❌ Secrets GitHub (FTP credentials)
- ❌ File `.env` completo

### Best Practices

1. **File .env:** Mai versionare su Git (è nel .gitignore)
2. **Secrets GitHub:** Usa sempre secrets, mai hardcoded
3. **Password:** Usa password complesse con caratteri speciali
4. **Backup:** Mantieni backup sicuri del database e del .env
5. **SSL:** Usa sempre HTTPS in produzione (già configurato)

---

## 📝 REGISTRO MODIFICHE

| Data | Modifica | Autore |
|------|----------|--------|
| 12 Nov 2025 | Creazione documento configurazione completa | Claude Code |
| 12 Nov 2025 | Fix credenziali database (localhost, username corretto) | Claude Code |
| 12 Nov 2025 | Aggiunto deploy automatico per branch `claude/**` | Claude Code |
| 12 Nov 2025 | Risolto errore SQLiteDatabaseDoesNotExistException | Claude Code |

---

**💡 Nota Finale:**

Questo documento contiene TUTTE le informazioni necessarie per riconfigurare il progetto da zero.

Se perdi le configurazioni:
1. Segui la sezione "Riconfigurazione da Zero"
2. Usa le credenziali in questo documento
3. Il progetto tornerà funzionante

**Mantieni questo documento aggiornato!** 📚

---

*Documento creato da Claude Code Assistant*
*Per domande o problemi, fare riferimento alla sezione Troubleshooting*
