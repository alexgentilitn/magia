<?php
/**
 * TEST PRECISIONE - Confronta valori attesi vs salvati
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🎯 Test Precisione Valori</h1>";
echo "<style>
body { font-family: monospace; background: #1e1e1e; color: #d4d4d4; padding: 20px; }
h1 { color: #4ec9b0; }
h2 { color: #569cd6; }
.success { color: #4ec9b0; font-weight: bold; }
.error { color: #f48771; font-weight: bold; }
.box { background: #2d2d2d; padding: 15px; margin: 10px 0; border-left: 3px solid #569cd6; }
table { border-collapse: collapse; margin: 10px 0; width: 100%; }
td, th { padding: 8px; border: 1px solid #3e3e3e; }
th { background: #264f78; }
.match { background: #1a4a1a; }
.diff { background: #4a1a1a; }
</style>";

require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

// Valori attesi da Excel
$valoriAttesi = [
    'peso' => 57.50,
    'bmi' => 19.09,
    'peso_corporeo_senza_grassi' => 41.69,
    'muscolo_scheletrico' => 42.30,
    'grasso_corporeo' => 27.50,
    'grasso_sottocutaneo' => 26.20,
    'grasso_viscerale' => 3,
    'acqua_corporea' => 49.80,
    'massa_muscolare' => 39.16,
    'massa_ossea' => 2.53,
];

echo "<div class='box'>";
echo "<h2>📋 Valori Attesi (da Excel)</h2>";
echo "<table>";
echo "<tr><th>Campo</th><th>Valore Excel</th></tr>";
foreach ($valoriAttesi as $campo => $valore) {
    echo "<tr><td>$campo</td><td>$valore</td></tr>";
}
echo "</table>";
echo "</div>";

// Ultime 3 pesate
echo "<div class='box'>";
echo "<h2>🔍 Ultime 3 Pesate nel Database</h2>";

$pesate = DB::table('pesate')
    ->orderBy('id', 'desc')
    ->limit(3)
    ->get();

if ($pesate->count() == 0) {
    echo "<span class='error'>❌ Nessuna pesata trovata nel database</span>";
} else {
    echo "<p>Clicca su una pesata per confrontarla:</p>";
    echo "<table>";
    echo "<tr><th>ID</th><th>Cliente ID</th><th>Peso</th><th>BMI</th><th>Data</th><th>Azione</th></tr>";

    foreach ($pesate as $p) {
        echo "<tr>";
        echo "<td>{$p->id}</td>";
        echo "<td>{$p->cliente_id}</td>";
        echo "<td>{$p->peso}</td>";
        echo "<td>{$p->bmi}</td>";
        echo "<td>{$p->data_rilevazione}</td>";
        echo "<td><a href='?pesata_id={$p->id}' style='color: #4ec9b0;'>Confronta</a></td>";
        echo "</tr>";
    }
    echo "</table>";
}

echo "</div>";

// Confronto se pesata selezionata
if (isset($_GET['pesata_id'])) {
    $pesataId = (int)$_GET['pesata_id'];
    $pesata = DB::table('pesate')->where('id', $pesataId)->first();

    if ($pesata) {
        echo "<div class='box'>";
        echo "<h2>⚖️ Confronto Pesata ID $pesataId</h2>";

        echo "<table>";
        echo "<tr><th>Campo</th><th>Excel (Atteso)</th><th>Database (Reale)</th><th>Differenza</th><th>Status</th></tr>";

        $differenze = 0;
        $totale = 0;

        foreach ($valoriAttesi as $campo => $valoreAtteso) {
            $totale++;
            $valoreDb = $pesata->$campo ?? null;

            // Calcola differenza
            if ($valoreDb === null) {
                $diff = 'NULL';
                $isMatch = false;
                $differenze++;
            } else {
                $diff = abs((float)$valoreAtteso - (float)$valoreDb);
                // Considera match se differenza < 0.01
                $isMatch = $diff < 0.01;
                if (!$isMatch) $differenze++;
            }

            $rowClass = $isMatch ? 'match' : 'diff';
            $status = $isMatch ? "✅" : "❌";

            echo "<tr class='$rowClass'>";
            echo "<td><strong>$campo</strong></td>";
            echo "<td>$valoreAtteso</td>";
            echo "<td>" . ($valoreDb ?? '<em>NULL</em>') . "</td>";
            echo "<td>" . (is_numeric($diff) ? number_format($diff, 4) : $diff) . "</td>";
            echo "<td>$status</td>";
            echo "</tr>";
        }

        echo "</table>";

        $percentualeCorretti = round((($totale - $differenze) / $totale) * 100, 1);

        echo "<br>";
        echo "<p><strong>Riepilogo:</strong></p>";
        echo "<ul>";
        echo "<li>Campi corretti: <span class='success'>" . ($totale - $differenze) . " / $totale</span></li>";
        echo "<li>Campi diversi: <span class='" . ($differenze > 0 ? 'error' : 'success') . "'>$differenze</span></li>";
        echo "<li>Precisione: <span class='" . ($percentualeCorretti >= 90 ? 'success' : 'error') . "'>$percentualeCorretti%</span></li>";
        echo "</ul>";

        if ($differenze > 0) {
            echo "<br>";
            echo "<p><span class='error'>⚠️ PROBLEMA RILEVATO</span></p>";
            echo "<ul>";
            echo "<li>Cache OPcache pulita? → <a href='clear-all-cache.php' style='color: #4ec9b0;'>Pulisci ora</a></li>";
            echo "<li>Import fatto dopo il deploy del fix?</li>";
            echo "<li>Mapping colonne corretto durante import?</li>";
            echo "</ul>";
        } else {
            echo "<br>";
            echo "<p><span class='success'>✅ PERFETTO! Tutti i valori corrispondono</span></p>";
        }

        echo "</div>";
    }
}

echo "<br><br>";
echo "<a href='test-precision.php' style='color: #569cd6;'>🔄 Ricarica</a> | ";
echo "<a href='clear-all-cache.php' style='color: #4ec9b0;'>🧹 Pulisci Cache</a> | ";
echo "<a href='test-insert-pesata.php' style='color: #dcdcaa;'>🔍 Test Inserimento</a>";
?>
