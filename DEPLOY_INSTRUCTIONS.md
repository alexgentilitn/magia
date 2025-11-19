# 🚀 Istruzioni Deploy - Fix Import Pesate

## Problemi Risolti

✅ **Errore ENUM `stato_cliente`**: Corretti i controller che usavano 'attiva' invece di 'attivo'
✅ **Campo `tipo_cliente` mancante**: Aggiunto in tutti i punti di creazione cliente
✅ **Route mancanti**: Le route esistono già, serve solo pulire la cache
✅ **Campo `codice_fiscale` NULL**: La migration per renderlo nullable è già presente

## File Modificati

1. `app/Http/Controllers/Admin/PesateController.php`
   - Aggiunto `tipo_cliente => 'effettiva'` durante creazione cliente nell'import

2. `app/Http/Controllers/GiornataProvaController.php`
   - Corretto `stato_cliente` da 'prova' a 'attivo' (ENUM non accettava 'prova')

3. `app/Http/Controllers/Auth/RegistrazioneController.php`
   - Aggiunto `tipo_cliente => 'effettiva'` durante registrazione

4. `app/Http/Controllers/Admin/ClientiController.php`
   - Aggiunto `tipo_cliente` con fallback a 'effettiva'

## Come Applicare le Modifiche sul Server

### Metodo 1: Pull da Git (Raccomandato)

```bash
# 1. Entra nella directory del progetto
cd /home/agstudiodiital/public_html/magia

# 2. Fai backup (sicurezza)
cp -r app app.backup.$(date +%Y%m%d_%H%M%S)

# 3. Pull delle modifiche dal branch
git pull origin claude/magia-2-014g9oftuhszylpebgmbjt-0122wXiaFGHiG8aJQWvpg2yD

# 4. Esegui lo script di deploy
bash deploy-update.sh
```

### Metodo 2: Manuale (se git pull non funziona)

```bash
# 1. Entra nella directory del progetto
cd /home/agstudiodiital/public_html/magia

# 2. Carica manualmente i file modificati via FTP/SFTP nella directory giusta

# 3. Esegui lo script di deploy
bash deploy-update.sh
```

### Comandi Individuali (se lo script non funziona)

```bash
php artisan route:clear
php artisan config:clear
php artisan view:clear
php artisan cache:clear
php artisan migrate --force
php artisan optimize
```

## Verifica del Fix

Dopo il deploy, verifica che tutto funzioni:

### 1. Controlla i Log
```bash
tail -f storage/logs/laravel.log
```

### 2. Prova l'Import Pesate
- Vai su: Admin → Pesate → Import Excel
- Carica il file Excel
- Verifica che non ci siano più errori di:
  - ❌ `stato_cliente` data truncated
  - ❌ `codice_fiscale` cannot be null

### 3. Verifica Creazione Clienti
I clienti creati automaticamente durante l'import dovrebbero avere:
- `stato_cliente = 'attivo'` ✅
- `tipo_cliente = 'effettiva'` ✅
- `codice_fiscale = NULL` (se non fornito) ✅

## Problemi?

Se continui ad avere errori:

1. **Verifica migration eseguita**:
   ```bash
   php artisan migrate:status
   ```
   Cerca: `2025_11_19_093641_make_clienti_fields_nullable_for_import`

2. **Verifica struttura database**:
   ```sql
   DESCRIBE clienti;
   ```
   - `codice_fiscale` deve essere nullable
   - `stato_cliente` deve essere ENUM('attivo', 'sospeso', 'inattivo', 'cancellato')
   - `tipo_cliente` deve essere ENUM('prova', 'effettiva')

3. **Controlla permessi file**:
   ```bash
   chmod -R 755 storage bootstrap/cache
   chown -R www-data:www-data storage bootstrap/cache
   ```

4. **Forza ricreazione cache**:
   ```bash
   php artisan optimize:clear
   php artisan optimize
   ```

## Note Importanti

- ⚠️ Il server aveva una **versione vecchia del codice** con valori errati
- ⚠️ La cache delle route impediva di vedere le route esistenti
- ✅ Tutti i fix sono stati applicati nel codice attuale
- ✅ Lo script `deploy-update.sh` pulisce tutte le cache necessarie

---

**Data Fix**: 2025-11-19
**Branch**: `claude/magia-2-014g9oftuhszylpebgmbjt-0122wXiaFGHiG8aJQWvpg2yD`
