# 🔄 GUIDA ROLLBACK - Ritorno a MySQL

**Data migrazione a JSON:** 14 Novembre 2025
**Tag backup:** `backup-before-json-migration`
**Branch:** `claude/confirm-status-01NaRPJZBUHxak94aM2zKA1u`

## ⚠️ SE QUALCOSA NON FUNZIONA

### Opzione 1: Rollback completo (RAPIDO)

```bash
# Torna al commit prima della migrazione
git reset --hard backup-before-json-migration

# Oppure usa l'hash del commit
git reset --hard 4706a86

# Push forzato (ATTENZIONE!)
git push -f origin claude/confirm-status-01NaRPJZBUHxak94aM2zKA1u
```

### Opzione 2: Ripristina solo .env MySQL

File: `.env`

```env
# Decommentare queste righe per tornare a MySQL
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=agstudiodiital_magia
DB_USERNAME=agstudiodiital_agstudiomagia
DB_PASSWORD=$Magia2015!
```

Poi:
```bash
php artisan config:clear
php artisan cache:clear
```

### Opzione 3: Mantieni JSON ma usa MySQL in parallelo

Lascia JSON per sviluppo, MySQL per produzione.

Non servono modifiche, funzionano entrambi.

## 📋 Configurazione MySQL Originale

### Credenziali Database
```
Host:     localhost
Porta:    3306
Database: agstudiodiital_magia
Username: agstudiodiital_agstudiomagia
Password: $Magia2015!
```

### File .env Originale
```env
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=agstudiodiital_magia
DB_USERNAME=agstudiodiital_agstudiomagia
DB_PASSWORD=$Magia2015!
```

## 🎯 Stato Prima della Migrazione

**Commit:** `4706a86` - "Completa migrazione database MySQL a JSON con dati reali"
**Branch:** `claude/confirm-status-01NaRPJZBUHxak94aM2zKA1u`

**Funzionante:**
- ✅ MySQL remoto connesso
- ✅ Models Eloquent funzionanti
- ✅ Controllers usano MySQL
- ✅ Database JSON creato ma non usato dall'app

## 📊 Dump Database MySQL

Il dump completo è disponibile nel messaggio dove l'hai incollato.
Contiene:
- 4 utenti
- 1 cliente
- 3 ruoli
- 1 sede
- 1 professionista
- 1 programma
- 2 lezioni
- 1 pagamento

## 🔧 Test Rollback

Per verificare che MySQL funzioni dopo rollback:

```bash
php artisan migrate:status
php artisan tinker
# poi in tinker:
\App\Models\Utente::count()
```

Deve restituire il numero di utenti.

## 📞 In Caso di Emergenza

1. **STOP** - Non fare altre modifiche
2. **ROLLBACK** - Usa Opzione 1 sopra
3. **VERIFICA** - Accedi all'app e controlla che funzioni
4. **COMUNICA** - Descrivi il problema riscontrato

## ✅ Checklist Post-Rollback

- [ ] App accessibile
- [ ] Login funzionante
- [ ] Clienti visibili
- [ ] Lezioni visibili
- [ ] Nessun errore 500

Se tutti i check ✅ allora il rollback è riuscito!

---

**Nota:** Questo file verrà mantenuto anche dopo la migrazione per sicurezza.
