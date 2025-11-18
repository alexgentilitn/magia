# ⚡ Quick Start - Sistema Permessi Collaboratori

## 🚀 Setup Iniziale (Solo Prima Volta)

### 1. Eseguire Migration
```bash
php artisan migrate --path=database/migrations/2025_11_17_create_utente_permesso_table.php
```

---

## 👨‍💼 Per Admin: Assegnare Permessi

### Via Interfaccia Web
1. Vai su **Dashboard Admin** → **Professionisti**
2. Clicca sul collaboratore
3. Clicca **"Gestisci Permessi"**
4. Seleziona i permessi desiderati
5. Clicca **"Salva Permessi"**

### Via Codice (Seeder/Tinker)
```php
use App\Models\Utente;
use App\Models\Permesso;

// Trova il collaboratore
$marco = Utente::where('email', 'marco@example.com')->first();

// Assegna permesso singolo
$permesso = Permesso::where('slug', 'gestione-presenze')->first();
$marco->assegnaPermessoIndividuale($permesso->id);

// Assegna permessi multipli
$marco->sincronizzaPermessiIndividuali([1, 2, 3, 5, 8]);

// Rimuovi tutti i permessi individuali
$marco->sincronizzaPermessiIndividuali([]);
```

---

## 🔒 Per Developer: Proteggere Routes

### Metodo 1: Middleware nelle Routes
```php
// routes/web.php

// Richiede UN permesso
Route::middleware(['auth', 'permesso:gestione-presenze'])->group(function () {
    Route::get('/presenze', [PresenzeController::class, 'index']);
});

// Richiede ALMENO UNO tra più permessi
Route::middleware(['auth', 'permesso:visualizza-dashboard,visualizza-clienti'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);
});
```

### Metodo 2: Controller
```php
public function index()
{
    // Verifica permesso
    if (!auth()->user()->haPermesso('visualizza-lezioni')) {
        abort(403, 'Non hai il permesso per visualizzare le lezioni');
    }

    // ... logica
}
```

### Metodo 3: Blade Template
```blade
@if(auth()->user()->haPermesso('gestione-compensi'))
    <a href="{{ route('professionista.compensi.index') }}">
        💰 Visualizza Compensi
    </a>
@endif
```

---

## 🧪 Testing Rapido

### Via Tinker
```bash
php artisan tinker
```

```php
// Trova collaboratore
$user = App\Models\Utente::find(5);

// Verifica permesso
$user->haPermesso('gestione-presenze'); // true/false

// Vedi tutti i permessi
$user->tuttiIPermessi()->pluck('nome');

// Assegna permesso
$user->assegnaPermessoIndividuale(10);

// Rimuovi permesso
$user->rimuoviPermessoIndividuale(10);
```

---

## 📋 Checklist Implementazione

- [x] Migration creata
- [x] Model Utente aggiornato
- [x] Middleware registrato
- [x] Controller creato
- [x] Routes aggiunte
- [x] Interfaccia admin pronta
- [x] Documentazione completa

---

## ⚠️ Note Importanti

1. **Super Admin**: Hanno SEMPRE tutti i permessi (nessun controllo)
2. **Priorità**: Permessi individuali > Permessi ruolo
3. **Reset**: Il pulsante "Reset" rimuove SOLO i permessi individuali
4. **Middleware**: Usa `permesso:nome-slug` (non l'ID)

---

## 🎯 Esempi Pratici

### Caso 1: Collaboratore con Accesso Limitato
```php
// Marco può gestire SOLO presenze (niente altro)
$marco->sincronizzaPermessiIndividuali([
    Permesso::where('slug', 'gestione-presenze')->first()->id
]);
```

### Caso 2: Collaboratore con Accesso Esteso
```php
// Sara può gestire presenze + visualizzare compensi + creare lezioni
$permessi = Permesso::whereIn('slug', [
    'gestione-presenze',
    'visualizza-compensi',
    'crea-lezioni'
])->pluck('id')->toArray();

$sara->sincronizzaPermessiIndividuali($permessi);
```

### Caso 3: Usare Solo Permessi del Ruolo
```php
// Luigi usa solo permessi del ruolo "collaboratore"
$luigi->sincronizzaPermessiIndividuali([]); // reset completo
```

---

## 🔧 Troubleshooting

### Permesso non funziona?
1. Verifica lo slug: `Permesso::where('slug', 'nome-permesso')->first()`
2. Controlla se attivo: `$permesso->attivo` deve essere `true`
3. Verifica assegnazione: `$user->permessiIndividuali`

### Migration non parte?
```bash
# Se sei in produzione, forza:
php artisan migrate --path=database/migrations/2025_11_17_create_utente_permesso_table.php --force
```

---

**Fatto! Sistema Permessi Collaboratori pronto all'uso! 🎉**
