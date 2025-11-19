# 📋 Script Aggiornamento Database - Import Pesate

## 🎯 Cosa fa questo script

Lo script `update_database.php` aggiorna la struttura della tabella `clienti` per permettere l'importazione di pesate con creazione automatica di clienti minimi.

### Modifiche applicate:

Rende **NULLABLE** i seguenti campi:
- ✅ `codice_fiscale` - Può essere vuoto
- ✅ `email` - Può essere vuoto
- ✅ `telefono_mobile` - Può essere vuoto  
- ✅ `data_nascita` - Può essere vuoto
- ✅ `indirizzo` - Può essere vuoto
- ✅ `citta` - Può essere vuoto
- ✅ `provincia` - Può essere vuoto
- ✅ `cap` - Può essere vuoto

## 🚀 Come eseguirlo

### Opzione 1: Via CLI (Raccomandato)

```bash
cd /home/user/magia
php update_database.php
```

### Opzione 2: Via Browser

1. Carica il file su server: `update_database.php`
2. Apri nel browser: `https://www.agstudio.digital/magia/update_database.php`
3. **IMPORTANTE**: Elimina il file subito dopo: `rm update_database.php`

## ✅ Output atteso

```
=============================================================
AGGIORNAMENTO DATABASE - Sistema Importazione Pesate
=============================================================

📡 Connessione al database...
✅ Connessione riuscita!

🔍 Verifica tabella 'clienti'...
✅ Tabella 'clienti' trovata

📋 Modifiche da applicare:
-----------------------------------------------------------
1. Codice Fiscale → NULL
2. Email → NULL
3. Telefono Mobile → NULL
4. Data Nascita → NULL
5. Indirizzo → NULL
6. Città → NULL
7. Provincia → NULL
8. CAP → NULL

🔄 Avvio transazione...
✅ Codice Fiscale → NULL
✅ Email → NULL
✅ Telefono Mobile → NULL
✅ Data Nascita → NULL
✅ Indirizzo → NULL
✅ Città → NULL
✅ Provincia → NULL
✅ CAP → NULL

💾 Commit modifiche al database...

=============================================================
✅ AGGIORNAMENTO COMPLETATO CON SUCCESSO!
=============================================================

📊 RIEPILOGO:
   • Modifiche applicate: 8
   • Modifiche skippate: 0
   • Errori: 0

🎉 Il database è ora pronto per l'importazione pesate!
   → I clienti possono essere creati con SOLO nome + cognome
   → L'import accetta valori con unità di misura (es: '57.50 kg')
```

## 🔒 Sicurezza

- ✅ Usa **transazioni** - Rollback automatico in caso di errore
- ✅ Verifica esistenza campi prima di modificarli
- ✅ Non sovrascrive dati esistenti
- ✅ Safe anche se eseguito più volte

## ⚠️ Importante

**Dopo l'esecuzione**, elimina il file per sicurezza:

```bash
rm /home/user/magia/update_database.php
rm /home/user/magia/README_UPDATE_DATABASE.md
```

## 🐛 Troubleshooting

### Errore: "Connection refused"
- Verifica che MySQL sia in esecuzione
- Controlla credenziali in `.env`

### Errore: "Access denied"
- L'utente deve avere permessi `ALTER TABLE`
- Contatta amministratore database

### Errore: "Table 'clienti' not found"
- Verifica nome database in `.env`
- Esegui prima migrations base: `php artisan migrate`

## 📞 Supporto

Per problemi o domande, contatta il team di sviluppo.
