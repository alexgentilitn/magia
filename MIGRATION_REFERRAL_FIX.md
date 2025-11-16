# Fix Migration Referral System

## 🐛 Problema Riscontrato

La migration `2025_11_16_000907_create_referral_system_table` è fallita con l'errore:

```
❌ ERRORE: There is no active transaction
```

### Causa del Problema

Il problema è causato da un **conflitto tra transazioni esplicite e commit impliciti DDL** in MySQL/MariaDB:

1. Lo script `esegui-migrations.php` esegue le migrations dentro una transazione esplicita (`DB::beginTransaction()`)
2. Le operazioni DDL (Data Definition Language) come `CREATE TABLE`, `DROP TABLE`, `ALTER TABLE` causano un **commit implicito automatico** in MySQL
3. Quando la migration originale eseguiva `Schema::create('referrals', ...)`, MySQL committava implicitamente la transazione
4. Lo script tentava poi di fare `DB::commit()`, ma la transazione non esisteva più → errore

### Stato Corrente

- ✅ La tabella `referrals` è stata creata correttamente (0 record)
- ⚠️ Potrebbe esistere un record errato nella tabella `migrations` (da verificare)
- ❌ La migration non è stata completata correttamente

## ✅ Soluzione Implementata

### 1. Rimozione Transazioni dallo Script `esegui-migrations.php`

**PROBLEMA PRINCIPALE**: Le operazioni DDL in MySQL/MariaDB **NON sono transazionali** e causano sempre **commit impliciti automatici**.

Lo script `esegui-migrations.php` tentava di wrappare le migrations in transazioni esplicite:

```php
// PRIMA (problematico)
DB::beginTransaction();
$migration->up();  // ← Operazioni DDL causano commit implicito!
DB::table('migrations')->insert(...);
DB::commit();  // ← FALLISCE: transazione già chiusa dal commit implicito
```

**SOLUZIONE**: Rimuovere completamente le transazioni, perché non servono (e causano errori):

```php
// DOPO (corretto)
$migration->up();  // Operazioni DDL con commit implicito
DB::table('migrations')->insert(...);  // Inserimento registro migration
```

### 2. Migration Idempotente

La migration è stata modificata per essere **completamente idempotente**:

**Prima**:
```php
if (!Schema::hasTable('referrals')) {
    Schema::create('referrals', ...);
}
```

**Dopo**:
```php
Schema::dropIfExists('referrals');
Schema::create('referrals', ...);
```

### 3. Script di Verifica e Cleanup

Creati script di utilità:
- `public/verifica-migrations.php` - Verifica record nella tabella migrations
- `public/cleanup-referral-migration.php` - Rimuove record errati

## 📋 Procedura di Risoluzione

### Step 1: Verifica Stato Migrations

Verifica se c'è un record errato nella tabella migrations:

```
https://www.agstudio.digital/magia/public/verifica-migrations.php?secret=$Magia2025!
```

Se viene trovato un record referral, rimuovilo:

```
https://www.agstudio.digital/magia/public/verifica-migrations.php?secret=$Magia2025!&delete=YES
```

### Step 2: Esegui le Migrations

Ora esegui le migrations con lo script corretto (SENZA transazioni):

```
https://www.agstudio.digital/magia/public/esegui-migrations.php?secret=$Magia2025!&confirm=YES
```

### Step 3: Verifica Successo

Controlla che la migration sia stata eseguita correttamente:

- ✅ Nessun errore nell'output
- ✅ La tabella `referrals` esiste
- ✅ La migration è registrata nella tabella `migrations`
- ✅ Batch 22 completato con successo

## 🔍 Dettagli Tecnici

### Struttura Tabella Referrals

```sql
CREATE TABLE referrals (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    cliente_invitante_id BIGINT UNSIGNED NOT NULL,
    cliente_invitato_id BIGINT UNSIGNED NULL,
    email_invitato VARCHAR(255) NULL,
    codice_invito VARCHAR(20) UNIQUE NOT NULL,
    stato ENUM('pending', 'registrato', 'convertito', 'scaduto') DEFAULT 'pending',
    data_invito TIMESTAMP NOT NULL,
    data_registrazione TIMESTAMP NULL,
    data_conversione TIMESTAMP NULL,
    sconto_invitante DECIMAL(5,2) NULL COMMENT '% sconto per chi invita',
    sconto_invitato DECIMAL(5,2) NULL COMMENT '% sconto per chi è invitato',
    sconto_applicato BOOLEAN DEFAULT FALSE,
    note TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,

    FOREIGN KEY (cliente_invitante_id) REFERENCES clienti(id) ON DELETE CASCADE,
    FOREIGN KEY (cliente_invitato_id) REFERENCES clienti(id) ON DELETE SET NULL,

    INDEX idx_codice_invito (codice_invito),
    INDEX idx_cliente_stato (cliente_invitante_id, stato)
);
```

### Perché Rimuovere le Transazioni è la Soluzione Corretta

**Fatti chiave sulle DDL operations in MySQL**:

1. ✅ Le operazioni DDL (`CREATE`, `DROP`, `ALTER`, ecc.) **NON supportano transazioni**
2. ✅ Causano **sempre** un commit implicito automatico
3. ✅ Non possono essere rollbackate
4. ✅ Questo è un comportamento **nativo e intenzionale** di MySQL/MariaDB

**Conclusione**: Usare `DB::beginTransaction()` con migrations DDL è **inutile e causa errori**.

### Perché `dropIfExists()` + `create()` è Sicuro

- ✅ La migration è **idempotente**: può essere eseguita più volte con lo stesso risultato
- ✅ La tabella ha 0 record, quindi non c'è rischio di perdita dati
- ✅ Permette di ricreare la tabella in caso di modifiche future
- ✅ È lo standard per migrations idempotenti in Laravel

## 📝 Note Importanti

### Per Future Migrations

1. **NON usare `Schema::hasTable()` con condizioni**: causa problemi con re-esecuzioni
2. **PREFERIRE `dropIfExists()` + `create()`**: garantisce idempotenza
3. **Le transazioni NON servono**: le DDL operations in MySQL non sono transazionali

### Comportamento MySQL/MariaDB DDL

Le seguenti operazioni causano **commit implicito automatico**:
- `CREATE TABLE`, `DROP TABLE`, `ALTER TABLE`
- `CREATE DATABASE`, `DROP DATABASE`
- `RENAME TABLE`, `TRUNCATE TABLE`
- `CREATE INDEX`, `DROP INDEX`

Fonte: [MySQL Documentation - Implicit Commit](https://dev.mysql.com/doc/refman/8.0/en/implicit-commit.html)

## ✅ Modifiche Committate

Branch: `claude/confirm-status-01NaRPJZBUHxak94aM2zKA1u`

**File modificati**:
- `database/migrations/2025_11_16_000907_create_referral_system_table.php` - Resa idempotente
- `public/esegui-migrations.php` - Rimosse transazioni (incompatibili con DDL)

**File creati**:
- `public/verifica-migrations.php` - Verifica record migrations
- `public/cleanup-referral-migration.php` - Cleanup record errati
- `MIGRATION_REFERRAL_FIX.md` - Documentazione completa
