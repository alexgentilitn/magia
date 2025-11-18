<?php
/**
 * Script per Abilitare/Disabilitare Debug Mode
 * Modifica temporaneamente APP_DEBUG nel file .env
 *
 * ELIMINA QUESTO FILE dopo aver risolto il problema!
 */

$envPath = __DIR__ . '/../.env';

if (!file_exists($envPath)) {
    die("Errore: file .env non trovato in: " . $envPath);
}

$action = $_GET['action'] ?? 'status';

?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debug Mode Toggle - MA.GIA DONNA</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            max-width: 700px;
            width: 100%;
            padding: 40px;
        }
        h1 {
            color: #764ba2;
            margin-bottom: 10px;
            font-size: 28px;
        }
        .status-box {
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid;
        }
        .status-box.success {
            background: #e8f5e9;
            border-color: #4CAF50;
            color: #2E7D32;
        }
        .status-box.error {
            background: #ffebee;
            border-color: #f44336;
            color: #c62828;
        }
        .status-box.warning {
            background: #fff3e0;
            border-color: #ff9800;
            color: #e65100;
        }
        .status-box.info {
            background: #e3f2fd;
            border-color: #2196F3;
            color: #1565C0;
        }
        .btn {
            display: inline-block;
            padding: 12px 30px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            margin: 5px;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(118, 75, 162, 0.3);
        }
        .btn-danger {
            background: linear-gradient(135deg, #f44336 0%, #d32f2f 100%);
        }
        .btn-success {
            background: linear-gradient(135deg, #4CAF50 0%, #388E3C 100%);
        }
        .code {
            background: #2d2d2d;
            color: #f8f8f2;
            padding: 15px;
            border-radius: 6px;
            font-family: 'Courier New', monospace;
            font-size: 14px;
            margin: 15px 0;
        }
        .warning-box {
            background: #fff3cd;
            border: 2px solid #ffc107;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
            color: #856404;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🐛 Debug Mode Toggle</h1>
        <p style="color: #666; margin-bottom: 30px;">Abilita/Disabilita la modalità debug</p>

<?php

if ($action === 'enable') {
    // ABILITA DEBUG
    $envContent = file_get_contents($envPath);
    $newContent = preg_replace('/^APP_DEBUG=false/m', 'APP_DEBUG=true', $envContent);

    if ($envContent === $newContent) {
        // Prova anche con spazi
        $newContent = preg_replace('/^APP_DEBUG\s*=\s*false/m', 'APP_DEBUG=true', $envContent);
    }

    if (file_put_contents($envPath, $newContent)) {
        echo '<div class="status-box success">';
        echo '<strong>✅ Debug Mode ABILITATO!</strong><br>';
        echo 'Gli errori verranno ora mostrati a video.';
        echo '</div>';

        echo '<div class="status-box info">';
        echo '<strong>Prossimi passi:</strong><br>';
        echo '1. Vai su: <strong>Admin → Clienti → [Seleziona cliente] → Pesate → Nuova Pesata</strong><br>';
        echo '2. Compila i dati obbligatori (data e peso)<br>';
        echo '3. Clicca "Salva Pesata"<br>';
        echo '4. <strong>Ora vedrai chiaramente</strong> se ci sono errori o se il salvataggio è riuscito';
        echo '</div>';

        echo '<div class="warning-box">';
        echo '<strong>⚠️ IMPORTANTE:</strong><br>';
        echo '- Il debug mode espone informazioni sensibili<br>';
        echo '- Usalo SOLO per diagnosticare il problema<br>';
        echo '- DISATTIVALO non appena hai capito il problema';
        echo '</div>';

        echo '<p style="text-align: center; margin-top: 30px;">';
        echo '<a href="/magia/public/admin/clienti" class="btn">👥 Vai ai Clienti</a> ';
        echo '<a href="?action=status" class="btn">🔄 Verifica Stato</a> ';
        echo '<a href="?action=disable" class="btn btn-danger">❌ Disattiva Debug</a>';
        echo '</p>';

    } else {
        echo '<div class="status-box error">';
        echo '<strong>❌ Errore:</strong> Impossibile scrivere il file .env<br>';
        echo 'Verifica i permessi del file.';
        echo '</div>';
    }

} elseif ($action === 'disable') {
    // DISABILITA DEBUG
    $envContent = file_get_contents($envPath);
    $newContent = preg_replace('/^APP_DEBUG=true/m', 'APP_DEBUG=false', $envContent);

    if ($envContent === $newContent) {
        $newContent = preg_replace('/^APP_DEBUG\s*=\s*true/m', 'APP_DEBUG=false', $envContent);
    }

    if (file_put_contents($envPath, $newContent)) {
        echo '<div class="status-box success">';
        echo '<strong>✅ Debug Mode DISATTIVATO!</strong><br>';
        echo 'L\'applicazione è tornata in modalità produzione sicura.';
        echo '</div>';

        echo '<p style="text-align: center; margin-top: 30px;">';
        echo '<a href="/magia/public/admin/dashboard" class="btn">← Dashboard</a> ';
        echo '<a href="?action=status" class="btn">🔄 Verifica Stato</a>';
        echo '</p>';

    } else {
        echo '<div class="status-box error">';
        echo '<strong>❌ Errore:</strong> Impossibile scrivere il file .env';
        echo '</div>';
    }

} else {
    // MOSTRA STATO ATTUALE
    $envContent = file_get_contents($envPath);

    if (preg_match('/^APP_DEBUG\s*=\s*true/m', $envContent)) {
        echo '<div class="status-box success">';
        echo '<strong>🟢 Debug Mode: ATTIVO</strong><br>';
        echo 'Gli errori vengono mostrati a video.';
        echo '</div>';

        echo '<div class="code">APP_DEBUG=true</div>';

        echo '<p style="text-align: center; margin-top: 30px;">';
        echo '<a href="/magia/public/admin/clienti" class="btn">👥 Vai ai Clienti</a> ';
        echo '<a href="?action=disable" class="btn btn-danger">❌ Disattiva Debug</a>';
        echo '</p>';

    } else {
        echo '<div class="status-box warning">';
        echo '<strong>🔴 Debug Mode: DISATTIVO</strong><br>';
        echo 'Gli errori NON vengono mostrati all\'utente.';
        echo '</div>';

        echo '<div class="code">APP_DEBUG=false</div>';

        echo '<div class="status-box info">';
        echo '<strong>Problema:</strong><br>';
        echo 'Quando provi a salvare una pesata, se ci sono errori non li vedi!<br><br>';
        echo '<strong>Soluzione:</strong><br>';
        echo 'Abilita temporaneamente il debug mode per vedere cosa sta succedendo.';
        echo '</div>';

        echo '<p style="text-align: center; margin-top: 30px;">';
        echo '<a href="?action=enable" class="btn btn-success">✅ Abilita Debug Mode</a>';
        echo '</p>';
    }
}

?>

        <div style="margin-top: 30px; padding: 15px; background: #fff3cd; border-radius: 6px; color: #856404; text-align: center;">
            <strong>⚠️ ELIMINA QUESTO FILE</strong> dopo aver risolto il problema:<br>
            <code>/public/enable-debug.php</code>
        </div>
    </div>
</body>
</html>
