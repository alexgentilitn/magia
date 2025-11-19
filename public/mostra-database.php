<?php
/**
 * Visualizzatore Completo Database
 * Mostra tutte le tabelle e la loro struttura
 *
 * ELIMINA QUESTO FILE dopo l'uso!
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Carica configurazione database dal .env
$envPath = __DIR__ . '/../.env';
if (!file_exists($envPath)) {
    die("Errore: file .env non trovato");
}

$envContent = file_get_contents($envPath);
preg_match('/DB_HOST=(.+)/', $envContent, $host);
preg_match('/DB_PORT=(.+)/', $envContent, $port);
preg_match('/DB_DATABASE=(.+)/', $envContent, $database);
preg_match('/DB_USERNAME=(.+)/', $envContent, $username);
preg_match('/DB_PASSWORD=(.+)/', $envContent, $password);

$dbHost = trim($host[1] ?? 'localhost');
$dbPort = trim($port[1] ?? '3306');
$dbName = trim($database[1] ?? '');
$dbUser = trim($username[1] ?? '');
$dbPass = trim($password[1] ?? '');

$showData = isset($_GET['data']) ? $_GET['data'] : null;

?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Explorer - MA.GIA DONNA</title>
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
            box-shadow: 0 10px 40px rgba(0,0,0,0.3);
            max-width: 1400px;
            margin: 0 auto;
            padding: 40px;
        }
        h1 {
            color: #764ba2;
            margin-bottom: 10px;
            font-size: 32px;
        }
        .subtitle {
            color: #666;
            margin-bottom: 30px;
            font-size: 14px;
        }
        .db-info {
            background: #f0f9ff;
            border-left: 4px solid #0ea5e9;
            padding: 15px 20px;
            margin-bottom: 30px;
            border-radius: 6px;
        }
        .table-card {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            margin-bottom: 20px;
            overflow: hidden;
        }
        .table-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 20px;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .table-header:hover {
            background: linear-gradient(135deg, #5568d3 0%, #653a8b 100%);
        }
        .table-name {
            font-weight: bold;
            font-size: 18px;
        }
        .table-stats {
            font-size: 13px;
            opacity: 0.9;
        }
        .table-body {
            padding: 20px;
            display: none;
        }
        .table-body.active {
            display: block;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            font-size: 13px;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }
        th {
            background: #f9fafb;
            font-weight: bold;
            color: #374151;
            position: sticky;
            top: 0;
        }
        tr:hover {
            background: #f9fafb;
        }
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: bold;
        }
        .badge-primary {
            background: #dbeafe;
            color: #1e40af;
        }
        .badge-success {
            background: #d1fae5;
            color: #065f46;
        }
        .badge-warning {
            background: #fef3c7;
            color: #92400e;
        }
        .badge-danger {
            background: #fee2e2;
            color: #991b1b;
        }
        .btn {
            display: inline-block;
            padding: 8px 16px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-size: 13px;
            margin: 5px;
        }
        .btn:hover {
            background: #5568d3;
        }
        .btn-small {
            padding: 4px 10px;
            font-size: 12px;
        }
        .warning-box {
            background: #fff3cd;
            border: 2px solid #ffc107;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            text-align: center;
            color: #856404;
        }
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
            margin: 20px 0;
        }
        .stat-box {
            background: #f9fafb;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #667eea;
        }
        .stat-value {
            font-size: 28px;
            font-weight: bold;
            color: #764ba2;
        }
        .stat-label {
            font-size: 13px;
            color: #666;
            margin-top: 5px;
        }
        .data-table {
            max-height: 400px;
            overflow-y: auto;
        }
        .code {
            background: #2d2d2d;
            color: #f8f8f2;
            padding: 3px 6px;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🗄️ Database Explorer</h1>
        <p class="subtitle">Visualizzazione completa di tutte le tabelle e strutture del database</p>

<?php

try {
    // Connessione database
    $dsn = "mysql:host=$dbHost;port=$dbPort;dbname=$dbName;charset=utf8mb4";
    $pdo = new PDO($dsn, $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    echo '<div class="db-info">';
    echo '<strong>📊 Database:</strong> <span class="code">' . htmlspecialchars($dbName) . '</span><br>';
    echo '<strong>🖥️ Host:</strong> ' . htmlspecialchars($dbHost) . ':' . htmlspecialchars($dbPort) . '<br>';
    echo '<strong>👤 User:</strong> ' . htmlspecialchars($dbUser);
    echo '</div>';

    // Ottieni tutte le tabelle
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);

    echo '<div class="grid">';
    echo '<div class="stat-box">';
    echo '<div class="stat-value">' . count($tables) . '</div>';
    echo '<div class="stat-label">Tabelle Totali</div>';
    echo '</div>';

    $totalRecords = 0;
    $totalSize = 0;

    foreach ($tables as $table) {
        $count = $pdo->query("SELECT COUNT(*) FROM `$table`")->fetchColumn();
        $totalRecords += $count;
    }

    echo '<div class="stat-box">';
    echo '<div class="stat-value">' . number_format($totalRecords) . '</div>';
    echo '<div class="stat-label">Record Totali</div>';
    echo '</div>';

    echo '<div class="stat-box">';
    echo '<div class="stat-value">' . count(array_filter($tables, function($t) use ($pdo) {
        return $pdo->query("SELECT COUNT(*) FROM `$t`")->fetchColumn() > 0;
    })) . '</div>';
    echo '<div class="stat-label">Tabelle con Dati</div>';
    echo '</div>';

    echo '</div>';

    echo '<p style="margin: 20px 0; color: #666;">Clicca su una tabella per vedere la struttura e i dati</p>';

    // Per ogni tabella
    foreach ($tables as $index => $table) {
        $count = $pdo->query("SELECT COUNT(*) FROM `$table`")->fetchColumn();

        echo '<div class="table-card">';
        echo '<div class="table-header" onclick="toggleTable(' . $index . ')">';
        echo '<div>';
        echo '<span class="table-name">📋 ' . htmlspecialchars($table) . '</span>';
        if ($count > 0) {
            echo ' <span class="badge badge-success">' . number_format($count) . ' record</span>';
        } else {
            echo ' <span class="badge badge-warning">vuota</span>';
        }
        echo '</div>';
        echo '<div class="table-stats">';
        echo '<span id="toggle-' . $index . '">▼</span>';
        echo '</div>';
        echo '</div>';

        echo '<div class="table-body" id="table-' . $index . '">';

        // Struttura tabella
        echo '<h3 style="color: #764ba2; margin-bottom: 15px;">📐 Struttura</h3>';
        $columns = $pdo->query("DESCRIBE `$table`")->fetchAll();

        echo '<table>';
        echo '<thead><tr>';
        echo '<th>Campo</th>';
        echo '<th>Tipo</th>';
        echo '<th>Null</th>';
        echo '<th>Key</th>';
        echo '<th>Default</th>';
        echo '<th>Extra</th>';
        echo '</tr></thead>';
        echo '<tbody>';

        foreach ($columns as $col) {
            echo '<tr>';
            echo '<td><strong>' . htmlspecialchars($col['Field']) . '</strong></td>';
            echo '<td><span class="code">' . htmlspecialchars($col['Type']) . '</span></td>';
            echo '<td>' . ($col['Null'] == 'YES' ? '<span class="badge badge-warning">YES</span>' : '<span class="badge badge-danger">NO</span>') . '</td>';
            echo '<td>';
            if ($col['Key'] == 'PRI') echo '<span class="badge badge-primary">PRIMARY</span>';
            else if ($col['Key'] == 'MUL') echo '<span class="badge badge-success">INDEX</span>';
            else if ($col['Key'] == 'UNI') echo '<span class="badge badge-warning">UNIQUE</span>';
            else echo '-';
            echo '</td>';
            echo '<td>' . htmlspecialchars($col['Default'] ?? 'NULL') . '</td>';
            echo '<td>' . htmlspecialchars($col['Extra']) . '</td>';
            echo '</tr>';
        }

        echo '</tbody></table>';

        // Foreign Keys
        try {
            $fks = $pdo->query("
                SELECT
                    kcu.CONSTRAINT_NAME,
                    kcu.COLUMN_NAME,
                    kcu.REFERENCED_TABLE_NAME,
                    kcu.REFERENCED_COLUMN_NAME,
                    COALESCE(rc.DELETE_RULE, 'N/A') as DELETE_RULE
                FROM information_schema.KEY_COLUMN_USAGE kcu
                LEFT JOIN information_schema.REFERENTIAL_CONSTRAINTS rc
                    ON kcu.CONSTRAINT_NAME = rc.CONSTRAINT_NAME
                    AND kcu.CONSTRAINT_SCHEMA = rc.CONSTRAINT_SCHEMA
                WHERE kcu.TABLE_SCHEMA = '$dbName'
                AND kcu.TABLE_NAME = '$table'
                AND kcu.REFERENCED_TABLE_NAME IS NOT NULL
            ")->fetchAll();
        } catch (PDOException $e) {
            // Fallback se la query non funziona
            $fks = [];
        }

        if (count($fks) > 0) {
            echo '<h3 style="color: #764ba2; margin: 20px 0 15px;">🔗 Foreign Keys</h3>';
            echo '<table>';
            echo '<thead><tr><th>Constraint</th><th>Colonna</th><th>Riferimento</th><th>On Delete</th></tr></thead>';
            echo '<tbody>';
            foreach ($fks as $fk) {
                echo '<tr>';
                echo '<td>' . htmlspecialchars($fk['CONSTRAINT_NAME']) . '</td>';
                echo '<td><strong>' . htmlspecialchars($fk['COLUMN_NAME']) . '</strong></td>';
                echo '<td>' . htmlspecialchars($fk['REFERENCED_TABLE_NAME']) . '.' . htmlspecialchars($fk['REFERENCED_COLUMN_NAME']) . '</td>';
                echo '<td><span class="badge badge-warning">' . htmlspecialchars($fk['DELETE_RULE']) . '</span></td>';
                echo '</tr>';
            }
            echo '</tbody></table>';
        }

        // Dati (se ci sono e se richiesto)
        if ($count > 0) {
            echo '<h3 style="color: #764ba2; margin: 20px 0 15px;">📊 Dati (' . number_format($count) . ' record)</h3>';

            if ($showData == $table) {
                $limit = 100;
                $data = $pdo->query("SELECT * FROM `$table` ORDER BY id DESC LIMIT $limit")->fetchAll();

                echo '<div class="data-table">';
                echo '<table>';
                echo '<thead><tr>';
                foreach (array_keys($data[0] ?? []) as $colName) {
                    echo '<th>' . htmlspecialchars($colName) . '</th>';
                }
                echo '</tr></thead>';
                echo '<tbody>';

                foreach ($data as $row) {
                    echo '<tr>';
                    foreach ($row as $value) {
                        if (is_null($value)) {
                            echo '<td><span style="color: #999; font-style: italic;">NULL</span></td>';
                        } else if (strlen($value) > 100) {
                            echo '<td>' . htmlspecialchars(substr($value, 0, 100)) . '...</td>';
                        } else {
                            echo '<td>' . htmlspecialchars($value) . '</td>';
                        }
                    }
                    echo '</tr>';
                }

                echo '</tbody></table>';
                echo '</div>';

                if ($count > $limit) {
                    echo '<p style="color: #666; font-size: 12px; margin-top: 10px;">Mostrati i primi ' . $limit . ' record su ' . number_format($count) . '</p>';
                }

                echo '<a href="?" class="btn btn-small">Nascondi Dati</a>';
            } else {
                echo '<a href="?data=' . urlencode($table) . '#table-' . $index . '" class="btn btn-small">Mostra Primi 100 Record</a>';
            }
        }

        echo '</div>';
        echo '</div>';
    }

} catch (PDOException $e) {
    echo '<div style="background: #fee2e2; border-left: 4px solid #ef4444; padding: 20px; border-radius: 6px; color: #991b1b;">';
    echo '<strong>❌ Errore Database:</strong><br>';
    echo htmlspecialchars($e->getMessage());
    echo '</div>';
}

?>

        <div class="warning-box">
            <strong>⚠️ ELIMINA QUESTO FILE</strong> dopo aver finito l'analisi:<br>
            <code>/public/mostra-database.php</code><br><br>
            <small>Questo file espone la struttura completa del database e non deve rimanere pubblico</small>
        </div>

        <div style="margin-top: 20px; text-align: center;">
            <a href="/magia/public/admin/impostazioni" class="btn">⚙️ Vai alle Impostazioni</a>
            <a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="btn">🔄 Ricarica</a>
        </div>

        <div style="margin-top: 20px; padding: 15px; background: #f9fafb; border-radius: 6px; font-size: 12px; color: #666; text-align: center;">
            <strong>Database Explorer v1.0</strong> - MA.GIA DONNA
        </div>
    </div>

    <script>
    function toggleTable(index) {
        const body = document.getElementById('table-' + index);
        const toggle = document.getElementById('toggle-' + index);

        if (body.classList.contains('active')) {
            body.classList.remove('active');
            toggle.textContent = '▼';
        } else {
            body.classList.add('active');
            toggle.textContent = '▲';
        }
    }

    // Se c'è un hash, apri quella tabella
    window.addEventListener('load', function() {
        const hash = window.location.hash;
        if (hash) {
            const id = hash.replace('#table-', '');
            if (id) {
                toggleTable(parseInt(id));
            }
        }
    });
    </script>
</body>
</html>
