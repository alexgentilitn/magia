# 📝 Preferenze di Sviluppo - MA.GIA DONNA

**Documento delle preferenze tecniche e workflow preferiti dal team**

---

## 🗄️ Database & Migrations

### Creazione Tabelle Database

**PREFERENZA:** Script SQL diretti per phpMyAdmin

❌ **EVITARE:**
- Script PHP da eseguire via browser (`run-migrations.php`)
- Migration Laravel eseguite da browser

✅ **PREFERITO:**
```sql
-- Fornire sempre codice SQL completo da eseguire in phpMyAdmin
CREATE TABLE `nome_tabella` (
  ...
);

-- Includere sempre la registrazione della migration
INSERT INTO `migrations` (`migration`, `batch`) VALUES
('YYYY_MM_DD_HHMMSS_nome_migration', [batch_number]);
```

**Motivazione:**
- Accesso diretto e immediato via phpMyAdmin Aruba
- Maggior controllo sull'esecuzione
- Nessuna dipendenza da script PHP temporanei
- Più familiare per il team

---

## 📋 Formato Script SQL

Quando si fornisce uno script SQL, includere sempre:

1. **Header descrittivo**
   ```sql
   -- ============================================
   -- Script SQL: [Descrizione]
   -- Database: agstudiodiital_magia
   -- Data: YYYY-MM-DD
   -- ============================================
   ```

2. **Step numerati**
   ```sql
   -- STEP 1: Crea la tabella
   CREATE TABLE ...

   -- STEP 2: Registra migration
   INSERT INTO migrations ...
   ```

3. **Commenti sui campi**
   ```sql
   `campo` tipo NOT NULL COMMENT 'Descrizione campo',
   ```

4. **Istruzioni di verifica**
   ```sql
   -- Verifica risultato
   DESCRIBE nome_tabella;
   SELECT * FROM migrations WHERE migration LIKE '%nome_migration%';
   ```

---

## 🚀 Deploy e Workflow

### Branch Ufficiale
```
claude/fix-github-ftp-push-01QcEnaTAL1KMQVP9iPVzptc
```

### Processo Standard
1. Sviluppo su branch ufficiale
2. Commit con messaggi descrittivi
3. Push automatico → Deploy FTP
4. Verifica su produzione

---

## 📊 Logging e Debug

**Preferenze:**
- Log dettagliati per operazioni database
- Messaggi di errore in italiano
- Screenshot per problematiche UI

---

## 🔧 Configurazione Ambiente

### Server Produzione
- **Hosting:** Aruba Shared Hosting
- **Accesso:** NO SSH, solo FTP e phpMyAdmin
- **Database:** MySQL 5.7 via phpMyAdmin

### Strumenti Preferiti
- **DB Management:** phpMyAdmin
- **Deploy:** GitHub Actions + FTP
- **Cache Clear:** Script PHP custom (`clear-cache.php`)

---

## 📌 Note Aggiuntive

### Comunicazione
- **Lingua:** Sempre italiano
- **Formato risposte:** Codice SQL ready-to-use con istruzioni chiare
- **Documentazione:** Commenti inline nel codice

### Best Practices
- ✅ Testare script SQL prima di fornirli
- ✅ Includere query di verifica
- ✅ Documentare modifiche strutturali
- ✅ Mantenere batch numbers migrations consistenti

---

**Ultimo Aggiornamento:** 2025-11-17
**Responsabile:** Alex Gentili
**Versione Documento:** 1.0
