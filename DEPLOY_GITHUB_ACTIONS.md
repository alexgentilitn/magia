# 🚀 Deploy Automatico - GitHub Actions + FTP

**Guida Completa al Deploy Automatico su Aruba via GitHub Actions**

---

## 📋 Riepilogo Rapido

**Sistema:** GitHub Actions → FTP Automatico
**Workflow:** `.github/workflows/deploy.yml`
**Tempo Deploy:** 2-5 minuti dall'ultimo push
**Destinazione:** Aruba FTP → `www.agstudio.digital/magia/`

---

## ⚠️ BRANCH ABILITATI (CRITICO!)

**Solo questi branch triggerano il deploy automatico:**

```yaml
branches:
  - claude/fix-github-ftp-push-01QcEnaTAL1KMQVP9iPVzptc  # Branch precedente
  - claude/Magia_Brench-01G4cTM33nQqZ3K3UtX3NGqm        # Branch CORRENTE ⭐
```

### ‼️ IMPORTANTE

- **Push su altri branch = NESSUN deploy automatico**
- Se crei un nuovo branch, DEVI aggiungerlo a `.github/workflows/deploy.yml`
- Il branch DEVE seguire il pattern `claude/*` per essere accettato dal sistema

---

## 🔧 Come Funziona

### Processo Completo

```
1. Sviluppatore fa PUSH su branch abilitato
   ↓
2. GitHub rileva il push (entro 10 secondi)
   ↓
3. GitHub Actions avvia workflow "Deploy to Aruba FTP"
   ↓
4. Checkout del codice dal repository
   ↓
5. Connessione FTP ad Aruba
   ↓
6. Upload files via FTP (esclusi .git, node_modules, .env, ecc.)
   ↓
7. Deploy completato ✅
   ↓
8. File disponibili su https://www.agstudio.digital/magia/
```

### Timeline Tipica

| Tempo | Azione |
|-------|--------|
| 00:00 | Push eseguito su GitHub |
| 00:10 | GitHub Actions triggered |
| 00:30 | Workflow avviato |
| 01:00 | Connessione FTP Aruba |
| 01:30 | Upload files iniziato |
| 04:00 | Upload completato |
| 04:30 | Deploy completato ✅ |

**Totale:** ~4-5 minuti per deploy completo

---

## 📂 File Deployati ed Esclusi

### ✅ File Deployati

**Tutto il repository ECCETTO:**
- `.git/` e `.gitignore`
- `node_modules/`
- `.env` (già presente sul server)
- `.env.example`
- `README.md`
- `.github/`

### ⚠️ File Speciali

**`.env` - NON deployato automaticamente:**
- È stato deployato MANUALMENTE una volta
- Modifiche al `.env` vanno fatte direttamente su server via FTP
- NON committare mai `.env` nel repository

**`vendor/` - DEPLOYATO:**
- La cartella `vendor/` È versionata nel repository (insolito ma necessario)
- Motivo: Hosting Aruba senza SSH → impossibile fare `composer install`
- Viene deployata come qualsiasi altra cartella

---

## 🔐 Secrets GitHub Actions

**Dove:** Repository Settings → Secrets and variables → Actions
**URL:** https://github.com/alexgentilitn/magia/settings/secrets/actions

### Secrets Configurati

| Secret | Valore | Descrizione |
|--------|--------|-------------|
| `FTP_HOST` | `ftp.agstudio.digital` | Hostname server FTP Aruba |
| `FTP_USER` | `agstudiodiital@agstudio.digital` | Username FTP completo |
| `FTP_PASSWORD` | `[confidenziale]` | Password FTP |
| `FTP_PATH` | `/www.agstudio.digital/magia` | Path destinazione su server |

**⚠️ Non modificare questi secrets senza necessità!**

---

## ✅ Come Verificare il Deploy

### 1. Controllo GitHub Actions

**Dopo ogni push:**

1. Vai su: https://github.com/alexgentilitn/magia/actions
2. Cerca il workflow più recente (nome commit)
3. Verifica lo stato:
   - 🟡 **Giallo (In Progress)** → Deploy in corso, attendi
   - ✅ **Verde (Success)** → Deploy completato con successo
   - ❌ **Rosso (Failed)** → Deploy fallito, leggi i log

