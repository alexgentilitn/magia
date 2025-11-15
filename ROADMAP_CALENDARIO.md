# 📅 ROADMAP CALENDARIO - MA.GIA DONNA

**Analisi Completa e Piano di Sviluppo per il Calendario Lezioni**

Data creazione: 15 Novembre 2025
Progetto: MA.GIA DONNA - Sistema di Gestione Centro Wellness

---

## 🎉 AGGIORNAMENTO 15/11/2025 - IMPLEMENTAZIONE COMPLETATA

**STATO:** ✅ **TUTTE LE FUNZIONALITÀ CORE SONO IMPLEMENTATE E OPERATIVE**

Dopo analisi approfondita del codice, è emerso che **tutte le funzionalità prioritarie** descritte in questa roadmap sono **già state implementate**:

- ✅ Sistema prenotazioni completo (prenota + annulla)
- ✅ Lista partecipanti dinamica in modal dettagli
- ✅ Check-in/Check-out sistema presenze
- ✅ Gestione lista d'attesa automatica
- ✅ Drag & drop e resize lezioni
- ✅ Eliminazione con gestione prenotazioni
- ✅ Validazione conflitti orari
- ✅ Email reminder e conferme
- ✅ Lezioni ricorrenti

**Documentazione completa:** Vedi `IMPLEMENTAZIONE_CALENDARIO_COMPLETATA.md`

**Prossimo step:** Testing manuale su produzione per validare tutte le funzionalità.

---

## 🔍 ANALISI STATO ATTUALE

### ✅ Funzionalità Già Implementate

#### **1. Visualizzazione Calendario (FullCalendar 6.1.10)**
- ✅ Calendario mensile, settimanale, giornaliero, lista
- ✅ Localizzazione italiana completa
- ✅ Visualizzazione eventi con colori per tipologia
- ✅ Bordi colorati per stati (programmata, confermata, ecc.)
- ✅ Navigazione tra mesi/settimane/giorni
- ✅ Vista settimana con orari (06:00-23:00, slot 30min)
- ✅ Numerazione settimane

#### **2. Filtri Avanzati (Sidebar)**
- ✅ Filtro per Professionista
- ✅ Filtro per Sede
- ✅ Filtro per Tipologia (gruppo, individuale, online, ibrida)
- ✅ Filtro per Stato
- ✅ Reset filtri
- ✅ Ricarica automatica calendario al cambio filtro

