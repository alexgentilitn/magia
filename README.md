# MA.GIA DONNA - Web Application

**Applicazione Laravel per Gestione Centro Wellness/Fitness**

---

## 🚀 Per Claude Code e Sviluppatori

### 📖 Documentazione Essenziale

**Se sei una nuova sessione di Claude Code o un nuovo sviluppatore:**

1. **Inizia qui:** [`QUICK_START.md`](./QUICK_START.md) ⚡ (2 minuti)
2. **Leggi la memoria completa:** [`CLAUDE_MEMORY.md`](./CLAUDE_MEMORY.md) 🧠 (10 minuti) - **OBBLIGATORIO**
3. **Documentazione tecnica:** [`GUIDA PROGETTO/`](./GUIDA%20PROGETTO/) 📚

### 🎯 Quick Links

- **Repository:** https://github.com/alexgentilitn/magia
- **Produzione:** https://www.agstudio.digital/magia/public/
- **GitHub Actions:** https://github.com/alexgentilitn/magia/actions
- **Diagnostica:** https://www.agstudio.digital/magia/public/diagnose.php

---

## 📌 Informazioni Progetto

### Stack Tecnologico
- **Framework:** Laravel 10.x
- **PHP:** 8.4.14
- **Database:** MariaDB/MySQL 5.7.44
- **Frontend:** Tailwind CSS, Alpine.js, SweetAlert2
- **Hosting:** Aruba Shared Hosting (senza SSH)

### Deploy
- **Sistema:** GitHub Actions → FTP automatico
- **Trigger:** Push su branch `main` o `claude/**`
- **Tempo:** 2-5 minuti

### Funzionalità Implementate

#### Backend e Sicurezza
✅ Dashboard amministratore completa
✅ Gestione clienti (CRUD completo)
✅ Sistema autenticazione multi-ruolo (super_admin, admin, professionista, cliente)
✅ Gestione lezioni, programmi, sedi, professionisti
✅ Sistema pagamenti integrato
✅ Deploy automatico FTP via GitHub Actions
✅ Script diagnostica e troubleshooting
✅ Rate limiting su login (5 tentativi/minuto)
✅ Security guard per script debug (IP whitelist + rate limiting)
✅ Middleware protezione CSRF

#### Area Cliente Privata
✅ Dashboard cliente con KPI e statistiche
✅ Gestione profilo (dati personali, indirizzo, preferenze)
✅ Tracking parametri corporei (peso, massa grassa/magra, circonferenze)
✅ Gestione prenotazioni lezioni (visualizza, prenota, annulla)
✅ Storico pagamenti con integrazione PayPal
✅ Download materiali didattici e schede allenamento

#### Funzionalità Pubbliche
✅ Landing page "Giornata di Prova" con form lead generation
✅ Pagina ringraziamento post-registrazione
✅ Mappa interattiva Google Maps con 5 sedi
✅ Pagina dettaglio singola sede
✅ Pagine errore personalizzate (404, 403, 500)

#### Pagamenti Online
✅ Integrazione completa PayPal
✅ Checkout page responsive
✅ Success/Cancel callbacks
✅ Webhook handler per notifiche PayPal
✅ Log transazioni su database
✅ Supporto Sandbox e Live mode

#### Documentazione
✅ SMTP_CONFIGURATION.md - Setup email produzione
✅ GOOGLE_MAPS_SETUP.md - Guida completa Google Maps API
✅ PAYPAL_SETUP.md - Integrazione PayPal passo-passo

---

## 🔧 Setup Locale (se possibile)

### Prerequisiti
- PHP >= 8.1
- Composer
- MySQL/MariaDB

### Installazione

```bash
# Clone repository
git clone https://github.com/alexgentilitn/magia.git
cd magia

# Installa dipendenze
composer install

# Copia .env
cp .env.example .env

# Genera APP_KEY
php artisan key:generate

# Configura database nel .env
# Vedi CLAUDE_MEMORY.md per credenziali produzione

# Migrazioni (ATTENZIONE: solo su DB locale)
php artisan migrate

# Avvia server locale
php artisan serve
```