### 2. Controllo File su Produzione

**Se hai creato/modificato un file, verifica che sia stato deployato:**

Esempio:
```
File locale: public/test.php
URL verifica: https://www.agstudio.digital/magia/public/test.php
```

Se il file non è presente dopo 5 minuti:
- Verifica che il workflow sia ✅ verde
- Controlla che il file non sia nell'exclude list
- Verifica log del workflow per errori FTP

### 3. Test Funzionalità

**Dopo deploy di nuove funzionalità:**

1. Apri il sito: https://www.agstudio.digital/magia/public/
2. Testa la funzionalità modificata
3. Controlla console browser per errori JS
4. Verifica log Laravel se errori: `storage/logs/laravel.log`

---

## 🐛 Troubleshooting Deploy

### Problema 1: Workflow Non Parte Dopo Push

**Sintomo:** Push eseguito ma workflow non appare su GitHub Actions

**Cause possibili:**
- Branch non abilitato in `.github/workflows/deploy.yml`
- Push fatto su branch sbagliato

**Soluzione:**
1. Verifica branch corrente: `git branch`
2. Controlla che sia `claude/Magia_Brench-01G4cTM33nQqZ3K3UtX3NGqm`
3. Se hai creato nuovo branch, aggiungi a `.github/workflows/deploy.yml`:
   ```yaml
   branches:
     - claude/fix-github-ftp-push-01QcEnaTAL1KMQVP9iPVzptc
     - claude/Magia_Brench-01G4cTM33nQqZ3K3UtX3NGqm
     - [tuo-nuovo-branch]  # Aggiungi qui
   ```

---

### Problema 2: Workflow Fallisce (❌ Rosso)

**Sintomo:** Workflow avviato ma termina con errore

**Cause comuni:**

**A) Errore FTP - Credenziali**
```
Error: FTP Login failed
```
**Soluzione:**
- Verifica secrets FTP in GitHub (Settings → Secrets)
- Controlla username: deve essere `agstudiodiital@agstudio.digital` (email completa)
- Verifica password FTP

**B) Errore FTP - Permessi**
```
Error: Permission denied
```
**Soluzione:**
- Verifica permessi cartelle su Aruba via FTP client
- Cartella destinazione deve essere scrivibile

**C) Errore FTP - Timeout**
```
Error: Connection timeout
```
**Soluzione:**
- Server Aruba temporaneamente irraggiungibile
- Riprova deploy manualmente dopo qualche minuto

**D) Errore Checkout**
```
Error: Unable to checkout repository
```
**Soluzione:**
- Problema GitHub (raro)
- Riprova dopo qualche minuto

---

### Problema 3: Deploy OK ma File Non Presente

**Sintomo:** Workflow ✅ verde ma file non su produzione

**Cause possibili:**

**A) File nell'exclude list**
Verifica che il file NON sia in questa lista:
```yaml
exclude: |
  **/.git*
  **/node_modules/**
  .env
  README.md
  .github/**
```

**B) Path FTP errato**
- Verifica secret `FTP_PATH` = `/www.agstudio.digital/magia`
- Controlla che la cartella esista su Aruba

**C) Permessi file/cartelle**
- Connettiti via FTP client (FileZilla, ecc.)
- Verifica permessi: cartelle 755, file 644
- Correggi se necessario

---

### Problema 4: Deploy Lentissimo

**Sintomo:** Deploy impiega >10 minuti

**Cause:**
- Server Aruba sovraccarico
- Upload di molti file grandi (es. vendor/ completo)

**Soluzioni:**
- Aspetta che completi
- Per deploy frequenti, considera escludere file grandi non modificati
- Verifica che non stai uploadando file inutili

---

## 🔄 Deploy Manuale (Workflow Dispatch)

**Quando usare:**
- Vuoi ri-deployare senza fare nuovo commit
- Deploy automatico fallito, vuoi riprovare
- Testing

**Come fare:**

