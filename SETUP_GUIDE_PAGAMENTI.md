# 🚀 Guida Setup Sistema Pagamenti - MA.GIA DONNA

## 📦 **Cosa è Stato Implementato**

Sono state completate **Fase 1** e **Fase 1.2** del progetto:

### ✅ **Fase 1 - Sistema Pagamenti Clienti**
- Registrazione con scelta metodo pagamento (PayPal / Bonifico)
- Integrazione PayPal REST API v2
- Caricamento ricevuta bonifico bancario
- Pannello admin verifica bonifici
- Email conferma registrazione post-pagamento
- Attivazione automatica account

### ✅ **Fase 1.2 - Storico Pagamenti Professionisti**
- Tracciamento pagamenti effettivi ai collaboratori
- Calcolo automatico compenso da lezioni completate (AJAX)
- Ritenuta fiscale 20% automatica
- Dashboard statistiche pagamenti (admin)
- Visualizzazione compensi maturati vs pagati (professionista)
- Storico completo con grafico Chart.js

---

## 🗄️ **STEP 1: Setup Database**

### **Accesso Pannello Setup**

```
http://tuodominio.com/database/setup/
```

### **Esecuzione Script**

1. **Script 001** - Crea tabella `pagamenti_professionisti`
   - Esegui: `http://tuodominio.com/database/setup/001_create_pagamenti_professionisti_table.php`
   - Verifica: Schermata verde ✅ con struttura tabella

2. **Script 002** - Aggiunge campi pagamento a tabella `utenti`
   - Esegui: `http://tuodominio.com/database/setup/002_add_payment_fields_to_utenti_table.php`
   - Verifica: 9 campi aggiunti con successo

3. **Sicurezza** - DOPO l'esecuzione:
   ```bash
   rm -rf database/setup/
   # oppure
   mv database/setup/ ../backup_migrations/
   ```

**Documentazione completa:** `/database/setup/README.md`

---

## ⚙️ **STEP 2: Configurazione PayPal**

### **2.1 - Ottieni Credenziali PayPal**

