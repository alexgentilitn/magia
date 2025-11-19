# 🎯 BRANCH UFFICIALE DEL PROGETTO

## ✅ BRANCH PRINCIPALE

**Nome:** `claude/fix-github-ftp-push-01QcEnaTAL1KMQVP9iPVzptc`

Questo è il **branch unico e ufficiale** del progetto "MA.GIA DONNA".

---

## 📋 PERCHÉ QUESTO BRANCH?

Il branch `main` su GitHub era fermo alla versione vecchia (commit 089a7cd6) che aveva causato la perdita di dati del 16/11/2025.

A causa delle protezioni di GitHub, non è possibile sovrascrivere `main` con push --force.

**Soluzione:** Il branch `claude/fix-github-ftp-push-01QcEnaTAL1KMQVP9iPVzptc` contiene:
- ✅ Versione completa ripristinata (commit 5e314210)
- ✅ Dashboard con Chart.js
- ✅ Sistema Report e Statistiche (PDF + Excel)
- ✅ Calendario avanzato con prenotazioni
- ✅ Sistema Email notifications
- ✅ Sistema Presenze
- ✅ Tutte le 576,697 linee di codice recuperate
- ✅ Configurazione protezione branch

---

## 🚀 COME LAVORARE

### Da Qualsiasi Computer/Sessione

```bash
# 1. Clone del repository
git clone https://github.com/alexgentilitn/magia.git
cd magia

# 2. Checkout del branch ufficiale
git checkout claude/fix-github-ftp-push-01QcEnaTAL1KMQVP9iPVzptc

# 3. Pull delle ultime modifiche
git pull origin claude/fix-github-ftp-push-01QcEnaTAL1KMQVP9iPVzptc

# 4. Fai le tue modifiche

# 5. Commit
git add .
git commit -m "Descrizione modifiche"

# 6. Push (deploy automatico)
git push origin claude/fix-github-ftp-push-01QcEnaTAL1KMQVP9iPVzptc
```

---

## ⚙️ CONFIGURAZIONE DEPLOY

Il workflow `.github/workflows/deploy.yml` è configurato per deployare **SOLO** da questo branch:

```yaml
on:
  push:
    branches:
      - claude/fix-github-ftp-push-01QcEnaTAL1KMQVP9iPVzptc
```

Ogni push su questo branch → Deploy automatico su FTP Aruba (5-10 minuti)

---

## 🔒 REGOLE IMPORTANTI

### ✅ DA FARE SEMPRE:
- Lavorare su `claude/fix-github-ftp-push-01QcEnaTAL1KMQVP9iPVzptc`
- Pull prima di iniziare
- Push su questo branch
- Verificare deploy su GitHub Actions

### ❌ NON FARE MAI:
- NON usare branch `main` (è vecchio e non aggiornato)
- NON creare altri branch
- NON fare "Add files via upload" su GitHub

---

## 📍 LINK UTILI

### Repository
- **GitHub:** https://github.com/alexgentilitn/magia
- **Branch:** claude/fix-github-ftp-push-01QcEnaTAL1KMQVP9iPVzptc
- **Actions:** https://github.com/alexgentilitn/magia/actions

### Produzione
- **Dashboard:** https://www.agstudio.digital/magia/public/admin/dashboard
- **Pubblico:** https://www.agstudio.digital/magia/public/

### Deploy FTP
- **Host:** ftp.agstudio.digital
- **User:** magia@agstudio.digital
- **Path:** /home/agstudiodiital/agstudio.digital/magia

---

## 🔄 STATO ATTUALE

### Ultimo Commit
- **Hash:** e70a476f
- **Messaggio:** Temp: Abilita deploy da branch claude per ripristino immediato
- **Data:** 16 Novembre 2025

### Deploy
- ✅ Workflow attivo
- ✅ Deploy automatico configurato
- ✅ Secrets GitHub configurati

---

## 🆘 TROUBLESHOOTING

### Deploy Non Parte

Verifica di essere sul branch corretto:
```bash
git branch
# Deve mostrare: * claude/fix-github-ftp-push-01QcEnaTAL1KMQVP9iPVzptc
```

### Push Fallisce

Assicurati di pushare sul branch corretto:
```bash
git push origin claude/fix-github-ftp-push-01QcEnaTAL1KMQVP9iPVzptc
```

### Sei su Branch Sbagliato

Torna al branch corretto:
```bash
git checkout claude/fix-github-ftp-push-01QcEnaTAL1KMQVP9iPVzptc
git pull origin claude/fix-github-ftp-push-01QcEnaTAL1KMQVP9iPVzptc
```

---

## 📝 NOTA IMPORTANTE

Il nome del branch può sembrare lungo, ma è importante mantenerlo così perché:
1. È l'unico branch con la versione completa e corretta
2. È già configurato per il deploy automatico
3. Tutti i commit di ripristino sono qui
4. Cambiarlo richiederebbe riconfigurazione completa

**Usa sempre questo branch - è la tua versione ufficiale del progetto.**

---

**Ultimo aggiornamento:** 16 Novembre 2025, ore 16:00 UTC
**Status:** ✅ **ATTIVO E PROTETTO**
