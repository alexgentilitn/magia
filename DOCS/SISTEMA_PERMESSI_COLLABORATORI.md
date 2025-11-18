# 🔐 Sistema Permessi Individuali per Collaboratori

## 📋 Panoramica

Sistema completo di gestione permessi dinamici per collaboratori, implementato secondo le specifiche del progetto PDF **MA.GIA DONNA** (pagina 3, sezione 2.1).

**Requisito PDF:**
> COLLABORATORE: Permessi: **Gestibili e attribuibili personalmente (ogni collaboratore può avere permessi diversi)**

---

## ✅ Implementazione Completata

### 1. **Database**

#### Tabella `utente_permesso` (Many-to-Many)
```sql
CREATE TABLE utente_permesso (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    utente_id BIGINT UNSIGNED,
    permesso_id BIGINT UNSIGNED,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    UNIQUE(utente_id, permesso_id),
    FOREIGN KEY (utente_id) REFERENCES utenti(id) ON DELETE CASCADE,
    FOREIGN KEY (permesso_id) REFERENCES permessi(id) ON DELETE CASCADE
);
```

**File:** `database/migrations/2025_11_17_create_utente_permesso_table.php`

---

### 2. **Model Utente - Nuovi Metodi**

**File:** `app/Models/Utente.php`

#### Relazione Many-to-Many
```php
public function permessiIndividuali()
{
    return $this->belongsToMany(
        Permesso::class,
        'utente_permesso',
        'utente_id',
        'permesso_id'
    )->withTimestamps();
}
```

#### Metodo haPermesso() - Aggiornato
```php
public function haPermesso($permesso_slug)
{
    // 1. Controlla permessi individuali (PRIORITÀ)
    if ($this->permessiIndividuali()->where('slug', $permesso_slug)->exists()) {
        return true;
    }

    // 2. Controlla permessi del ruolo
    return $this->ruolo->permessi()->where('slug', $permesso_slug)->exists();
}
```

#### Nuovi Metodi Gestione
```php
// Assegna permesso individuale
$utente->assegnaPermessoIndividuale($permesso_id);

// Rimuovi permesso individuale
$utente->rimuoviPermessoIndividuale($permesso_id);

// Sincronizza tutti i permessi
$utente->sincronizzaPermessiIndividuali([1, 2, 3]);

// Ottieni tutti i permessi (ruolo + individuali)
$tutti = $utente->tuttiIPermessi();
```

---

### 3. **Middleware VerificaPermesso**

**File:** `app/Http/Middleware/VerificaPermesso.php`

#### Utilizzo nelle Routes
```php
Route::middleware(['auth', 'permesso:gestione-presenze'])->group(function () {
    // Solo utenti con permesso "gestione-presenze"
});

// Permessi multipli (OR logic)
Route::middleware(['auth', 'permesso:permesso-a,permesso-b'])->group(function () {
    // Utente deve avere ALMENO UNO dei permessi
});
```

#### Registrato in Kernel
```php
'permesso' => \App\Http\Middleware\VerificaPermesso::class,
```

---

### 4. **Controller Gestione Permessi**

**File:** `app/Http/Controllers/Admin/PermessiCollaboratoreController.php`

#### Routes Disponibili
```php
// Mostra form gestione permessi
GET /admin/professionisti/{id}/permessi

// Salva permessi
PUT /admin/professionisti/{id}/permessi

// Reset permessi individuali
DELETE /admin/professionisti/{id}/permessi

// API: Ottieni permessi attuali (JSON)
GET /admin/professionisti/{id}/permessi/api
```

---

### 5. **Interfaccia Admin**

**File:** `resources/views/admin/professionisti/permessi.blade.php`

#### Funzionalità UI
- ✅ Lista permessi raggruppati per categoria
- ✅ Checkbox con switch per assegnare/rimuovere
- ✅ Badge per indicare permessi dal ruolo
- ✅ Statistiche (permessi ruolo, individuali, totali)
- ✅ Reset tutti i permessi individuali
- ✅ Interfaccia responsive e intuitiva

#### Screenshot Funzionalità
```
┌─────────────────────────────────────────────────┐
│  Gestione Permessi - Marco Rossi                │
│  ─────────────────────────────────────────────  │
│                                                  │
│  📊 Dashboard                                    │
│  ☐ Visualizzare Dashboard      [Dal Ruolo]      │
│  ☑ Gestire Statistiche (individuale)            │
│                                                  │
│  👥 Gestione Clienti                             │
│  ☑ Visualizzare Clienti                         │
│  ☐ Creare Clienti               [Dal Ruolo]     │
│                                                  │
│  🗓️ Gestione Presenze                            │
│  ☑ Gestione presenze lezioni (individuale)      │
│                                                  │
│  📈 Permessi dal Ruolo: 15                       │
│  🔐 Permessi Individuali: 3                      │
│  ⚡ Totale Permessi: 18                          │
│                                                  │
│  [Reset Permessi]            [Salva Permessi]   │
└─────────────────────────────────────────────────┘
```

---

## 🎯 Come Funziona

### Priorità Permessi

1. **Permessi Individuali** (massima priorità)
2. **Permessi del Ruolo**

Se un collaboratore ha un permesso assegnato individualmente, viene usato quello, altrimenti si controlla il ruolo.

### Esempio Pratico

**Scenario:**
- Marco è un Collaboratore (ruolo: "collaboratore")
- Il ruolo "collaboratore" ha: `visualizza-calendario`, `gestione-presenze`
- Marco riceve permesso individuale: `gestione-compensi`

