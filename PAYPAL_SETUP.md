# 💳 Configurazione PayPal - Guida Completa

Guida passo-passo per integrare PayPal nel progetto MA.GIA DONNA

---

## 📋 Indice
1. [Prerequisiti](#1-prerequisiti)
2. [Installazione PayPal SDK](#2-installazione-paypal-sdk)
3. [Creazione Account PayPal Business](#3-creazione-account-paypal-business)
4. [Configurazione API Credentials](#4-configurazione-api-credentials)
5. [Configurazione Laravel](#5-configurazione-laravel)
6. [Testing in Sandbox](#6-testing-in-sandbox)
7. [Configurazione Webhook](#7-configurazione-webhook)
8. [Go Live - Produzione](#8-go-live---produzione)
9. [Troubleshooting](#9-troubleshooting)

---

## 1. Prerequisiti

### Requisiti Tecnici
- ✅ PHP >= 8.1
- ✅ Composer installato
- ✅ Laravel 10.x
- ✅ Database configurato
- ✅ HTTPS attivo sul dominio (obbligatorio per produzione)

### Tabelle Database Necessarie
Assicurati che esistano le seguenti tabelle:
- `ordini` - Per gestire gli ordini
- `transazioni_paypal` - Per log transazioni PayPal

Se non esistono, verranno create con le migration apposite.

---

## 2. Installazione PayPal SDK

### Passo 2.1: Installa il Package via Composer

Connettiti al server via FTP/SSH ed esegui:

```bash
cd /path/to/magia
composer require paypal/rest-api-sdk-php
```

**Output atteso:**
```
Installing paypal/rest-api-sdk-php (v1.14.0)
  - Downloading paypal/rest-api-sdk-php (v1.14.0)
  - Installing paypal/rest-api-sdk-php (v1.14.0): Extracting archive
```

### Passo 2.2: Verifica Installazione

```bash
composer show paypal/rest-api-sdk-php
```

Dovresti vedere le informazioni del package installato.

---

## 3. Creazione Account PayPal Business

### Passo 3.1: Registra Account Business

1. Vai su: https://www.paypal.com/it/business
2. Clicca su **"Apri un conto business"**
3. Compila i dati richiesti:
   - Nome attività: **MA.GIA DONNA**
   - Tipologia: Servizi / Fitness
   - Email aziendale
   - Dati fiscali (Partita IVA)

### Passo 3.2: Verifica Account

1. Conferma email
2. Completa la verifica identità
3. Collega conto bancario (per ricevere pagamenti)

⚠️ **IMPORTANTE**: Il conto deve essere verificato per ricevere pagamenti in produzione.

---

## 4. Configurazione API Credentials

### Passo 4.1: Accedi a PayPal Developer

1. Vai su: https://developer.paypal.com/
2. Accedi con il tuo account PayPal Business
3. Vai su **Dashboard**

### Passo 4.2: Crea App per Sandbox (Test)

1. Vai su **"My Apps & Credentials"**
2. Seleziona **"Sandbox"**
3. Clicca su **"Create App"**
4. Compila:
   - App Name: `MA.GIA DONNA Sandbox`
   - Sandbox Business Account: (seleziona quello creato automaticamente)
5. Clicca su **"Create App"**

### Passo 4.3: Copia le Credenziali Sandbox

Vedrai due credenziali importanti:

```
Client ID (esempio):
AYSq3RDGsm34tLKJH9876FfSdfghj32tLHKJ9pu123456789_uFLmnPqr

Secret (esempio):
EJkl9Mnop4QRstu5vWxYz1aBcDefGhI2jKlMnOpQrStU3vWxYz4AbCd567890
```

⚠️ **NON CONDIVIDERE MAI IL SECRET!**

### Passo 4.4: Salva le Credenziali

Copia e salva in un luogo sicuro:
- Client ID Sandbox
- Secret Sandbox

---

## 5. Configurazione Laravel

### Passo 5.1: Modifica File .env

Aggiungi le seguenti righe al file `.env`:

```env
# ============================================
# PAYPAL CONFIGURATION
# ============================================

# Mode: sandbox (test) o live (produzione)
PAYPAL_MODE=sandbox

# Sandbox Credentials (per test)
PAYPAL_CLIENT_ID=TUO_CLIENT_ID_SANDBOX
PAYPAL_SECRET=TUO_SECRET_SANDBOX

# Webhook ID (da configurare dopo)
PAYPAL_WEBHOOK_ID=
```

### Passo 5.2: Configura config/services.php

Il file è già configurato! Verifica che contenga:

```php
'paypal' => [
    'mode' => env('PAYPAL_MODE', 'sandbox'),
    'client_id' => env('PAYPAL_CLIENT_ID'),
    'secret' => env('PAYPAL_SECRET'),
    'webhook_id' => env('PAYPAL_WEBHOOK_ID'),
],
```

### Passo 5.3: Clear Config Cache

```bash
php artisan config:clear
php artisan cache:clear
```

---

## 6. Testing in Sandbox

### Passo 6.1: Crea Account Test

1. Vai su: https://developer.paypal.com/dashboard/accounts
2. Troverai già account di test:
   - **Business Account** (venditore - MA.GIA DONNA)
   - **Personal Account** (acquirente - cliente test)

3. Annota le credenziali degli account test:
   - Email: `sb-xxxxx@business.example.com`
   - Password: (disponibile cliccando sull'account)

### Passo 6.2: Test Connessione API

Visita nel browser:
```
https://tuodominio.com/paypal/test
```

Dovresti vedere:
```json
{
  "status": "success",
  "message": "PayPal connection successful!",
  "mode": "sandbox",
  "token_preview": "A21AAKxxxxxxxxxxxx..."
}
```

### Passo 6.3: Test Pagamento Completo

1. Crea un ordine di test nel database
2. Vai su: `https://tuodominio.com/paypal/checkout?ordine_id=1`
3. Clicca su "Paga con PayPal"
4. Sarai reindirizzato a PayPal Sandbox
5. Accedi con l'account **Personal (acquirente)**:
   - Email: account test creato automaticamente
   - Password: visualizzabile in Developer Dashboard
6. Conferma il pagamento
7. Sarai reindirizzato alla pagina "Thank You"

### Account Sandbox di Default

PayPal crea automaticamente account con fondi illimitati per test:

**Buyer Account (Cliente)**:
- Email: `sb-xxxxx@personal.example.com`
- Password: (vedi Developer Dashboard)
- Ha $9,999.99 disponibili per test

**Seller Account (MA.GIA DONNA)**:
- Email: `sb-yyyyy@business.example.com`
- Password: (vedi Developer Dashboard)
- Riceve i pagamenti test

---

## 7. Configurazione Webhook

I webhook permettono a PayPal di notificare l'applicazione di eventi importanti (pagamenti, rimborsi, etc).

### Passo 7.1: Crea Webhook in Sandbox

1. Vai su: https://developer.paypal.com/dashboard/webhooks
2. Seleziona **"Sandbox"**
3. Clicca su **"Create Webhook"**
4. Compila:
   - **Webhook URL**: `https://tuodominio.com/paypal/webhook`
   - **Event types**: Seleziona:
     - ✅ `Payment sale completed`
     - ✅ `Payment sale refunded`
     - ✅ `Payment sale reversed`
     - ✅ `Payment capture completed`

5. Clicca su **"Save"**

### Passo 7.2: Copia Webhook ID

Dopo aver creato il webhook, vedrai un **Webhook ID**:

```
Esempio: 6YH12345-67HG-89IJ-KL01-MNOPQR234567
```

### Passo 7.3: Aggiungi Webhook ID al .env

```env
PAYPAL_WEBHOOK_ID=6YH12345-67HG-89IJ-KL01-MNOPQR234567
```

### Passo 7.4: Test Webhook

1. In PayPal Developer Dashboard → Webhooks
2. Clicca sul tuo webhook
3. Vai su **"Webhook simulator"**
4. Seleziona evento: `Payment sale completed`
5. Clicca **"Send Test"**

Controlla i log di Laravel:
```bash
tail -f storage/logs/laravel.log
```

Dovresti vedere: `PayPal Webhook Received`

---

## 8. Go Live - Produzione

### Passo 8.1: Crea App per Live

1. Vai su: https://developer.paypal.com/dashboard/
2. Seleziona **"Live"** (non Sandbox)
3. Clicca **"Create App"**
4. Nome: `MA.GIA DONNA Production`
5. Clicca **"Create App"**

### Passo 8.2: Copia Credenziali Live

Vedrai le credenziali di **produzione**:
- Client ID Live
- Secret Live

⚠️ **ATTENZIONE**: Queste credenziali accettano VERI pagamenti!

### Passo 8.3: Configura Webhook Live

1. Vai su **Webhooks** → **Live**
2. Crea webhook con URL produzione:
   - URL: `https://www.agstudio.digital/magia/public/paypal/webhook`
   - Eventi: stessi della sandbox
3. Copia il **Webhook ID Live**

### Passo 8.4: Aggiorna .env Produzione

Nel server di produzione, modifica `.env`:

```env
# PRODUZIONE
PAYPAL_MODE=live
PAYPAL_CLIENT_ID=TUO_CLIENT_ID_LIVE
PAYPAL_SECRET=TUO_SECRET_LIVE
PAYPAL_WEBHOOK_ID=TUO_WEBHOOK_ID_LIVE
```

### Passo 8.5: Clear Cache

```bash
php artisan config:clear
php artisan cache:clear
```

### Passo 8.6: Test Live

⚠️ **ATTENZIONE**: Questo userà SOLDI VERI!

1. Crea un ordine di €0.01 per test
2. Completa il pagamento con un account PayPal personale
3. Verifica che il pagamento appaia nel conto business PayPal

### Passo 8.7: Rimuovi Route di Debug

Nel file `routes/web.php`, commenta o rimuovi:

```php
// DEBUG - RIMUOVERE IN PRODUZIONE
// Route::get('/paypal/test', [PayPalController::class, 'testConnection'])->name('paypal.test');
```

---

## 9. Troubleshooting

### Errore: "Authentication failed due to invalid credentials"

**Causa**: Client ID o Secret errati

**Soluzione**:
1. Verifica che le credenziali in `.env` siano corrette
2. Controlla di usare credenziali Sandbox se `PAYPAL_MODE=sandbox`
3. Controlla di usare credenziali Live se `PAYPAL_MODE=live`
4. Esegui `php artisan config:clear`

### Errore: "SSL certificate problem"

**Causa**: Certificato SSL non configurato correttamente

**Soluzione**:
```bash
# In ambiente di sviluppo locale, puoi disabilitare verifica SSL
# SOLO PER TEST LOCALE - MAI IN PRODUZIONE!
```

In produzione, assicurati di avere HTTPS attivo.

### Errore: "This transaction couldn't be completed"

**Causa**: Account sandbox non ha fondi sufficienti

**Soluzione**:
1. Vai su Developer Dashboard → Accounts
2. Clicca sull'account Personal
3. Clicca **"Set Balance"**
4. Imposta a $9,999.99

### Webhook non riceve eventi

**Causa**: URL webhook non raggiungibile o non configurato

**Soluzione**:
1. Verifica che l'URL sia pubblico (non localhost)
2. Verifica che non ci siano firewall che bloccano PayPal IPs
3. Controlla i log: `storage/logs/laravel.log`
4. Usa ngrok per test in locale:
   ```bash
   ngrok http 8000
   # Usa l'URL ngrok per il webhook
   ```

### Pagamenti completati ma database non aggiornato

**Causa**: Query database fallite o permessi

**Soluzione**:
1. Controlla i log Laravel
2. Verifica che le tabelle `ordini` e `transazioni_paypal` esistano
3. Verifica permessi di scrittura database

---

## 📊 Database Schema

### Tabella: ordini

```sql
CREATE TABLE ordini (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    cliente_id BIGINT UNSIGNED,
    numero_ordine VARCHAR(50),
    descrizione TEXT,
    importo DECIMAL(10, 2),
    stato_pagamento ENUM('in_attesa', 'completato', 'fallito', 'rimborsato', 'contestato'),
    paypal_payment_id VARCHAR(100),
    paypal_payer_id VARCHAR(100),
    paypal_status VARCHAR(50),
    data_pagamento DATETIME,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (cliente_id) REFERENCES users(id) ON DELETE CASCADE
);
```

### Tabella: transazioni_paypal

```sql
CREATE TABLE transazioni_paypal (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    ordine_id BIGINT UNSIGNED,
    payment_id VARCHAR(100),
    payer_id VARCHAR(100),
    stato VARCHAR(50),
    importo DECIMAL(10, 2),
    valuta VARCHAR(3) DEFAULT 'EUR',
    response_raw TEXT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (ordine_id) REFERENCES ordini(id) ON DELETE CASCADE
);
```

---

## 🔐 Security Best Practices

### 1. Proteggi le Credenziali

✅ **DO**:
- Salva credenziali solo in `.env`
- Aggiungi `.env` al `.gitignore`
- Usa credenziali diverse per Sandbox e Live

❌ **DON'T**:
- Mai committare credenziali su Git
- Mai condividere il Secret
- Mai hardcodare credenziali nel codice

### 2. Verifica Webhook Signature

Il controller include già la verifica della firma webhook:

```php
private function verifyWebhookSignature(Request $request)
{
    // Implementa verifica firma PayPal
    // Vedi: https://developer.paypal.com/docs/api/webhooks/v1/#verify-webhook-signature
}
```

### 3. Usa HTTPS in Produzione

PayPal richiede HTTPS per:
- Return URL
- Cancel URL
- Webhook URL

### 4. Log Transazioni

Tutte le transazioni vengono loggate in:
- `storage/logs/paypal.log` (PayPal SDK)
- `storage/logs/laravel.log` (Laravel)
- Database: tabella `transazioni_paypal`

---

## 💰 Costi e Commissioni

### Commissioni PayPal Italia (2024)

**Transazioni Standard**:
- 3,4% + €0,35 per transazione

**Esempio**:
- Vendita €50,00
- Commissione: (€50 × 3,4%) + €0,35 = €2,05
- **Netto ricevuto: €47,95**

**Micropagamenti** (< €10):
- 5% + €0,05
- Da attivare contattando PayPal

### Costi Mensili

- **Conto Business**: GRATUITO
- **API Calls**: GRATUITE
- **Prelievi su conto bancario**: GRATUITI (1-2 giorni lavorativi)

---

## 📞 Supporto

### PayPal Support
- **Telefono**: 800 966 565 (gratuito da Italia)
- **Email**: servizio.clienti@paypal.it
- **Developer Forum**: https://www.paypal-community.com/

### Documentazione
- **REST API**: https://developer.paypal.com/api/rest/
- **Webhooks**: https://developer.paypal.com/api/webhooks/
- **SDK PHP**: https://github.com/paypal/PayPal-PHP-SDK

---

## ✅ Checklist Go-Live

Prima di attivare i pagamenti in produzione:

- [ ] Account PayPal Business verificato
- [ ] Conto bancario collegato
- [ ] Credenziali Live configurate in `.env`
- [ ] Webhook configurato e testato
- [ ] HTTPS attivo sul dominio
- [ ] Test pagamento completato con successo
- [ ] Database tabelle create
- [ ] Log funzionanti
- [ ] Route di debug rimosse
- [ ] Email di conferma configurate
- [ ] Policy rimborsi definita
- [ ] Termini e condizioni aggiornati

---

**Ultimo aggiornamento**: 15 Novembre 2024
**Autore**: Claude Code (AI Assistant)
**Progetto**: MA.GIA DONNA
