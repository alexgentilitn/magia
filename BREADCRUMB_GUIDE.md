# 🧭 Guida Breadcrumb Navigation - MA.GIA DONNA

**Data:** 15 Novembre 2025
**Component:** `resources/views/components/breadcrumb.blade.php`

---

## 🎯 Cosa sono i Breadcrumb

I breadcrumb (briciole di pane) mostrano il percorso di navigazione corrente:

```
🏠 Dashboard > Clienti > Maria Rossi
```

Migliorano:
- ✅ **UX:** L'utente sa dove si trova
- ✅ **Navigazione:** Click rapido per tornare indietro
- ✅ **Accessibilità:** Screen reader friendly

---

## ⚡ Utilizzo Base

### Sintassi

```blade
<x-breadcrumb :items="[
    ['label' => 'Nome Sezione', 'url' => route('nome.route')],
    ['label' => 'Dettaglio'] // ultimo elemento senza URL
]" />
```

### Esempio: Lista Clienti

```blade
@extends('layouts.admin')

@section('titolo', 'Clienti')

@section('contenuto')
<div class="p-6">
    <!-- Breadcrumb -->
    <x-breadcrumb :items="[
        ['label' => 'Gestione', 'icon' => 'fas fa-cog'],
        ['label' => 'Clienti']
    ]" />

    <h1>Lista Clienti</h1>
    <!-- ... resto contenuto ... -->
</div>
@endsection
```

### Esempio: Dettaglio Cliente

```blade
<x-breadcrumb :items="[
    ['label' => 'Gestione', 'icon' => 'fas fa-cog'],
    ['label' => 'Clienti', 'url' => route('admin.clienti.index')],
    ['label' => $cliente->nomeCompleto]
]" />
```

### Esempio: Modifica Cliente

```blade
<x-breadcrumb :items="[
    ['label' => 'Gestione', 'icon' => 'fas fa-cog'],
    ['label' => 'Clienti', 'url' => route('admin.clienti.index')],
    ['label' => $cliente->nomeCompleto, 'url' => route('admin.clienti.show', $cliente->id)],
    ['label' => 'Modifica']
]" />
```

---

## 🎨 Opzioni Component

### Parametri Item

| Parametro | Tipo | Descrizione | Obbligatorio |
|-----------|------|-------------|--------------|
| `label` | string | Testo da visualizzare | ✅ Sì |
| `url` | string | URL del link (se assente: testo semplice) | ❌ No |
| `icon` | string | Classe icona Font Awesome | ❌ No |

### Icone Suggerite

```php
'fas fa-cog'          // Gestione
'fas fa-users'        // Clienti
'fas fa-calendar-alt' // Lezioni
'fas fa-dumbbell'     // Programmi
'fas fa-euro-sign'    // Pagamenti
'fas fa-map-marker-alt' // Sedi
'fas fa-user-tie'     // Professionisti
'fas fa-chart-line'   // Report
```

---

## 📝 Pattern per Ogni Sezione

### CRUD Standard

#### Index (Lista)
```blade
<x-breadcrumb :items="[
    ['label' => 'Sezione Padre'],
    ['label' => 'Nome Risorsa']
]" />
```

#### Show (Dettaglio)
```blade
<x-breadcrumb :items="[
    ['label' => 'Sezione Padre'],
    ['label' => 'Nome Risorsa', 'url' => route('admin.risorsa.index')],
    ['label' => $risorsa->nome]
]" />
```

#### Create (Nuovo)
```blade
<x-breadcrumb :items="[
    ['label' => 'Sezione Padre'],
    ['label' => 'Nome Risorsa', 'url' => route('admin.risorsa.index')],
    ['label' => 'Nuovo', 'icon' => 'fas fa-plus']
]" />
```

#### Edit (Modifica)
```blade
<x-breadcrumb :items="[
    ['label' => 'Sezione Padre'],
    ['label' => 'Nome Risorsa', 'url' => route('admin.risorsa.index')],
    ['label' => $risorsa->nome, 'url' => route('admin.risorsa.show', $risorsa->id)],
    ['label' => 'Modifica', 'icon' => 'fas fa-edit']
]" />
```

---

## ✅ Esempi Completi per Sezione

### 1. Clienti

**Index:**
```blade
<x-breadcrumb :items="[
    ['label' => 'Gestione', 'icon' => 'fas fa-cog'],
    ['label' => 'Clienti']
]" />
```

