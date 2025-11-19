<?php
/**
 * Debug Import Pesate - Visualizza tutti i dati dall'Excel
 * ELIMINARE DOPO IL DEBUG
 */

require_once __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

// Configurazione
$storagePath = __DIR__ . '/../storage/app/';

// Trova l'ultimo file Excel caricato
$files = glob($storagePath . 'imports/*.xlsx');
if (empty($files)) {
    $files = glob($storagePath . 'imports/*.xls');
}

if (empty($files)) {
    die('<h1>Nessun file Excel trovato in storage/app/imports/</h1>');
}

// Prendi il file più recente
usort($files, function($a, $b) {
    return filemtime($b) - filemtime($a);
});
$filePath = $files[0];

?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debug Import Pesate</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .null-value { color: #ef4444; font-weight: bold; }
        .has-value { color: #22c55e; }
        table { font-size: 11px; }
        th, td { padding: 4px 8px; white-space: nowrap; }
    </style>
</head>
<body class="bg-gray-100 p-4">
    <div class="max-w-full mx-auto">
        <div class="bg-white rounded-lg shadow p-4 mb-4">
            <h1 class="text-xl font-bold text-gray-800 mb-2">Debug Import Pesate</h1>
            <p class="text-sm text-gray-600">File: <strong><?= basename($filePath) ?></strong></p>
            <p class="text-sm text-gray-500">Modificato: <?= date('d/m/Y H:i:s', filemtime($filePath)) ?></p>
        </div>

        <?php
        try {
            $spreadsheet = IOFactory::load($filePath);
            $worksheet = $spreadsheet->getActiveSheet();
            $highestRow = $worksheet->getHighestRow();
            $highestColumn = $worksheet->getHighestColumn();

            // Leggi header (riga 2)
            $headers = [];
            foreach (range('A', $highestColumn) as $col) {
                $headers[$col] = $worksheet->getCell($col . '2')->getValue();
            }

            echo '<div class="bg-blue-50 rounded-lg p-3 mb-4">';
            echo '<p class="text-sm"><strong>Righe totali:</strong> ' . $highestRow . '</p>';
            echo '<p class="text-sm"><strong>Colonne:</strong> A - ' . $highestColumn . '</p>';
            echo '<p class="text-sm"><strong>Riga dati da:</strong> 3</p>';
            echo '</div>';

            // Mostra headers
            echo '<div class="bg-yellow-50 rounded-lg p-3 mb-4">';
            echo '<h3 class="font-bold text-sm mb-2">Intestazioni Colonne (Riga 2):</h3>';
            echo '<div class="grid grid-cols-4 gap-2 text-xs">';
            foreach ($headers as $col => $header) {
                $headerText = $header ?: '(vuoto)';
                echo "<div><strong>{$col}:</strong> {$headerText}</div>";
            }
            echo '</div></div>';

            // Tabella dati
            echo '<div class="bg-white rounded-lg shadow overflow-x-auto">';
            echo '<table class="w-full border-collapse">';

            // Header tabella
            echo '<thead class="bg-gray-800 text-white sticky top-0">';
            echo '<tr>';
            echo '<th class="border px-2 py-1">Riga</th>';
            foreach ($headers as $col => $header) {
                $shortHeader = $header ? substr($header, 0, 15) : $col;
                echo "<th class='border px-2 py-1' title='{$header}'>{$col}<br><span class='text-xs font-normal'>{$shortHeader}</span></th>";
            }
            echo '</tr>';
            echo '</thead>';

            // Dati
            echo '<tbody>';
            for ($row = 3; $row <= $highestRow; $row++) {
                $rowClass = $row % 2 === 0 ? 'bg-gray-50' : 'bg-white';
                echo "<tr class='{$rowClass}'>";
                echo "<td class='border px-2 py-1 font-bold text-center'>{$row}</td>";

                foreach (range('A', $highestColumn) as $col) {
                    $value = $worksheet->getCell($col . $row)->getValue();

                    // Gestione valori
                    if ($value === null || $value === '') {
                        echo '<td class="border px-2 py-1 null-value text-center">NULL</td>';
                    } else {
                        // Limita lunghezza per visualizzazione
                        $displayValue = strlen($value) > 20 ? substr($value, 0, 20) . '...' : $value;
                        echo "<td class='border px-2 py-1 has-value' title='{$value}'>{$displayValue}</td>";
                    }
                }

                echo '</tr>';
            }
            echo '</tbody>';
            echo '</table>';
            echo '</div>';

            // Statistiche per colonna
            echo '<div class="bg-white rounded-lg shadow p-4 mt-4">';
            echo '<h3 class="font-bold text-sm mb-3">Statistiche Valori NULL per Colonna:</h3>';
            echo '<div class="grid grid-cols-6 gap-2 text-xs">';

            foreach ($headers as $col => $header) {
                $nullCount = 0;
                for ($row = 3; $row <= $highestRow; $row++) {
                    $value = $worksheet->getCell($col . $row)->getValue();
                    if ($value === null || $value === '') {
                        $nullCount++;
                    }
                }

                $total = $highestRow - 2;
                $percentage = round(($nullCount / $total) * 100);
                $colorClass = $nullCount === 0 ? 'bg-green-100 text-green-800' : ($nullCount === $total ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800');

                $headerShort = $header ? substr($header, 0, 12) : $col;
                echo "<div class='p-2 rounded {$colorClass}'>";
                echo "<strong>{$col}</strong>: {$nullCount}/{$total} NULL ({$percentage}%)<br>";
                echo "<span class='text-[10px]'>{$headerShort}</span>";
                echo "</div>";
            }

            echo '</div></div>';

        } catch (Exception $e) {
            echo '<div class="bg-red-100 text-red-800 p-4 rounded">';
            echo '<strong>Errore:</strong> ' . $e->getMessage();
            echo '</div>';
        }
        ?>

        <div class="mt-4 p-3 bg-red-50 border border-red-200 rounded text-sm text-red-800">
            <strong>ATTENZIONE:</strong> Eliminare questo file dopo il debug!
            <code class="bg-red-100 px-1">public/debug-import.php</code>
        </div>
    </div>
</body>
</html>
