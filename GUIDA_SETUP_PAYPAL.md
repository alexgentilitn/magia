# 🔧 GUIDA SETUP PAYPAL - MA.GIA DONNA

**Data:** 2025-11-17
**Fase:** Sistema Pagamenti Clienti (Fase 1 - Priorità Critica)

---

## 📋 STATO ATTUALE

✅ **Codice completo e funzionante:**
- Controller: `PagamentoClienteController.php`
- Service: `PayPalService.php`
- Route: Tutte configurate in `web.php`
- Views: Tutte create (pagamento, bonifico, bonifici admin)
- Database: Tabelle create e campi aggiunti

❌ **Mancante:**
- Credenziali PayPal nel file `.env` del server

---

## 🎯 STEP 1: OTTENERE CREDENZIALI PAYPAL SANDBOX

### 1.1 Accedi al PayPal Developer Dashboard

- **URL:** https://developer.paypal.com/dashboard/
- Accedi con il tuo account PayPal Business esistente
- Se non hai un account, creane uno su https://www.paypal.com/bizsignup/

### 1.2 Crea un'App Sandbox

1. Nella dashboard, vai su **"My Apps & Credentials"**
2. Seleziona il tab **"Sandbox"**
3. Click sul bottone **"Create App"**
4. Inserisci questi dati:
   - **App Name:** `MA.GIA DONNA - Test`
   - **Sandbox Business Account:** Seleziona l'account di test (o creane uno)
5. Click su **"Create App"**

### 1.3 Copia le Credenziali

Dopo aver creato l'app vedrai:

```
Client ID: AeT7X... (circa 80 caratteri)
Secret: EJL9... (circa 80 caratteri)
```

**⚠️ IMPORTANTE:** Copia ENTRAMBE le credenziali in un posto sicuro. Le userai nel prossimo step.

---

## 🖥️ STEP 2: CONFIGURARE IL SERVER (VIA FTP)

**⚠️ ATTENZIONE:** Il file `.env` NON viene deployato automaticamente. Devi modificarlo manualmente via FTP.

### 2.1 Connettiti al Server FTP

- **Host:** ftp.agstudio.digital
- **Username:** magia@agstudio.digital
- **Path:** `/home/agstudiodiital/agstudio.digital/magia/`

### 2.2 Scarica il File .env

1. Naviga nella directory del progetto
2. Trova il file `.env` (nella root del progetto)
3. Scaricalo sul tuo computer per fare un backup
4. Apri il file con un editor di testo

### 2.3 Aggiungi la Configurazione PayPal

Alla fine del file `.env`, aggiungi queste righe:

```env
# ============================================
# PAYPAL CONFIGURATION
# ============================================
# Mode: sandbox (test) o live (produzione)
PAYPAL_MODE=sandbox

# Credenziali PayPal Sandbox (per test)
PAYPAL_CLIENT_ID=IL_TUO_CLIENT_ID_QUI
PAYPAL_CLIENT_SECRET=IL_TUO_SECRET_QUI

# Quando sei pronto per la produzione:
# 1. Cambia PAYPAL_MODE=live
# 2. Sostituisci con credenziali LIVE dal PayPal Dashboard
```

**Sostituisci:**
- `IL_TUO_CLIENT_ID_QUI` → con il Client ID copiato nello Step 1.3
- `IL_TUO_SECRET_QUI` → con il Secret copiato nello Step 1.3

### 2.4 Carica il File Modificato

1. Salva il file `.env` modificato
2. Caricalo via FTP sovrascrivendo il file esistente
3. Verifica che i permessi siano corretti (644)

### 2.5 Clear Cache Laravel

Vai su: https://www.agstudio.digital/magia/public/clear-cache.php

Questo caricherà la nuova configurazione.

---

## 🧪 STEP 3: TESTING DEL SISTEMA PAGAMENTI

### 3.1 Test PayPal Sandbox

**URL Test:** https://www.agstudio.digital/magia/public/registrazione

1. **Registra un nuovo utente di test:**
   - Compila il form di registrazione
   - Usa dati fittizi (email valida ma di test)
   - Usa codice fiscale di test: `RSSMRA85M01H501U`

2. **Verrai reindirizzato alla pagina di scelta pagamento:**
   - Dovresti vedere 2 opzioni: PayPal e Bonifico
   - Click su **"Paga con PayPal"**

3. **Verifica redirect a PayPal:**
   - Dovresti essere reindirizzato su `sandbox.paypal.com`
   - Se vedi errore → Controlla credenziali in `.env`

4. **Effettua il pagamento di test:**
   - **Email PayPal Sandbox:** `sb-buyer@personal.example.com`
   - **Password:** (generata automaticamente, trovila nella dashboard PayPal)
   - Oppure: Click su "Pay with Debit or Credit Card" per test senza login

5. **Conferma il pagamento**

6. **Verifica redirect di successo:**
   - Dovresti tornare su `agstudio.digital/magia/public/pagamento/paypal/success`
   - Lo stato utente dovrebbe passare a `attivo`
   - Dovresti poter fare login

### 3.2 Test Bonifico con Ricevuta

1. **Registra un nuovo utente di test**

2. **Scegli "Bonifico Bancario"**

3. **Verifica form upload:**
   - Dovresti vedere IBAN del centro
   - Importo da pagare (€50.00)
   - Form per caricare ricevuta

4. **Carica una ricevuta di test:**
   - Usa un'immagine JPG/PNG qualsiasi
   - Inserisci una data bonifico
   - Aggiungi note opzionali
   - Click "Conferma Caricamento"

