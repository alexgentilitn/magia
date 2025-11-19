<?php
/**
 * TEST FLUSSO IMPORT - Simula il processo di import
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🔄 TEST FLUSSO IMPORT PESATE</h1>";
echo "<style>
body { font-family: monospace; background: #1e1e1e; color: #d4d4d4; padding: 20px; }
h1 { color: #4ec9b0; }
h2 { color: #569cd6; margin-top: 20px; }
.success { color: #4ec9b0; font-weight: bold; }
.error { color: #f48771; font-weight: bold; }
.warning { color: #dcdcaa; font-weight: bold; }
.box { background: #2d2d2d; padding: 15px; margin: 10px 0; border-left: 3px solid #569cd6; }
pre { background: #1e1e1e; padding: 10px; overflow-x: auto; border: 1px solid #3e3e3e; }
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
echo "<h2>🧪 Test 1: Creazione Cliente come fa l'Import</h2>";

// Simula dati da Excel
$nomeExcel = "Amato";
$cognomeExcel = "Nella";
$codiceFiscaleExcel = null; // Spesso manca nel file Excel

echo "<strong>Dati simulati da Excel:</strong><br>";
echo "Nome: $nomeExcel<br>";
echo "Cognome: $cognomeExcel<br>";
echo "Codice Fiscale: " . ($codiceFiscaleExcel ?? '<em>NULL</em>') . "<br>";
echo "<br>";

// Tenta di creare cliente come fa findOrCreateCliente
try {
    // Prima cerca se esiste
    $clienteEsistente = Cliente::where(function($q) use ($nomeExcel, $cognomeExcel) {
            $q->whereRaw('LOWER(TRIM(nome)) = ?', [strtolower(trim($nomeExcel))])
              ->whereRaw('LOWER(TRIM(cognome)) = ?', [strtolower(trim($cognomeExcel))]);
        })
        ->first();

    if ($clienteEsistente) {
        echo "<span class='success'>✅ Cliente già esiste (ID: {$clienteEsistente->id})</span><br>";
        $cliente = $clienteEsistente;
    } else {
        echo "<span class='warning'>⚠️ Cliente non trovato, tento creazione...</span><br>";

        $datiCliente = [
            'nome' => ucwords(strtolower(trim($nomeExcel))),
            'cognome' => ucwords(strtolower(trim($cognomeExcel))),
            'codice_fiscale' => $codiceFiscaleExcel ? strtoupper(trim($codiceFiscaleExcel)) : null,
            'stato_cliente' => 'attivo',
            'tipo_cliente' => 'effettiva',
            'data_iscrizione' => now(),
            'utente_id' => 1, // auth()->id() in produzione
        ];

        echo "<strong>Dati per creazione:</strong><br>";
        echo "<pre>" . print_r($datiCliente, true) . "</pre>";

        $cliente = Cliente::create($datiCliente);

        echo "<span class='success'>✅✅✅ CLIENTE CREATO CON SUCCESSO!</span><br>";
        echo "ID: <strong>{$cliente->id}</strong><br>";
        echo "Nome completo: {$cliente->nome} {$cliente->cognome}<br>";
        echo "Stato: {$cliente->stato_cliente}<br>";
        echo "Tipo: {$cliente->tipo_cliente}<br>";
    }

} catch (\Exception $e) {
    echo "<span class='error'>❌❌❌ ERRORE CREAZIONE CLIENTE:</span><br>";
    echo "<strong>Messaggio:</strong> " . $e->getMessage() . "<br>";

    if ($e instanceof \Illuminate\Database\QueryException) {
        echo "<strong>SQL State:</strong> " . $e->errorInfo[0] . "<br>";
        echo "<strong>Error Code:</strong> " . $e->errorInfo[1] . "<br>";
        echo "<strong>Error Message:</strong> " . $e->errorInfo[2] . "<br>";
    }

    echo "<pre>" . $e->getTraceAsString() . "</pre>";
    $cliente = null;
}

echo "</div>";

// Test 2: Inserimento Pesata
if ($cliente) {
    echo "<div class='box'>";
    echo "<h2>🎯 Test 2: Inserimento Pesata</h2>";

    try {
        $pesataData = [
            'cliente_id' => $cliente->id,
            'sede' => 'Test Import Flow',
            'peso' => 68.5,
            'bmi' => 23.4,
            'grasso_corporeo' => 28.0,
            'acqua_corporea' => 52.0,
            'data_rilevazione' => date('Y-m-d'),
        ];

        echo "<strong>Dati pesata:</strong><br>";
        echo "<pre>" . print_r($pesataData, true) . "</pre>";

        $pesata = Pesata::create($pesataData);

        echo "<span class='success'>✅✅✅ PESATA CREATA CON SUCCESSO!</span><br>";
        echo "ID: <strong>{$pesata->id}</strong><br>";
        echo "Cliente: {$cliente->nome} {$cliente->cognome}<br>";
        echo "Peso: {$pesata->peso} kg<br>";
        echo "Data: {$pesata->data_rilevazione}<br>";

    } catch (\Exception $e) {
        echo "<span class='error'>❌❌❌ ERRORE CREAZIONE PESATA:</span><br>";
        echo "<strong>Messaggio:</strong> " . $e->getMessage() . "<br>";
        echo "<pre>" . $e->getTraceAsString() . "</pre>";
    }

    echo "</div>";
}

// Test 3: Verifica finale database
echo "<div class='box'>";
echo "<h2>📊 Test 3: Stato Database</h2>";

$totalClienti = Cliente::count();
$totalPesate = Pesata::count();

echo "<strong>Totale Clienti:</strong> $totalClienti<br>";
echo "<strong>Totale Pesate:</strong> $totalPesate<br>";

if ($totalPesate > 0) {
    echo "<br><strong>Ultime 3 pesate:</strong><br>";
    $ultime = Pesata::with('cliente')
        ->orderBy('id', 'desc')
        ->limit(3)
        ->get();

    echo "<table style='border-collapse: collapse; margin: 10px 0;'>";
    echo "<tr style='background: #264f78;'><th style='padding: 8px; border: 1px solid #3e3e3e;'>ID</th><th style='padding: 8px; border: 1px solid #3e3e3e;'>Cliente</th><th style='padding: 8px; border: 1px solid #3e3e3e;'>Peso</th><th style='padding: 8px; border: 1px solid #3e3e3e;'>Data</th></tr>";
    foreach ($ultime as $p) {
        echo "<tr>";
        echo "<td style='padding: 8px; border: 1px solid #3e3e3e;'>{$p->id}</td>";
        echo "<td style='padding: 8px; border: 1px solid #3e3e3e;'>{$p->cliente->nome} {$p->cliente->cognome}</td>";
        echo "<td style='padding: 8px; border: 1px solid #3e3e3e;'>{$p->peso} kg</td>";
        echo "<td style='padding: 8px; border: 1px solid #3e3e3e;'>{$p->data_rilevazione}</td>";
        echo "</tr>";
    }
    echo "</table>";
}

echo "</div>";

// Conclusione
echo "<div class='box' style='border-left-color: #4ec9b0;'>";
echo "<h2>✅ CONCLUSIONE</h2>";
echo "<p><strong>Risultati:</strong></p>";
echo "<ul>";
if ($cliente) {
    echo "<li><span class='success'>✅ Creazione cliente funziona</span></li>";
} else {
    echo "<li><span class='error'>❌ Creazione cliente fallita</span></li>";
}
if (isset($pesata)) {
    echo "<li><span class='success'>✅ Creazione pesata funziona</span></li>";
} else {
    echo "<li><span class='error'>❌ Creazione pesata fallita</span></li>";
}
echo "</ul>";

echo "<p><strong>Se entrambi funzionano ma l'import non va:</strong></p>";
echo "<ol>";
echo "<li>Cache non pulita → <a href='clear-all-cache.php' style='color: #4ec9b0;'>Pulisci cache</a></li>";
echo "<li>Problema nel controller import → Controlla log Laravel</li>";
echo "<li>File Excel malformattato → Verifica mapping colonne</li>";
echo "</ol>";
echo "</div>";

echo "<br><br>";
echo "<a href='test-insert-pesata.php' style='color: #4ec9b0;'>🔍 Test Inserimento Base</a> | ";
echo "<a href='clear-all-cache.php' style='color: #569cd6;'>🧹 Pulisci Cache</a> | ";
echo "<a href='test-import-flow.php' style='color: #dcdcaa;'>🔄 Ricarica Test</a>";
?>