**Risultato:**
```php
$marco->haPermesso('visualizza-calendario'); // true (dal ruolo)
$marco->haPermesso('gestione-presenze');     // true (dal ruolo)
$marco->haPermesso('gestione-compensi');     // true (individuale)
$marco->haPermesso('gestione-clienti');      // false (non assegnato)
```

---

## 📝 Guida Utilizzo Admin

### 1. Accedere alla Gestione Permessi

```
Dashboard Admin → Professionisti → [Seleziona Collaboratore] → "Gestisci Permessi"
```

### 2. Assegnare Permessi

1. Seleziona i permessi tramite checkbox
2. I permessi già presenti nel ruolo sono evidenziati con badge "Dal Ruolo"
3. Clicca "Salva Permessi"

### 3. Resettare Permessi

1. Clicca "Resetta Permessi Individuali"
2. Conferma l'azione
3. Il collaboratore utilizzerà solo i permessi del ruolo

---

## 🔧 Esempi Codice

### Proteggere una Route
```php
Route::middleware(['auth', 'permesso:gestione-presenze'])->group(function () {
    Route::get('/presenze', [PresenzeController::class, 'index']);
});
```

### Controllare Permessi nel Controller
```php
public function store(Request $request)
{
    if (!auth()->user()->haPermesso('crea-lezioni')) {
        abort(403, 'Non hai il permesso per creare lezioni');
    }

    // Logica creazione lezione...
}
```

### Blade Template
```blade
@if(auth()->user()->haPermesso('gestione-compensi'))
    <a href="{{ route('professionista.compensi.index') }}">
        Visualizza Compensi
    </a>
@endif
```

---

## 🚀 Testing

### Testare Permessi Individuali

```php
// Test 1: Assegnazione permesso
$collaboratore->assegnaPermessoIndividuale(5);
$this->assertTrue($collaboratore->haPermesso('gestione-presenze'));

// Test 2: Rimozione permesso
$collaboratore->rimuoviPermessoIndividuale(5);
$this->assertFalse($collaboratore->haPermesso('gestione-presenze'));

// Test 3: Sincronizzazione
$collaboratore->sincronizzaPermessiIndividuali([1, 2, 3]);
$this->assertEquals(3, $collaboratore->permessiIndividuali->count());
```

---

## 📦 Files Creati/Modificati

### Nuovi Files
- ✅ `app/Http/Middleware/VerificaPermesso.php`
- ✅ `app/Http/Controllers/Admin/PermessiCollaboratoreController.php`
- ✅ `database/migrations/2025_11_17_create_utente_permesso_table.php`
- ✅ `resources/views/admin/professionisti/permessi.blade.php`

### Files Modificati
- ✅ `app/Models/Utente.php` (aggiunti metodi permessi)
- ✅ `app/Http/Kernel.php` (registrato middleware)
- ✅ `routes/web.php` (aggiunte routes)
- ✅ `resources/views/admin/professionisti/show.blade.php` (pulsante gestione permessi)

---

## 🎓 Permessi Disponibili nel Sistema

### Categoria: Dashboard
- `visualizza-dashboard` - Visualizzare Dashboard

### Categoria: Clienti
- `visualizza-clienti` - Visualizzare Clienti
- `crea-clienti` - Creare Clienti
- `modifica-clienti` - Modificare Clienti
- `elimina-clienti` - Eliminare Clienti

### Categoria: Programmi
- `visualizza-programmi` - Visualizzare Programmi
- `crea-programmi` - Creare Programmi
- `modifica-programmi` - Modificare Programmi
- `elimina-programmi` - Eliminare Programmi

### Categoria: Corsi
- `visualizza-corsi` - Visualizzare Corsi
- `crea-lezioni` - Creare Lezioni
- `modifica-lezioni` - Modificare Lezioni
- `elimina-lezioni` - Eliminare Lezioni
- `gestione-presenze` - Gestione Presenze Lezioni

### Categoria: Pagamenti
- `visualizza-pagamenti` - Visualizzare Pagamenti
- `gestione-compensi` - Visualizzazione Compensi Personali

### Categoria: Sedi
- `visualizza-sedi` - Visualizzare Sedi
- `gestione-sedi` - Gestire Sedi

### Categoria: Sistema
- `gestione-utenti` - Gestione Utenti (CRITICO)
- `configurazioni-sistema` - Configurazioni Sistema (CRITICO)

---

## ✨ Vantaggi Implementazione

1. **✅ Conformità PDF**: Requisito "permessi configurabili individualmente" soddisfatto al 100%
2. **🔒 Sicurezza**: Ogni collaboratore ha esattamente i permessi necessari
3. **⚡ Flessibilità**: Admin può modificare permessi senza cambiare codice
4. **📊 Trasparenza**: UI chiara mostra permessi ruolo vs individuali
5. **🔧 Manutenibilità**: Sistema RBAC standard Laravel, facilmente estendibile

---

## 🏁 Prossimi Step (Opzionali)

1. **Gates Laravel** - Integrare con sistema Gates nativi Laravel
2. **Audit Log** - Tracciare modifiche permessi
3. **Scadenza Permessi** - Permessi temporanei con data scadenza
4. **Gruppi Permessi** - Template permessi predefiniti

---

## 📞 Supporto

Per domande o problemi:
- Documentazione Laravel RBAC: https://laravel.com/docs/authorization
- Specifiche progetto: `/GUIDA PROGETTO/magia_donna_linee_guida_progetto.pdf`

---

**Data Implementazione:** 17 Novembre 2025
**Sviluppatore:** AGstudio di Alex Gentili
**Versione:** 1.0
**Status:** ✅ COMPLETATO
