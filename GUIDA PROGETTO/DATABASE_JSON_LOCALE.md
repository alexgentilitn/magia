# 📦 Database JSON Locale - Guida Completa

## 🎯 Soluzione Implementata

Abbiamo implementato un **database file-based puro PHP** che funziona **senza SQLite** e senza dipendenze esterne complesse.

### Libreria Utilizzata
- **Nome:** jajo/jsondb
- **Repository:** https://github.com/donjajo/php-jsondb
- **Versione:** Installata via Composer
- **Requisiti:** Solo PHP (no SQLite, no estensioni)

---

## 📂 Struttura Files

```
/database/jsondb/           # Directory database JSON
  └── clienti.json          # Tabella clienti (esempio)

/app/Helpers/
  └── JsonDatabase.php      # Helper per gestire JsonDB

/app/Models/
  └── ClienteJson.php       # Model per i clienti

/test-jsondb.php            # Test base JsonDB
/test-json-helper.php       # Test helper Laravel
/test-cliente-json.php      # Test Model completo
```

---

## 🚀 Come Usare

### 1. Helper JsonDatabase (Basso Livello)

```php
use App\Helpers\JsonDatabase;

// SELECT ALL
$clienti = JsonDatabase::all('clienti');

// WHERE
$risultati = JsonDatabase::where('clienti', ['nome' => 'Maria']);

// FIND (singolo record)
$cliente = JsonDatabase::find('clienti', ['id' => 1]);

// INSERT
JsonDatabase::insert('clienti', [
    'id' => 1,
    'nome' => 'Maria',
    'cognome' => 'Rossi',
    'email' => 'maria@example.com'
]);

// UPDATE
JsonDatabase::update('clienti',
    ['id' => 1],                    // condizione
    ['telefono' => '333-9999999']   // nuovi dati
);

// DELETE
JsonDatabase::delete('clienti', ['id' => 1]);

// COUNT
$totale = JsonDatabase::count('clienti');

// Verifica esistenza tabella
if (JsonDatabase::tableExists('clienti')) {
    // ...
}

// Crea nuova tabella
JsonDatabase::createTable('nuova_tabella');
```

### 2. Model ClienteJson (Alto Livello) ⭐ CONSIGLIATO

```php
use App\Models\ClienteJson;

// Inizializza tabella (prima volta)
ClienteJson::initTable();

// CREATE - ID automatico
ClienteJson::create([
    'nome' => 'Maria',
    'cognome' => 'Rossi',
    'email' => 'maria.rossi@example.com',
    'telefono' => '333-1234567',
    'data_nascita' => '1990-05-15'
]);
// Aggiunge automaticamente: id, created_at, updated_at

// ALL
$clienti = ClienteJson::all();

// FIND by ID
$cliente = ClienteJson::find(1);

// FIND by Email
$cliente = ClienteJson::findByEmail('maria@example.com');

// WHERE
$rossi = ClienteJson::where(['cognome' => 'Rossi']);

// SEARCH (cerca in nome, cognome, email)
$risultati = ClienteJson::search('maria');

// UPDATE
ClienteJson::update(1, [
    'telefono' => '333-9999999'
]);
// Aggiorna automaticamente: updated_at

// DELETE
ClienteJson::delete(1);

// COUNT
$totale = ClienteJson::count();

// EXISTS
if (ClienteJson::exists(1)) {
    // Cliente esiste
}
```

---

## 📋 Metodi Disponibili

### Helper JsonDatabase

| Metodo | Descrizione |
|--------|-------------|
| `all($table)` | Tutti i record |
| `where($table, $conditions)` | Filtra con condizioni |
| `find($table, $conditions)` | Singolo record |
| `insert($table, $data)` | Inserisce record |
| `update($table, $where, $data)` | Aggiorna record |
| `delete($table, $conditions)` | Elimina record |
| `count($table)` | Conta record |
| `tableExists($table)` | Verifica esistenza |
| `createTable($table)` | Crea tabella vuota |

### Model ClienteJson

| Metodo | Descrizione |
|--------|-------------|
| `all()` | Tutti i clienti |
| `where($conditions)` | Filtra clienti |
| `find($id)` | Cliente per ID |
| `findByEmail($email)` | Cliente per email |
| `create($data)` | Crea cliente (ID auto) |
| `update($id, $data)` | Aggiorna cliente |
| `delete($id)` | Elimina cliente |
| `count()` | Conta clienti |
| `exists($id)` | Verifica esistenza |
| `search($query)` | Cerca in nome/cognome/email |
| `initTable()` | Inizializza tabella |

---