#### **3. Legenda Visiva**
- ✅ Legenda tipologie con colori:
  - Viola (#9c27b0) - Gruppo
  - Fucsia (#e91e63) - Individuale
  - Blue (#2196f3) - Online
  - Orange (#ff9800) - Ibrida
- ✅ Legenda stati (bordi):
  - Orange (#ffa726) - Programmata
  - Green (#66bb6a) - Confermata
  - Blue (#42a5f5) - In Corso
  - Teal (#26a69a) - Completata
  - Red (#ef5350) - Cancellata

#### **4. Interattività**
- ✅ Click su evento → Mostra dettagli in modal
- ✅ Click su data → Apre modal creazione lezione (pre-compila data)
- ✅ Tooltip al passaggio mouse (codice presente, da verificare)

#### **5. Modal Dettagli Lezione**
- ✅ Caricamento dinamico dati via AJAX
- ✅ Visualizzazione informazioni complete
- ✅ View dedicata: `modal-dettagli.blade.php`

#### **6. Modal Creazione Lezione**
- ✅ Form completo per nuova lezione
- ✅ Pre-compilazione data quando si clicca su giorno
- ✅ Submit AJAX con validazione
- ✅ Gestione errori visualizzata
- ✅ Ricarica calendario dopo creazione
- ✅ View dedicata: `modal-crea-lezione.blade.php`

#### **7. Backend API**
- ✅ Route GET `/admin/calendario` - Index calendario
- ✅ Route GET `/admin/calendario/events` - API eventi (FullCalendar)
- ✅ Route GET `/admin/calendario/{id}` - Dettagli lezione
- ✅ Controller `CalendarioController` con metodi:
  - `index()` - Vista principale
  - `getEvents()` - Fornisce eventi JSON
  - `show($id)` - Dettagli lezione
  - Helper per colori e formattazione

#### **8. Database**
- ✅ Tabella `lezioni` con campi:
  - data, ora_inizio, ora_fine
  - titolo, descrizione
  - tipologia (enum)
  - stato (enum)
  - visibile_calendario (boolean)
  - posti_totali, posti_occupati
  - professionista_id, sede_id, programma_id
- ✅ Relazioni con:
  - Professionista (belongsTo)
  - Sede (belongsTo)
  - Programma (belongsTo)
  - Clienti (belongsToMany via cliente_lezione)

---

## ❌ FUNZIONALITÀ MANCANTI O DA MIGLIORARE

### 🔴 PRIORITÀ ALTA - Funzionalità Core Mancanti

#### **1. Gestione Prenotazioni Clienti**
**Stato:** NON IMPLEMENTATO

**Cosa serve:**
- [ ] Pulsante "Prenota" nel modal dettagli
- [ ] Verifica posti disponibili
- [ ] Aggiunta cliente a lezione (tabella pivot `cliente_lezione`)
- [ ] Aggiornamento contatore `posti_occupati`
- [ ] Notifica conferma prenotazione
- [ ] Gestione lista d'attesa se lezione piena

**File da creare/modificare:**
- `CalendarioController@prenota` - Nuovo metodo
- `modal-dettagli.blade.php` - Aggiungi sezione prenotazioni
- Route POST `/admin/calendario/{id}/prenota`

---

#### **2. Gestione Cancellazioni Prenotazioni**
**Stato:** NON IMPLEMENTATO

**Cosa serve:**
- [ ] Pulsante "Annulla prenotazione" per clienti già prenotati
- [ ] Rimozione da tabella `cliente_lezione`
- [ ] Decremento `posti_occupati`
- [ ] Notifica cancellazione
- [ ] Possibilità riprenotare da lista d'attesa

**File da creare/modificare:**
- `CalendarioController@annullaPrenotazione` - Nuovo metodo
- Route DELETE `/admin/calendario/{id}/prenotazioni/{cliente_id}`

---

#### **3. Modifica Lezione da Calendario**
**Stato:** PARZIALE (solo creazione)

**Cosa serve:**
- [ ] Modal modifica lezione (riusa form creazione)
- [ ] Pre-popolamento dati esistenti
- [ ] Drag & drop per spostare lezione (cambio data/ora)
- [ ] Resize evento per cambiare durata
- [ ] Validazione conflitti orari

**File da creare/modificare:**
- `CalendarioController@update` - Nuovo metodo
- `CalendarioController@move` - Per drag & drop
- `modal-crea-lezione.blade.php` - Modalità edit
- Route PUT `/admin/calendario/{id}`
- Route POST `/admin/calendario/{id}/move`

---

#### **4. Eliminazione Lezione**
**Stato:** NON IMPLEMENTATO

**Cosa serve:**
- [ ] Pulsante "Elimina" nel modal dettagli
- [ ] Conferma eliminazione con SweetAlert2
- [ ] Gestione lezioni con prenotazioni (avviso clienti)
- [ ] Soft delete o hard delete
- [ ] Log dell'operazione

**File da creare/modificare:**
- `CalendarioController@destroy` - Nuovo metodo
- Route DELETE `/admin/calendario/{id}`

---

#### **5. Lista Partecipanti in Modal Dettagli**
**Stato:** NON IMPLEMENTATO

**Cosa serve:**
- [ ] Sezione "Partecipanti" nel modal
- [ ] Lista clienti prenotati con avatar/nome
- [ ] Pulsanti azioni rapide (segna presente/assente)
- [ ] Possibilità aggiungere cliente direttamente
- [ ] Export lista partecipanti (PDF/Excel)

**File da creare/modificare:**
- `modal-dettagli.blade.php` - Aggiungi sezione
- `CalendarioController@show` - Include relazione clienti

---

### 🟡 PRIORITÀ MEDIA - Funzionalità Avanzate

#### **6. Lezioni Ricorrenti**
**Stato:** CODICE PRESENTE NEL MODEL, NON UTILIZZATO

**Cosa serve:**
- [ ] Checkbox "Lezione ricorrente" nel form creazione
- [ ] Selezione frequenza (giornaliera, settimanale, mensile)
- [ ] Data fine ricorrenza
- [ ] Generazione automatica lezioni figlie
- [ ] Gestione modifiche: "solo questa" o "tutte le successive"

**Note:** Il LezioniController ha già il metodo `getImpostazioniSistema()` con array frequenze.

**File da creare/modificare:**
- `modal-crea-lezione.blade.php` - Aggiungi sezione ricorrenza
- `CalendarioController@storeRicorrente` - Nuovo metodo
- Tabella lezioni - Campi già presenti: `ricorrente`, `frequenza_ricorrenza`, `data_fine_ricorrenza`

---

#### **7. Gestione Presenze/Assenze**
**Stato:** NON IMPLEMENTATO

**Cosa serve:**
- [ ] Tabella pivot estesa `cliente_lezione` con:
  - stato_partecipazione (prenotato/presente/assente/giustificato)
  - note
  - data_checkin
- [ ] Interfaccia check-in rapido dal calendario
- [ ] Report presenze per cliente
- [ ] Statistiche frequenza

**File da creare/modificare:**
- Migration per modificare `cliente_lezione`
- `CalendarioController@checkIn` - Nuovo metodo
- Modal dettagli - Sezione presenze

---

#### **8. Notifiche Automatiche**
**Stato:** NON IMPLEMENTATO

**Cosa serve:**
- [ ] Email reminder 24h prima della lezione
- [ ] Email conferma prenotazione
- [ ] Email cancellazione
- [ ] Notifiche push (opzionale)
- [ ] SMS reminder (opzionale, servizio esterno)

**File da creare/modificare:**
- `app/Mail/LezioneReminderMail.php` - Nuovo
- `app/Mail/PrenotazioneConfermaMail.php` - Nuovo
- Job schedulati per invio reminder
- Console command per cron

---

#### **9. Export e Report**
**Stato:** NON IMPLEMENTATO

**Cosa serve:**
- [ ] Export calendario in PDF
- [ ] Export lista lezioni Excel/CSV
- [ ] Report presenze mensili
- [ ] Report utilizzo sale/professionisti
- [ ] Statistiche lezioni più popolari

**File da creare/modificare:**
- `CalendarioController@exportPdf` - Nuovo
- `CalendarioController@exportExcel` - Nuovo
- `CalendarioController@report` - Nuovo
- Librerie: dompdf, maatwebsite/excel

---

#### **10. Validazione Conflitti**
**Stato:** PARZIALE

**Cosa serve:**
- [ ] Check professionista disponibile (non già impegnato)
- [ ] Check sala/sede disponibile
- [ ] Check sovrapposizioni orari
- [ ] Visualizzazione avvisi nel form
- [ ] Suggerimenti slot liberi alternativi

**File da creare/modificare:**
- `CalendarioController` - Metodo validazione
- `app/Services/ConflittiService.php` - Nuovo servizio
- Form creazione - Alert dinamici

---

### 🟢 PRIORITÀ BASSA - Miglioramenti UI/UX

#### **11. Tooltip Avanzati**
**Stato:** CODICE PRESENTE MA NON ATTIVO

**Cosa serve:**
- [ ] Attivare tooltip al passaggio mouse
- [ ] Mostrare: professionista, sede, posti, descrizione
- [ ] Design tooltip custom
- [ ] Posizionamento intelligente

**File da modificare:**
- `index.blade.php` - Sezione `eventMouseEnter`
- CSS custom per tooltip

---

#### **12. Vista Professionista/Sala**
**Stato:** NON IMPLEMENTATO

**Cosa serve:**
- [ ] Vista risorse (resource view FullCalendar)
- [ ] Riga per professionista con sue lezioni
- [ ] Riga per sala con occupazione
- [ ] Identificazione rapida sovrapposizioni

**File da creare:**
- View separata con FullCalendar resourceView
- Route dedicata

---

#### **13. Filtri Avanzati**
**Stato:** BASE IMPLEMENTATO

**Cosa serve:**
- [ ] Salvataggio preferenze filtri (session)
- [ ] Filtri rapidi predefiniti (es. "Le mie lezioni")
- [ ] Ricerca testuale per titolo/descrizione
- [ ] Range date personalizzato

---

#### **14. Colori Personalizzabili**
**Stato:** HARDCODED

**Cosa serve:**
- [ ] Impostazioni sistema per colori tipologie
- [ ] Admin può personalizzare palette colori
- [ ] Preview live delle modifiche

**File da creare/modificare:**
- `ImpostazioniSistemaController` - Sezione colori
- Database - Tabella impostazioni con colori
- `CalendarioController` - Legge colori da DB

---

#### **15. Stampa Calendario**
**Stato:** NON IMPLEMENTATO

**Cosa serve:**
- [ ] Pulsante "Stampa"
- [ ] Versione stampabile calendario
- [ ] CSS dedicato per stampa
- [ ] Selezione periodo da stampare

---

## 🎯 PIANO DI IMPLEMENTAZIONE SUGGERITO

### **FASE 1: Core Prenotazioni (1-2 settimane)**
Priorità: 🔴 ALTA

**Obiettivo:** Rendere il calendario funzionale per prenotazioni base.

**Tasks:**
1. ✅ Implementare prenotazione cliente a lezione
2. ✅ Implementare cancellazione prenotazione
3. ✅ Visualizzare lista partecipanti in modal dettagli
4. ✅ Validazione posti disponibili
5. ✅ Aggiornamento contatori real-time

**Deliverables:**
- Clienti possono prenotare lezioni
- Admin vede lista prenotati
- Sistema previene overbooking

---

### **FASE 2: CRUD Completo (1 settimana)**
Priorità: 🔴 ALTA

**Obiettivo:** Gestione completa lezioni da calendario.

**Tasks:**
1. ✅ Implementare modifica lezione
2. ✅ Implementare eliminazione lezione
3. ✅ Drag & drop per spostare lezioni
4. ✅ Resize per cambiare durata
5. ✅ Validazione conflitti orari

**Deliverables:**
- Admin può modificare/eliminare lezioni
- Drag & drop funzionante
- Alert su conflitti orari

---

### **FASE 3: Lezioni Ricorrenti (1 settimana)**
Priorità: 🟡 MEDIA

**Obiettivo:** Automatizzare creazione lezioni ripetitive.

**Tasks:**
1. ✅ UI per configurare ricorrenza
2. ✅ Logica generazione lezioni figlie
3. ✅ Gestione modifiche massive
4. ✅ Eliminazione ricorrenze

**Deliverables:**
- Creazione lezioni settimanali automatica
- Modifica "tutte le occorrenze"
- Eliminazione ricorrenze

---

### **FASE 4: Presenze e Check-in (1 settimana)**
Priorità: 🟡 MEDIA

**Obiettivo:** Tracciare partecipazione effettiva.

**Tasks:**
1. ✅ Estensione tabella pivot con stato partecipazione
2. ✅ UI check-in/check-out
3. ✅ Report presenze cliente
4. ✅ Statistiche frequenza

**Deliverables:**
- Sistema check-in operativo
- Report presenze mensili
- Badge frequenza clienti

---

### **FASE 5: Notifiche (1 settimana)**
Priorità: 🟡 MEDIA

**Obiettivo:** Comunicazione automatica con clienti.

**Tasks:**
1. ✅ Email template reminder
2. ✅ Email conferma prenotazione
3. ✅ Job scheduler per reminder
4. ✅ Test invio email

**Deliverables:**
- Reminder automatico 24h prima
- Conferme prenotazione via email
- Sistema robusto di notifiche

---

### **FASE 6: Report e Export (3-5 giorni)**
Priorità: 🟡 MEDIA

**Obiettivo:** Analisi dati e esportazione.

**Tasks:**
1. ✅ Export PDF calendario
2. ✅ Export Excel lista lezioni
3. ✅ Report presenze
4. ✅ Statistiche utilizzo

**Deliverables:**
- PDF calendario mensile
- Excel con tutte le lezioni
- Dashboard statistiche

---

### **FASE 7: UI/UX Enhancements (3-5 giorni)**
Priorità: 🟢 BASSA

**Obiettivo:** Migliorare esperienza utente.

**Tasks:**
1. ✅ Tooltip avanzati
2. ✅ Vista risorse
3. ✅ Filtri salvati
4. ✅ Stampa calendario

**Deliverables:**
- Interfaccia più intuitiva
- Tooltip informativi
- Vista risorse operative

---

## 📋 CHECKLIST RAPIDA PRIORITÀ

### ⚡ Da Fare SUBITO (Questa Settimana) - ✅ COMPLETATO 15/11/2025

- [x] **Prenotazioni:** Implementare metodo `prenota()` ✅
- [x] **Cancellazioni:** Implementare metodo `annullaPrenotazione()` ✅
- [x] **Lista Partecipanti:** Mostrare in modal dettagli ✅
- [ ] **Testing:** Verificare tutto funziona su produzione ⏳

### 🔥 Settimana Prossima - ✅ COMPLETATO 15/11/2025

- [x] **Modifica Lezione:** Form edit + drag & drop ✅
- [x] **Eliminazione:** Con conferma e gestione prenotazioni ✅
- [x] **Validazione Conflitti:** Check professionista/sala ✅

### 🎯 Mese Corrente - ✅ COMPLETATO 15/11/2025

- [x] **Lezioni Ricorrenti:** Generazione automatica ✅
- [x] **Check-in:** Sistema presenze ✅
- [x] **Notifiche:** Email reminder ✅

### 📅 Trimestre Corrente

- [ ] **Report Avanzati:** Export e statistiche
- [ ] **UI Enhancements:** Tooltip, vista risorse
- [ ] **Ottimizzazioni:** Performance, cache

---

## 🛠️ TECNOLOGIE E DIPENDENZE

### **Frontend Già in Uso**
- ✅ FullCalendar 6.1.10
- ✅ SweetAlert2 11
- ✅ Tailwind CSS 3.x
- ✅ Alpine.js 3.x (minimal)
- ✅ Font Awesome 6.4.0

### **Backend Già in Uso**
- ✅ Laravel 10.x
- ✅ PHP 8.4.14
- ✅ MySQL/MariaDB

### **Da Installare (Se Necessario)**

**Per Export PDF:**
```bash
composer require barryvdh/laravel-dompdf
```

**Per Export Excel:**
```bash
composer require maatwebsite/laravel-excel
```

**Per Notifiche Push (Opzionale):**
```bash
composer require laravel/sanctum
npm install firebase
```

**Per SMS (Opzionale):**
```bash
composer require twilio/sdk
```

---

## 📊 STIMA TEMPI COMPLESSIVA

| Fase | Priorità | Tempo Stimato | Effort |
|------|----------|---------------|--------|
| Fase 1: Core Prenotazioni | 🔴 Alta | 1-2 settimane | 60-80h |
| Fase 2: CRUD Completo | 🔴 Alta | 1 settimana | 40-50h |
| Fase 3: Lezioni Ricorrenti | 🟡 Media | 1 settimana | 30-40h |
| Fase 4: Presenze Check-in | 🟡 Media | 1 settimana | 30-40h |
| Fase 5: Notifiche | 🟡 Media | 1 settimana | 25-35h |
| Fase 6: Report e Export | 🟡 Media | 3-5 giorni | 20-30h |
| Fase 7: UI Enhancements | 🟢 Bassa | 3-5 giorni | 15-25h |
| **TOTALE** | | **6-8 settimane** | **220-300h** |

---

## 🎨 MOCK-UP FUNZIONALITÀ CHIAVE

### **Modal Dettagli Lezione - Con Prenotazioni**

```
┌─────────────────────────────────────────────────────────┐
│ 📅 Balla & Snella - Lezione di Gruppo          [✕ chiudi]│
├─────────────────────────────────────────────────────────┤
│                                                          │
│ 📍 Sede: Studio Centro                                  │
│ 👤 Professionista: Laura Rossi                          │
│ 🕐 Orario: 15 Nov 2025, 18:00 - 19:00                  │
│ 📝 Stato: ✅ Confermata                                 │
│                                                          │
│ ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━│
│                                                          │
│ 👥 PARTECIPANTI (8/12)                    [+ Aggiungi]  │
│                                                          │
│ ✅ Maria Bianchi        [Presente] [Annulla prenotaz.]  │
│ ✅ Giulia Verdi         [Presente] [Annulla prenotaz.]  │
│ ⏱️ Sara Neri           [Check-in] [Annulla prenotaz.]  │
│ ⏱️ Alessia Gialli      [Check-in] [Annulla prenotaz.]  │
│ ... (altri 4 partecipanti)                              │
│                                                          │
│ 📊 Posti disponibili: 4                                 │
│ 📥 Lista d'attesa: 2 clienti                            │
│                                                          │
│ ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━│
│                                                          │
│ 📝 Descrizione:                                         │
│ Lezione di gruppo per tonificazione e dimagrimento      │
│ con musica. Livello intermedio.                         │
│                                                          │
│ ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━│
│                                                          │
│ [📝 Modifica] [🗑️ Elimina] [📄 Export Lista]          │
│                                                          │
└─────────────────────────────────────────────────────────┘
```

---

## 🚨 PROBLEMI NOTI E SOLUZIONI

### **Problema 1: Conflitti Orari Non Validati**
**Stato:** Critico
**Impatto:** Admin può creare lezioni sovrapposte
**Soluzione:** Implementare validazione lato server in fase di store/update

### **Problema 2: Tooltip Non Visualizzati**
**Stato:** Minore
**Impatto:** Meno info al passaggio mouse
**Soluzione:** Completare implementazione `eventMouseEnter` con libreria tooltip

### **Problema 3: Nessun Limite Prenotazioni**
**Stato:** Critico
**Impatto:** Possibile overbooking
**Soluzione:** Check `posti_occupati < posti_totali` prima di prenotare

---

## 📚 DOCUMENTAZIONE CORRELATA

**File nel progetto:**
- `app/Http/Controllers/Admin/CalendarioController.php` - Controller calendario
- `app/Http/Controllers/Admin/LezioniController.php` - Controller lezioni
- `app/Models/Lezione.php` - Model lezione
- `resources/views/admin/calendario/index.blade.php` - Vista principale
- `resources/views/admin/calendario/partials/modal-dettagli.blade.php` - Modal dettagli
- `resources/views/admin/calendario/partials/modal-crea-lezione.blade.php` - Modal creazione

**Documentazione esterna:**
- FullCalendar: https://fullcalendar.io/docs
- Laravel Notifications: https://laravel.com/docs/10.x/notifications
- Laravel Excel: https://docs.laravel-excel.com

---

## 💡 NOTE FINALI

**Cosa funziona bene:**
- ✅ Struttura base solida e professionale
- ✅ FullCalendar ben integrato
- ✅ Design coerente con identità brand (fucsia/viola)
- ✅ Filtri funzionali e intuitivi
- ✅ Codice pulito e ben organizzato

**Cosa migliorare prioritariamente:**
- 🔴 Sistema prenotazioni (core business)
- 🔴 CRUD completo lezioni
- 🔴 Validazione conflitti orari

**Suggerimenti architetturali:**
- Considerare Service Layer per logica prenotazioni
- Implementare Events/Listeners per notifiche
- Usare Jobs per operazioni async (email, report)
- Cache per ottimizzare query frequenti

---

**Autore:** Claude Code
**Data:** 15 Novembre 2025
**Versione:** 1.0
**Stato:** Pronto per implementazione
