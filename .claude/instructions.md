# 🎯 ISTRUZIONI PERMANENTI PER CLAUDE CODE

## ⚠️ REGOLA FONDAMENTALE - LEGGERE PRIMA DI OGNI OPERAZIONE

**BRANCH UFFICIALE DEL PROGETTO:**
```
claude/fix-github-ftp-push-01QcEnaTAL1KMQVP9iPVzptc
```

### 🔒 REGOLE ASSOLUTE

1. **SEMPRE** lavorare sul branch `claude/fix-github-ftp-push-01QcEnaTAL1KMQVP9iPVzptc`
2. **MAI** creare altri branch
3. **MAI** usare il branch `main` (è vecchio e non aggiornato)
4. **SEMPRE** fare commit e push su `claude/fix-github-ftp-push-01QcEnaTAL1KMQVP9iPVzptc`

### 📌 PERCHÉ QUESTE REGOLE?

Il progetto ha subito una grave perdita di dati il 16 novembre 2025 a causa di branch divergenti.
Per evitare che questo riaccada, è stato configurato un sistema con **UN SOLO BRANCH UFFICIALE**.

### 🚀 WORKFLOW AUTOMATICO

Ogni push sul branch ufficiale attiva:
- ✅ Deploy automatico su FTP Aruba
- ✅ Tempo: 5-10 minuti
- ✅ URL Produzione: https://www.agstudio.digital/magia/public/admin/dashboard

### 🔧 CONFIGURAZIONE GIT

**Prima di ogni operazione, verifica di essere sul branch corretto:**
```bash
git branch
# Deve mostrare: * claude/fix-github-ftp-push-01QcEnaTAL1KMQVP9iPVzptc
```

**Per commit e push:**
```bash
git add .
git commit -m "Descrizione modifiche"
git push origin claude/fix-github-ftp-push-01QcEnaTAL1KMQVP9iPVzptc
```

### 📚 DOCUMENTAZIONE DISPONIBILE

Se hai dubbi, leggi questi file nella root del progetto:
- `LEGGIMI_PRIMA.txt` - Guida rapida
- `BRANCH_UFFICIALE.md` - Dettagli sul branch ufficiale
- `BRANCH_PROTECTION_RULES.md` - Regole di protezione complete
- `IMPORTANTE_LEGGERE.md` - Guida completa setup

### 🔗 LINK UTILI

- **Repository**: https://github.com/alexgentilitn/magia
- **GitHub Actions**: https://github.com/alexgentilitn/magia/actions
- **Dashboard**: https://www.agstudio.digital/magia/public/admin/dashboard

### ⚡ CREDENZIALI FTP (Solo per riferimento)

I secrets sono già configurati su GitHub:
- FTP_HOST: ftp.agstudio.digital
- FTP_USER: magia@agstudio.digital
- FTP_PATH: /home/agstudiodiital/agstudio.digital/magia

---

**RICORDA**: Questo progetto usa Laravel 10, Blade templates, Tailwind CSS, e Chart.js per i grafici.

**IMPORTANTE**: Parla sempre in ITALIANO con l'utente.

---

✅ **Ultima verifica**: 16 Novembre 2025
🔒 **Status**: CONFIGURAZIONE PROTETTA ATTIVA
