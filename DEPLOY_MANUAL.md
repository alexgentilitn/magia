# 🚨 DEPLOY MANUALE URGENTE - Fix Import Pesate

## ⚠️ PROBLEMA ATTUALE

Il server di produzione usa **codice VECCHIO** con errori:
- ❌ `stato_cliente = 'attiva'` causa errore SQL "Data truncated"
- ❌ Route mancanti (cache non pulita)
- ❌ `codice_fiscale` NOT NULL causa errori
- ❌ **0 pesate salvate** nel database

## 🎯 OBIETTIVO

Deployare i fix immediatamente per far funzionare l'import pesate.

---

## ✅ METODO 1: Deploy Automatico (CONSIGLIATO)

### Sul Server di Produzione

```bash
# 1. Entra nella directory
cd /home/agstudiodiital/public_html/magia

# 2. Scarica e esegui script deploy
wget https://raw.githubusercontent.com/alexgentilitn/magia/claude/MAGIA_3-01LdNr3eLWNFtpfEAE2hHFJV/DEPLOY_URGENTE.sh
chmod +x DEPLOY_URGENTE.sh
bash DEPLOY_URGENTE.sh
```

Lo script fa automaticamente:
- ✅ Backup file esistenti
- ✅ Pull branch con fix
- ✅ Pulizia cache (route, config, view)
- ✅ Migration database
- ✅ Ottimizzazione Laravel

---

## 🔧 METODO 2: Deploy Git Manuale

Se lo script automatico non funziona:

```bash
cd /home/agstudiodiital/public_html/magia

# Backup
cp -r app app_backup_$(date +%Y%m%d_%H%M%S)

# Fetch e pull branch con fix
git fetch origin
git pull origin claude/MAGIA_3-01LdNr3eLWNFtpfEAE2hHFJV

# Pulizia cache (CRITICO!)
php artisan route:clear
php artisan config:clear
php artisan view:clear
php artisan cache:clear

# Migration
php artisan migrate --force

# Ottimizzazione
php artisan optimize
```

---

## 📝 METODO 3: Deploy FTP Manuale (se Git non disponibile)

### File da Caricare via FTP

Scarica questi file dal repository GitHub e caricali sul server:

#### 1. Controllers (PRIORITÀ ALTA)

**Path**: `app/Http/Controllers/Admin/PesateController.php`
```
Scarica da: https://github.com/alexgentilitn/magia/raw/claude/MAGIA_3-01LdNr3eLWNFtpfEAE2hHFJV/app/Http/Controllers/Admin/PesateController.php
Carica in: /home/agstudiodiital/public_html/magia/app/Http/Controllers/Admin/
```

**Path**: `app/Http/Controllers/Admin/ClientiController.php`
```
Scarica da: https://github.com/alexgentilitn/magia/raw/claude/MAGIA_3-01LdNr3eLWNFtpfEAE2hHFJV/app/Http/Controllers/Admin/ClientiController.php
Carica in: /home/agstudiodiital/public_html/magia/app/Http/Controllers/Admin/
```

**Path**: `app/Http/Controllers/Auth/RegistrazioneController.php`
```
Scarica da: https://github.com/alexgentilitn/magia/raw/claude/MAGIA_3-01LdNr3eLWNFtpfEAE2hHFJV/app/Http/Controllers/Auth/RegistrazioneController.php
Carica in: /home/agstudiodiital/public_html/magia/app/Http/Controllers/Auth/
```

**Path**: `app/Http/Controllers/GiornataProvaController.php`
```
Scarica da: https://github.com/alexgentilitn/magia/raw/claude/MAGIA_3-01LdNr3eLWNFtpfEAE2hHFJV/app/Http/Controllers/GiornataProvaController.php
Carica in: /home/agstudiodiital/public_html/magia/app/Http/Controllers/
```

#### 2. Migration (PRIORITÀ MEDIA)

