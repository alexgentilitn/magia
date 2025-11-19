# 🗄️ Database Setup - Sistema Pagamenti MA.GIA DONNA

Questa cartella contiene gli script PHP per impostare le tabelle e i campi necessari al funzionamento del **Sistema di Gestione Pagamenti** (Fase 1 + Fase 1.2 del progetto).

---

## 📋 **Cosa Fanno Questi Script**

### **Script 001** - `create_pagamenti_professionisti_table.php`
Crea la tabella `pagamenti_professionisti` per tracciare:
- ✅ Pagamenti effettivi ai professionisti/collaboratori
- ✅ Periodo di riferimento (da/a)
- ✅ Importo maturato vs importo pagato
- ✅ Ritenuta fiscale (20% collaboratori)
- ✅ Metodo pagamento (bonifico, contante, assegno)
- ✅ Storico completo pagamenti

### **Script 002** - `add_payment_fields_to_utenti_table.php`
Aggiunge campi alla tabella `utenti` per gestire:
- ✅ Pagamenti registrazione clienti via **PayPal**
- ✅ Caricamento ricevuta **Bonifico Bancario**
- ✅ Verifica admin bonifici in attesa
- ✅ Stato pagamento (in_attesa, completato, rifiutato)
- ✅ Storico transazioni PayPal

---

## 🚀 **Come Eseguire gli Script**

### **Metodo 1: Interfaccia Web (Consigliato)**

1. **Accedi al pannello di controllo:**
   ```
   http://tuodominio.com/database/setup/
   ```

2. **Esegui gli script in sequenza:**
   - Clicca su "Esegui →" per lo Script 001
   - Attendi il completamento (schermata verde ✅)
   - Clicca su "Esegui →" per lo Script 002
   - Verifica che non ci siano errori

3. **Controlla i risultati:**
   - Ogni script mostra la struttura delle tabelle create
   - Verifica che tutti i campi siano presenti
   - Controlla eventuali warning/errori

### **Metodo 2: Esecuzione Diretta**

Puoi anche eseguire gli script direttamente digitando l'URL:

```
http://tuodominio.com/database/setup/001_create_pagamenti_professionisti_table.php
http://tuodominio.com/database/setup/002_add_payment_fields_to_utenti_table.php
```

---

## ⚙️ **Configurazione Post-Setup**

### **1. Configurazione PayPal**

Dopo aver eseguito gli script, aggiungi le credenziali PayPal al file `.env`:

```env
# PayPal Configuration
PAYPAL_MODE=sandbox                    # sandbox o live
PAYPAL_CLIENT_ID=your_client_id_here
PAYPAL_CLIENT_SECRET=your_secret_here
```

