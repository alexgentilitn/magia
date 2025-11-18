# 🔧 CONFIGURAZIONE TECNICA - MA.GIA DONNA

**Documento di riferimento completo per il progetto MA.GIA DONNA**

*Ultimo aggiornamento: 17 Novembre 2025*

---

## 📋 INDICE

1. [Configurazione Database](#-configurazione-database)
2. [Configurazione Laravel (.env)](#-configurazione-laravel-env)
3. [Sistema Permessi](#-sistema-permessi)
4. [Struttura Database](#-struttura-database)
5. [Migrations](#-migrations)
6. [Troubleshooting](#-troubleshooting)

---

## 🗄️ CONFIGURAZIONE DATABASE

### Tipo Database
- **Sistema:** MariaDB / MySQL
- **Versione:** 5.7+ (verificato su produzione)
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
- ⚠️ Username completo è `agstudiodiital_agstudiomagia`
- ✅ Il database contiene tutte le tabelle Laravel migrate

---

## ⚙️ CONFIGURAZIONE LARAVEL (.env)

### File .env Configurato

```env
APP_NAME="MA.GIA DONNA"
APP_ENV=production
APP_KEY=base64:Jss+hm6Bdc2PuHAWtNffsQlLTLntHLxUDA/jccAD2QI=
APP_DEBUG=false
APP_URL=https://www.agstudio.digital/magia

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
```

---

## 🔐 SISTEMA PERMESSI

### Struttura Permessi

Il progetto implementa un sistema RBAC (Role-Based Access Control) con permessi individuali per i collaboratori.

#### Tabelle Coinvolte

1. **ruoli** - Definizione ruoli (Super Admin, Moderatore, Collaboratore)
2. **permessi** - Lista permessi disponibili
3. **ruolo_permesso** - Pivot table ruolo → permessi (many-to-many)
4. **utente_permesso** - Pivot table utente → permessi individuali (many-to-many)

#### Foreign Keys

```sql
-- Tabella ruolo_permesso
FOREIGN KEY (ruolo_id) REFERENCES ruoli(id) ON DELETE CASCADE
FOREIGN KEY (permesso_id) REFERENCES permessi(id) ON DELETE CASCADE

-- Tabella utente_permesso
FOREIGN KEY (utente_id) REFERENCES utenti(id) ON DELETE CASCADE
FOREIGN KEY (permesso_id) REFERENCES permessi(id) ON DELETE CASCADE
```

---

## 🗃️ STRUTTURA DATABASE

### Tabelle Principali

| Tabella | Descrizione | Migration |
|---------|-------------|-----------|
| `users` | Tabella Laravel default (non usata) | Laravel default |
| `utenti` | Utenti sistema (admin/collaboratori) | 2025_11_05_100001 |
| `clienti` | Clienti del centro | 2025_11_05_100002 |
| `ruoli` | Ruoli sistema RBAC | 2025_11_05_100000 |
| `permessi` | Permessi disponibili | 2025_11_05_100000 |
| `ruolo_permesso` | Pivot ruoli-permessi | 2025_11_05_100000 |
| `utente_permesso` | Pivot utenti-permessi individuali | **2025_11_17** |
| `programmi` | Programmi allenamento | 2025_11_13_085902 |
| `lezioni` | Lezioni/corsi | 2025_11_13_085936 |
| `sedi` | 5 sedi operative | 2025_11_13_085812 |
| `professionisti` | Professionisti esterni | 2025_11_13_100000 |
| `professionista_documenti` | Documenti professionisti | 2025_11_17_000002 |
| `pagamentos` | Pagamenti | 2025_11_13_091458 |
| `impostazioni` | Impostazioni utente | 2025_11_13_130000 |
| `impostazioni_sistema` | Impostazioni globali | 2025_11_13_164841 |

---

## 📦 MIGRATIONS

### Ordine di Esecuzione

**IMPORTANTE:** Le migrations devono essere eseguite nell'ordine corretto per rispettare le foreign key.

#### Ordine Corretto

1. **Base Tables** (senza foreign keys)
   ```
   2025_11_05_100000_create_ruoli_permessi_tables.php
   2025_11_05_100001_create_utenti_table.php
   2025_11_05_100002_create_clienti_table.php
   ```

2. **Tabelle Secondarie**
   ```
   2025_11_13_085812_create_sedi_table.php
   2025_11_13_085902_create_programmi_table.php
   2025_11_13_085936_create_lezioni_table.php
   2025_11_13_100000_create_professionisti_table.php
   ```

3. **Tabelle con Foreign Keys Multiple**
   ```
   2025_11_13_090010_create_cliente_programma_table.php
   2025_11_13_090048_create_cliente_lezione_table.php
   2025_11_13_091458_create_pagamentos_table.php
   ```

4. **Sistema Permessi Individuali** ⭐ NUOVO
   ```
   2025_11_17_create_utente_permesso_table.php
   ```

### Migration Utente-Permesso

**File:** `database/migrations/2025_11_17_create_utente_permesso_table.php`

**Prerequisiti:**
- ✅ Tabella `utenti` deve esistere
- ✅ Tabella `permessi` deve esistere

**Crea:**
- Tabella `utente_permesso` con foreign keys a `utenti` e `permessi`
- Constraint unique su `(utente_id, permesso_id)`
- Cascade on delete

**Esecuzione:**
```bash
# Via Artisan
php artisan migrate --path=database/migrations/2025_11_17_create_utente_permesso_table.php --force

# Via Super Admin
Dashboard → Super Admin → Strumenti Database → Esegui Migrations
```

---

## 🔍 PERCORSI CORRETTI

### File System

```
/home/user/magia/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── Admin/
│   │   │       ├── SuperAdminController.php
│   │   │       └── PermessiCollaboratoreController.php
│   │   └── Middleware/
│   │       └── VerificaPermesso.php
│   └── Models/
│       ├── Utente.php
│       ├── Permesso.php
│       └── Ruolo.php
├── database/
│   └── migrations/
│       ├── 2025_11_05_100000_create_ruoli_permessi_tables.php
│       ├── 2025_11_05_100001_create_utenti_table.php
│       └── 2025_11_17_create_utente_permesso_table.php ⭐
├── resources/
│   └── views/
│       └── admin/
│           ├── super-admin/
│           │   └── index.blade.php
│           └── professionisti/
│               └── permessi.blade.php
├── routes/
│   └── web.php
├── DOCS/
│   ├── SISTEMA_PERMESSI_COLLABORATORI.md
│   └── QUICK_START_PERMESSI.md
└── .env
```

### Routes Principali

```php
// Super Admin
/admin/super-admin
/admin/super-admin/migrations.status
/admin/super-admin/run-migrations

// Gestione Permessi Collaboratori
/admin/professionisti/{id}/permessi (GET, PUT, DELETE)
/admin/professionisti/{id}/permessi/api (GET)
```

---

## 🐛 TROUBLESHOOTING

### Errore Migration: "0 migration(s) eseguita/e con successo, 1 fallita/e"

**Possibili Cause:**

1. **Foreign Key Non Trovata**
   - Verifica che `utenti` e `permessi` esistano
   ```sql
   SHOW TABLES LIKE 'utenti';
   SHOW TABLES LIKE 'permessi';
   ```

2. **Connessione Database Fallita**
   - Verifica credenziali in `.env`
   - Controlla che MySQL sia accessibile

3. **Migration Già Eseguita**
   - Controlla stato: `php artisan migrate:status`
   - Verifica tabella `migrations`

**Soluzioni:**

#### Soluzione 1: Eseguire da CLI (Consigliato)
```bash
# SSH al server di produzione
cd /path/to/magia

# Esegui migration specifica
php artisan migrate --path=database/migrations/2025_11_17_create_utente_permesso_table.php --force

# Verifica esito
php artisan migrate:status
```

#### Soluzione 2: Verificare Prerequisiti
```bash
# Controlla tabelle esistenti
php artisan tinker
>>> DB::select('SHOW TABLES');

# Verifica tabella utenti
>>> DB::table('utenti')->count();

# Verifica tabella permessi
>>> DB::table('permessi')->count();
```

#### Soluzione 3: Eseguire Tutte le Migrations
```bash
# Se alcune migrations base mancano
php artisan migrate --force
```

### Errore: "Can't connect to MySQL server"

**Causa:** Socket MySQL non trovato o host errato

**Soluzione:**
1. Verifica `.env` → `DB_HOST=localhost` (non 127.0.0.1)
2. Pulisci cache config: `php artisan config:clear`
3. Verifica MySQL attivo: `systemctl status mysql`

### Migration Esegue ma Non Crea Tabella

**Causa:** Transaction rollback per errore foreign key

**Soluzione:**
```bash
# Verifica log Laravel
tail -100 storage/logs/laravel.log

# Verifica engine tabelle (deve essere InnoDB)
php artisan tinker
>>> DB::select("SHOW TABLE STATUS WHERE Name = 'utenti'");
```

---

## ✅ CHECKLIST DEPLOYMENT SISTEMA PERMESSI

- [x] Migration creata: `2025_11_17_create_utente_permesso_table.php`
- [x] Model Utente aggiornato con metodi permessi
- [x] Middleware `VerificaPermesso` creato e registrato
- [x] Controller `PermessiCollaboratoreController` creato
- [x] Routes aggiunte in `web.php`
- [x] View `permessi.blade.php` creata
- [x] Documentazione completa in `/DOCS`
- [ ] **Migration eseguita in produzione** ⚠️ DA FARE

---

## 📞 COME ESEGUIRE LA MIGRATION

### Metodo 1: Via Super Admin (Interfaccia Web)

1. Accedi a: `https://www.agstudio.digital/magia/admin/super-admin`
2. Clicca **"Esegui Migrations"**
3. Seleziona `2025_11_17_create_utente_permesso_table`
4. Clicca **"Esegui Selezionate"**

⚠️ **Se fallisce**, usa Metodo 2

### Metodo 2: Via SSH (Consigliato per Produzione)

```bash
# 1. Connetti via SSH al server
ssh agstudiodiital@agstudio.digital

# 2. Vai alla directory progetto
cd public_html/magia

# 3. Esegui migration specifica
php artisan migrate --path=database/migrations/2025_11_17_create_utente_permesso_table.php --force

# 4. Verifica successo
php artisan migrate:status | grep utente_permesso
```

### Metodo 3: Manuale SQL (Solo se i metodi sopra falliscono)

```sql
-- Connetti al database
USE agstudiodiital_magia;

-- Crea tabella
CREATE TABLE IF NOT EXISTS `utente_permesso` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `utente_id` BIGINT UNSIGNED NOT NULL,
  `permesso_id` BIGINT UNSIGNED NOT NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  UNIQUE KEY `unique_utente_permesso` (`utente_id`, `permesso_id`),
  CONSTRAINT `fk_utente_permesso_utente`
    FOREIGN KEY (`utente_id`)
    REFERENCES `utenti`(`id`)
    ON DELETE CASCADE,
  CONSTRAINT `fk_utente_permesso_permesso`
    FOREIGN KEY (`permesso_id`)
    REFERENCES `permessi`(`id`)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Registra migration
INSERT INTO `migrations` (`migration`, `batch`)
VALUES ('2025_11_17_create_utente_permesso_table',
        (SELECT COALESCE(MAX(batch), 0) + 1 FROM migrations AS m));
```

---

## 🎯 VERIFICA POST-MIGRATION

Dopo aver eseguito la migration, verifica:

```bash
# 1. Tabella creata
php artisan tinker
>>> Schema::hasTable('utente_permesso')  // deve essere true

# 2. Colonne corrette
>>> Schema::getColumnListing('utente_permesso')
// ['id', 'utente_id', 'permesso_id', 'created_at', 'updated_at']

# 3. Foreign keys attive
>>> DB::select("SHOW CREATE TABLE utente_permesso");

# 4. Test assegnazione permesso
>>> $utente = App\Models\Utente::first();
>>> $utente->assegnaPermessoIndividuale(1);
>>> $utente->permessiIndividuali()->count();  // deve essere > 0
```

---

## 📝 REGISTRO MODIFICHE

| Data | Modifica | Autore |
|------|----------|--------|
| 17 Nov 2025 | Creazione documento tecnico MA.GIA DONNA | Claude Code |
| 17 Nov 2025 | Implementazione sistema permessi individuali | Claude Code |
| 17 Nov 2025 | Creazione migration utente_permesso | Claude Code |
| 17 Nov 2025 | Documentazione troubleshooting migrations | Claude Code |

---

**💡 Nota Finale:**

Questo documento è specifico per il progetto **MA.GIA DONNA** e contiene i percorsi corretti, le credenziali e le procedure per gestire il sistema di permessi individuali per collaboratori.

**Mantieni questo documento aggiornato!** 📚

---

*Documento creato da Claude Code Assistant*
*Per domande: fare riferimento a `/DOCS/SISTEMA_PERMESSI_COLLABORATORI.md`*