**Path**: `database/migrations/2025_11_19_093641_make_clienti_fields_nullable_for_import.php`
```
Scarica da: https://github.com/alexgentilitn/magia/raw/claude/MAGIA_3-01LdNr3eLWNFtpfEAE2hHFJV/database/migrations/2025_11_19_093641_make_clienti_fields_nullable_for_import.php
Carica in: /home/agstudiodiital/public_html/magia/database/migrations/
```

### Dopo il Caricamento FTP

**SSH nel server ed esegui**:

```bash
cd /home/agstudiodiital/public_html/magia

# Pulizia cache (OBBLIGATORIO!)
php artisan route:clear
php artisan config:clear
php artisan view:clear
php artisan cache:clear

# Migration
php artisan migrate --force

# Ottimizzazione
php artisan optimize

# Verifica permessi
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

---

## 🔍 VERIFICA POST-DEPLOY

### 1. Verifica File Aggiornati

```bash
cd /home/agstudiodiital/public_html/magia

# Controlla che i fix siano presenti
grep "tipo_cliente.*effettiva" app/Http/Controllers/Admin/PesateController.php
# Deve mostrare la riga con tipo_cliente => 'effettiva'

grep "stato_cliente.*attivo" app/Http/Controllers/Admin/PesateController.php
# Deve mostrare la riga con stato_cliente => 'attivo'
```

### 2. Test Import Pesate

1. Vai su: **Admin → Pesate → Import Excel**
2. Carica un file Excel di test
3. **NON deve mostrare errori**:
   - ❌ "Data truncated for column 'stato_cliente'"
   - ❌ "Route not defined"
   - ❌ "Column 'codice_fiscale' cannot be null"

### 3. Verifica Database

```bash
# Accedi a MySQL
mysql -u [username] -p [database_name]

# Conta pesate (deve essere > 0 dopo import)
SELECT COUNT(*) as total_pesate FROM pesate;

# Verifica struttura tabella clienti
DESCRIBE clienti;
# codice_fiscale deve essere: varchar(16) | YES | ...
```

### 4. Controlla Log

```bash
tail -f storage/logs/laravel.log
```

Dopo un import test **NON devono apparire**:
- ❌ ERROR: Data truncated
- ❌ ERROR: Route not defined
- ❌ ERROR: cannot be null

---

## 🆘 TROUBLESHOOTING

### Problema: Route ancora non trovate

```bash
php artisan route:clear
php artisan optimize:clear
php artisan optimize
```

### Problema: Migration fallita

```bash
# Verifica quali migration sono eseguite
php artisan migrate:status

# Forza re-esecuzione specifica migration
php artisan migrate --path=/database/migrations/2025_11_19_093641_make_clienti_fields_nullable_for_import.php --force
```

### Problema: Permessi file

```bash
cd /home/agstudiodiital/public_html/magia
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### Problema: Cache persistente

```bash
# Pulizia totale cache
php artisan optimize:clear

# Cancella manualmente cache files
rm -rf bootstrap/cache/*.php
rm -rf storage/framework/cache/*
rm -rf storage/framework/views/*

# Ricrea cache
php artisan optimize
```

---

## 📊 CHECKLIST POST-DEPLOY

- [ ] File controller aggiornati (verifica grep)
- [ ] Cache pulita (route, config, view)
- [ ] Migration eseguita (codice_fiscale nullable)
- [ ] Permessi corretti (755 storage/)
- [ ] Test import funziona SENZA errori
- [ ] Pesate salvate in database (COUNT > 0)
- [ ] Log pulito (no ERROR SQL)

---

## 📞 Supporto

Se persistono problemi dopo il deploy:

1. **Controlla log dettagliato**:
   ```bash
   tail -100 storage/logs/laravel.log | grep ERROR
   ```

2. **Verifica versione codice**:
   ```bash
   git log --oneline -5
   # Deve mostrare: "Fix: Risolti errori SQL..."
   ```

3. **Controlla PHP info**:
   ```bash
   php artisan --version
   php -v
   ```

---

**Ultimo aggiornamento**: 2025-11-19
**Branch fix**: claude/MAGIA_3-01LdNr3eLWNFtpfEAE2hHFJV