1. Vai su: https://github.com/alexgentilitn/magia/actions
2. Click su workflow "Deploy to Aruba FTP" (a sinistra)
3. Click pulsante "Run workflow" (in alto a destra)
4. Seleziona branch da deployare
5. Click "Run workflow" (verde)
6. Attendi completamento

---

## 📝 Modifica Workflow

**File:** `.github/workflows/deploy.yml`

### Aggiungere Nuovo Branch

```yaml
on:
  push:
    branches:
      - claude/fix-github-ftp-push-01QcEnaTAL1KMQVP9iPVzptc
      - claude/Magia_Brench-01G4cTM33nQqZ3K3UtX3NGqm
      - claude/nuovo-branch-XXXXXXXXXX  # Aggiungi qui
```

Poi:
```bash
git add .github/workflows/deploy.yml
git commit -m "Aggiungi nuovo branch al workflow deploy"
git push
```

### Modificare File Esclusi

Per escludere altri file/cartelle:

```yaml
exclude: |
  **/.git*
  **/.git*/**
  **/node_modules/**
  .env
  .env.example
  README.md
  .github/**
  public/test-*.php  # Esempio: escludi test files
```

### Cambiare Destinazione FTP

**NON modificare nel workflow!** Usa i secrets:

```
Settings → Secrets → FTP_PATH
```

Modifica il valore da:
```
/www.agstudio.digital/magia
```
A:
```
/www.agstudio.digital/magia-nuova-cartella
```

---

## 📊 Monitoraggio Deploy

### Dashboard GitHub Actions

**URL:** https://github.com/alexgentilitn/magia/actions

**Cosa monitorare:**
- ✅ Tutti i deploy recenti riusciti
- ❌ Deploy falliti (investigare cause)
- ⏱️ Durata media deploy (ottimizzazione)

### Log Deploy

**Dove:** GitHub Actions → Click su workflow → View logs

**Cosa cercare nei log:**

**Deploy Success:**
```
✅ Deploy via FTP
  Connecting to ftp.agstudio.digital...
  Uploading 145 files...
  Upload completed: 145 files (12.3 MB)
  Deploy successful!
```

**Deploy Failed:**
```
❌ Deploy via FTP
  Error: FTP Login failed
  User: agstudiodiital@agstudio.digital
  Host: ftp.agstudio.digital
```

---

## ✅ Best Practices

### DO (Fare Sempre) ✅

1. **Verifica deploy dopo ogni push**
   - Controlla GitHub Actions
   - Testa su produzione

2. **Commit messaggi descrittivi**
   ```
   ✅ "Aggiungi export PDF calendario"
   ❌ "fix"
   ```

3. **Push solo codice testato**
   - Non pushare errori syntax
   - Testa in locale se possibile

4. **Monitora GitHub Actions**
   - Controlla almeno 1 volta al giorno
   - Risolvi deploy falliti subito

### DON'T (Non Fare Mai) ❌

1. **NON committare `.env`**
   - È già su server
   - Contiene dati sensibili

2. **NON modificare secrets senza motivo**
   - Deploy fallirà
   - Difficile ripristinare

3. **NON pushare su branch non configurati**
   - Deploy non partirà
   - Perderai tempo

4. **NON ignorare deploy falliti**
   - Produzioni potrebbe essere in stato inconsistente
   - Risolvi subito

---

## 🔗 Link Utili

| Risorsa | URL |
|---------|-----|
| **GitHub Actions Dashboard** | https://github.com/alexgentilitn/magia/actions |
| **Secrets GitHub** | https://github.com/alexgentilitn/magia/settings/secrets/actions |
| **Workflow File** | `.github/workflows/deploy.yml` |
| **Produzione** | https://www.agstudio.digital/magia/public/ |
| **Diagnostica** | https://www.agstudio.digital/magia/public/diagnose.php |

---

## 📚 Documentazione Correlata

- `CLAUDE_MEMORY.md` - Memoria completa progetto
- `QUICK_START.md` - Quick reference
- `README.md` - Panoramica progetto
- `.github/workflows/deploy.yml` - Workflow completo

---

**Ultima Revisione:** 16 Novembre 2025
**Autore:** Claude Code + AGstudio Digital
**Versione:** 1.0
