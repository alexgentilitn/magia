# 🚀 Esecuzione Migrations via Browser

## 📋 File Creato

**`public/execute-migrations.php`** - Script web per eseguire le migrations dal browser

---

## 🔐 Chiave di Sicurezza

Per motivi di sicurezza, lo script richiede una chiave di accesso:

```
CHIAVE: MAGIA2025SECURE
```

---

## 🌐 Come Eseguire

### Metodo 1: Via Browser (Raccomandato)

1. **Carica il file sul server**
   - Il file è già in `public/execute-migrations.php`
   - Verrà caricato automaticamente con il prossimo deploy

2. **Accedi all'URL**
   ```
   https://tuodominio.it/execute-migrations.php?key=MAGIA2025SECURE
   ```

3. **Segui le istruzioni a schermo**
   - Lo script verificherà la connessione al database
   - Mostrerà lo stato attuale delle migrations
   - Eseguirà automaticamente le migrations
   - Mostrerà un riepilogo completo

4. **⚠️ IMPORTANTE: Elimina il file dopo l'uso!**
   ```bash
   # Via SSH
   rm public/execute-migrations.php

   # Oppure via FTP/cPanel
   # Elimina manualmente il file dalla cartella public/
   ```

---

## 📊 Cosa Fa lo Script

### Step 1: Verifica Connessione Database
- Testa la connessione al database MySQL
- Mostra nome database, driver e host
- Se la connessione fallisce, si ferma qui

### Step 2: Stato Migrations
- Mostra quali migrations sono già eseguite
- Identifica le migrations pendenti
- Visualizza l'output del comando `migrate:status`

### Step 3: Esecuzione Migrations
- Esegue le 2 nuove migrations:
  1. `add_privacy_fields_to_clienti_table` (10 campi GDPR)
  2. `add_custom_fields_to_clienti_table` (25 campi anagrafica)
- Mostra output in tempo reale
- Gestisce eventuali errori

### Step 4: Riepilogo Modifiche
- Lista dettagliata dei 35 campi aggiunti
- Categorizzazione per tipo:
  - Consensi GDPR (10)
  - Dati anagrafici (3)
  - Obiettivi (2)
  - Dati medici (5)
  - Alimentazione (3)
  - Parametri corporei (3)
  - Circonferenze (7)
  - Timestamp (2)
  - Note (1)

### Step 5: Prossimi Passi
- Link diretti alle pagine da testare:
  - Form registrazione con GDPR
  - Privacy Policy
  - Termini e Condizioni
  - Pannello admin clienti

### Statistiche Esecuzione
- Tempo di esecuzione
- Numero campi aggiunti
- Numero migrations eseguite

---

## 🎨 Interfaccia

Lo script ha un'interfaccia web moderna con:
- ✅ Design responsive (mobile-friendly)
- 🎨 Colori brandizzati MA.GIA DONNA (rosa/viola)
- 📊 Output formattato e facile da leggere
- ⚡ Indicatori di stato visivi
- 📋 Sezioni collassabili
- 🔍 Log dettagliati

---

## 🔒 Sicurezza

### Protezioni Implementate:

1. **Chiave di Accesso Obbligatoria**
   - Senza la chiave corretta, lo script restituisce HTTP 403
   - La chiave deve essere passata via URL: `?key=MAGIA2025SECURE`

2. **File Temporaneo**
   - Lo script avvisa di eliminarlo dopo l'uso
   - Non lasciare mai questo file in produzione!

3. **Nessun Input Utente**
   - Lo script non accetta parametri pericolosi
   - Esegue solo le migrations predefinite

4. **Output Sicuro**
   - Tutti gli output sono escapati con `htmlspecialchars()`
   - Protezione contro XSS

---

## 🆘 Risoluzione Problemi

### Errore: "Accesso Negato"
**Causa**: Chiave errata o mancante
**Soluzione**: Usa l'URL completo con la chiave: `?key=MAGIA2025SECURE`

### Errore: "Connessione al database fallita"
**Causa**: Credenziali database errate in `.env`
**Soluzione**:
1. Verifica le credenziali in `.env`
2. Controlla che il database esista
3. Verifica che MySQL sia in esecuzione

### Errore: "Already exists" durante la migration
**Causa**: Migrations già eseguite in precedenza
**Soluzione**: Normale! Significa che i campi esistono già. Puoi ignorare.

### Errore: "Syntax error"
**Causa**: Versione MySQL troppo vecchia
**Soluzione**: Aggiorna MySQL a >= 5.7 o MariaDB >= 10.2

### Pagina Bianca
**Causa**: Errore PHP fatale
**Soluzione**:
1. Attiva `display_errors` in `php.ini`
2. Controlla i log PHP del server
3. Verifica che Laravel sia configurato correttamente

---

## 📝 Dopo l'Esecuzione

Una volta completate le migrations, testa:

### 1. Form Registrazione
```
https://tuodominio.it/registrazione
```
Dovresti vedere i 4 checkbox GDPR:
- ✓ Privacy Policy (obbligatorio)
- ✓ Termini e Condizioni (obbligatorio)
- ✓ Dati sensibili salute (obbligatorio)
- ✓ Marketing (opzionale)

### 2. Pagine Legali
```
https://tuodominio.it/privacy-policy
https://tuodominio.it/termini-condizioni
https://tuodominio.it/cookie-policy
```

### 3. Pannello Admin
```
https://tuodominio.it/admin/clienti/{id}/edit
```
Dovresti vedere 8 nuove sezioni:
- Obiettivi e Livello di Attività
- Dati Medici e Sanitari
- Alimentazione e Preferenze
- Parametri Corporei
- Circonferenze Corporee
- Ultime Misurazioni
- Note Professionista
- (più le sezioni esistenti)

---

## ⚠️ IMPORTANTE - POST-ESECUZIONE

### 1. Elimina lo Script
```bash
rm public/execute-migrations.php
```

### 2. Verifica Database
Connettiti al database e verifica che i campi siano stati aggiunti:
```sql
DESCRIBE clienti;
```

Dovresti vedere 35 nuovi campi.

### 3. Testa la Registrazione
- Prova a registrare una nuova cliente
- Verifica che i consensi vengano salvati
- Controlla che l'IP venga registrato

---

## 🔄 Alternative

Se preferisci NON usare lo script web, puoi:

### Via SSH
```bash
ssh user@server
cd /path/to/project
php artisan migrate --force
```

### Via File SQL
Se hai bisogno di un file SQL da importare manualmente, contatta lo sviluppatore.

---

## 📞 Supporto

Per problemi o domande:
- Controlla i log Laravel: `storage/logs/laravel.log`
- Contatta il team di sviluppo
- Consulta la documentazione Laravel

---

## ✅ Checklist Post-Migrazione

- [ ] Migrations eseguite con successo
- [ ] File `execute-migrations.php` eliminato
- [ ] Form registrazione testato
- [ ] Pagine legali accessibili
- [ ] Pannello admin aggiornato
- [ ] Database verificato (35 nuovi campi presenti)
- [ ] Prima registrazione GDPR testata
- [ ] Consensi salvati correttamente nel database

---

**Buon lavoro! 🎉**