## 🧪 Test

### Test Base JsonDB
```bash
php test-jsondb.php
```

### Test Helper Laravel
```bash
php test-json-helper.php
```

### Test Model Completo
```bash
php test-cliente-json.php
```

Tutti i test devono mostrare: **🎉 TUTTI I TEST SUPERATI!**

---

## 📁 Formato File JSON

Esempio di `database/jsondb/clienti.json`:

```json
[
    {
        "id": 1,
        "nome": "Maria",
        "cognome": "Rossi",
        "email": "maria.rossi@example.com",
        "telefono": "333-1234567",
        "data_nascita": "1990-05-15",
        "created_at": "2025-11-14 21:47:29",
        "updated_at": "2025-11-14 21:47:29"
    },
    {
        "id": 2,
        "nome": "Giulia",
        "cognome": "Bianchi",
        "email": "giulia.bianchi@example.com",
        "telefono": "333-7654321",
        "data_nascita": "1985-08-20",
        "created_at": "2025-11-14 21:47:29",
        "updated_at": "2025-11-14 21:47:29"
    }
]
```

---

## ✅ Vantaggi

1. **Puro PHP** - Nessuna dipendenza da SQLite o estensioni
2. **File-based** - Database in semplici file JSON
3. **Facile da leggere** - JSON human-readable
4. **Backup semplice** - Copia i file .json
5. **Git-friendly** - Puoi versionare i dati
6. **Zero configurazione** - Non servono server database
7. **Portable** - Funziona ovunque giri PHP
8. **Leggero** - Nessun overhead di server DB

---

## ⚠️ Limitazioni

1. **Non per grandi volumi** - Ottimale fino a ~1000 record per tabella
2. **No relazioni** - Non è un database relazionale
3. **No transazioni** - Nessun supporto ACID completo
4. **Performance** - Più lento di MySQL per grandi dataset
5. **Concorrenza** - Attenzione a scritture simultanee

---

## 🔧 Creazione Nuove Tabelle

### Opzione 1: Via Code

```php
use App\Helpers\JsonDatabase;

JsonDatabase::createTable('ordini');
JsonDatabase::insert('ordini', [
    'id' => 1,
    'cliente_id' => 1,
    'totale' => 150.00,
    'data' => date('Y-m-d')
]);
```

### Opzione 2: Manuale

Crea file: `/database/jsondb/ordini.json`

```json
[]
```

---

## 📊 Best Practices

### 1. Usa sempre ID numerici incrementali
```php
// ✅ BUONO
ClienteJson::create(['nome' => 'Maria', ...]); // ID auto

// ❌ EVITA
JsonDatabase::insert('clienti', ['id' => 'abc123', ...]); // ID stringa
```

### 2. Mantieni timestamps aggiornati
```php
// ✅ BUONO - usa Model (fa tutto automaticamente)
ClienteJson::create([...]);
ClienteJson::update(1, [...]);

// ⚠️ ATTENZIONE - se usi Helper, aggiungi manualmente
JsonDatabase::insert('clienti', [
    'created_at' => date('Y-m-d H:i:s'),
    'updated_at' => date('Y-m-d H:i:s'),
    ...
]);
```

### 3. Inizializza tabelle all'inizio
```php
// In un seeder o migration
ClienteJson::initTable();
```

### 4. Backup regolari
```bash
# Copia periodica
cp -r database/jsondb database/jsondb.backup
```

---

## 🛠️ Troubleshooting

### Errore: "File does not exist"
```php
// Soluzione: inizializza la tabella
ClienteJson::initTable();
```

### Errore: "Permission denied"
```bash
# Soluzione: imposta permessi
chmod 755 database/jsondb
chmod 644 database/jsondb/*.json
```

### Tabella vuota dopo insert
```php
// Verifica che il dato sia stato inserito
$count = ClienteJson::count();
echo "Totale: $count";

// Controlla il file JSON direttamente
cat database/jsondb/clienti.json
```

---

## 📚 Riferimenti

- **Libreria jajo/jsondb:** https://github.com/donjajo/php-jsondb
- **Documentazione:** Vedi README della libreria
- **File creati:**
  - `/app/Helpers/JsonDatabase.php`
  - `/app/Models/ClienteJson.php`
  - Tutti i test: `test-*.php`

---

## 🎉 Risultati Test

Tutti i test passati con successo:

- ✅ Test JsonDB base (test-jsondb.php)
- ✅ Test Helper Laravel (test-json-helper.php)
- ✅ Test Model CRUD completo (test-cliente-json.php)

**Database JSON file-based ATTIVO e funzionante!** 🚀
