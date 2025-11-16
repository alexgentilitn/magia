# 🔐 Configurazione GitHub Secrets per Deploy Automatico

Per abilitare il deploy automatico su Aruba, devi configurare i secrets su GitHub **UNA VOLTA SOLA**.

## 📋 Passi da seguire:

### 1️⃣ Vai alle impostazioni Secrets

Apri questo link (sostituendo con il tuo username se diverso):

```
https://github.com/alexgentilitn/ea/settings/secrets/actions
```

Oppure manualmente:
1. Vai su GitHub al repository: `https://github.com/alexgentilitn/ea`
2. Clicca su **"Settings"** (in alto a destra)
3. Nel menu laterale sinistro, clicca su **"Secrets and variables"**
4. Clicca su **"Actions"**

---

### 2️⃣ Aggiungi i 4 Secrets

Clicca sul pulsante verde **"New repository secret"** e aggiungi questi 4 secrets:

#### Secret 1: FTP_HOST
- **Name:** `FTP_HOST`
- **Secret:** `ftp.agstudio.digital`
- Clicca **"Add secret"**

#### Secret 2: FTP_USER
- **Name:** `FTP_USER`
- **Secret:** `magia@agstudio.digital`
- Clicca **"Add secret"**

#### Secret 3: FTP_PASSWORD
- **Name:** `FTP_PASSWORD`
- **Secret:** `$magia2025!`
- Clicca **"Add secret"**

#### Secret 4: FTP_PATH
- **Name:** `FTP_PATH`
- **Secret:** `/home/agstudiodiital/agstudio.digital/magia`
- Clicca **"Add secret"**

---

### 3️⃣ Verifica i Secrets

Quando hai finito, dovresti vedere 4 secrets:
- ✅ FTP_HOST
- ✅ FTP_USER
- ✅ FTP_PASSWORD
- ✅ FTP_PATH

---

## 🎯 È tutto! Deploy automatico attivo!

Da questo momento:

1. **Claude Code crea file** qui
2. **Push automatico su GitHub**
3. **GitHub Actions fa il deploy** (30-60 secondi)
4. **File online su** https://www.agstudio.digital/magia/

---

## 🔍 Come verificare che funziona:

### Metodo 1: Guarda le Actions
1. Vai su `https://github.com/alexgentilitn/ea/actions`
2. Vedrai una lista di "workflow runs"
3. Ogni push creerà un nuovo workflow
4. Clicca su uno per vedere i dettagli
5. Se vedi ✅ verde = Deploy riuscito!

### Metodo 2: Forza un deploy manuale (test)
1. Vai su `https://github.com/alexgentilitn/ea/actions`
2. Clicca su **"Deploy to Aruba FTP"** (sulla sinistra)
3. Clicca sul pulsante **"Run workflow"** (a destra)
4. Seleziona il branch `claude/github-file-creation-011CUbkFR93UkHeGMfbo49s3`
5. Clicca **"Run workflow"**
6. Aspetta 30-60 secondi
7. Vai su `https://www.agstudio.digital/magia/index.php`
8. Dovresti vedere **"Ciao"**! 🎉

---

## ⚠️ Troubleshooting

### ❌ Workflow fallisce con "Login failed"
- Verifica username e password nei secrets
- Username deve essere: `magia@agstudio.digital`
- Password: `$magia2025!` (con il simbolo $)

### ❌ Workflow fallisce con "Directory not found"
- Verifica il path FTP: `/home/agstudiodiital/agstudio.digital/magia`
- Controlla nel cPanel che la cartella esista

### ❌ File non appaiono online
- Controlla che il deploy sia completato (✅ verde su Actions)
- Verifica il path sia corretto
- Prova ad aprire direttamente: `https://www.agstudio.digital/magia/index.php`

---

## 🎨 Workflow futuro

Ogni volta che Claude Code crea/modifica file:

```
Claude: "Creo una pagina contatti.php"
   ↓
Claude: Crea file + Commit + Push
   ↓
GitHub Actions: Deploy automatico (30-60 sec)
   ↓
Tu: Apri browser → https://www.agstudio.digital/magia/contatti.php
   ↓
✅ Pagina online e funzionante!
```

**Zero comandi da eseguire!** 🚀

---

## 📞 Supporto

Se hai problemi:
1. Controlla i logs delle Actions su GitHub
2. Verifica che tutti i 4 secrets siano configurati
3. Testa con "Run workflow" manualmente
4. Chiedi a Claude Code di verificare la configurazione

---

Fatto con ❤️ usando GitHub Actions
