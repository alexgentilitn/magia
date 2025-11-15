# 📊 Guida DataTables.js - MA.GIA DONNA

**Data:** 15 Novembre 2025
**Versione:** DataTables 1.13.8 + Responsive 2.5.0

---

## 🎯 Cosa è DataTables

DataTables trasforma le tabelle HTML statiche in tabelle interattive con:
- ✅ **Ordinamento** colonne (click su intestazione)
- ✅ **Ricerca/filtro** globale
- ✅ **Paginazione** automatica
- ✅ **Responsive** su mobile
- ✅ **Localizzazione** italiana

---

## ⚡ Quick Start

### Metodo 1: Auto-inizializzazione (Più Semplice)

Aggiungi semplicemente la classe `datatable` alla tua tabella:

```blade
<table class="min-w-full datatable">
    <thead>
        <tr>
            <th>Nome</th>
            <th>Email</th>
            <th>Azioni</th>
        </tr>
    </thead>
    <tbody>
        @foreach($items as $item)
        <tr>
            <td>{{ $item->nome }}</td>
            <td>{{ $item->email }}</td>
            <td>...</td>
        </tr>
        @endforeach
    </tbody>
</table>
```

**NOTA:** Rimuovi la paginazione Laravel (`{{ $items->links() }}`) e usa `->get()` invece di `->paginate()` nel controller.

---

### Metodo 2: Inizializzazione Manuale

Per controllo completo, usa lo script:

```blade
@push('scripts')
<script>
$(document).ready(function() {
    $('#mia-tabella').DataTable({
        language: dataTablesItaliano, // Configurazione italiana globale
        pageLength: 50,
        order: [[0, 'desc']], // Ordina per prima colonna discendente
        // ... altre opzioni
    });
});
</script>
@endpush
```

---

## 🎨 Personalizzazione

### Ordine Iniziale

```javascript
order: [[2, 'desc']] // Ordina per terza colonna discendente
```

### Numero Elementi per Pagina

```javascript
pageLength: 100,
lengthMenu: [[25, 50, 100, -1], [25, 50, 100, "Tutti"]]
```

### Disabilita Ordinamento su Colonne

```javascript
columnDefs: [
    { orderable: false, targets: [3, 4] } // Disabilita su colonna 4 e 5 (azioni)
]
```

### Ricerca Personalizzata

```javascript
search: {
    search: "Attivo" // Pre-compila ricerca
}
```

---

## 📝 Esempio Completo: Tabella Clienti

### Controller

```php
public function index(Request $request)
{
    // USA ->get() invece di ->paginate() per DataTables
    $clienti = Cliente::with('programma')
        ->orderBy('created_at', 'desc')
        ->get(); // NON ->paginate(20)

    return view('admin.clienti.index', compact('clienti'));
}
```

### View

```blade
<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full datatable" id="clienti-table">
        <thead class="bg-gray-50">
            <tr>
                <th>Cliente</th>
                <th>Email</th>
                <th>Telefono</th>
                <th>Programma</th>
                <th>Stato</th>
                <th class="no-sort">Azioni</th>
            </tr>
        </thead>
        <tbody>
            @foreach($clienti as $cliente)
            <tr>
                <td>{{ $cliente->nomeCompleto }}</td>
                <td>{{ $cliente->email }}</td>
                <td>{{ $cliente->telefono_mobile }}</td>
                <td>{{ $cliente->programma_attuale ?? 'Nessuno' }}</td>
                <td>
                    <span class="badge">{{ $cliente->stato_cliente }}</span>
                </td>
                <td>
                    <a href="{{ route('admin.clienti.edit', $cliente->id) }}">Modifica</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    $('#clienti-table').DataTable({
        language: dataTablesItaliano,
        responsive: true,
        pageLength: 25,
        columnDefs: [
            { orderable: false, targets: -1 } // Disabilita ordinamento su ultima colonna (azioni)
        ],
        order: [[4, 'asc']] // Ordina per stato (colonna 5)
    });
});
</script>
@endpush
```

**IMPORTANTE:** Rimuovi `{{ $clienti->links() }}` dalla view!

---

## 🚨 Troubleshooting

### Problema: "Paginazione duplicata"
**Soluzione:** Rimuovi `{{ $items->links() }}` dalla view.

### Problema: "Non vedo tutti i record"
**Soluzione:** Usa `->get()` invece di `->paginate()` nel controller.

### Problema: "Tabella non ordinabile"
**Soluzione:** Verifica che la tabella abbia `<thead>` e `<tbody>` separati.

### Problema: "Azioni eliminate dall'ordinamento"
**Soluzione:** Aggiungi `columnDefs: [{ orderable: false, targets: -1 }]`.

---

## 🎨 Stili MA.GIA DONNA

Gli stili DataTables sono già personalizzati con i colori brand:
- **Paginazione attiva:** Gradient viola-fucsia
- **Hover:** Rosa chiaro (#fce7f3)
- **Stripe:** Rosa pallido (#fdf2f8)
- **Bordo intestazione:** Fucsia (#E91E8C)

---

## 📚 Riferimenti

- **Documentazione:** https://datatables.net/
- **Esempi:** https://datatables.net/examples/
- **API:** https://datatables.net/reference/api/

---

## ✅ View da Aggiornare

Applica DataTables a queste view principali:

- [ ] `admin/clienti/index.blade.php`
- [ ] `admin/lezioni/index.blade.php`
- [ ] `admin/pagamenti/index.blade.php`
- [ ] `admin/professionisti/index.blade.php`
- [ ] `admin/programmi/index.blade.php`
- [ ] `admin/sedi/index.blade.php`

---

**Creato da:** Claude Code
**Data:** 15 Novembre 2025