1. Vai su [PayPal Developer](https://developer.paypal.com/)
2. Login con account PayPal Business
3. Vai su "My Apps & Credentials"
4. Crea una nuova app (o usa esistente)
5. Copia **Client ID** e **Secret**

### **2.2 - Configura .env**

Aggiungi al file `.env`:

```env
# PayPal Configuration
PAYPAL_MODE=sandbox                          # sandbox per test, live per produzione
PAYPAL_CLIENT_ID=your_sandbox_client_id
PAYPAL_CLIENT_SECRET=your_sandbox_secret

# Per produzione (quando pronto):
# PAYPAL_MODE=live
# PAYPAL_CLIENT_ID=your_live_client_id
# PAYPAL_CLIENT_SECRET=your_live_secret
```

### **2.3 - Test Account PayPal Sandbox**

PayPal fornisce account test automaticamente:
- **Buyer Account:** Use per testare acquisti
- **Merchant Account:** Riceve i pagamenti test

Trova gli account test su: Developer Dashboard → Sandbox → Accounts

---

## 📁 **STEP 3: Verifica Storage**

### **Crea e Verifica Permessi Cartelle**

```bash
# Crea cartelle storage se non esistono
mkdir -p storage/app/private/bonifici
mkdir -p storage/app/private/ricevute_pagamenti

# Imposta permessi
chmod -R 775 storage/app/private/bonifici
chmod -R 775 storage/app/private/ricevute_pagamenti

# Verifica owner (www-data per Apache, nginx per Nginx)
chown -R www-data:www-data storage/app/private/
```

---

## 🧪 **STEP 4: Testing Funzionalità**

### **Test 1: Registrazione Cliente - PayPal**

1. Vai su: `http://tuodominio.com/registrazione`
2. Compila form registrazione completo
3. Seleziona metodo: **PayPal**
4. Verifica redirect a PayPal Sandbox
5. Login con account buyer test
6. Conferma pagamento
7. Verifica redirect success: `http://tuodominio.com/pagamento/paypal/success`
8. Controlla:
   - ✅ Email conferma ricevuta
   - ✅ Account attivato (`utenti.attivo = 1`)
   - ✅ `stato_pagamento = 'completato'`
   - ✅ PayPal Order ID salvato

**Query verifica:**
```sql
SELECT id, nome, cognome, email, stato_pagamento, metodo_pagamento, paypal_order_id
FROM utenti
WHERE email = 'email_test@example.com';
```

---

### **Test 2: Registrazione Cliente - Bonifico**

1. Vai su: `http://tuodominio.com/registrazione`
2. Compila form registrazione
3. Seleziona metodo: **Bonifico Bancario**
4. Visualizza coordinate bancarie (IBAN, causale)
5. Carica ricevuta PDF/immagine (file test)
6. Verifica messaggio conferma caricamento
7. Controlla tabella `utenti`:
   ```sql
   SELECT id, nome, cognome, email, stato_pagamento, ricevuta_bonifico_path
   FROM utenti
   WHERE stato_pagamento = 'in_attesa';
   ```

**Verifica Admin - Approvazione Bonifico:**

1. Login come admin
2. Vai su: `/admin/pagamenti/bonifici`
3. Verifica dashboard "Bonifici Da Verificare"
4. Click "Visualizza" ricevuta → Apre PDF
5. Click "Approva" → Compila note (opzionale)
6. Conferma approvazione
7. Verifica:
   - ✅ `stato_pagamento = 'completato'`
   - ✅ `attivo = 1`
   - ✅ Email conferma inviata al cliente
   - ✅ Cliente può fare login

**Test Rifiuto Bonifico:**
1. Click "Rifiuta" su bonifico in attesa
2. Inserisci motivazione (es: "Importo errato")
3. Conferma rifiuto
4. Verifica:
   - ✅ `stato_pagamento = 'rifiutato'`
   - ✅ `note_verifica_bonifico` salvate
   - ✅ Account NON attivato

---

### **Test 3: Pagamenti Professionisti**

**Prerequisiti:**
- Almeno 1 professionista con `tariffa_oraria` configurata
- Almeno 3-4 lezioni completate nel periodo scelto

**Step Test:**

1. Login come admin
2. Vai su: `/admin/professionisti/pagamenti/crea`
3. Seleziona professionista dal dropdown
4. Imposta periodo (es: 01/11/2025 - 30/11/2025)
5. Click **"Calcola Compenso"** (AJAX)
6. Verifica auto-compilazione:
   - Importo Maturato (calcolato da lezioni)
   - Ritenuta Fiscale 20%
   - Importo Netto
   - Numero lezioni trovate
   - Ore totali
7. Seleziona metodo pagamento (Bonifico)
8. Compila numero bonifico e IBAN (opzionale)
9. Aggiungi note (opzionale)
10. Click "Registra Pagamento"

**Verifica Database:**
```sql
SELECT * FROM pagamenti_professionisti ORDER BY id DESC LIMIT 1;
```

**Verifica Area Professionista:**

1. Logout admin, login come professionista
2. Vai su: `/professionista/compensi`
3. Verifica 4 cards:
   - Compenso Maturato (viola)
   - **Totale Pagato** (verde) ← Deve mostrare il pagamento appena creato
   - **Da Ricevere** (arancione) ← Differenza maturato-pagato
   - Tariffa Oraria (blu)
4. Verifica widget "Ultimi Pagamenti Ricevuti"
5. Verifica grafico Chart.js con doppia barra (maturato vs pagato)
6. Verifica tabella mensile con colonne "Maturato" e "Pagato"
7. Click "Vedi tutti" → Storico completo

---

## 🐛 **Troubleshooting**

### **Errore: "Class 'PagamentoProfessionista' not found"**

```bash
composer dump-autoload
php artisan config:clear
php artisan cache:clear
```

### **Errore: "Table doesn't exist"**

Gli script di setup non sono stati eseguiti. Vai su `/database/setup/` ed esegui gli script.

### **PayPal Redirect Error**

Verifica:
1. `.env` ha credenziali corrette
2. `PAYPAL_MODE=sandbox` (non live)
3. Route `pagamento.paypal.success` esiste in `routes/web.php`
4. URL return/cancel configurati correttamente

**Debug PayPal:**
```php
// In PayPalService.php, aggiungi:
Log::info('PayPal Response', ['response' => $response->json()]);
```

### **Bonifico Upload Fallisce**

Verifica permessi storage:
```bash
ls -la storage/app/private/bonifici/
# Deve essere scrivibile da www-data
```

### **Calcola Compenso AJAX Non Funziona**

1. Apri DevTools → Network
2. Click "Calcola Compenso"
3. Verifica richiesta POST a `/admin/professionisti/pagamenti/calcola`
4. Controlla response JSON:
   ```json
   {
     "compenso_maturato": "150.00",
     "ritenuta_fiscale": "30.00",
     "importo_netto": "120.00",
     "numero_lezioni": 3,
     "ore_totali": 3
   }
   ```

**Se 500 Error:**
- Controlla `storage/logs/laravel.log`
- Verifica che esistano lezioni completate nel periodo
- Verifica `tariffa_oraria` del professionista non sia NULL

---

## 📋 **Checklist Setup Completo**

Prima di considerare il sistema pronto:

### Database
- [ ] Script 001 eseguito senza errori
- [ ] Script 002 eseguito senza errori
- [ ] Tabella `pagamenti_professionisti` creata
- [ ] 9 campi aggiunti a tabella `utenti`
- [ ] Cartella `/database/setup/` cancellata/protetta

### Configurazione
- [ ] Credenziali PayPal Sandbox in `.env`
- [ ] Storage `private/bonifici` creato e scrivibile
- [ ] Storage `private/ricevute_pagamenti` creato e scrivibile
- [ ] Config cache cleared (`php artisan config:clear`)

### Test Pagamenti Clienti
- [ ] Registrazione PayPal funzionante
- [ ] Caricamento bonifico funzionante
- [ ] Pannello verifica bonifici accessibile
- [ ] Approvazione bonifico → Attiva account
- [ ] Rifiuto bonifico → NON attiva account
- [ ] Email conferma ricevuta post-pagamento

### Test Pagamenti Professionisti
- [ ] Form creazione pagamento accessibile
- [ ] Calcolo AJAX compenso funzionante
- [ ] Salvataggio pagamento in DB
- [ ] Visualizzazione in area professionista
- [ ] 4 cards statistiche mostrano dati corretti
- [ ] Widget "Ultimi Pagamenti" popolato
- [ ] Grafico Chart.js mostra maturato vs pagato
- [ ] Tabella mensile mostra entrambe le colonne

### Produzione (quando pronto)
- [ ] PayPal: Switch a credenziali LIVE
- [ ] `.env`: `PAYPAL_MODE=live`
- [ ] Test completo in sandbox prima del go-live
- [ ] Backup database pre-produzione

---

## 📂 **Files Creati/Modificati**

### **Nuovi Files Backend:**
```
app/Models/PagamentoProfessionista.php
app/Http/Controllers/Admin/PagamentiProfessionistiController.php
app/Http/Controllers/PagamentoClienteController.php
app/Services/PayPalService.php
app/Mail/ConfermaRegistrazione.php
```

### **Nuove Views Admin:**
```
resources/views/admin/professionisti/pagamenti/index.blade.php
resources/views/admin/professionisti/pagamenti/create.blade.php
resources/views/admin/professionisti/pagamenti/show.blade.php
resources/views/admin/professionisti/pagamenti/edit.blade.php
resources/views/admin/professionisti/pagamenti/storico.blade.php
resources/views/admin/pagamenti/bonifici.blade.php
```

### **Nuove Views Registrazione:**
```
resources/views/registrazione/pagamento.blade.php
resources/views/registrazione/bonifico.blade.php
resources/views/registrazione/bonifico-caricato.blade.php
resources/views/emails/conferma-registrazione.blade.php
```

### **Views Modificate:**
```
resources/views/professionista/compensi/index.blade.php  (integrato pagamenti reali)
app/Http/Controllers/Professionista/CompensiController.php  (aggiunto PagamentoProfessionista)
```

### **Configurazione:**
```
config/services.php  (aggiunta config PayPal)
routes/web.php  (aggiunte route pagamenti)
```

### **Database Setup:**
```
database/setup/001_create_pagamenti_professionisti_table.php
database/setup/002_add_payment_fields_to_utenti_table.php
database/setup/index.php
database/setup/README.md
```

---

## 🎯 **Prossime Fasi**

Dopo aver completato questo setup, le prossime funzionalità da implementare sono:

### **Fase 2 - Chat Interna** (5-7 giorni)
- Messaggistica real-time tra admin/professionisti/clienti
- Notifiche in-app

### **Fase 2 - Notifiche Push** (2-3 giorni)
- Browser push notifications
- Email notifications avanzate

### **Fase 3 - Ottimizzazioni** (3-4 giorni)
- Scheduler pubblicazione ricette
- Variabili dinamiche email
- Storico programmi cliente

---

## 📞 **Supporto**

**Documentazione:**
- Setup Database: `/database/setup/README.md`
- Feature Tracker: `/FEATURE_TRACKER.md` (progresso generale)

**Log e Debug:**
```bash
# Laravel logs
tail -f storage/logs/laravel.log

# PayPal debug
# Abilita in PayPalService.php:
Log::info('PayPal', $response);

# Database queries
# Abilita in .env:
DB_LOG_QUERIES=true
```

---

**Versione:** 1.0
**Data:** Novembre 2025
**Status:** ✅ Fase 1 + 1.2 Complete - Pronto per Testing
