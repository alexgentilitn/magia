<?php
/**
 * Test lettura BMI da file Excel
 * Questo script verifica come viene letto il valore BMI dal file Excel
 */

require __DIR__.'/../vendor/autoload.php';

use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;

echo "<h1>🧪 Test Lettura BMI da Excel</h1>";
echo "<pre>";

// Bootstrap Laravel
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "📂 Cerco l'ultimo file Excel importato...\n\n";

// Cerca file nella cartella storage/app/imports
$files = Storage::disk('local')->files('imports');
if (empty($files)) {
    echo "❌ Nessun file trovato in storage/app/imports\n";
    echo "Carica un file Excel dalla pagina di import prima di eseguire questo test.\n";
    exit;
}

// Prendi l'ultimo file
$latestFile = collect($files)->sortByDesc(function($file) {
    return Storage::disk('local')->lastModified($file);
})->first();

$path = Storage::disk('local')->path($latestFile);
echo "✅ File trovato: " . basename($latestFile) . "\n\n";

// Carica Excel
try {
    $spreadsheet = IOFactory::load($path);
    $worksheet = $spreadsheet->getActiveSheet();

    echo "📊 Foglio: " . $worksheet->getTitle() . "\n";
    echo "📈 Righe totali: " . $worksheet->getHighestRow() . "\n\n";

    // Leggi intestazioni
    echo "=== INTESTAZIONI (Riga 1) ===\n";
    $headers = [];
    $highestColumn = $worksheet->getHighestColumn();
    $colIndex = 'A';

    while ($colIndex <= $highestColumn) {
        $header = $worksheet->getCell($colIndex . '1')->getValue();
        $headers[$colIndex] = $header;
        echo "$colIndex: " . ($header ?: '(vuoto)') . "\n";
        $colIndex++;
    }

    // Trova colonna BMI
    echo "\n=== RICERCA COLONNA BMI ===\n";
    $bmiColumn = null;

    foreach ($headers as $col => $header) {
        $headerLower = strtolower(trim($header ?? ''));
        if (stripos($headerLower, 'bmi') !== false ||
            stripos($headerLower, 'indice massa') !== false ||
            stripos($headerLower, 'body mass') !== false) {
            $bmiColumn = $col;
            echo "✅ Colonna BMI trovata: $col - \"$header\"\n";
            break;
        }
    }

    if (!$bmiColumn) {
        echo "❌ Nessuna colonna BMI trovata automaticamente\n";
        echo "\nProva manuale - inserisci la lettera della colonna BMI: ";
        // Per test web, proviamo le colonne più comuni
        $possibleCols = ['D', 'E', 'F', 'G', 'H'];
        foreach ($possibleCols as $testCol) {
            $val = $worksheet->getCell($testCol . '2')->getValue();
            if (is_numeric($val) && $val >= 10 && $val <= 100) {
                echo "\n🔍 Possibile colonna BMI: $testCol (valore esempio: $val)\n";
                $bmiColumn = $testCol;
                break;
            }
        }
    }

    if (!$bmiColumn) {
        echo "\n❌ Impossibile determinare la colonna BMI\n";
        echo "Controlla manualmente il file Excel\n";
        exit;
    }

    // Test lettura valori BMI
    echo "\n=== TEST LETTURA VALORI BMI (Colonna $bmiColumn) ===\n";
    echo sprintf("%-6s %-15s %-15s %-15s %-10s\n",
        "Riga", "getValue()", "getFormattedValue()", "Tipo", "Valido?");
    echo str_repeat("-", 70) . "\n";

    // Helper per pulire numeri (simula la funzione del controller)
    function pulisciNumero($valore) {
        if (is_null($valore) || $valore === '') {
            return null;
        }
        $valore = trim(strval($valore));
        $valore = str_replace('%', '', $valore);
        $numero = str_replace([',', ' '], ['.', ''], $valore);
        return is_numeric($numero) ? (float) $numero : null;
    }

    // Helper per validare BMI (simula la funzione del controller)
    function validaBMI($valore) {
        if (is_null($valore)) {
            return null;
        }
        if ($valore < 10 || $valore > 100) {
            return null;
        }
        return $valore;
    }

    // Testa prime 10 righe di dati (dalla riga 2)
    $startRow = 2;
    $maxRow = min($startRow + 9, $worksheet->getHighestRow());

    for ($row = $startRow; $row <= $maxRow; $row++) {
        $cell = $bmiColumn . $row;
        $rawValue = $worksheet->getCell($cell)->getValue();
        $formattedValue = $worksheet->getCell($cell)->getFormattedValue();
        $tipo = gettype($rawValue);

        $pulito = pulisciNumero($rawValue);
        $validato = validaBMI($pulito);

        $status = is_null($validato) ? '❌ NULL' : '✅ OK';

        echo sprintf("%-6s %-15s %-15s %-15s %-10s",
            $row,
            var_export($rawValue, true),
            var_export($formattedValue, true),
            $tipo,
            $status
        );

        if ($pulito !== $validato) {
            echo " (pulito: $pulito → validato: " . var_export($validato, true) . ")";
        }

        echo "\n";
    }

    echo "\n=== TEST SPECIFICO VALORE 19.09 ===\n";

    // Cerca la riga con BMI 19.09
    for ($row = 2; $row <= $worksheet->getHighestRow(); $row++) {
        $value = $worksheet->getCell($bmiColumn . $row)->getValue();
        $pulito = pulisciNumero($value);

        if ($pulito == 19.09) {
            echo "✅ Trovato valore 19.09 alla riga $row\n";
            echo "   Raw value: " . var_export($value, true) . "\n";
            echo "   Tipo: " . gettype($value) . "\n";
            echo "   Dopo pulisciNumero(): $pulito\n";
            echo "   Dopo validaBMI(): " . var_export(validaBMI($pulito), true) . "\n";

            // Verifica condizione if
            if ($pulito < 10 || $pulito > 100) {
                echo "   ❌ RIFIUTATO: fuori range (10-100)\n";
            } else {
                echo "   ✅ ACCETTATO: dentro range\n";
            }
            break;
        }
    }

} catch (\Exception $e) {
    echo "❌ Errore: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}

echo "\n</pre>";
