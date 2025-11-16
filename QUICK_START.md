# ⚡ QUICK START - MA.GIA DONNA

**Riferimento Rapido per Sessioni Claude Code**

🔍 **Per documentazione completa → Vedi `CLAUDE_MEMORY.md`**

---

## 🎯 PRIMO PASSO - LEGGI QUESTO

Se sei una nuova sessione di Claude Code:
1. ✅ Leggi questo file (2 minuti)
2. ✅ Leggi `CLAUDE_MEMORY.md` (10 minuti) - OBBLIGATORIO
3. ✅ Verifica branch corrente: `git branch`
4. ✅ Pull: `git pull origin [branch-name]`
5. ✅ Vai al lavoro! 🚀

---

## 📌 INFO ESSENZIALI (da memoria)

### Progetto
- **Nome:** MA.GIA DONNA
- **Tipo:** Laravel 10.x + MySQL
- **Repo:** https://github.com/alexgentilitn/magia
- **Live:** https://www.agstudio.digital/magia/public/

### ⚠️ Branch Attuale - IMPORTANTE!

**Branch di lavoro UFFICIALE:**
```
claude/Magia_Brench-01G4cTM33nQqZ3K3UtX3NGqm
```

**TUTTE le modifiche vanno fatte SOLO su questo branch!**

❌ NON pushare su `magia-brench` (errore 403)
❌ NON pushare su `main`
✅ Usa SEMPRE `claude/Magia_Brench-01G4cTM33nQqZ3K3UtX3NGqm`

### Database
```
Host: localhost
DB:   agstudiodiital_magia
User: agstudiodiital_agstudiomagia
Pass: $Magia2015!
```

### Deploy FTP Automatico

**⚠️ IMPORTANTE - BRANCH ABILITATI:**
Solo questi branch attivano il deploy automatico:
- `claude/fix-github-ftp-push-01QcEnaTAL1KMQVP9iPVzptc` (precedente)
- `claude/Magia_Brench-01G4cTM33nQqZ3K3UtX3NGqm` ⭐ **CORRENTE**

**Push su altri branch = NESSUN deploy!**

**Processo:**
1. Push su branch abilitato
2. GitHub Actions parte (30 sec)
3. Deploy FTP (2-5 min)
4. Verifica su https://github.com/alexgentilitn/magia/actions

**Se deploy non parte:**
→ Controlla di essere sul branch corretto
→ Verifica `.github/workflows/deploy.yml`

---

## ⚠️ COSE DA NON FARE

❌ **NON** modificare `.env` via git (solo FTP diretto)
❌ **NON** rimuovere `vendor/` dal repo (necessario per hosting)
❌ **NON** pushare secrets o credenziali
❌ **NON** cambiare secrets GitHub senza motivo

---

## ✅ COSE DA FARE SEMPRE

✅ Leggi `CLAUDE_MEMORY.md` prima di iniziare
✅ Testa su produzione dopo ogni deploy
✅ Usa `diagnose.php` se errori
✅ Commit messaggi in italiano
✅ Branch sempre `claude/[nome]-[session-id]`

---

## 🔧 TOOL UTILI

### Diagnostica Errori
```
https://www.agstudio.digital/magia/public/diagnose.php
```

### Pulisci Cache
```
https://www.agstudio.digital/magia/public/clear-cache.php
```

### GitHub Actions
```
https://github.com/alexgentilitn/magia/actions
```

---

## 📞 SECRETS GITHUB (già configurati)

- `FTP_HOST` = `ftp.agstudio.digital`
- `FTP_USER` = `agstudiodiital`
- `FTP_PASSWORD` = [configurato]
- `FTP_PATH` = `/public_html/magia`

---

## 🚨 SE QUALCOSA NON FUNZIONA

1. Vai su `diagnose.php`
2. Leggi errori in `storage/logs/laravel.log` (via FTP)
3. Verifica GitHub Actions (deploy fallito?)
4. Pulisci cache con `clear-cache.php`
5. Controlla che `.env` esista sul server
6. Leggi `CLAUDE_MEMORY.md` sezione Troubleshooting

---

## 📚 DOCUMENTAZIONE

| File | Contenuto |
|------|-----------|
| **CLAUDE_MEMORY.md** | 🧠 TUTTO - leggi sempre questo |
| **QUICK_START.md** | ⚡ Questo file - quick reference |
| **GUIDA PROGETTO/** | 📖 Documentazione tecnica dettagliata |
| **ENV_TEMPLATE_PRODUCTION.txt** | 📝 Template .env |

---

**Ora vai a leggere `CLAUDE_MEMORY.md`! 👉**

È lì che c'è TUTTO il contesto del progetto.
