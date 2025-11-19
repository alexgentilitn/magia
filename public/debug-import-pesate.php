<?php
/**
 * Script Debug: Visualizza log import pesate e stato database
 */

// Bootstrap Laravel
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "<h1>🔍 Debug Import Pesate</h1><hr>";

// Test 1: Verifica tabella pesate
echo "<h2>1️⃣ Verifica Tabella Pesate</h2>";
try {
    $hasTable = Schema::hasTable('pesate');
    echo $hasTable ? "✅ Tabella 'pesate' esiste<br>" : "❌ Tabella 'pesate' NON esiste<br>";

    if ($hasTable) {
        $count = \App\Models\Pesata::count();
        echo "Numero pesate nel database: <strong>{$count}</strong><br>";

        // Ultime 5 pesate
        $ultimePesate = \App\Models\Pesata::with('cliente')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        if ($ultimePesate->count() > 0) {
            echo "<h3>Ultime 5 pesate inserite:</h3>";
            echo "<table border='1' cellpadding='8' cellspacing='0' style='border-collapse: collapse; width: 100%;'>";
            echo "<tr><th>ID</th><th>Cliente</th><th>Peso</th><th>Data Rilevazione</th><th>Creato il</th></tr>";
            foreach ($ultimePesate as $p) {
                echo "<tr>";
                echo "<td>{$p->id}</td>";
                echo "<td>{$p->cliente->cognome} {$p->cliente->nome}</td>";
                echo "<td>{$p->peso} kg</td>";
                echo "<td>{$p->data_rilevazione}</td>";
                echo "<td>{$p->created_at}</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "ℹ️ Nessuna pesata trovata nel database<br>";
        }
    }
} catch (\Exception $e) {
    echo "❌ Errore: " . $e->getMessage() . "<br>";
}

echo "<hr>";

// Test 2: Verifica clienti creati
echo "<h2>2️⃣ Clienti Creati Recentemente</h2>";
try {
    $clientiRecenti = \App\Models\Cliente::orderBy('created_at', 'desc')
        ->limit(10)
        ->get();

    if ($clientiRecenti->count() > 0) {
        echo "<table border='1' cellpadding='8' cellspacing='0' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>ID</th><th>Nome</th><th>Codice Cliente</th><th>Pesate</th><th>Creato il</th></tr>";
        foreach ($clientiRecenti as $c) {
            $numPesate = $c->pesate()->count();
            $highlight = $numPesate === 0 ? "background-color: #fff3cd;" : "";
            echo "<tr style='{$highlight}'>";
            echo "<td>{$c->id}</td>";
            echo "<td>{$c->cognome} {$c->nome}</td>";
            echo "<td>{$c->codice_cliente}</td>";
            echo "<td><strong>{$numPesate}</strong></td>";
            echo "<td>{$c->created_at}</td>";
            echo "</tr>";
        }
        echo "</table>";
        echo "<p style='color: orange;'>⚠️ Le righe evidenziate sono clienti SENZA pesate</p>";
    } else {
        echo "ℹ️ Nessun cliente trovato<br>";
    }
} catch (\Exception $e) {
    echo "❌ Errore: " . $e->getMessage() . "<br>";
}

echo "<hr>";

// Test 3: Ultimi log dell'applicazione
echo "<h2>3️⃣ Log Import Pesate (ultimi 100 log)</h2>";
try {
    $logPath = storage_path('logs/laravel.log');

    if (file_exists($logPath)) {
        $logContent = file_get_contents($logPath);
        $logLines = explode("\n", $logContent);
        $logLines = array_reverse($logLines);

        // Filtra solo log relativi a import pesate
        $importLogs = [];
        foreach ($logLines as $line) {
            if (stripos($line, 'import') !== false ||
                stripos($line, 'pesata') !== false ||
                stripos($line, 'cliente') !== false) {
                $importLogs[] = $line;
                if (count($importLogs) >= 100) break;
            }
        }

        if (count($importLogs) > 0) {
            echo "<div style='background: #f5f5f5; padding: 15px; border-radius: 5px; max-height: 500px; overflow-y: auto;'>";
            echo "<pre style='margin: 0; font-size: 11px; white-space: pre-wrap;'>";
            foreach (array_reverse($importLogs) as $log) {
                // Evidenzia errori in rosso
                if (stripos($log, 'error') !== false) {
                    echo "<span style='color: red;'>{$log}</span>\n";
                } elseif (stripos($log, 'warning') !== false) {
                    echo "<span style='color: orange;'>{$log}</span>\n";
                } elseif (stripos($log, 'success') !== false || stripos($log, 'creata con ID') !== false) {
                    echo "<span style='color: green;'>{$log}</span>\n";
                } else {
                    echo "{$log}\n";
                }
            }
            echo "</pre>";
            echo "</div>";
        } else {
            echo "ℹ️ Nessun log relativo a import/pesate trovato<br>";
        }
    } else {
        echo "❌ File log non trovato: {$logPath}<br>";
    }
} catch (\Exception $e) {
    echo "❌ Errore lettura log: " . $e->getMessage() . "<br>";
}

echo "<hr>";
echo "<h2>🧪 Come usare questo script</h2>";
echo "<ol>";
echo "<li>Fai un import pesate</li>";
echo "<li>Ricarica questa pagina</li>";
echo "<li>Verifica:</li>";
echo "<ul>";
echo "<li>Sezione 1: Quante pesate ci sono nel database</li>";
echo "<li>Sezione 2: Se i clienti hanno pesate associate</li>";
echo "<li>Sezione 3: I log dell'import per vedere errori</li>";
echo "</ul>";
echo "</ol>";

echo "<hr>";
echo "<p style='color: red;'><strong>⚠️ ELIMINA QUESTO FILE dopo il debug!</strong></p>";
if (isset($_GET['delete'])) {
    unlink(__FILE__);
    echo "✅ File eliminato!";
    exit;
}
echo "<a href='?delete=1' style='color: red;'>Elimina questo file</a>";