**⚠️ IMPORTANTE:**
- Non eseguire migrazioni su database di produzione senza backup
- Le credenziali produzione sono in `CLAUDE_MEMORY.md`
- Il server di produzione NON ha accesso SSH

---

## 📚 Documentazione

| File/Cartella | Descrizione |
|---------------|-------------|
| **CLAUDE_MEMORY.md** | 🧠 Memoria completa progetto - LEGGI SEMPRE |
| **QUICK_START.md** | ⚡ Quick reference per sessioni rapide |
| **GUIDA PROGETTO/** | 📖 Documentazione tecnica dettagliata |
| **.claude/** | 🤖 Configurazione Claude Code |
| **ENV_TEMPLATE_PRODUCTION.txt** | 📝 Template .env produzione |

---

## 🚨 Troubleshooting

### Errore 500?
1. Vai su https://www.agstudio.digital/magia/public/diagnose.php
2. Verifica `.env` esista sul server
3. Controlla permessi `storage/` e `bootstrap/cache/` (755)
4. Pulisci cache: https://www.agstudio.digital/magia/public/clear-cache.php

### Deploy fallito?
1. Controlla GitHub Actions: https://github.com/alexgentilitn/magia/actions
2. Verifica secrets GitHub configurati (FTP_HOST, FTP_USER, FTP_PASSWORD, FTP_PATH)
3. Vedi logs del workflow per errori specifici

### Altre domande?
Leggi `CLAUDE_MEMORY.md` → sezione Troubleshooting

---

## 🔐 Sicurezza

**NON committare mai:**
- File `.env` completo
- Credenziali database
- Password FTP
- Secrets GitHub Actions

**File protetti:**
- `.env` → nel `.gitignore`
- `.env` → escluso da deploy FTP

---

## 📞 Links Utili

### Produzione
- **Sito:** https://www.agstudio.digital/magia/public/
- **Login Admin:** https://www.agstudio.digital/magia/public/admin/login
- **Diagnostica:** https://www.agstudio.digital/magia/public/diagnose.php
- **Clear Cache:** https://www.agstudio.digital/magia/public/clear-cache.php

### Sviluppo
- **GitHub Repo:** https://github.com/alexgentilitn/magia
- **GitHub Actions:** https://github.com/alexgentilitn/magia/actions
- **Secrets:** https://github.com/alexgentilitn/magia/settings/secrets/actions

### Documentazione
- **Laravel 10:** https://laravel.com/docs/10.x
- **Tailwind CSS:** https://tailwindcss.com/docs
- **Alpine.js:** https://alpinejs.dev/

---

## 📅 Info Versione

- **Progetto:** MA.GIA DONNA
- **Versione Laravel:** 10.x
- **Ultimo aggiornamento:** 14 Novembre 2025
- **Branch corrente:** `claude/confirm-status-01NaRPJZBUHxak94aM2zKA1u`

---

## 📝 Note Speciali

### Vendor Versionato
⚠️ La cartella `vendor/` è versionata nel repository (insolito ma necessario).

**Motivo:** Hosting Aruba condiviso senza accesso SSH → impossibile eseguire `composer install` sul server.

**Soluzione:** `vendor/` è committato e deployato via FTP.

### File .env
⚠️ Il file `.env` è nel repository ma ESCLUSO dal deploy FTP.

**Deploy .env:**
- È stato deployato UNA VOLTA manualmente
- Modifiche future: solo via FTP direttamente sul server
- NON modificare `.env` via git

---

## 🤝 Contributi

Per contribuire al progetto:

1. Crea nuovo branch: `claude/[feature]-[session-id]`
2. Sviluppa funzionalità
3. Commit con messaggi descrittivi (italiano)
4. Push su GitHub → deploy automatico
5. Testa su produzione
6. Crea Pull Request se necessario

---

## 📄 Licenza

Progetto proprietario - AGstudio Digital

---

**💬 Per sessioni Claude Code:**

Prima di iniziare a lavorare, **leggi `CLAUDE_MEMORY.md`** - contiene TUTTO il contesto del progetto! 🧠

---

*Creato con ❤️ da AGstudio Digital*
*Powered by Laravel 10.x*
