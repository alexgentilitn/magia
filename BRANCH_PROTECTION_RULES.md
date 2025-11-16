# 🛡️ REGOLE PROTEZIONE BRANCH - LEGGERE ATTENTAMENTE

## ⚠️ REGOLA ASSOLUTA: LAVORA SEMPRE SU MAIN

**IMPORTANTE:** Questo progetto ha subito una grave perdita di dati dovuta a branch divergenti.
Per evitare che questo riaccada, seguire SEMPRE queste regole:

---

## ✅ REGOLE OBBLIGATORIE

### 1. **Branch Unico: MAIN**
- ✅ **SEMPRE** lavorare sul branch `main`
- ❌ **MAI** creare branch diversi
- ❌ **MAI** fare commit su branch `claude/*` o altri

### 2. **Deploy Automatico**
- ✅ Deploy automatico **SOLO da `main`**
- ✅ Ogni push su `main` → deploy automatico su FTP
- ⏱️ Tempo deploy: 5-10 minuti

### 3. **Workflow di Lavoro**

#### Da Computer/Smartphone:
```bash
# 1. SEMPRE verificare di essere su main
git checkout main

# 2. Pull per ottenere ultime modifiche
git pull origin main

# 3. Fare modifiche

# 4. Commit
git add .
git commit -m "Descrizione modifiche"

# 5. Push su main
git push origin main
```

#### Da Claude Code:
```
1. Claude lavora SEMPRE su branch main
2. Claude fa commit su main
3. Claude fa push su main
4. Deploy automatico parte
```

---

## 🚨 COSA NON FARE MAI

### ❌ NON creare branch separati
- Tutti i branch diversi da `main` causano divergenza
- La divergenza porta a perdita di dati

### ❌ NON fare "Add files via upload" su GitHub
- Carica sempre via git push
- L'upload manuale crea branch divergenti

### ❌ NON modificare `.github/workflows/deploy.yml`
- Il workflow è configurato per deployare SOLO da main
- Non aggiungere altri branch

---

## 📋 CHECKLIST PRE-COMMIT

Prima di ogni commit, verifica:

- [ ] Sono sul branch `main`? → `git branch` (deve mostrare `* main`)
- [ ] Ho fatto pull delle ultime modifiche? → `git pull origin main`
- [ ] Il commit è sul branch corretto? → Verifica che sia main
- [ ] Dopo il push, il deploy parte automaticamente? → Controlla su GitHub Actions

---

## 🔄 STATO CORRENTE DEL PROGETTO

### Branch Attivo
- **Branch Principale:** `main`
- **URL Repository:** https://github.com/alexgentilitn/magia
- **URL Produzione:** https://www.agstudio.digital/magia/public/admin/dashboard

### Configurazione Deploy
```yaml
# Deploy SOLO da main
on:
  push:
    branches:
      - main  # ← UNICO branch consentito
```

### Secrets GitHub Configurati
- `FTP_HOST`: ftp.agstudio.digital
- `FTP_USER`: magia@agstudio.digital
- `FTP_PASSWORD`: (configurato)
- `FTP_PATH`: /home/agstudiodiital/agstudio.digital/magia

---

## 🆘 IN CASO DI EMERGENZA

### Se hai fatto modifiche su un branch sbagliato:

1. **NON fare push di quel branch**
2. Salvare le modifiche:
   ```bash
   git stash
   ```
3. Tornare su main:
   ```bash
   git checkout main
   git pull origin main
   ```
4. Ripristinare modifiche:
   ```bash
   git stash pop
   ```
5. Commit e push su main:
   ```bash
   git add .
   git commit -m "Descrizione"
   git push origin main
   ```

### Se il deploy ha caricato una versione sbagliata:

1. Contattare immediatamente il team
2. Verificare ultimo commit corretto su GitHub
3. Fare rollback al commit corretto
4. Pushare nuovamente su main

---

## 📞 SUPPORTO

### Monitoraggio Deploy
- **GitHub Actions:** https://github.com/alexgentilitn/magia/actions
- Ogni push su main attiva un workflow
- Verifica che lo stato sia ✅ verde

### Verifica Versione Online
- **Dashboard:** https://www.agstudio.digital/magia/public/admin/dashboard
- Dopo deploy (5-10 min), verifica che le modifiche siano online

---

## 📝 STORICO INCIDENTI

### 16 Novembre 2025 - Perdita Dati per Branch Divergente

**Cosa è successo:**
- Commit "Add files via upload" ha creato branch divergente
- Deploy automatico ha caricato versione vecchia
- Persi: Dashboard, Report, Calendario avanzato, Sistema Email, Presenze

**Soluzione applicata:**
- Ripristinato commit e5e8a4bb (versione corretta)
- Creato branch `main` come unico branch ufficiale
- Modificato workflow per deploy SOLO da `main`
- Eliminati tutti i branch divergenti

**Prevenzione:**
- Deploy SOLO da `main`
- Documentazione protezione branch
- Checklist pre-commit obbligatoria

---

## ⚖️ REGOLA D'ORO

> **Un solo branch, una sola verità: MAIN**
>
> Tutto il resto è rischio di perdita dati.

---

**Ultimo aggiornamento:** 16 Novembre 2025
**Versione corretta ripristinata:** Commit 5e314210
**Deploy automatico:** ✅ Attivo su branch `main`

---

**🔒 QUESTE REGOLE SONO INVIOLABILI**

Non modificare mai questo file senza aver consultato l'intero team.
La sicurezza dei dati dipende dal rispetto di queste regole.
