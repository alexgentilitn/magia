# Risoluzione Errori Migrations

## Problema Riscontrato

L'errore `Duplicate column name 'password_temp_expires_at'` indica che alcune colonne esistono già nel database ma la migration non è registrata nella tabella `migrations`.

## Soluzione Implementata

### 1. Scripts di Diagnostica e Risoluzione

Sono stati creati due script nella cartella `public/`:

#### **verifica-colonne-utenti.php**
Script per diagnosticare lo stato delle colonne nella tabella `utenti`:
```
https://tuodominio.com/verifica-colonne-utenti.php?secret=$Magia2025!
```

#### **risolvi-migration-duplicata.php**
Script per risolvere automaticamente il problema:
```
https://tuodominio.com/risolvi-migration-duplicata.php?secret=$Magia2025!
```

### 2. Migrations Aggiornate

Tutte le migrations che aggiungono colonne sono state aggiornate con controlli `Schema::hasColumn()` per prevenire errori di duplicazione:

- ✅ `2025_11_13_120000_add_password_temp_expires_to_utenti.php`
- ✅ `2025_11_15_213036_add_privacy_fields_to_clienti_table.php`
- ✅ `2025_11_15_213108_add_custom_fields_to_clienti_table.php`
- ✅ `2025_11_16_000650_add_campi_impedenziometria_to_clienti_table.php`
- ✅ `2025_11_15_232815_add_prenotazioni_fields_to_prenotazioni_table.php` (già aveva i controlli)

### 3. Conflitti Risolti

Rimosso conflitto tra migrations per evitare duplicazioni:
- `allergie`/`intolleranze` vs `allergie_intolleranze`
- `livello_attivita` presente in due migrations diverse

## Procedura di Risoluzione

### OPZIONE 1: Automatica (Consigliata)

1. Esegui lo script di risoluzione:
   ```
   https://tuodominio.com/risolvi-migration-duplicata.php?secret=$Magia2025!
   ```

2. Riesegui le migrations:
   ```
   https://tuodominio.com/esegui-migrations.php?secret=$Magia2025!&confirm=yes
   ```

### OPZIONE 2: Manuale (Solo se necessario)

Se le colonne esistono ma la migration non è registrata, registrala manualmente:

```sql
-- Verifica se le colonne esistono
DESCRIBE utenti;

-- Se esistono, registra la migration
INSERT INTO migrations (migration, batch)
VALUES ('2025_11_13_120000_add_password_temp_expires_to_utenti',
        (SELECT MAX(batch) + 1 FROM (SELECT batch FROM migrations) as m));
```

## Prevenzione Futura

Le migrations sono state aggiornate per controllare sempre se le colonne esistono prima di aggiungerle:

```php
public function up(): void
{
    Schema::table('tabella', function (Blueprint $table) {
        if (!Schema::hasColumn('tabella', 'nome_colonna')) {
            $table->tipo('nome_colonna');
        }
    });
}
```

Questo rende le migrations **idempotenti** e sicure da rieseguire.

## Test

Dopo aver applicato la soluzione, verifica che:
1. Tutte le migrations siano eseguite senza errori
2. Le 14 migrations mancanti siano state completate
3. Nessun errore di colonne duplicate

## Note Tecniche

- Le colonne già esistenti **non verranno ricreate**
- Le migrations **verranno registrate** correttamente nella tabella `migrations`
- La struttura del database **rimarrà intatta**
- Nessun dato verrà perso durante il processo

## Support

In caso di problemi:
1. Esegui prima lo script di verifica per diagnosticare
2. Controlla i log degli errori
3. Verifica le connessioni database
4. Contatta il supporto tecnico se l'errore persiste
