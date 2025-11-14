<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup Impostazioni Sistema</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
            min-height: 100vh;
        }
        .container { max-width: 1000px; margin: 0 auto; }
        .card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        h1 { color: #764ba2; margin-bottom: 10px; }
        h2 { color: #333; margin: 20px 0 10px; font-size: 18px; }
        .step {
            background: #f8f9fa;
            border-left: 4px solid #764ba2;
            padding: 15px;
            margin: 10px 0;
            border-radius: 4px;
        }
        .success { border-left-color: #10b981; background: #d1fae5; }
        .error { border-left-color: #ef4444; background: #fee2e2; }
        .warning { border-left-color: #f59e0b; background: #fef3c7; }
        .info { border-left-color: #3b82f6; background: #dbeafe; }
        pre {
            background: #1f2937;
            color: #10b981;
            padding: 15px;
            border-radius: 5px;
            overflow-x: auto;
            font-size: 12px;
            margin: 10px 0;
        }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #764ba2; color: white; font-weight: 600; }
        tr:hover { background: #f9f9f9; }
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
        .badge-info { background: #dbeafe; color: #1e40af; }
        code {
            background: #f3f4f6;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 12px;
        }
    </style>
</head>
<body>

<?php
// Connessione database - valori di default
$host = 'localhost';
$dbname = 'progetti_ai';
$username = 'root';
$password = '';

// Funzione per leggere .env di Laravel
function parseEnvFile($filePath) {
    if (!file_exists($filePath)) {
        return [];
    }

    $env = [];
    $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lines as $line) {
        // Salta commenti
        if (strpos(trim($line), '#') === 0) {
            continue;
        }

        // Cerca chiave=valore
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);

            // Rimuovi virgolette se presenti
            if (preg_match('/^(["\'])(.*)\\1$/', $value, $matches)) {
                $value = $matches[2];
            }

            $env[$key] = $value;
        }
    }

    return $env;
}

// Leggi credenziali dal .env se esiste
$envFile = dirname(__DIR__) . '/.env';
$env = parseEnvFile($envFile);

if (!empty($env)) {
    if (isset($env['DB_HOST'])) $host = $env['DB_HOST'];
    if (isset($env['DB_DATABASE'])) $dbname = $env['DB_DATABASE'];
    if (isset($env['DB_USERNAME'])) $username = $env['DB_USERNAME'];
    if (isset($env['DB_PASSWORD'])) $password = $env['DB_PASSWORD'];
}

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo '<div class="container">';
    echo '<div class="card">';
    echo '<h1>🚀 Setup Impostazioni Sistema</h1>';
    echo '<p style="color: #666;">Script di installazione per il sistema di gestione impostazioni configurabili</p>';
    echo '</div>';

    // STEP 1: Verifica connessione
    echo '<div class="card">';
    echo '<h2>📡 Step 1: Verifica Connessione Database</h2>';
    echo '<div class="step success">';
    echo '<strong>✅ Connesso con successo!</strong><br>';
    echo "Database: <code>$dbname</code> su <code>$host</code>";
    echo '</div>';
    echo '</div>';

    // STEP 2: Crea tabella se non esiste
    echo '<div class="card">';
    echo '<h2>🗄️ Step 2: Creazione Tabella</h2>';

    $tableExists = $pdo->query("SHOW TABLES LIKE 'impostazioni_sistema'")->rowCount() > 0;

    if ($tableExists) {
        echo '<div class="step warning">';
        echo '<strong>⚠️ Tabella già esistente</strong><br>';
        echo 'La tabella <code>impostazioni_sistema</code> esiste già. Procedo con i dati.';
        echo '</div>';
    } else {
        echo '<div class="step info">';
        echo '<strong>🔨 Creazione tabella...</strong>';
        echo '</div>';

        $createTable = "CREATE TABLE impostazioni_sistema (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            categoria VARCHAR(50) NOT NULL COMMENT 'Categoria: tipologia_lezione, stato_lezione, ecc.',
            chiave VARCHAR(50) NOT NULL COMMENT 'Chiave univoca (es: individuale, confermata)',
            etichetta VARCHAR(100) NOT NULL COMMENT 'Nome visualizzato (es: Lezione Individuale)',
            descrizione TEXT NULL COMMENT 'Descrizione opzionale',
            colore VARCHAR(7) NULL COMMENT 'Colore esadecimale per badge (#764ba2)',
            icona VARCHAR(50) NULL COMMENT 'Classe icona FontAwesome (fa-user)',
            ordinamento INT UNSIGNED DEFAULT 0 COMMENT 'Ordine visualizzazione (0 = primo)',
            attivo TINYINT(1) DEFAULT 1 COMMENT 'Se disattivato non appare nelle select',
            di_sistema TINYINT(1) DEFAULT 0 COMMENT 'Se true, non può essere eliminato',
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL,
            UNIQUE KEY unique_categoria_chiave (categoria, chiave),
            INDEX idx_categoria (categoria),
            INDEX idx_attivo (attivo),
            INDEX idx_ordinamento (ordinamento)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Configurazioni di sistema (tipologie, stati, ecc.)';";

        $pdo->exec($createTable);

        echo '<div class="step success">';
        echo '<strong>✅ Tabella creata con successo!</strong>';
        echo '</div>';
    }
    echo '</div>';

    // STEP 3: Controlla dati esistenti
    echo '<div class="card">';
    echo '<h2>📊 Step 3: Verifica Dati Esistenti</h2>';

    $count = $pdo->query("SELECT COUNT(*) FROM impostazioni_sistema")->fetchColumn();

    if ($count > 0) {
        echo '<div class="step warning">';
        echo "<strong>⚠️ Trovati $count record esistenti</strong><br>";
        echo 'I dati esistenti NON saranno sovrascritti. Solo i record mancanti saranno aggiunti.';
        echo '</div>';
    } else {
        echo '<div class="step info">';
        echo '<strong>📋 Nessun dato presente</strong><br>';
        echo 'Procedo con l\'inserimento completo dei dati predefiniti.';
        echo '</div>';
    }
    echo '</div>';

    // STEP 4: Inserimento dati
    echo '<div class="card">';
    echo '<h2>💾 Step 4: Inserimento Dati Predefiniti</h2>';

    $dati = [
        // Tipologie Lezione
        ['tipologia_lezione', 'individuale', 'Lezione Individuale', 'Lezione uno-a-uno con il professionista', '#3b82f6', 'fa-user', 1, 1, 1],
        ['tipologia_lezione', 'gruppo', 'Lezione di Gruppo', 'Lezione con più partecipanti', '#10b981', 'fa-users', 2, 1, 1],
        ['tipologia_lezione', 'online', 'Lezione Online', 'Lezione svolta completamente da remoto', '#8b5cf6', 'fa-video', 3, 1, 1],
        ['tipologia_lezione', 'ibrida', 'Lezione Ibrida (Presenza + Online)', 'Alcuni in presenza, altri da remoto', '#f59e0b', 'fa-network-wired', 4, 1, 1],

        // Stati Lezione
        ['stato_lezione', 'programmata', 'Programmata', 'Lezione pianificata ma non ancora confermata', '#94a3b8', 'fa-calendar-plus', 1, 1, 1],
        ['stato_lezione', 'confermata', 'Confermata', 'Lezione confermata e pronta', '#3b82f6', 'fa-calendar-check', 2, 1, 1],
        ['stato_lezione', 'in_corso', 'In Corso', 'Lezione attualmente in svolgimento', '#f59e0b', 'fa-circle-play', 3, 1, 1],
        ['stato_lezione', 'completata', 'Completata', 'Lezione terminata con successo', '#10b981', 'fa-circle-check', 4, 1, 1],
        ['stato_lezione', 'cancellata', 'Cancellata', 'Lezione annullata', '#ef4444', 'fa-circle-xmark', 5, 1, 1],
        ['stato_lezione', 'rinviata', 'Rinviata', 'Lezione posticipata a data da definire', '#8b5cf6', 'fa-clock', 6, 1, 1],

        // Frequenze Ricorrenza
        ['frequenza_ricorrenza', 'giornaliera', 'Ogni giorno', 'Ripeti la lezione ogni giorno', '#3b82f6', 'fa-calendar-day', 1, 1, 1],
        ['frequenza_ricorrenza', 'settimanale', 'Ogni settimana', 'Ripeti la lezione ogni settimana', '#10b981', 'fa-calendar-week', 2, 1, 1],
        ['frequenza_ricorrenza', 'bisettimanale', 'Ogni 2 settimane', 'Ripeti la lezione ogni due settimane', '#f59e0b', 'fa-calendar-days', 3, 1, 1],
        ['frequenza_ricorrenza', 'mensile', 'Ogni mese', 'Ripeti la lezione ogni mese', '#8b5cf6', 'fa-calendar', 4, 1, 1],
    ];

    $inseriti = 0;
    $saltati = 0;

    $stmt = $pdo->prepare("
        INSERT INTO impostazioni_sistema
        (categoria, chiave, etichetta, descrizione, colore, icona, ordinamento, attivo, di_sistema, created_at, updated_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
        ON DUPLICATE KEY UPDATE id=id
    ");

    foreach ($dati as $record) {
        $categoria = $record[0];
        $chiave = $record[1];

        // Verifica se esiste già
        $exists = $pdo->prepare("SELECT COUNT(*) FROM impostazioni_sistema WHERE categoria = ? AND chiave = ?");
        $exists->execute([$categoria, $chiave]);

        if ($exists->fetchColumn() > 0) {
            $saltati++;
        } else {
            $stmt->execute($record);
            $inseriti++;
        }
    }

    echo '<div class="step success">';
    echo "<strong>✅ Inserimento completato!</strong><br>";
    echo "• <span class='badge badge-success'>Inseriti: $inseriti</span><br>";
    echo "• <span class='badge badge-info'>Saltati (già presenti): $saltati</span>";
    echo '</div>';
    echo '</div>';

    // STEP 5: Riepilogo finale
    echo '<div class="card">';
    echo '<h2>📋 Step 5: Riepilogo Dati Inseriti</h2>';

    $categorie = [
        'tipologia_lezione' => 'Tipologie Lezione',
        'stato_lezione' => 'Stati Lezione',
        'frequenza_ricorrenza' => 'Frequenze Ricorrenza'
    ];

    foreach ($categorie as $catKey => $catNome) {
        $stmt = $pdo->prepare("
            SELECT chiave, etichetta, descrizione, colore, icona, attivo
            FROM impostazioni_sistema
            WHERE categoria = ?
            ORDER BY ordinamento
        ");
        $stmt->execute([$catKey]);
        $records = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo "<h3 style='color: #333; margin-top: 20px;'>$catNome</h3>";
        echo '<table>';
        echo '<tr><th>Chiave</th><th>Etichetta</th><th>Descrizione</th><th>Colore</th><th>Icona</th><th>Attivo</th></tr>';

        foreach ($records as $r) {
            $colorBadge = $r['colore'] ? "<span style='display:inline-block; width:20px; height:20px; background:{$r['colore']}; border-radius:3px; border:1px solid #ddd;'></span> {$r['colore']}" : '-';
            $iconaBadge = $r['icona'] ? "<i class='fas {$r['icona']}'></i> {$r['icona']}" : '-';
            $attivoBadge = $r['attivo'] ? "<span class='badge badge-success'>SÌ</span>" : "<span class='badge badge-error'>NO</span>";

            echo "<tr>";
            echo "<td><code>{$r['chiave']}</code></td>";
            echo "<td><strong>{$r['etichetta']}</strong></td>";
            echo "<td style='font-size:11px; color:#666;'>{$r['descrizione']}</td>";
            echo "<td>$colorBadge</td>";
            echo "<td>$iconaBadge</td>";
            echo "<td>$attivoBadge</td>";
            echo "</tr>";
        }

        echo '</table>';
    }
    echo '</div>';

    // STEP 6: Istruzioni finali
    echo '<div class="card" style="background: #d1fae5; border-left: 4px solid #10b981;">';
    echo '<h2 style="color: #065f46;">🎉 Installazione Completata!</h2>';
    echo '<div style="color: #047857; line-height: 1.8;">';
    echo '<p><strong>✅ Il sistema di impostazioni è ora attivo!</strong></p>';
    echo '<p style="margin-top: 15px;">Puoi ora:</p>';
    echo '<ul style="margin-left: 20px; margin-top: 10px;">';
    echo '<li>Accedere al pannello admin → <strong>Impostazioni Sistema</strong></li>';
    echo '<li>Aggiungere nuove tipologie, stati o frequenze personalizzate</li>';
    echo '<li>Modificare colori e icone delle opzioni esistenti</li>';
    echo '<li>Attivare/disattivare opzioni senza eliminarle</li>';
    echo '<li>Riordinare le opzioni come preferisci</li>';
    echo '</ul>';
    echo '<p style="margin-top: 20px; padding: 15px; background: white; border-radius: 5px;">';
    echo '<strong>🔒 Sicurezza:</strong> Puoi eliminare questo file dopo l\'installazione:<br>';
    echo '<code>rm public/setup-impostazioni.php</code>';
    echo '</p>';
    echo '</div>';
    echo '</div>';

    echo '</div>'; // chiudi container

} catch(PDOException $e) {
    echo '<div class="container"><div class="card" style="border-left: 4px solid #ef4444;">';
    echo '<h1>❌ Errore Database</h1>';
    echo '<div class="step error">';
    echo '<strong>Impossibile connettersi al database</strong><br>';
    echo '<p style="margin-top: 10px;">Dettagli errore:</p>';
    echo '<pre style="background: #fee2e2; color: #991b1b;">' . htmlspecialchars($e->getMessage()) . '</pre>';
    echo '<p style="margin-top: 10px;"><strong>Verifica:</strong></p>';
    echo '<ul style="margin-left: 20px; color: #7f1d1d;">';
    echo "<li>Host: <code>$host</code></li>";
    echo "<li>Database: <code>$dbname</code></li>";
    echo "<li>Username: <code>$username</code></li>";
    echo '<li>Password: ***</li>';
    echo '</ul>';
    echo '</div>';
    echo '</div></div>';
}
?>

<!-- Link a FontAwesome per visualizzare le icone -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

</body>
</html>
