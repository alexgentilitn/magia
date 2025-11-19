<?php
/**
 * TEST INSERIMENTO PESATA
 * Diagnostica completa per capire perché le pesate non vengono salvate
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🔍 TEST INSERIMENTO PESATA - Diagnostica Completa</h1>";
echo "<style>
body { font-family: monospace; background: #1e1e1e; color: #d4d4d4; padding: 20px; }
h1 { color: #4ec9b0; }
h2 { color: #569cd6; margin-top: 30px; }
.success { color: #4ec9b0; font-weight: bold; }
.error { color: #f48771; font-weight: bold; }
.warning { color: #dcdcaa; font-weight: bold; }
.box { background: #2d2d2d; padding: 15px; margin: 10px 0; border-left: 3px solid #569cd6; }
pre { background: #1e1e1e; padding: 10px; overflow-x: auto; border: 1px solid #3e3e3e; }
table { border-collapse: collapse; margin: 10px 0; }
td, th { padding: 8px; border: 1px solid #3e3e3e; text-align: left; }
th { background: #264f78; }
</style>";

// Carica Laravel
require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

use App\Models\Cliente;
use App\Models\Pesata;
use Illuminate\Support\Facades\DB;

echo "<div class='box'>";
echo "<h2>📋 Step 1: Verifica Connessione Database</h2>";

try {
    DB::connection()->getPdo();
    echo "<span class='success'>✅ Connessione database OK</span><br>";
    echo "Database: <strong>" . DB::connection()->getDatabaseName() . "</strong><br>";
    echo "Driver: " . DB::connection()->getDriverName() . "<br>";
} catch (\Exception $e) {
    echo "<span class='error'>❌ ERRORE Connessione: " . $e->getMessage() . "</span>";
    exit;
}
echo "</div>";

// Step 2: Verifica tabelle esistono
echo "<div class='box'>";
echo "<h2>🗄️ Step 2: Verifica Tabelle</h2>";

$tables = ['clienti', 'pesate'];
foreach ($tables as $table) {
    try {
        $exists = DB::select("SHOW TABLES LIKE '$table'");
        if ($exists) {
            $count = DB::table($table)->count();
            echo "<span class='success'>✅ Tabella '$table' esiste</span> - Record: $count<br>";
        } else {
            echo "<span class='error'>❌ Tabella '$table' NON esiste!</span><br>";
        }
    } catch (\Exception $e) {
        echo "<span class='error'>❌ Errore verifica tabella $table: " . $e->getMessage() . "</span><br>";
    }
}
echo "</div>";

// Step 3: Verifica struttura tabella pesate
echo "<div class='box'>";
echo "<h2>📐 Step 3: Struttura Tabella Pesate</h2>";

try {
    $columns = DB::select("DESCRIBE pesate");
    echo "<table>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    foreach ($columns as $col) {
        echo "<tr>";
        echo "<td>{$col->Field}</td>";
        echo "<td>{$col->Type}</td>";
        echo "<td>{$col->Null}</td>";
        echo "<td>{$col->Key}</td>";
        echo "<td>{$col->Default}</td>";
        echo "</tr>";
    }
    echo "</table>";
} catch (\Exception $e) {
    echo "<span class='error'>❌ Errore: " . $e->getMessage() . "</span>";
}
echo "</div>";

// Step 4: Verifica foreign key constraints
echo "<div class='box'>";
echo "<h2>🔗 Step 4: Foreign Key Constraints</h2>";

try {
    $fks = DB::select("
        SELECT
            CONSTRAINT_NAME,
            COLUMN_NAME,
            REFERENCED_TABLE_NAME,
            REFERENCED_COLUMN_NAME
        FROM information_schema.KEY_COLUMN_USAGE
        WHERE TABLE_SCHEMA = DATABASE()
        AND TABLE_NAME = 'pesate'
        AND REFERENCED_TABLE_NAME IS NOT NULL
    ");

    if (count($fks) > 0) {
        echo "<table>";
        echo "<tr><th>Constraint</th><th>Column</th><th>References</th></tr>";
        foreach ($fks as $fk) {
            echo "<tr>";
            echo "<td>{$fk->CONSTRAINT_NAME}</td>";
            echo "<td>{$fk->COLUMN_NAME}</td>";
            echo "<td>{$fk->REFERENCED_TABLE_NAME}.{$fk->REFERENCED_COLUMN_NAME}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<span class='warning'>⚠️ Nessuna foreign key trovata</span><br>";
    }
} catch (\Exception $e) {
    echo "<span class='error'>❌ Errore: " . $e->getMessage() . "</span>";
}
echo "</div>";

// Step 5: Trova o crea cliente di test
echo "<div class='box'>";
echo "<h2>👤 Step 5: Cliente di Test</h2>";

$cliente = null;

// Cerca primo cliente esistente
$cliente = Cliente::first();

if ($cliente) {
    echo "<span class='success'>✅ Cliente esistente trovato</span><br>";
    echo "ID: <strong>{$cliente->id}</strong><br>";
    echo "Nome: <strong>{$cliente->nome} {$cliente->cognome}</strong><br>";
    echo "Stato: {$cliente->stato_cliente}<br>";
    echo "Tipo: {$cliente->tipo_cliente}<br>";
} else {
    // Crea cliente di test
    echo "<span class='warning'>⚠️ Nessun cliente trovato, creo cliente di test...</span><br>";

    try {
        $cliente = Cliente::create([
            'nome' => 'Test',
            'cognome' => 'Diagnostica',
            'codice_fiscale' => 'TSTDGN00A01H501Z',
            'stato_cliente' => 'attivo',
            'tipo_cliente' => 'effettiva',
            'data_iscrizione' => now(),
            'utente_id' => 1,
        ]);

        echo "<span class='success'>✅ Cliente di test creato con ID: {$cliente->id}</span><br>";
    } catch (\Exception $e) {
        echo "<span class='error'>❌ ERRORE creazione cliente: " . $e->getMessage() . "</span><br>";
        echo "<pre>" . $e->getTraceAsString() . "</pre>";
    }
}
echo "</div>";

// Step 6: TEST INSERIMENTO con SQL DIRETTO
echo "<div class='box'>";
echo "<h2>💉 Step 6: Test Inserimento SQL Diretto</h2>";

if ($cliente) {
    try {
        $sql = "INSERT INTO pesate (
            cliente_id,
            sede,
            peso,
            data_rilevazione,
            created_at,
            updated_at
        ) VALUES (?, ?, ?, ?, NOW(), NOW())";

        $params = [
            $cliente->id,
            'Sede Test',
            75.5,
            date('Y-m-d')
        ];

        echo "<strong>Query SQL:</strong><br>";
        echo "<pre>$sql</pre>";
        echo "<strong>Parametri:</strong><br>";
        echo "<pre>" . print_r($params, true) . "</pre>";

        $result = DB::insert($sql, $params);

        if ($result) {
            $lastId = DB::getPdo()->lastInsertId();
            echo "<span class='success'>✅✅✅ INSERIMENTO SQL RIUSCITO!</span><br>";
            echo "ID pesata creata: <strong>$lastId</strong><br>";

            // Verifica record inserito
            $pesata = DB::table('pesate')->where('id', $lastId)->first();
            echo "<br><strong>Dati pesata inserita:</strong><br>";
            echo "<pre>" . print_r($pesata, true) . "</pre>";
        } else {
            echo "<span class='error'>❌ Inserimento fallito (result = false)</span><br>";
        }

    } catch (\Exception $e) {
        echo "<span class='error'>❌❌❌ ERRORE SQL DIRETTO:</span><br>";
        echo "<strong>Messaggio:</strong> " . $e->getMessage() . "<br>";
        echo "<strong>Codice:</strong> " . $e->getCode() . "<br>";

        if ($e instanceof \Illuminate\Database\QueryException) {
            echo "<strong>SQL State:</strong> " . $e->errorInfo[0] . "<br>";
            echo "<strong>Error Code:</strong> " . $e->errorInfo[1] . "<br>";
            echo "<strong>Error Message:</strong> " . $e->errorInfo[2] . "<br>";
        }

        echo "<br><strong>Stack Trace:</strong><br>";
        echo "<pre>" . $e->getTraceAsString() . "</pre>";
    }
} else {
    echo "<span class='error'>❌ Impossibile testare: cliente non disponibile</span>";
}
echo "</div>";

// Step 7: TEST INSERIMENTO con Eloquent Model
echo "<div class='box'>";
echo "<h2>🎯 Step 7: Test Inserimento con Eloquent (Model Pesata)</h2>";

if ($cliente) {
    try {
        $pesataData = [
            'cliente_id' => $cliente->id,
            'sede' => 'Test Eloquent',
            'peso' => 72.3,
            'bmi' => 24.5,
            'grasso_corporeo' => 25.0,
            'acqua_corporea' => 55.0,
            'massa_muscolare' => 30.0,
            'data_rilevazione' => date('Y-m-d'),
        ];

        echo "<strong>Dati da inserire:</strong><br>";
        echo "<pre>" . print_r($pesataData, true) . "</pre>";

        $pesata = Pesata::create($pesataData);

        echo "<span class='success'>✅✅✅ INSERIMENTO ELOQUENT RIUSCITO!</span><br>";
        echo "ID pesata creata: <strong>{$pesata->id}</strong><br>";
        echo "<br><strong>Oggetto Pesata:</strong><br>";
        echo "<pre>" . print_r($pesata->toArray(), true) . "</pre>";

    } catch (\Exception $e) {
        echo "<span class='error'>❌❌❌ ERRORE ELOQUENT:</span><br>";
        echo "<strong>Messaggio:</strong> " . $e->getMessage() . "<br>";
        echo "<strong>Codice:</strong> " . $e->getCode() . "<br>";

        if ($e instanceof \Illuminate\Database\QueryException) {
            echo "<strong>SQL State:</strong> " . $e->errorInfo[0] . "<br>";
            echo "<strong>Error Code:</strong> " . $e->errorInfo[1] . "<br>";
            echo "<strong>Error Message:</strong> " . $e->errorInfo[2] . "<br>";
            echo "<br><strong>Query SQL:</strong><br>";
            echo "<pre>" . $e->getSql() . "</pre>";
        }

        echo "<br><strong>Stack Trace:</strong><br>";
        echo "<pre>" . $e->getTraceAsString() . "</pre>";
    }
} else {
    echo "<span class='error'>❌ Impossibile testare: cliente non disponibile</span>";
}
echo "</div>";

// Step 8: Verifica totale pesate
echo "<div class='box'>";
echo "<h2>📊 Step 8: Riepilogo Finale</h2>";

try {
    $totalPesate = DB::table('pesate')->count();
    $totalClienti = DB::table('clienti')->count();

    echo "<strong>Totale Clienti:</strong> $totalClienti<br>";
    echo "<strong>Totale Pesate:</strong> <span class='" . ($totalPesate > 0 ? 'success' : 'error') . "'>$totalPesate</span><br>";

    if ($totalPesate > 0) {
        echo "<br><strong>Ultime 5 pesate inserite:</strong><br>";
        $ultimi = DB::table('pesate')
            ->join('clienti', 'pesate.cliente_id', '=', 'clienti.id')
            ->select('pesate.*', 'clienti.nome', 'clienti.cognome')
            ->orderBy('pesate.id', 'desc')
            ->limit(5)
            ->get();

        echo "<table>";
        echo "<tr><th>ID</th><th>Cliente</th><th>Peso</th><th>Data</th><th>Creato</th></tr>";
        foreach ($ultimi as $p) {
            echo "<tr>";
            echo "<td>{$p->id}</td>";
            echo "<td>{$p->nome} {$p->cognome}</td>";
            echo "<td>{$p->peso} kg</td>";
            echo "<td>{$p->data_rilevazione}</td>";
            echo "<td>{$p->created_at}</td>";
            echo "</tr>";
        }
        echo "</table>";
    }

} catch (\Exception $e) {
    echo "<span class='error'>❌ Errore: " . $e->getMessage() . "</span>";
}

echo "</div>";

// Step 9: Verifica Model Pesata
echo "<div class='box'>";
echo "<h2>🔍 Step 9: Verifica Model Pesata</h2>";

echo "<strong>Fillable fields:</strong><br>";
$pesataModel = new Pesata();
echo "<pre>" . print_r($pesataModel->getFillable(), true) . "</pre>";

echo "<strong>Table name:</strong> " . $pesataModel->getTable() . "<br>";
echo "<strong>Connection:</strong> " . $pesataModel->getConnectionName() . "<br>";

echo "</div>";

echo "<div class='box' style='border-left-color: #4ec9b0;'>";
echo "<h2>✅ Test Completato</h2>";
echo "<p>Controlla i risultati sopra per identificare il problema.</p>";
echo "<p><strong>Prossimi passi:</strong></p>";
echo "<ul>";
echo "<li>Se SQL diretto funziona ma Eloquent no → problema nel Model</li>";
echo "<li>Se entrambi falliscono → problema constraint/permissions database</li>";
echo "<li>Se vedi errori foreign key → problema cliente_id</li>";
echo "</ul>";
echo "</div>";
?>
