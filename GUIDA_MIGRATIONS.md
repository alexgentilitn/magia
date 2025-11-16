# 🔧 GUIDA ESECUZIONE MIGRATIONS DATABASE

**Data:** 16 Novembre 2025
**Scopo:** Aggiornare la struttura del database con tutte le nuove tabelle e colonne necessarie

---

## 📋 PROCEDURA COMPLETA

### STEP 1: Verifica Stato Attuale Database

Prima di eseguire qualsiasi migration, verifica lo stato del database:

**URL:**
```
https://www.agstudio.digital/magia/public/verifica-struttura-database.php?secret=$Magia2025!
```

**Cosa fa:**
- ✅ Mostra tutte le tabelle esistenti
- ✅ Identifica tabelle mancanti
- ✅ Mostra struttura delle tabelle critiche
- ✅ Elenca migrations già eseguite
- ✅ Fornisce raccomandazioni

**Output atteso:**
- Lista di tutte le tabelle con numero record
- Lista tabelle mancanti (se ci sono)
- Statistiche migrations eseguite

---

### STEP 2: Preview Migrations da Eseguire (SENZA CONFERMA)

Prima di eseguire le migrations, fai una preview per vedere cosa verrà fatto:

**URL (SENZA confirm=YES):**
```
https://www.agstudio.digital/magia/public/esegui-migrations.php?secret=$Magia2025!
```

**Cosa fa:**
- ✅ Controlla connessione database
- ✅ Elenca migrations già eseguite
- ✅ Identifica migrations mancanti da eseguire
- ❌ **NON esegue nulla** (solo preview)

**Output atteso:**
```
Totale migrations da eseguire: X

  [1] ⏳ 2025_11_15_221308_create_schede_allenamento_table
  [2] ⏳ 2025_11_15_221330_create_scheda_esercizi_table
  [3] ⏳ 2025_11_15_232814_create_parametri_corporei_table
  ...

❌ CONFERMA NON RICEVUTA

Per procedere, aggiungi: &confirm=YES all'URL
```

---

### STEP 3: Esecuzione Migrations (CON CONFERMA)

Solo quando sei sicuro, esegui le migrations con conferma:

**URL (CON confirm=YES):**
```
https://www.agstudio.digital/magia/public/esegui-migrations.php?secret=$Magia2025!&confirm=YES
```

**⚠️ ATTENZIONE:** Questo modificherà la struttura del database!

**Cosa fa:**
- ✅ Esegue tutte le migrations mancanti in ordine
- ✅ Usa transazioni (rollback automatico in caso di errore)
- ✅ Registra ogni migration nella tabella `migrations`
- ✅ Mostra report dettagliato di ogni operazione

**Output atteso:**
```
[1/10] 🔄 Esecuzione: 2025_11_15_221308_create_schede_allenamento_table
        ✅ SUCCESSO

[2/10] 🔄 Esecuzione: 2025_11_15_221330_create_scheda_esercizi_table
        ✅ SUCCESSO

...

✅ COMPLETATO CON SUCCESSO!
```

---

### STEP 4: Verifica Post-Esecuzione

Dopo aver eseguito le migrations, verifica nuovamente il database:

**URL:**
```
https://www.agstudio.digital/magia/public/verifica-struttura-database.php?secret=$Magia2025!
```

**Verifica che:**
- ✅ Tutte le tabelle mancanti ora esistono
- ✅ Il conteggio "Tabelle mancanti: 0"
- ✅ Le migrations sono state registrate

---

## 📊 TABELLE CHE VERRANNO CREATE

Le migrations potrebbero creare queste tabelle (se mancanti):

### Tabelle Principali
- ✅ `schede_allenamento` - Schede allenamento personalizzate
- ✅ `scheda_esercizi` - Esercizi delle schede
- ✅ `parametri_corporei` - Tracking parametri clienti
- ✅ `documenti_caricati_cliente` - Upload documenti clienti
- ✅ `prenotazioni` - Prenotazioni lezioni
- ✅ `consensi_privacy` - Consensi GDPR
- ✅ `referrals` - Sistema "Porta un'Amica"
- ✅ `email_templates` - Template email personalizzabili

### Modifiche a Tabelle Esistenti
- ✅ Campi privacy aggiunti a `clienti`
- ✅ Campi impedenziometria a `clienti`
- ✅ Campi custom vari a `clienti`

