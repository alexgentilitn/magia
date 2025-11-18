<?php
/**
 * Visualizza l'ultimo errore completo dal log
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

$logFile = __DIR__ . '/../storage/logs/laravel.log';

?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ultimo Errore - MA.GIA DONNA</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Courier New', monospace;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
            font-size: 13px;
        }
        .container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.3);
            max-width: 1400px;
            margin: 0 auto;
            padding: 30px;
        }
        h1 {
            color: #764ba2;
            margin-bottom: 20px;
            font-size: 24px;
        }
        .error-block {
            background: #2d2d2d;
            color: #f8f8f2;
            padding: 20px;
            border-radius: 8px;
            overflow-x: auto;
            margin: 20px 0;
            white-space: pre-wrap;
            word-wrap: break-word;
            border-left: 4px solid #f44336;
        }
        .error-line {
            color: #f44336;
            font-weight: bold;
        }
        .file-path {
            color: #4CAF50;
        }
        .line-number {
            color: #2196F3;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #764ba2;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            margin: 5px;
        }
        .info-box {
            background: #e3f2fd;
            padding: 15px;
            border-radius: 6px;
            margin: 15px 0;
            border-left: 4px solid #2196F3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Visualizzatore Ultimo Errore</h1>

<?php

if (!file_exists($logFile)) {
    echo '<div class="error-block">⚠️ File di log non trovato: ' . htmlspecialchars($logFile) . '</div>';
    echo '</div></body></html>';
    exit;
}

// Leggi il file al contrario per trovare l'ultimo errore
$lines = file($logFile);
$lines = array_reverse($lines);

$errorFound = false;
$errorContent = [];
$inError = false;

foreach ($lines as $line) {
    // Inizia un nuovo errore
    if (preg_match('/^\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\]/', $line, $matches)) {
        if ($inError) {
            // Abbiamo finito di leggere l'errore precedente
            break;
        }
        if (stripos($line, '.ERROR:') !== false || stripos($line, 'exception') !== false) {
            $inError = true;
            $errorFound = true;
        }
    }

    if ($inError) {
        $errorContent[] = $line;
    }
}

if (!$errorFound) {
    echo '<div class="info-box">✓ Nessun errore recente trovato nel log!</div>';
} else {
    $errorContent = array_reverse($errorContent);
    $errorText = implode('', $errorContent);

    // Estrai informazioni chiave
    preg_match('/\.ERROR:\s*(.+?)(?:\s*\{|$)/s', $errorText, $errorMsg);
    preg_match('/in (.+?):(\d+)/', $errorText, $fileInfo);

    echo '<div class="info-box">';
    echo '<strong>📍 Ultimo errore trovato:</strong><br>';
    if (isset($errorMsg[1])) {
        echo '<strong>Messaggio:</strong> ' . htmlspecialchars(trim($errorMsg[1])) . '<br>';
    }
    if (isset($fileInfo[1], $fileInfo[2])) {
        echo '<strong>File:</strong> <span class="file-path">' . htmlspecialchars($fileInfo[1]) . '</span><br>';
        echo '<strong>Riga:</strong> <span class="line-number">' . htmlspecialchars($fileInfo[2]) . '</span>';
    }
    echo '</div>';

    // Evidenzia parti importanti
    $errorText = htmlspecialchars($errorText);
    $errorText = preg_replace('/(\.ERROR:.*?)$/m', '<span class="error-line">$1</span>', $errorText);
    $errorText = preg_replace('/(\/.+?\.php)/', '<span class="file-path">$1</span>', $errorText);
    $errorText = preg_replace('/:(\d+)/', ':<span class="line-number">$1</span>', $errorText);

    echo '<div class="error-block">' . $errorText . '</div>';

    // Suggerimenti
    echo '<div class="info-box">';
    echo '<strong>💡 Cosa fare:</strong><br>';
    echo '1. Leggi il messaggio di errore sopra<br>';
    echo '2. Controlla il file e la riga indicati<br>';
    echo '3. Se serve, copia tutto l\'errore e mandamelo per aiutarti a risolverlo';
    echo '</div>';
}

echo '<div style="margin-top: 20px; text-align: center;">';
echo '<a href="check-error.php" class="btn">← Torna al Check Completo</a> ';
echo '<a href="view-logs.php" class="btn">📋 Visualizza Tutti i Log</a> ';
echo '<a href="' . $_SERVER['PHP_SELF'] . '" class="btn">🔄 Aggiorna</a>';
echo '</div>';

?>

        <div style="margin-top: 30px; padding: 15px; background: #fff3cd; border-radius: 6px; color: #856404; text-align: center;">
            <strong>⚠️ ELIMINA QUESTO FILE</strong> dopo aver risolto:<br>
            <code>/public/view-last-error.php</code>
        </div>
    </div>
</body>
</html>
