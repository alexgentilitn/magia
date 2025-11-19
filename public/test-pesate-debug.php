<?php
/**
 * Script di Debug per Salvataggio Pesate
 * Testa il salvataggio effettivo nel database e mostra gli errori
 *
 * ELIMINA QUESTO FILE dopo aver risolto il problema!
 */

// Carica l'autoloader di Laravel
require __DIR__.'/../vendor/autoload.php';

// Carica l'applicazione Laravel
$app = require_once __DIR__.'/../bootstrap/app.php';

// Avvia Laravel
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debug Salvataggio Pesate - MA.GIA DONNA</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            max-width: 1000px;
            margin: 0 auto;
            padding: 40px;
        }
        h1 {
            color: #764ba2;
            margin-bottom: 10px;
            font-size: 28px;
        }
        .test-section {
            background: #f9f9f9;
            border-left: 4px solid #764ba2;
            padding: 20px;
            margin: 20px 0;
            border-radius: 6px;
        }
        .test-section h3 {
            color: #764ba2;
            margin-bottom: 15px;
        }
        .success {
            background: #e8f5e9;
            border-color: #4CAF50;
            color: #2E7D32;
        }
        .error {
            background: #ffebee;
            border-color: #f44336;
            color: #c62828;
        }
        .warning {
            background: #fff3e0;
            border-color: #ff9800;
            color: #e65100;
        }
        .info {
            background: #e3f2fd;
            border-color: #2196F3;
            color: #1565C0;
        }
        .code {
            background: #2d2d2d;
            color: #f8f8f2;
            padding: 15px;
            border-radius: 6px;
            font-family: 'Courier New', monospace;
            font-size: 13px;
            margin: 10px 0;
            overflow-x: auto;
            white-space: pre-wrap;
        }
        .status-icon {
            font-size: 20px;
            margin-right: 8px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        table th, table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        table th {
            background: #f5f5f5;
            font-weight: 600;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 6px;
            margin: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Debug Salvataggio Pesate</h1>
        <p style="color: #666; margin-bottom: 30px;">Test completo del sistema di salvataggio</p>

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\Pesata;
use App\Models\Cliente;

$errors = [];
$successes = [];
$warnings = [];

// TEST 1: Verifica connessione database
echo '<div class="test-section info">';
echo '<h3><span class="status-icon">🔌</span> Test 1: Connessione Database</h3>';
try {
    DB::connection()->getPdo();
    $dbName = DB::connection()->getDatabaseName();
    echo "✅ Connessione riuscita al database: <strong>{$dbName}</strong>";
    $successes[] = "Database connesso";
} catch (Exception $e) {
    echo "❌ Errore di connessione: " . htmlspecialchars($e->getMessage());
    $errors[] = "Connessione database fallita";
}
echo '</div>';

// TEST 2: Verifica esistenza tabella pesate
echo '<div class="test-section info">';
echo '<h3><span class="status-icon">📋</span> Test 2: Tabella Pesate</h3>';
if (Schema::hasTable('pesate')) {
    $count = DB::table('pesate')->count();
    echo "✅ Tabella 'pesate' esiste<br>";
    echo "📊 Record presenti: <strong>{$count}</strong>";
    $successes[] = "Tabella pesate presente";

    // Mostra ultimi 5 record
    $ultimePesate = DB::table('pesate')
        ->join('clienti', 'pesate.cliente_id', '=', 'clienti.id')
        ->select('pesate.*', 'clienti.nome', 'clienti.cognome')
        ->orderBy('pesate.created_at', 'desc')
        ->limit(5)
        ->get();

    if ($ultimePesate->count() > 0) {
        echo '<table>';
        echo '<tr><th>ID</th><th>Cliente</th><th>Peso</th><th>Data</th><th>Creato</th></tr>';
        foreach ($ultimePesate as $pesata) {
            echo '<tr>';
            echo '<td>' . $pesata->id . '</td>';
            echo '<td>' . htmlspecialchars($pesata->nome . ' ' . $pesata->cognome) . '</td>';
            echo '<td>' . $pesata->peso . ' kg</td>';
            echo '<td>' . $pesata->data_rilevazione . '</td>';
            echo '<td>' . $pesata->created_at . '</td>';
            echo '</tr>';
        }
        echo '</table>';
    }
} else {
    echo "❌ Tabella 'pesate' NON esiste";
    $errors[] = "Tabella mancante";
}
echo '</div>';

// TEST 3: Verifica esistenza clienti
echo '<div class="test-section info">';
echo '<h3><span class="status-icon">👥</span> Test 3: Clienti Disponibili</h3>';
try {
    $clienteTest = Cliente::first();
    if ($clienteTest) {
        echo "✅ Trovato cliente di test: <strong>{$clienteTest->nome} {$clienteTest->cognome}</strong> (ID: {$clienteTest->id})";
        $successes[] = "Cliente trovato per test";
    } else {
        echo "⚠️ Nessun cliente presente nel database";
        $warnings[] = "Nessun cliente per test";
    }
} catch (Exception $e) {
    echo "❌ Errore: " . htmlspecialchars($e->getMessage());
    $errors[] = "Errore lettura clienti";
}
echo '</div>';

// TEST 4: Prova inserimento reale
echo '<div class="test-section info">';
echo '<h3><span class="status-icon">💾</span> Test 4: Inserimento Test nel Database</h3>';

if (isset($clienteTest) && $clienteTest) {
    try {
        $datiTest = [
            'cliente_id' => $clienteTest->id,
            'sede' => 'Test Debug',
            'peso' => 75.50,
            'bmi' => 24.5,
            'data_rilevazione' => date('Y-m-d'),
            'note' => 'Test inserimento da script debug - ' . date('Y-m-d H:i:s')
        ];

        echo "<strong>Dati che sto per inserire:</strong>";
        echo '<div class="code">' . json_encode($datiTest, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . '</div>';

        // Prova inserimento con Eloquent
        $pesataTest = Pesata::create($datiTest);

        if ($pesataTest && $pesataTest->id) {
            echo '<div class="test-section success">';
            echo "✅ <strong>INSERIMENTO RIUSCITO!</strong><br>";
            echo "ID creato: <strong>{$pesataTest->id}</strong><br>";
            echo "Cliente: {$clienteTest->nome} {$clienteTest->cognome}<br>";
            echo "Peso: {$pesataTest->peso} kg<br>";
            echo "Data: {$pesataTest->data_rilevazione}";
            echo '</div>';

            $successes[] = "Inserimento test riuscito";

            // Verifica che sia stato salvato
            $verifica = Pesata::find($pesataTest->id);
            if ($verifica) {
                echo "<p>✅ Verifica: record trovato nel database</p>";
            }

        } else {
            echo '<div class="test-section error">';
            echo "❌ L'inserimento è stato eseguito ma non ha restituito un ID";
            echo '</div>';
            $errors[] = "Inserimento senza ID";
        }

    } catch (Exception $e) {
        echo '<div class="test-section error">';
        echo "❌ <strong>ERRORE DURANTE L'INSERIMENTO:</strong><br>";
        echo "<strong>Messaggio:</strong> " . htmlspecialchars($e->getMessage()) . "<br>";
        echo "<strong>File:</strong> " . $e->getFile() . "<br>";
        echo "<strong>Riga:</strong> " . $e->getLine() . "<br>";
        echo '<div class="code">' . htmlspecialchars($e->getTraceAsString()) . '</div>';
        echo '</div>';
        $errors[] = "Errore inserimento: " . $e->getMessage();
    }
} else {
    echo "⚠️ Impossibile eseguire il test senza un cliente";
}
echo '</div>';

// TEST 5: Verifica configurazione sessioni
echo '<div class="test-section info">';
echo '<h3><span class="status-icon">🔐</span> Test 5: Configurazione Sessioni</h3>';
echo "Driver sessione: <strong>" . config('session.driver') . "</strong><br>";
echo "Lifetime sessione: <strong>" . config('session.lifetime') . "</strong> minuti<br>";

// Verifica che la directory delle sessioni sia scrivibile
$sessionPath = storage_path('framework/sessions');
if (file_exists($sessionPath)) {
    if (is_writable($sessionPath)) {
        echo "✅ Directory sessioni scrivibile: {$sessionPath}";
        $successes[] = "Sessioni configurate correttamente";
    } else {
        echo "❌ Directory sessioni NON scrivibile: {$sessionPath}";
        $errors[] = "Permessi sessioni mancanti";
    }
} else {
    echo "⚠️ Directory sessioni non esiste: {$sessionPath}";
    $warnings[] = "Directory sessioni mancante";
}
echo '</div>';

// TEST 6: Verifica APP_DEBUG
echo '<div class="test-section info">';
echo '<h3><span class="status-icon">🐛</span> Test 6: Modalità Debug</h3>';
$debug = config('app.debug');
if ($debug) {
    echo "✅ APP_DEBUG attivo - Gli errori vengono mostrati";
} else {
    echo '<div class="test-section warning">';
    echo "⚠️ <strong>APP_DEBUG disattivo</strong><br>";
    echo "Gli errori NON vengono mostrati all'utente.<br>";
    echo "Questo potrebbe essere il motivo per cui non vedi nessun feedback!<br><br>";
    echo "<strong>Soluzione temporanea:</strong><br>";
    echo "Modifica il file <code>.env</code> e cambia:<br>";
    echo '<div class="code">APP_DEBUG=true</div>';
    echo "Poi riprova a salvare una pesata.";
    echo '</div>';
    $warnings[] = "Debug mode disattivo";
}
echo '</div>';

// RIEPILOGO
echo '<div class="test-section ' . (count($errors) > 0 ? 'error' : (count($warnings) > 0 ? 'warning' : 'success')) . '">';
echo '<h3>📊 Riepilogo Test</h3>';
echo '<table>';
echo '<tr><th>Categoria</th><th>Risultato</th></tr>';
echo '<tr><td>✅ Successi</td><td><strong>' . count($successes) . '</strong></td></tr>';
echo '<tr><td>⚠️ Warning</td><td><strong>' . count($warnings) . '</strong></td></tr>';
echo '<tr><td>❌ Errori</td><td><strong>' . count($errors) . '</strong></td></tr>';
echo '</table>';

if (count($errors) > 0) {
    echo '<p style="margin-top: 15px;"><strong>Problemi riscontrati:</strong></p>';
    echo '<ul>';
    foreach ($errors as $error) {
        echo '<li>' . htmlspecialchars($error) . '</li>';
    }
    echo '</ul>';
}

if (count($warnings) > 0) {
    echo '<p style="margin-top: 15px;"><strong>Attenzioni:</strong></p>';
    echo '<ul>';
    foreach ($warnings as $warning) {
        echo '<li>' . htmlspecialchars($warning) . '</li>';
    }
    echo '</ul>';
}

if (count($errors) === 0 && count($warnings) === 0) {
    echo '<p style="margin-top: 15px; font-size: 18px;">🎉 <strong>Tutti i test sono passati!</strong></p>';
    echo '<p>Il sistema dovrebbe funzionare correttamente. Se ancora non funziona, il problema potrebbe essere:</p>';
    echo '<ul>';
    echo '<li>JavaScript che blocca il submit del form</li>';
    echo '<li>Problema di routing</li>';
    echo '<li>Cache del browser</li>';
    echo '</ul>';
}

echo '</div>';

?>

        <div style="margin-top: 30px; padding-top: 20px; border-top: 2px solid #f0f0f0; text-align: center;">
            <a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="btn">🔄 Ripeti Test</a>
            <a href="/magia/public/admin/dashboard" class="btn">← Dashboard</a>
        </div>

        <div style="margin-top: 20px; padding: 15px; background: #fff3cd; border-radius: 6px; color: #856404;">
            <strong>⚠️ IMPORTANTE:</strong> Elimina questo file dopo aver risolto il problema:<br>
            <code>/public/test-pesate-debug.php</code>
        </div>
    </div>
</body>
</html>