**Come ottenere le credenziali:**
1. Vai su [PayPal Developer](https://developer.paypal.com/)
2. Accedi con il tuo account PayPal Business
3. Vai su "My Apps & Credentials"
4. Crea una nuova app (o usa quella esistente)
5. Copia **Client ID** e **Secret**

### **2. Configurazione Storage**

Verifica che la cartella storage sia scrivibile:

```bash
chmod -R 775 storage/app/private/bonifici
chmod -R 775 storage/app/private/ricevute_pagamenti
```

### **3. Test Funzionalità**

**Test Pagamenti Clienti (PayPal):**
1. Vai alla pagina di registrazione: `/registrazione`
2. Compila il form e invia
3. Seleziona metodo "PayPal"
4. Verifica redirect a PayPal Sandbox
5. Completa pagamento test
6. Verifica redirect success e attivazione account

**Test Bonifici Clienti:**
1. Vai alla pagina di registrazione
2. Seleziona metodo "Bonifico"
3. Carica una ricevuta di test
4. Vai su `/admin/pagamenti/bonifici`
5. Verifica/Approva il bonifico
6. Controlla attivazione account cliente

**Test Pagamenti Professionisti:**
1. Vai su `/admin/professionisti/pagamenti/crea`
2. Seleziona un professionista con lezioni completate
3. Imposta periodo con lezioni
4. Clicca "Calcola Compenso" (AJAX)
5. Verifica calcolo automatico: maturato, ritenuta 20%, netto
6. Salva pagamento
7. Verifica visualizzazione in `/professionista/compensi`

---

## 🔍 **Struttura Database Creata**

### **Tabella: `pagamenti_professionisti`**

| Campo                 | Tipo            | Descrizione                           |
|-----------------------|-----------------|---------------------------------------|
| id                    | BIGINT (PK)     | ID univoco                            |
| professionista_id     | BIGINT (FK)     | Riferimento a professionisti.id       |
| utente_id             | BIGINT (FK)     | Riferimento a utenti.id               |
| periodo_da            | DATE            | Inizio periodo compenso               |
| periodo_a             | DATE            | Fine periodo compenso                 |
| importo_maturato      | DECIMAL(10,2)   | Compenso lordo maturato               |
| importo_pagato        | DECIMAL(10,2)   | Importo effettivamente pagato         |
| ritenuta_fiscale      | DECIMAL(10,2)   | Ritenuta d'acconto 20%                |
| importo_netto         | DECIMAL(10,2)   | Netto da pagare                       |
| metodo_pagamento      | ENUM            | bonifico, contante, assegno           |
| data_pagamento        | DATETIME        | Data pagamento effettivo              |
| numero_bonifico       | VARCHAR(100)    | Numero bonifico (opzionale)           |
| iban                  | VARCHAR(34)     | IBAN professionista (opzionale)       |
| stato                 | ENUM            | completato, in_attesa, annullato      |
| ricevuta_path         | VARCHAR         | Path ricevuta pagamento               |
| note                  | TEXT            | Note admin                            |
| pagato_da             | BIGINT (FK)     | Admin che ha registrato il pagamento  |
| created_at            | TIMESTAMP       | Data creazione record                 |
| updated_at            | TIMESTAMP       | Data ultima modifica                  |

**Indici creati:**
- `utente_id` (performance query per professionista)
- `professionista_id`
- `data_pagamento` (ordinamento storico)
- `stato` (filtri rapidi)
- `periodo_da, periodo_a` (query per range date)

### **Campi Aggiunti a `utenti`**

| Campo                     | Tipo            | Descrizione                           |
|---------------------------|-----------------|---------------------------------------|
| metodo_pagamento          | ENUM            | paypal, bonifico                      |
| stato_pagamento           | ENUM            | in_attesa, completato, rifiutato      |
| data_pagamento            | DATETIME        | Data completamento pagamento          |
| importo_pagamento         | DECIMAL(10,2)   | Importo registrazione                 |
| paypal_order_id           | VARCHAR(100)    | ID ordine PayPal                      |
| paypal_payer_id           | VARCHAR(100)    | ID pagatore PayPal                    |
| ricevuta_bonifico_path    | VARCHAR         | Path ricevuta bonifico caricata       |
| data_bonifico             | DATE            | Data bonifico dichiarata              |
| note_verifica_bonifico    | TEXT            | Note admin su verifica                |

**Indici creati:**
- `stato_pagamento` (filtri rapidi bonifici da verificare)
- `data_pagamento` (ordinamento cronologico)
- `paypal_order_id` (lookup transazioni PayPal)

---

## 🔒 **Sicurezza - IMPORTANTE!**

### ⚠️ **DOPO L'ESECUZIONE DEGLI SCRIPT:**

1. **CANCELLA questa cartella:**
   ```bash
   rm -rf /path/to/magia/database/setup/
   ```

2. **Oppure proteggi con .htaccess:**
   Crea un file `.htaccess` in `/database/setup/`:
   ```apache
   Order Deny,Allow
   Deny from all
   ```

3. **Oppure sposta fuori dalla webroot:**
   ```bash
   mv database/setup/ ../backup_migrations/
   ```

**Perché è importante:**
- Questi script modificano il database in modo permanente
- Un accesso non autorizzato potrebbe corrompere i dati
- Script esposti = vulnerabilità di sicurezza

---

## 🐛 **Troubleshooting**

### **Errore: "Table already exists"**
✅ **Soluzione:** Lo script ha rilevato che la tabella esiste già. Nessuna azione necessaria. Verifica la struttura mostrata nello script.

### **Errore: "Column already exists"**
✅ **Soluzione:** Il campo esiste già. Lo script salta automaticamente i campi esistenti e aggiunge solo quelli mancanti.

### **Errore: "Access denied"**
❌ **Problema:** L'utente database non ha i permessi sufficienti.
✅ **Soluzione:** Verifica le credenziali in `.env` e assicurati che l'utente abbia privilegi `CREATE` e `ALTER`.

### **Errore: "SQLSTATE[42000]"**
❌ **Problema:** Errore sintassi SQL o incompatibilità versione MySQL.
✅ **Soluzione:**
- Verifica versione MySQL/MariaDB (richiesto >= 5.7)
- Controlla il log completo mostrato nello script
- Esegui manualmente la query problematica per debug

### **Errore: "Call to undefined function"**
❌ **Problema:** Autoload Laravel non caricato correttamente.
✅ **Soluzione:**
```bash
composer dump-autoload
php artisan config:clear
php artisan cache:clear
```

---

## 📊 **Verifica Post-Installazione**

### **Query di Verifica Manuale**

```sql
-- Verifica struttura pagamenti_professionisti
DESCRIBE pagamenti_professionisti;

-- Verifica campi utenti
SELECT COLUMN_NAME, COLUMN_TYPE
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_NAME = 'utenti'
AND COLUMN_NAME LIKE '%pagamento%';

-- Test query pagamenti
SELECT COUNT(*) FROM pagamenti_professionisti;

-- Test utenti con pagamenti
SELECT COUNT(*) FROM utenti WHERE metodo_pagamento IS NOT NULL;
```

---

## 📞 **Supporto**

Se riscontri problemi durante l'esecuzione degli script:

1. **Controlla i log Laravel:**
   ```bash
   tail -f storage/logs/laravel.log
   ```

2. **Verifica permessi:**
   ```bash
   ls -la storage/app/private/
   ```

3. **Test connessione database:**
   ```bash
   php artisan tinker
   >>> DB::connection()->getPdo();
   ```

4. **Backup prima di procedere:**
   ```bash
   php artisan db:backup  # se disponibile
   # oppure
   mysqldump -u user -p database > backup.sql
   ```

---

## ✅ **Checklist Finale**

Prima di considerare il setup completo, verifica:

- [ ] Script 001 eseguito senza errori
- [ ] Script 002 eseguito senza errori
- [ ] Tabella `pagamenti_professionisti` creata con tutti i campi
- [ ] Campi pagamento aggiunti a tabella `utenti`
- [ ] Indici database creati correttamente
- [ ] Credenziali PayPal configurate in `.env`
- [ ] Storage `private/bonifici` creato e scrivibile
- [ ] Test registrazione PayPal funzionante
- [ ] Test caricamento bonifico funzionante
- [ ] Test calcolo pagamenti professionisti funzionante
- [ ] **Cartella `/database/setup/` cancellata o protetta**

---

## 🎉 **Funzionalità Abilitate**

Dopo il setup, il sistema supporta:

### **Area Admin:**
- ✅ Gestione completa pagamenti professionisti (CRUD)
- ✅ Calcolo automatico compensi da lezioni
- ✅ Storico pagamenti per professionista
- ✅ Verifica bonifici registrazione clienti
- ✅ Approvazione/rifiuto bonifici con motivazione
- ✅ Dashboard statistiche pagamenti

### **Area Professionista:**
- ✅ Visualizzazione compensi maturati vs pagati
- ✅ Differenza "da ricevere" in tempo reale
- ✅ Storico completo pagamenti ricevuti
- ✅ Grafico comparativo maturato/pagato (Chart.js)
- ✅ Dettaglio lezioni per periodo

### **Area Pubblica:**
- ✅ Registrazione con scelta pagamento (PayPal/Bonifico)
- ✅ Integrazione PayPal Checkout v2
- ✅ Caricamento ricevuta bonifico con preview
- ✅ Email conferma post-pagamento
- ✅ Attivazione automatica account

---

**Versione:** 1.0
**Data:** Novembre 2025
**Progetto:** MA.GIA DONNA - Sistema Gestione Palestra
**Sviluppatore:** Claude AI Assistant