**Show:**
```blade
<x-breadcrumb :items="[
    ['label' => 'Gestione', 'icon' => 'fas fa-cog'],
    ['label' => 'Clienti', 'url' => route('admin.clienti.index')],
    ['label' => $cliente->nomeCompleto]
]" />
```

**Create:**
```blade
<x-breadcrumb :items="[
    ['label' => 'Gestione', 'icon' => 'fas fa-cog'],
    ['label' => 'Clienti', 'url' => route('admin.clienti.index')],
    ['label' => 'Nuova Cliente', 'icon' => 'fas fa-plus']
]" />
```

**Edit:**
```blade
<x-breadcrumb :items="[
    ['label' => 'Gestione', 'icon' => 'fas fa-cog'],
    ['label' => 'Clienti', 'url' => route('admin.clienti.index')],
    ['label' => $cliente->nomeCompleto, 'url' => route('admin.clienti.show', $cliente->id)],
    ['label' => 'Modifica', 'icon' => 'fas fa-edit']
]" />
```

---

### 2. Lezioni

**Index:**
```blade
<x-breadcrumb :items="[
    ['label' => 'Gestione', 'icon' => 'fas fa-cog'],
    ['label' => 'Lezioni']
]" />
```

**Show:**
```blade
<x-breadcrumb :items="[
    ['label' => 'Gestione', 'icon' => 'fas fa-cog'],
    ['label' => 'Lezioni', 'url' => route('admin.lezioni.index')],
    ['label' => $lezione->titolo . ' - ' . $lezione->data->format('d/m/Y')]
]" />
```

---

### 3. Report

**Index:**
```blade
<x-breadcrumb :items="[
    ['label' => 'Analisi', 'icon' => 'fas fa-chart-line'],
    ['label' => 'Report']
]" />
```

**Presenze:**
```blade
<x-breadcrumb :items="[
    ['label' => 'Analisi', 'icon' => 'fas fa-chart-line'],
    ['label' => 'Report', 'url' => route('admin.report.index')],
    ['label' => 'Presenze']
]" />
```

---

### 4. Impostazioni

```blade
<x-breadcrumb :items="[
    ['label' => 'Sistema', 'icon' => 'fas fa-cogs'],
    ['label' => 'Impostazioni']
]" />
```

---

## 🎨 Stile e Personalizzazione

### Colori MA.GIA DONNA

Il component usa automaticamente:
- **Link normali:** Grigio (#6b7280) → Fucsia hover
- **Ultimo elemento:** Fucsia (#E91E8C)
- **Separatori:** Grigio chiaro (#9ca3af)
- **Home icon:** Grigio → Fucsia hover

### Posizionamento

```blade
<div class="p-6">
    <!-- Breadcrumb sempre PRIMA del titolo -->
    <x-breadcrumb :items="..." />

    <!-- Poi header con titolo -->
    <div class="mb-6 flex justify-between items-center">
        <h1>Titolo Pagina</h1>
        <button>Azione</button>
    </div>

    <!-- Poi contenuto -->
</div>
```

---

## 📋 Checklist Implementazione

Per ogni view, aggiungi breadcrumb seguendo questo ordine:

1. ✅ Aggiungi `<x-breadcrumb />` all'inizio di `@section('contenuto')`
2. ✅ Definisci array `items` con percorso completo
3. ✅ Verifica che l'ultimo elemento NON abbia `url`
4. ✅ Aggiungi icone dove appropriato
5. ✅ Testa navigazione click sui link

---

## ✅ View da Aggiornare

### Priorità Alta
- [ ] `admin/clienti/index.blade.php`
- [ ] `admin/clienti/show.blade.php`
- [ ] `admin/clienti/edit.blade.php`
- [ ] `admin/clienti/create.blade.php`

### Priorità Media
- [ ] `admin/lezioni/index.blade.php`
- [ ] `admin/lezioni/show.blade.php`
- [ ] `admin/lezioni/edit.blade.php`
- [ ] `admin/lezioni/create.blade.php`
- [ ] `admin/pagamenti/index.blade.php`
- [ ] `admin/programmi/index.blade.php`
- [ ] `admin/sedi/index.blade.php`
- [ ] `admin/professionisti/index.blade.php`

### Priorità Bassa
- [ ] `admin/report/index.blade.php`
- [ ] `admin/report/presenze.blade.php`
- [ ] `admin/calendario/index.blade.php`
- [ ] `admin/impostazioni/smtp.blade.php`

---

**Creato da:** Claude Code
**Data:** 15 Novembre 2025
