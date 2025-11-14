<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test API Calendario</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
            min-height: 100vh;
        }
        .container { max-width: 1200px; margin: 0 auto; }
        .card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        h1 { color: #764ba2; margin-bottom: 10px; }
        h2 { color: #333; margin: 20px 0 10px; font-size: 18px; }
        .test-section {
            background: #f8f9fa;
            border-left: 4px solid #764ba2;
            padding: 15px;
            margin: 10px 0;
            border-radius: 4px;
        }
        .success { border-left-color: #10b981; background: #d1fae5; }
        .error { border-left-color: #ef4444; background: #fee2e2; }
        .warning { border-left-color: #f59e0b; background: #fef3c7; }
        pre {
            background: #1f2937;
            color: #10b981;
            padding: 15px;
            border-radius: 5px;
            overflow-x: auto;
            font-size: 12px;
            margin: 10px 0;
        }
        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
            margin: 2px;
        }
        .badge-success { background: #d1fae5; color: #065f46; }
        .badge-error { background: #fee2e2; color: #991b1b; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #764ba2; color: white; font-weight: 600; }
        tr:hover { background: #f9f9f9; }
    </style>
</head>
<body>

<?php
// Connessione database
$host = 'localhost';
$dbname = 'progetti_ai';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo '<div class="container">';
    echo '<div class="card">';
    echo '<h1>🧪 Test API Calendario - Simulazione FullCalendar</h1>';
    echo '<p style="color: #666;">Questo script simula esattamente cosa fa il calendario quando richiede gli eventi</p>';
    echo '</div>';

    // Test 1: Query di base (come fa CalendarioController)
    echo '<div class="card">';
    echo '<h2>📊 Test 1: Query Base (tutte le lezioni visibili)</h2>';

    $query = "SELECT
        id, titolo, data,
        DATE_FORMAT(ora_inizio, '%H:%i') as ora_inizio,
        DATE_FORMAT(ora_fine, '%H:%i') as ora_fine,
        tipologia, stato, visibile_calendario,
        posti_occupati, posti_totali
    FROM lezioni
    WHERE visibile_calendario = 1
    AND deleted_at IS NULL
    ORDER BY data ASC, ora_inizio ASC";

    $stmt = $pdo->query($query);
    $lezioni = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($lezioni) > 0) {
        echo '<div class="test-section success">';
        echo '<strong>✅ Query riuscita:</strong> Trovate ' . count($lezioni) . ' lezioni visibili<br>';
        echo '<small>Questa è la query base che usa il CalendarioController</small>';
        echo '</div>';

        echo '<table>';
        echo '<tr><th>ID</th><th>Titolo</th><th>Data</th><th>Orario</th><th>Tipo</th><th>Posti</th></tr>';
        foreach ($lezioni as $l) {
            $dataFormatted = date('d/m/Y', strtotime($l['data']));
            echo "<tr>";
            echo "<td><strong>{$l['id']}</strong></td>";
            echo "<td>{$l['titolo']}</td>";
            echo "<td>{$dataFormatted}</td>";
            echo "<td>{$l['ora_inizio']} - {$l['ora_fine']}</td>";
            echo "<td>{$l['tipologia']}</td>";
            echo "<td>{$l['posti_occupati']}/{$l['posti_totali']}</td>";
            echo "</tr>";
        }
        echo '</table>';
    } else {
        echo '<div class="test-section error">';
        echo '<strong>❌ Problema:</strong> Nessuna lezione trovata con visibile_calendario=1';
        echo '</div>';
    }
    echo '</div>';

    // Test 2: Simulazione richiesta NOVEMBRE 2025
    echo '<div class="card">';
    echo '<h2>📅 Test 2: Eventi per NOVEMBRE 2025 (come richiede FullCalendar)</h2>';

    $start = '2025-11-01';
    $end = '2025-11-30';

    echo '<div class="test-section">';
    echo "<strong>Range richiesto:</strong> dal <code>$start</code> al <code>$end</code><br>";
    echo '<small>FullCalendar richiede gli eventi in questo range quando visualizzi novembre 2025</small>';
    echo '</div>';

    $query = "SELECT
        id, titolo, data,
        DATE_FORMAT(ora_inizio, '%H:%i') as ora_inizio,
        DATE_FORMAT(ora_fine, '%H:%i') as ora_fine,
        tipologia, stato, posti_occupati, posti_totali
    FROM lezioni
    WHERE visibile_calendario = 1
    AND deleted_at IS NULL
    AND data BETWEEN ? AND ?
    ORDER BY data ASC, ora_inizio ASC";

    $stmt = $pdo->prepare($query);
    $stmt->execute([$start, $end]);
    $eventi_novembre = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($eventi_novembre) > 0) {
        echo '<div class="test-section success">';
        echo '<strong>✅ Trovati ' . count($eventi_novembre) . ' eventi in novembre 2025</strong>';
        echo '</div>';

        echo '<table>';
        echo '<tr><th>ID</th><th>Titolo</th><th>Data</th><th>Orario</th><th>JSON FullCalendar</th></tr>';
        foreach ($eventi_novembre as $e) {
            $dataFormatted = date('d/m/Y', strtotime($e['data']));
            $jsonEvent = [
                'id' => $e['id'],
                'title' => $e['titolo'] . " ({$e['posti_occupati']}/{$e['posti_totali']})",
                'start' => $e['data'] . 'T' . $e['ora_inizio'] . ':00',
                'end' => $e['data'] . 'T' . $e['ora_fine'] . ':00',
            ];
            echo "<tr>";
            echo "<td><strong>{$e['id']}</strong></td>";
            echo "<td>{$e['titolo']}</td>";
            echo "<td><strong>{$dataFormatted}</strong></td>";
            echo "<td>{$e['ora_inizio']} - {$e['ora_fine']}</td>";
            echo "<td><pre style='margin:0;padding:5px;font-size:10px;'>" . json_encode($jsonEvent, JSON_PRETTY_PRINT) . "</pre></td>";
            echo "</tr>";
        }
        echo '</table>';
    } else {
        echo '<div class="test-section warning">';
        echo '<strong>⚠️ Nessun evento in novembre 2025</strong><br>';
        echo 'Questo è normale se non hai creato lezioni per novembre. Le tue lezioni di pilates sono in ottobre!';
        echo '</div>';
    }
    echo '</div>';

    // Test 3: Simulazione richiesta OTTOBRE 2025
    echo '<div class="card">';
    echo '<h2>📅 Test 3: Eventi per OTTOBRE 2025</h2>';

    $start = '2025-10-01';
    $end = '2025-10-31';

    echo '<div class="test-section">';
    echo "<strong>Range richiesto:</strong> dal <code>$start</code> al <code>$end</code><br>";
    echo '<small>Qui dovrebbero apparire le lezioni di pilates</small>';
    echo '</div>';

    $stmt = $pdo->prepare($query);
    $stmt->execute([$start, $end]);
    $eventi_ottobre = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($eventi_ottobre) > 0) {
        echo '<div class="test-section success">';
        echo '<strong>✅ Trovati ' . count($eventi_ottobre) . ' eventi in ottobre 2025</strong>';
        echo '</div>';

        echo '<table>';
        echo '<tr><th>ID</th><th>Titolo</th><th>Data</th><th>Orario</th><th>Ricorrente</th></tr>';
        foreach ($eventi_ottobre as $e) {
            $dataFormatted = date('d/m/Y', strtotime($e['data']));
            echo "<tr>";
            echo "<td><strong>{$e['id']}</strong></td>";
            echo "<td>{$e['titolo']}</td>";
            echo "<td><strong>{$dataFormatted}</strong></td>";
            echo "<td>{$e['ora_inizio']} - {$e['ora_fine']}</td>";
            echo "<td>{$e['tipologia']}</td>";
            echo "</tr>";
        }
        echo '</table>';
    } else {
        echo '<div class="test-section error">';
        echo '<strong>❌ Nessun evento in ottobre 2025</strong>';
        echo '</div>';
    }
    echo '</div>';

    // Riepilogo
    echo '<div class="card" style="background: #fef3c7; border-left: 4px solid #f59e0b;">';
    echo '<h2>💡 Conclusione</h2>';
    echo '<div style="color: #78350f; line-height: 1.8;">';

    if (count($eventi_novembre) > 0) {
        echo '<p><strong>✅ Il calendario in NOVEMBRE 2025 mostrerebbe ' . count($eventi_novembre) . ' evento/i</strong></p>';
    } else {
        echo '<p><strong>⚠️ Il calendario in NOVEMBRE 2025 è vuoto</strong></p>';
        echo '<p>Le tue lezioni di pilates sono tutte in <strong>OTTOBRE 2025</strong>.</p>';
    }

    if (count($eventi_ottobre) > 0) {
        echo '<p><strong>✅ Il calendario in OTTOBRE 2025 mostrerebbe ' . count($eventi_ottobre) . ' eventi</strong></p>';
        echo '<p style="margin-top: 15px; padding: 10px; background: white; border-radius: 5px;">';
        echo '🎯 <strong>Soluzione:</strong> Nel calendario, usa le frecce per navigare al mese di <strong>OTTOBRE 2025</strong> per vedere le lezioni di pilates.';
        echo '</p>';
    }

    echo '</div>';
    echo '</div>';

    echo '</div>'; // chiudi container

} catch(PDOException $e) {
    echo '<div class="container"><div class="card" style="border-left: 4px solid #ef4444;">';
    echo '<h1>❌ Errore Connessione Database</h1>';
    echo '<p style="color: #991b1b;">' . htmlspecialchars($e->getMessage()) . '</p>';
    echo '</div></div>';
}
?>

</body>
</html>