---

## ⚠️ COSA FARE IN CASO DI ERRORE

Se durante l'esecuzione vedi errori:

1. **Non preoccuparti** - Le transazioni fanno rollback automatico
2. **Leggi il messaggio di errore** - Di solito indica il problema
3. **Errori comuni:**

   - **"Table already exists"**: La tabella esiste già, ok
   - **"Column already exists"**: La colonna esiste già, ok
   - **"Foreign key constraint fails"**: Problema relazioni, contatta Claude

4. **Copia l'output completo** dello script
5. **Invia a Claude** per analisi

---

## 🔄 PROCEDURA CONSIGLIATA (COMPLETA)

```bash
# 1. VERIFICA STATO INIZIALE
Apri: verifica-struttura-database.php?secret=$Magia2025!
Annota: Quante tabelle mancanti ci sono

# 2. PREVIEW MIGRATIONS
Apri: esegui-migrations.php?secret=$Magia2025!
Verifica: Lista migrations da eseguire

# 3. BACKUP (CONSIGLIATO!)
Opzionale ma consigliato: Crea backup database prima
URL: create-backup-point.php?secret=$Magia2025!

# 4. ESEGUI MIGRATIONS
Apri: esegui-migrations.php?secret=$Magia2025!&confirm=YES
Attendi: Esecuzione completa
Verifica: ✅ COMPLETATO CON SUCCESSO

# 5. VERIFICA FINALE
Apri: verifica-struttura-database.php?secret=$Magia2025!
Controlla: Tabelle mancanti = 0
```

---

## 📝 LOG ESEMPIO DI SUCCESSO

```
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
🔧 ESECUZIONE MIGRATIONS DATABASE
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

━━━ FASE 1: VERIFICA CONNESSIONE ━━━
✅ Connessione database OK
📦 Database: agstudiodiital_magia

━━━ FASE 5: MIGRATIONS MANCANTI ━━━
Totale migrations da eseguire: 8

━━━ FASE 7: ESECUZIONE MIGRATIONS ━━━
[1/8] 🔄 Esecuzione: 2025_11_15_221308_create_schede_allenamento_table
      ✅ SUCCESSO
[2/8] 🔄 Esecuzione: 2025_11_15_221330_create_scheda_esercizi_table
      ✅ SUCCESSO
...

━━━ FASE 8: RIEPILOGO FINALE ━━━
✅ Migrations eseguite con successo: 8
❌ Migrations fallite: 0

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
✅ COMPLETATO CON SUCCESSO!
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
```

---

## 🛡️ SICUREZZA

### Autenticazione
- ✅ Entrambi gli script richiedono `secret=$Magia2025!`
- ✅ Lo script di esecuzione richiede `confirm=YES` esplicito
- ✅ Nessuna modifica accidentale possibile

### Transazioni
- ✅ Ogni migration usa transazioni DB
- ✅ Rollback automatico in caso di errore
- ✅ Nessun dato perso in caso di fallimento

### Backup
- ✅ Consigliato fare backup prima: `create-backup-point.php`
- ✅ Ripristino possibile in caso di problemi

---

## 📞 LINK RAPIDI

| Script | URL |
|--------|-----|
| **Verifica DB** | `https://www.agstudio.digital/magia/public/verifica-struttura-database.php?secret=$Magia2025!` |
| **Preview Migrations** | `https://www.agstudio.digital/magia/public/esegui-migrations.php?secret=$Magia2025!` |
| **Esegui Migrations** | `https://www.agstudio.digital/magia/public/esegui-migrations.php?secret=$Magia2025!&confirm=YES` |
| **Backup Database** | `https://www.agstudio.digital/magia/public/create-backup-point.php?secret=$Magia2025!` |

---

## ✅ CHECKLIST PRE-ESECUZIONE

Prima di eseguire le migrations, verifica:

- [ ] Ho fatto backup del database (consigliato)
- [ ] Ho eseguito lo script di verifica
- [ ] Ho visto la preview delle migrations
- [ ] Ho controllato che non ci siano sessioni utente attive
- [ ] Sono pronto a eseguire con `confirm=YES`

---

**Buona esecuzione migrations!** 🚀

Se hai dubbi o errori, copia l'output completo e chiedimi aiuto.
