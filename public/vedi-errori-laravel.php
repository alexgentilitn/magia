<?php
/**
 * VISUALIZZA ERRORI LARAVEL
 *
 * URL: https://www.agstudio.digital/magia/public/vedi-errori-laravel.php?secret=magia2024
 *
 * Legge gli ultimi errori dal log Laravel per debug
 */

$secret = $_GET['secret'] ?? '';
if ($secret !== 'magia2024') {
    die('Accesso negato. Usa: ?secret=magia2024');
}

$logFile = __DIR__ . '/../storage/logs/laravel.log';
$numLines = isset($_GET['lines']) ? (int)$_GET['lines'] : 100;

?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Errori Laravel - MA.GIA DONNA</title>
    <style>
        body {
            font-family: 'Courier New', monospace;
            background: #1e1e1e;
            color: #d4d4d4;
            padding: 20px;
            margin: 0;
        }
        .container {
            max-width: 1400px;
            margin: 0 auto;
            background: #252526;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.5);
        }
        h1 {
            color: #4ec9b0;
            border-bottom: 2px solid #4ec9b0;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .info {
            background: #264f78;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            color: #d4d4d4;
        }
        .controls {
            margin-bottom: 20px;
        }
        .controls a {
            display: inline-block;
            padding: 10px 20px;
            background: #0e639c;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-right: 10px;
            transition: background 0.3s;
        }
        .controls a:hover {
            background: #1177bb;
        }
        .log-container {
            background: #1e1e1e;
            padding: 20px;
            border-radius: 5px;
            overflow-x: auto;
            max-height: 80vh;
            border: 1px solid #3e3e42;
        }
        .log-line {
            margin: 5px 0;
            padding: 5px;
            border-left: 3px solid transparent;
        }
        .log-line.error {
            color: #f48771;
            border-left-color: #f48771;
            background: rgba(244, 135, 113, 0.1);
        }
        .log-line.warning {
            color: #dcdcaa;
            border-left-color: #dcdcaa;
            background: rgba(220, 220, 170, 0.1);
        }
        .log-line.info {
            color: #4fc1ff;
            border-left-color: #4fc1ff;
        }
        .log-line.debug {
            color: #858585;
        }
        .timestamp {
            color: #608b4e;
            font-weight: bold;
        }
        .stack-trace {
            color: #ce9178;
            margin-left: 20px;
            font-size: 0.9em;
        }
        .highlight {
            background: rgba(255, 255, 0, 0.2);
            padding: 2px 4px;
        }
        .error-summary {
            background: #f48771;
            color: #1e1e1e;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-weight: bold;
        }
        .success-summary {
            background: #4ec9b0;
            color: #1e1e1e;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>📋 Errori Laravel - MA.GIA DONNA</h1>

        <div class="info">
            <strong>File Log:</strong> <?= $logFile ?><br>
            <strong>Data/Ora:</strong> <?= date('d/m/Y H:i:s') ?><br>
            <strong>Righe mostrate:</strong> Ultime <?= $numLines ?>
        </div>

        <div class="controls">
            <a href="?secret=magia2024&lines=50">📄 50 Righe</a>
            <a href="?secret=magia2024&lines=100">📄 100 Righe</a>
            <a href="?secret=magia2024&lines=200">📄 200 Righe</a>
            <a href="?secret=magia2024&lines=500">📄 500 Righe</a>
            <a href="?secret=magia2024&lines=1000">📄 1000 Righe</a>
        </div>

        <?php
        if (!file_exists($logFile)) {
            echo '<div class="success-summary">✅ Nessun file di log presente - Nessun errore registrato!</div>';
            echo '<div class="info">Il file laravel.log non esiste ancora. Questo significa che non ci sono stati errori.</div>';
        } else {
            $fileSize = filesize($logFile);
            $fileSizeMB = round($fileSize / 1024 / 1024, 2);

            echo '<div class="info">';
            echo '<strong>Dimensione file:</strong> ' . number_format($fileSize) . ' bytes (' . $fileSizeMB . ' MB)<br>';
            echo '<strong>Ultima modifica:</strong> ' . date('d/m/Y H:i:s', filemtime($logFile));
            echo '</div>';

            // Leggi ultime righe
            $lines = [];
            $file = new SplFileObject($logFile);
            $file->seek(PHP_INT_MAX);
            $totalLines = $file->key() + 1;

            $startLine = max(0, $totalLines - $numLines);

            $file->seek($startLine);
            while (!$file->eof()) {
                $lines[] = $file->current();
                $file->next();
            }

            // Conta errori recenti
            $errorCount = 0;
            $warningCount = 0;
            $lastErrors = [];

            foreach ($lines as $line) {
                if (stripos($line, '.ERROR:') !== false || stripos($line, 'Exception') !== false) {
                    $errorCount++;
                    if (count($lastErrors) < 3) {
                        $lastErrors[] = $line;
                    }
                }
                if (stripos($line, '.WARNING:') !== false) {
                    $warningCount++;
                }
            }

            if ($errorCount > 0) {
                echo '<div class="error-summary">';
                echo '🚨 ERRORI TROVATI: ' . $errorCount . ' errori negli ultimi ' . $numLines . ' righe';
                echo '</div>';

                if (!empty($lastErrors)) {
                    echo '<div class="info">';
                    echo '<strong>Ultimi errori rilevati:</strong><br>';
                    foreach ($lastErrors as $err) {
                        $short = substr(strip_tags($err), 0, 200);
                        echo '<div style="margin: 5px 0; padding: 5px; background: rgba(244, 135, 113, 0.2);">';
                        echo htmlspecialchars($short);
                        if (strlen($err) > 200) echo '...';
                        echo '</div>';
                    }
                    echo '</div>';
                }
            } else {
                echo '<div class="success-summary">';
                echo '✅ Nessun errore critico negli ultimi ' . $numLines . ' righe';
                if ($warningCount > 0) {
                    echo ' (Warning: ' . $warningCount . ')';
                }
                echo '</div>';
            }

            // Mostra log
            echo '<div class="log-container">';

            foreach ($lines as $line) {
                $line = trim($line);
                if (empty($line)) continue;

                $class = 'log-line';

                if (stripos($line, '.ERROR:') !== false || stripos($line, 'Exception') !== false) {
                    $class .= ' error';
                } elseif (stripos($line, '.WARNING:') !== false) {
                    $class .= ' warning';
                } elseif (stripos($line, '.INFO:') !== false) {
                    $class .= ' info';
                } elseif (stripos($line, '.DEBUG:') !== false) {
                    $class .= ' debug';
                }

                // Evidenzia timestamp
                $line = preg_replace(
                    '/\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\]/',
                    '<span class="timestamp">[$1]</span>',
                    $line
                );

                // Evidenzia stack trace
                if (preg_match('/^#\d+/', $line)) {
                    $class .= ' stack-trace';
                }

                // Evidenzia keywords
                $keywords = ['ERROR', 'Exception', 'SQLSTATE', 'failed', 'Unable', 'Class not found'];
                foreach ($keywords as $keyword) {
                    $line = str_ireplace($keyword, '<span class="highlight">' . $keyword . '</span>', $line);
                }

                echo '<div class="' . $class . '">' . $line . '</div>';
            }

            echo '</div>';

            echo '<div class="info" style="margin-top: 20px;">';
            echo '<strong>📊 Statistiche:</strong><br>';
            echo 'Righe totali nel file: ' . number_format($totalLines) . '<br>';
            echo 'Righe mostrate: ' . count($lines) . '<br>';
            echo 'Errori trovati: ' . $errorCount . '<br>';
            echo 'Warning trovati: ' . $warningCount;
            echo '</div>';
        }
        ?>

        <div class="controls" style="margin-top: 20px;">
            <a href="diagnosi-completa-export.php?secret=magia2024">← Torna alla Diagnosi</a>
            <a href="/admin/report">Vai ai Report</a>
        </div>

        <div class="info" style="margin-top: 20px; font-size: 0.9em; opacity: 0.7;">
            <strong>File:</strong> /public/vedi-errori-laravel.php<br>
            <strong>Tip:</strong> Aggiungi <code>&lines=N</code> per vedere N righe
        </div>
    </div>
</body>
</html>