5. **Verifica stato "in_verifica":**
   - Lo stato utente deve essere `in_verifica`
   - L'utente NON può ancora fare login

6. **Vai nell'area admin:**
   - Login: https://www.agstudio.digital/magia/public/admin/login
   - Vai su: **Pagamenti → Bonifici da Verificare**

7. **Verifica la ricevuta:**
   - Dovresti vedere il bonifico caricato
   - Click "Visualizza Ricevuta" → Vedi l'immagine caricata
   - Click "Approva" → Utente diventa `attivo`

8. **Verifica attivazione:**
   - Torna al login cliente
   - Prova a fare login con l'utente approvato
   - Dovresti accedere alla dashboard

### 3.3 Test Email di Conferma (Opzionale)

**Nota:** Richiede configurazione SMTP funzionante

Dopo l'approvazione di un pagamento, verifica che venga inviata:
- Email a: utente registrato
- Oggetto: "Benvenuta in MA.GIA DONNA!"
- Template: `emails/conferma-registrazione.blade.php`

---

## 📊 STEP 4: VERIFICA DATI IN DATABASE

Dopo i test, verifica in phpMyAdmin:

### Tabella `utenti`:

```sql
SELECT
    id,
    nome,
    cognome,
    email,
    stato_pagamento,
    metodo_pagamento,
    importo_pagamento,
    data_pagamento,
    stato
FROM utenti
WHERE email LIKE '%test%'
ORDER BY id DESC;
```

**Verifica:**
- `stato_pagamento` = `completato` (per PayPal) o `in_verifica` (per bonifico non approvato)
- `metodo_pagamento` = `paypal` o `bonifico`
- `importo_pagamento` = `50.00`
- `data_pagamento` = timestamp del pagamento
- `stato` = `attivo` (dopo approvazione)

### Controllo ricevute caricate:

```sql
SELECT
    id,
    nome,
    email,
    ricevuta_bonifico_path,
    data_bonifico,
    note_verifica_bonifico
FROM utenti
WHERE metodo_pagamento = 'bonifico'
ORDER BY id DESC;
```

---

## 🚀 STEP 5: PASSAGGIO IN PRODUZIONE (QUANDO PRONTO)

### 5.1 Crea App PayPal Live

1. Vai su https://developer.paypal.com/dashboard/
2. Tab **"Live"** (non Sandbox)
3. Create App → Nome: `MA.GIA DONNA - Produzione`
4. Copia le credenziali LIVE

### 5.2 Modifica .env in Produzione

```env
PAYPAL_MODE=live
PAYPAL_CLIENT_ID=live_client_id_qui
PAYPAL_CLIENT_SECRET=live_secret_qui
```

### 5.3 Test con Pagamento Reale

⚠️ **ATTENZIONE:** In modalità LIVE i pagamenti sono REALI!

- Testa con il tuo account PayPal personale
- Verifica che i soldi arrivino sul conto business
- Fai un rimborso di test per verificare il processo completo

---

## 🔍 TROUBLESHOOTING

### Errore: "Invalid credentials"

**Causa:** Client ID o Secret errati nel `.env`

**Soluzione:**
1. Verifica di aver copiato le credenziali complete
2. Verifica che non ci siano spazi prima/dopo
3. Clear cache: https://www.agstudio.digital/magia/public/clear-cache.php

---

### Errore: "INSTRUMENT_DECLINED"

**Causa:** Carta di test non valida in sandbox

**Soluzione:**
1. Usa un account PayPal Sandbox di test
2. Oppure usa le carte di test ufficiali: https://developer.paypal.com/tools/sandbox/card-testing/

---

### Errore: "Redirect URI mismatch"

**Causa:** URL di ritorno non autorizzato

**Soluzione:**
1. Vai sulla dashboard PayPal
2. Modifica l'app
3. Aggiungi URL: `https://www.agstudio.digital/magia/public/pagamento/paypal/success`

---

### Bonifico: Ricevuta non visualizzabile

**Causa:** Path storage non configurato o permessi errati

**Soluzione:**
1. Verifica che esista: `storage/app/private/bonifici/`
2. Permessi directory: 755
3. Crea symlink: `php artisan storage:link`

---

## 📞 SUPPORTO

- **Documentazione PayPal:** https://developer.paypal.com/docs/api/overview/
- **Sandbox Accounts:** https://developer.paypal.com/dashboard/accounts
- **Testing Tools:** https://developer.paypal.com/tools/

---

## ✅ CHECKLIST FINALE

Prima di considerare completo il setup, verifica:

- [ ] Credenziali PayPal Sandbox aggiunte al `.env` del server
- [ ] Cache Laravel cancellata dopo modifica `.env`
- [ ] Test registrazione → PayPal → Pagamento → Login cliente: OK
- [ ] Test registrazione → Bonifico → Upload → Verifica admin → Login: OK
- [ ] Ricevuta bonifico visualizzabile dall'admin: OK
- [ ] Email di conferma inviata dopo approvazione (se SMTP configurato): OK
- [ ] Dati correttamente salvati in tabella `utenti`: OK
- [ ] File ricevuta salvato in `storage/app/private/bonifici/`: OK

---

**🎉 SISTEMA PAGAMENTI CLIENTI: PRONTO PER L'USO!**

---

**Ultima Modifica:** 2025-11-17
**Autore:** Claude Code
**Versione:** 1.0
