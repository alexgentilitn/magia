# 📧 Configurazione SMTP per Produzione

⚠️ **IMPORTANTE**: Attualmente l'applicazione usa **Mailpit** (server test locale) che **NON funziona in produzione**!

## 🔧 Setup Richiesto

Scegli uno dei provider SMTP consigliati e aggiorna il file `.env` in produzione.

---

## 📮 OPZIONE 1: Gmail SMTP (Gratuito, facile)

**Vantaggi:** Gratuito, semplice da configurare
**Limiti:** Max 500 email/giorno

### Configurazione .env:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=tua_email@gmail.com
MAIL_PASSWORD=tua_app_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="info@donnamagia.it"
MAIL_FROM_NAME="MA.GIA DONNA - Balla & Snella"
```

### Come ottenere App Password:
1. Vai su: https://myaccount.google.com/apppasswords
2. Crea una nuova "App Password" per "Mail"
3. Copia la password generata (16 caratteri)
4. Usa quella password nel campo `MAIL_PASSWORD`

---

## 📮 OPZIONE 2: SendGrid (Consigliato per produzione)

**Vantaggi:** 100 email/giorno GRATIS, tracking, analytics
**Limiti:** Richiede registrazione

### Configurazione .env:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=SG.xxxxxxxxxxxxxxxxxxxxxxxxxxxx
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="info@donnamagia.it"
MAIL_FROM_NAME="MA.GIA DONNA - Balla & Snella"
```

### Come ottenere API Key:
1. Registrati su: https://signup.sendgrid.com/
2. Vai in Settings > API Keys
3. Crea nuova API Key con permessi "Mail Send"
4. Copia la chiave e usala come `MAIL_PASSWORD`

---

## 📮 OPZIONE 3: Mailgun (Alternativa)

**Vantaggi:** Potente, affidabile, ben supportato da Laravel
**Limiti:** Richiede carta di credito (ma 5000 email/mese GRATIS primi 3 mesi)

### Configurazione .env:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailgun.org
MAIL_PORT=587
MAIL_USERNAME=postmaster@tuo-dominio.mailgun.org
MAIL_PASSWORD=your_mailgun_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="info@donnamagia.it"
MAIL_FROM_NAME="MA.GIA DONNA - Balla & Snella"
```

### Come configurare:
1. Registrati su: https://www.mailgun.com/
2. Aggiungi il tuo dominio o usa sandbox
3. Copia le credenziali SMTP

---

## 📮 OPZIONE 4: Aruba PEC/Email (Se hai hosting Aruba)

Se hai un servizio email con Aruba:

### Configurazione .env:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtps.aruba.it
MAIL_PORT=465
MAIL_USERNAME=tua_email@agstudio.digital
MAIL_PASSWORD=tua_password_email
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS="info@donnamagia.it"
MAIL_FROM_NAME="MA.GIA DONNA - Balla & Snella"
```

---

## ✅ Testing Email dopo configurazione

Dopo aver aggiornato `.env`, testa l'invio email:

### 1. Via pannello admin
- Vai in `/admin/settings`
- Sezione "Test Email"
- Invia email di test

### 2. Via Artisan (SSH se disponibile)
```bash
php artisan tinker
Mail::raw('Test email', function($msg) { $msg->to('test@example.com')->subject('Test'); });
```

---

## 🚨 IMPORTANTE - Branding

⚠️ **Tutte le email devono essere intestate a "Balla & Snella"** (come da PDF specs pagina 4)

Esempio footer email:
```
Servizio erogato da MA.GIA DONNA
In convenzione con Balla & Snella Trentino
```

---

## 📝 TODO Immediato

1. [ ] Scegliere provider SMTP (consigliato: SendGrid)
2. [ ] Ottenere credenziali SMTP
3. [ ] Aggiornare `.env` in produzione via FTP
4. [ ] Testare invio email
5. [ ] Verificare ricezione e spam folder

---

## 🔧 File da modificare (GIÀ PREPARATI)

- ✅ `.env` (aggiorna manualmente via FTP)
- ✅ Template email in `resources/views/emails/` (già esistenti)
- ✅ Mail classes in `app/Mail/` (già pronte)

**Lo sviluppatore ha già preparato tutto il sistema email**, serve solo configurare SMTP!

---

Generato automaticamente - Setup Notturno Claude
Data: 2025-11-15
