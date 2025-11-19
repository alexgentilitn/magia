# 🚀 Istruzioni per Eseguire le Migrations

## 📁 File Creato

È stato creato il file `public/run-migrations.php` che permette di eseguire le migrations direttamente dal browser.

## 🔧 Come Usarlo

### 1. Carica il file sul server
Assicurati che il file `public/run-migrations.php` sia presente nella cartella `public/` del tuo server.

### 2. Apri il browser
Vai all'URL:
```
http://tuosito.com/run-migrations.php?token=magia2025
```

**⚠️ IMPORTANTE:** Sostituisci `tuosito.com` con il tuo dominio reale!

### 3. Controlla l'output
Lo script mostrerà:
- ✅ Verifica connessione database
- 📋 Lista migrations pending
- ⚙️ Esecuzione migrations in tempo reale
- 🗄️ Verifica tabelle create
- 📊 Stato finale

## 🔒 Sicurezza

### Token di Sicurezza
Il file è protetto da un token. Il token predefinito è `magia2025`.

**Per cambiare il token:**
1. Apri `public/run-migrations.php`
2. Modifica la riga:
   ```php
   define('MIGRATION_TOKEN', 'magia2025');
   ```
3. Usa il nuovo token nell'URL: `?token=TUO_NUOVO_TOKEN`

### ⚠️ ELIMINA IL FILE DOPO L'USO!
Per sicurezza, **ELIMINA SUBITO** il file dopo aver eseguito le migrations:

```bash
rm public/run-migrations.php
```

Oppure via FTP, cancella il file `public/run-migrations.php`.

## 📊 Cosa Fanno le Migrations

Le 3 migrations che verranno eseguite:

### 1. `create_professionisti_table.php`
Crea la tabella principale `professionisti` con tutti i campi:
- Dati anagrafici (nome, cognome, codice_fiscale, data_nascita, sesso)
- Contatti e indirizzo completo
- Dati professionali (bio, esperienza, partita_iva, contratto)
- Tariffe (oraria, gruppo, privata)
- Disponibilità settimanale e temporale
- Media (foto profilo, galleria, video)
- Social media (Instagram, Facebook, LinkedIn, TikTok)
- Lingue parlate
- Certificazioni e qualifiche
- Performance e statistiche

### 2. `add_pending_stato_to_professionisti.php`
Aggiunge lo stato `pending` per il workflow di approvazione:
- `pending` → professionista in attesa di approvazione
- `attivo` → professionista approvato e attivo
- `sospeso` → professionista temporaneamente sospeso
- `inattivo` → professionista disattivato

### 3. `create_professionista_documenti_table.php`
Crea la tabella `professionista_documenti` per gestire:
- Certificati e attestati
- Curriculum Vitae
- Documenti d'identità
- Contratti
- Altri documenti

Con funzionalità:
- Upload/download documenti
- Verifica documenti da parte admin
- Gestione scadenze
- Tracking dimensione e tipo file

## 🐛 Risoluzione Problemi

### Errore: "Accesso Negato"
→ Hai dimenticato il token nell'URL
→ Soluzione: Aggiungi `?token=magia2025` all'URL

### Errore: "SQLSTATE[HY000] [2002] No such file or directory"
→ Il database non è raggiungibile
→ Soluzione: Verifica le credenziali in `.env`:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nome_database
DB_USERNAME=utente
DB_PASSWORD=password
```

### Errore: "Database not found"
→ Il database non esiste
→ Soluzione: Crea il database:
```sql
CREATE DATABASE nome_database CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### Errore: "Access denied for user"
→ Credenziali database errate
→ Soluzione: Verifica username e password in `.env`

### Errore: "Syntax error in migration"
→ Versione MySQL/MariaDB incompatibile
→ Soluzione: Verifica di avere MySQL 5.7+ o MariaDB 10.2+

## ✅ Dopo le Migrations

Una volta completate con successo, puoi:

1. **Accedere all'area admin:**
   ```
   http://tuosito.com/admin/professionisti
   ```

2. **Creare il primo professionista** usando la form completa con tutti i campi

3. **Testare le funzionalità:**
   - Upload foto profilo
   - Gestione documenti
   - Galleria foto
   - Workflow approvazione
   - Gestione disponibilità
   - Certificazioni

## 📞 Supporto

Se riscontri problemi:
1. Controlla i log Laravel in `storage/logs/laravel.log`
2. Attiva il debug in `.env`: `APP_DEBUG=true`
3. Controlla che tutte le dipendenze siano installate: `composer install`

---

**Buon lavoro! 🎯**
