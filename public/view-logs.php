<?php
/**
 * Visualizzatore Log Laravel
 * Mostra gli ultimi log per debug
 *
 * ELIMINA QUESTO FILE dopo l'uso!
 */

$logFile = __DIR__ . '/../storage/logs/laravel.log';
$lines = $_GET['lines'] ?? 100;

?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log Viewer - MA.GIA DONNA</title>
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
            max-width: 1400px;
            margin: 0 auto;
            padding: 40px;
        }
        h1 {
            color: #764ba2;
            margin-bottom: 20px;
        }
        .controls {
            margin-bottom: 20px;
            padding: 15px;
            background: #f5f5f5;
            border-radius: 6px;
        }
        .controls a {
            display: inline-block;
            padding: 10px 20px;
            background: #764ba2;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            margin: 5px;
        }
        .controls a:hover {
            background: #5a3878;
        }
        .log-container {
            background: #1e1e1e;
            color: #d4d4d4;
            padding: 20px;
            border-radius: 8px;
            font-family: 'Courier New', monospace;
            font-size: 13px;
            max-height: 800px;
            overflow-y: auto;
            white-space: pre-wrap;
            word-wrap: break-word;
        }
        .log-error { color: #f48771; }
        .log-warning { color: #dcdcaa; }
        .log-info { color: #4ec9b0; }
        .log-debug { color: #569cd6; }
        .highlight {
            background: #3c3c00;
            padding: 2px 5px;
            border-left: 3px solid #dcdcaa;
        }
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }
        .stat-box {
            padding: 15px;
            border-radius: 8px;
            text-align: center;
        }
        .stat-box.error { background: #ffebee; color: #c62828; }
        .stat-box.warning { background: #fff3e0; color: #e65100; }
        .stat-box.info { background: #e3f2fd; color: #1565C0; }
        .stat-box .number { font-size: 32px; font-weight: bold; }
        .stat-box .label { font-size: 14px; margin-top: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>📋 Log Viewer - Ultimi Eventi</h1>

        <div class="controls">
            <a href="?lines=50">Ultimi 50</a>
            <a href="?lines=100">Ultimi 100</a>
            <a href="?lines=200">Ultimi 200</a>
            <a href="?lines=500">Ultimi 500</a>
            <a href="?clear=1" onclick="return confirm('Vuotare il file di log?')" style="background: #f44336;">🗑️ Svuota Log</a>
            <a href="?refresh=1" style="background: #4CAF50;">🔄 Aggiorna</a>
        </div>

<?php

if (isset($_GET['clear'])) {
    if (file_exists($logFile)) {
        file_put_contents($logFile, '');
        echo '<div style="padding: 15px; background: #e8f5e9; color: #2E7D32; border-radius: 6px; margin-bottom: 20px;">';
        echo '✅ Log svuotato! <a href="?" style="color: #2E7D32; font-weight: bold;">Ricarica la pagina</a>';
        echo '</div>';
    }
}

if (!file_exists($logFile)) {
    echo '<div style="padding: 20px; background: #fff3e0; border-radius: 8px; color: #e65100;">';
    echo '<strong>⚠️ File di log non trovato:</strong><br>';
    echo '<code>' . htmlspecialchars($logFile) . '</code>';
    echo '</div>';
} else {
    $fileSize = filesize($logFile);
    $fileSizeFormatted = number_format($fileSize / 1024, 2) . ' KB';

    // Leggi le ultime N righe
    $command = "tail -n {$lines} " . escapeshellarg($logFile);
    $logContent = shell_exec($command);

    if (empty($logContent)) {
        $logContent = "Nessun log presente.";
    }

    // Conta i tipi di log
    $errors = substr_count($logContent, '.ERROR:');
    $warnings = substr_count($logContent, '.WARNING:');
    $infos = substr_count($logContent, '.INFO:');

    // Conta le occorrenze di PESATA
    $pesataLogs = substr_count(strtoupper($logContent), 'PESATA');

    echo '<div class="stats">';
    echo '<div class="stat-box error"><div class="number">' . $errors . '</div><div class="label">Errori</div></div>';
    echo '<div class="stat-box warning"><div class="number">' . $warnings . '</div><div class="label">Warning</div></div>';
    echo '<div class="stat-box info"><div class="number">' . $infos . '</div><div class="label">Info</div></div>';
    echo '<div class="stat-box info"><div class="number">' . $pesataLogs . '</div><div class="label">Log Pesate</div></div>';
    echo '</div>';

    echo '<div style="margin-bottom: 15px; color: #666;">';
    echo '<strong>File:</strong> ' . basename($logFile) . ' (' . $fileSizeFormatted . ')<br>';
    echo '<strong>Righe mostrate:</strong> ' . $lines . '<br>';
    echo '<strong>Ultimo aggiornamento:</strong> ' . date('Y-m-d H:i:s', filemtime($logFile));
    echo '</div>';

    // Applica colori al log
    $logContent = htmlspecialchars($logContent);
    $logContent = preg_replace('/^.*\.ERROR:.*/m', '<span class="log-error">$0</span>', $logContent);
    $logContent = preg_replace('/^.*\.WARNING:.*/m', '<span class="log-warning">$0</span>', $logContent);
    $logContent = preg_replace('/^.*\.INFO:.*/m', '<span class="log-info">$0</span>', $logContent);
    $logContent = preg_replace('/^.*\.DEBUG:.*/m', '<span class="log-debug">$0</span>', $logContent);

    // Evidenzia le sezioni PESATA
    $logContent = preg_replace('/(=+\s*.*PESATA.*\s*=+)/i', '<span class="highlight">$1</span>', $logContent);
    $logContent = preg_replace('/(PESATA)/i', '<strong>$1</strong>', $logContent);

    echo '<div class="log-container">' . $logContent . '</div>';

    echo '<div style="margin-top: 20px; padding: 15px; background: #fff3cd; border-radius: 6px; color: #856404;">';
    echo '<strong>💡 Suggerimento:</strong> Prova a salvare una pesata ora e poi aggiorna questa pagina per vedere cosa viene loggato.';
    echo '</div>';
}

?>

        <div style="margin-top: 20px; text-align: center;">
            <a href="/magia/public/admin/dashboard" class="controls" style="text-decoration: none;">← Dashboard</a>
        </div>

        <div style="margin-top: 20px; padding: 15px; background: #fff3cd; border-radius: 6px; color: #856404; text-align: center;">
            <strong>⚠️ ELIMINA QUESTO FILE</strong> dopo aver risolto:<br>
            <code>/public/view-logs.php</code>
        </div>
    </div>

    <script>
        // Auto-refresh ogni 5 secondi se richiesto
        <?php if (isset($_GET['refresh'])): ?>
        setTimeout(() => location.reload(), 5000);
        <?php endif; ?>
    </script>
</body>
</html>
