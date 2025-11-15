<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Importazione Tabelle Schede Allenamento - MA.GIA DONNA</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #e91e63 0%, #9c27b0 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            font-size: 28px;
            margin-bottom: 10px;
        }
        .header p {
            opacity: 0.9;
            font-size: 14px;
        }
        .content {
            padding: 30px;
        }
        .step {
            margin-bottom: 25px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 10px;
            border-left: 4px solid #e91e63;
        }
        .step h3 {
            color: #9c27b0;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .step-number {
            background: #e91e63;
            color: white;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 14px;
        }
        .success {
            background: #d4edda;
            border-color: #28a745;
            color: #155724;
            padding: 15px;
            border-radius: 8px;
            margin: 10px 0;
        }
        .error {
            background: #f8d7da;
            border-color: #dc3545;
            color: #721c24;
            padding: 15px;
            border-radius: 8px;
            margin: 10px 0;
        }
        .warning {
            background: #fff3cd;
            border-color: #ffc107;
            color: #856404;
            padding: 15px;
            border-radius: 8px;
            margin: 10px 0;
        }
        .info {
            background: #d1ecf1;
            border-color: #17a2b8;
            color: #0c5460;
            padding: 15px;
            border-radius: 8px;
            margin: 10px 0;
        }
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin: 20px 0;
        }
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
        }
        .stat-card .number {
            font-size: 36px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .stat-card .label {
            font-size: 12px;
            opacity: 0.9;
            text-transform: uppercase;
        }
        .delete-warning {
            background: #dc3545;
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-top: 30px;
            text-align: center;
            font-weight: bold;
        }
        .sql-preview {
            background: #2d2d2d;
            color: #f8f8f2;
            padding: 15px;
            border-radius: 8px;
            font-family: 'Courier New', monospace;
            font-size: 12px;
            overflow-x: auto;
            margin: 10px 0;
            max-height: 200px;
            overflow-y: auto;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h1>🏋️ Importazione Tabelle Schede Allenamento</h1>
        <p>Sistema di gestione programmi personalizzati - MA.GIA DONNA</p>
    </div>

    <div class="content">
        <?php
        // Configurazione database
        $db_host = 'localhost';
        $db_user = 'agstudiodiital_agstudiomagia';
        $db_pass = '$Magia2015!';
        $db_name = 'agstudiodiital_magia';
        $db_port = 3306;

        $start_time = microtime(true);
        $errors = [];
        $successes = [];

        // STEP 1: Connessione al database
        echo '<div class="step">';
        echo '<h3><span class="step-number">1</span> Connessione al Database</h3>';

        $conn = @mysqli_connect($db_host, $db_user, $db_pass, $db_name, $db_port);

        if (!$conn) {
            echo '<div class="error">';
            echo '<strong>❌ Errore di connessione:</strong><br>';
            echo htmlspecialchars(mysqli_connect_error());
            echo '</div>';
            echo '</div></div></body></html>';
            exit;
        }

        mysqli_set_charset($conn, "utf8mb4");

        echo '<div class="success">';
        echo '<strong>✅ Connessione riuscita!</strong><br>';
        echo 'Database: <strong>' . htmlspecialchars($db_name) . '</strong><br>';
        echo 'Server: <strong>' . htmlspecialchars($db_host) . ':' . $db_port . '</strong>';
        echo '</div>';
        echo '</div>';

        // STEP 2: Creazione tabella schede_allenamento
        echo '<div class="step">';
        echo '<h3><span class="step-number">2</span> Creazione Tabella "schede_allenamento"</h3>';

        $sql_schede = "CREATE TABLE IF NOT EXISTS `schede_allenamento` (
            `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `cliente_id` BIGINT UNSIGNED NOT NULL,
            `professionista_id` BIGINT UNSIGNED NULL,
            `nome_scheda` VARCHAR(255) NOT NULL,
            `descrizione` TEXT NULL,
            `obiettivi` TEXT NULL,
            `data_inizio` DATE NULL,
            `data_fine` DATE NULL,
            `durata_settimane` INT NULL,
            `note_generali` TEXT NULL,
            `note_alimentazione` TEXT NULL,
            `consigli_professionista` TEXT NULL,
            `stato` ENUM('bozza', 'attiva', 'completata', 'archiviata') DEFAULT 'bozza',
            `inviata_email` TINYINT(1) DEFAULT 0,
            `data_invio_email` TIMESTAMP NULL,
            `pdf_path` VARCHAR(255) NULL,
            `created_at` TIMESTAMP NULL,
            `updated_at` TIMESTAMP NULL,
            `deleted_at` TIMESTAMP NULL,
            CONSTRAINT `fk_schede_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `clienti` (`id`) ON DELETE CASCADE,
            CONSTRAINT `fk_schede_professionista` FOREIGN KEY (`professionista_id`) REFERENCES `utenti` (`id`) ON DELETE SET NULL,
            INDEX `idx_schede_cliente` (`cliente_id`),
            INDEX `idx_schede_professionista` (`professionista_id`),
            INDEX `idx_schede_stato` (`stato`),
            INDEX `idx_schede_data_inizio` (`data_inizio`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

        $result_schede = mysqli_query($conn, $sql_schede);

        if ($result_schede) {
            echo '<div class="success">';
            echo '<strong>✅ Tabella "schede_allenamento" creata con successo!</strong><br>';
            echo 'Campi: 17 colonne + relazioni con clienti e utenti';
            echo '</div>';
            $successes[] = 'schede_allenamento';
        } else {
            $error_msg = mysqli_error($conn);
            if (strpos($error_msg, 'already exists') !== false) {
                echo '<div class="warning">';
                echo '<strong>⚠️ Tabella "schede_allenamento" già esistente (saltata)</strong>';
                echo '</div>';
            } else {
                echo '<div class="error">';
                echo '<strong>❌ Errore creazione tabella:</strong><br>';
                echo htmlspecialchars($error_msg);
                echo '</div>';
                $errors[] = ['table' => 'schede_allenamento', 'error' => $error_msg];
            }
        }

        echo '</div>';

        // STEP 3: Creazione tabella scheda_esercizi
        echo '<div class="step">';
        echo '<h3><span class="step-number">3</span> Creazione Tabella "scheda_esercizi"</h3>';

        $sql_esercizi = "CREATE TABLE IF NOT EXISTS `scheda_esercizi` (
            `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `scheda_allenamento_id` BIGINT UNSIGNED NOT NULL,
            `giorno_settimana` ENUM('Lunedì', 'Martedì', 'Mercoledì', 'Giovedì', 'Venerdì', 'Sabato', 'Domenica', 'Riposo') NOT NULL,
            `ordine` INT DEFAULT 0,
            `nome_esercizio` VARCHAR(255) NOT NULL,
            `descrizione` TEXT NULL,
            `istruzioni_esecuzione` TEXT NULL,
            `serie` INT NULL,
            `ripetizioni` VARCHAR(255) NULL,
            `recupero_secondi` INT NULL,
            `peso_suggerito` VARCHAR(255) NULL,
            `durata_minuti` INT NULL,
            `intensita` VARCHAR(255) NULL,
            `note_esecuzione` TEXT NULL,
            `varianti` TEXT NULL,
            `video_url` VARCHAR(255) NULL,
            `immagine_url` VARCHAR(255) NULL,
            `categoria` ENUM('forza', 'cardio', 'stretching', 'mobilità', 'altro') DEFAULT 'forza',
            `muscoli_target` JSON NULL,
            `created_at` TIMESTAMP NULL,
            `updated_at` TIMESTAMP NULL,
            CONSTRAINT `fk_esercizi_scheda` FOREIGN KEY (`scheda_allenamento_id`) REFERENCES `schede_allenamento` (`id`) ON DELETE CASCADE,
            INDEX `idx_esercizi_scheda` (`scheda_allenamento_id`),
            INDEX `idx_esercizi_giorno` (`giorno_settimana`),
            INDEX `idx_esercizi_ordine` (`ordine`),
            INDEX `idx_esercizi_categoria` (`categoria`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

        $result_esercizi = mysqli_query($conn, $sql_esercizi);

        if ($result_esercizi) {
            echo '<div class="success">';
            echo '<strong>✅ Tabella "scheda_esercizi" creata con successo!</strong><br>';
            echo 'Campi: 19 colonne + relazione con schede_allenamento';
            echo '</div>';
            $successes[] = 'scheda_esercizi';
        } else {
            $error_msg = mysqli_error($conn);
            if (strpos($error_msg, 'already exists') !== false) {
                echo '<div class="warning">';
                echo '<strong>⚠️ Tabella "scheda_esercizi" già esistente (saltata)</strong>';
                echo '</div>';
            } else {
                echo '<div class="error">';
                echo '<strong>❌ Errore creazione tabella:</strong><br>';
                echo htmlspecialchars($error_msg);
                echo '</div>';
                $errors[] = ['table' => 'scheda_esercizi', 'error' => $error_msg];
            }
        }

        echo '</div>';

        // STEP 4: Riepilogo
        echo '<div class="step">';
        echo '<h3><span class="step-number">4</span> Riepilogo Operazioni</h3>';

        $end_time = microtime(true);
        $execution_time = round($end_time - $start_time, 2);

        echo '<div class="stats">';
        echo '<div class="stat-card">';
        echo '<div class="number">2</div>';
        echo '<div class="label">Tabelle Totali</div>';
        echo '</div>';

        echo '<div class="stat-card">';
        echo '<div class="number">' . count($successes) . '</div>';
        echo '<div class="label">Create</div>';
        echo '</div>';

        echo '<div class="stat-card">';
        echo '<div class="number">' . count($errors) . '</div>';
        echo '<div class="label">Errori</div>';
        echo '</div>';

        echo '<div class="stat-card">';
        echo '<div class="number">' . $execution_time . 's</div>';
        echo '<div class="label">Tempo Esecuzione</div>';
        echo '</div>';
        echo '</div>';

        if (count($successes) > 0) {
            echo '<div class="success">';
            echo '<strong>🎉 Operazione completata!</strong><br>';
            echo 'Sono state create <strong>' . count($successes) . '</strong> nuove tabelle per il sistema di schede allenamento personalizzate.';
            echo '</div>';
        }

        if (count($errors) > 0) {
            echo '<div class="error">';
            echo '<strong>⚠️ Attenzione:</strong> si sono verificati ' . count($errors) . ' errori durante l\'esecuzione.<br>';
            echo 'Controlla i log sopra per i dettagli.';
            echo '</div>';
        }

        echo '</div>';

        // STEP 5: Dettaglio struttura
        echo '<div class="step">';
        echo '<h3><span class="step-number">5</span> Struttura Database</h3>';

        echo '<h4 style="margin-top: 20px; color: #9c27b0;">📋 Tabella: schede_allenamento</h4>';
        echo '<div class="info">';
        echo '<strong>Scopo:</strong> Gestisce le schede di allenamento personalizzate per ogni cliente<br><br>';
        echo '<strong>Campi principali:</strong><br>';
        echo '• cliente_id, professionista_id (relazioni)<br>';
        echo '• nome_scheda, descrizione, obiettivi<br>';
        echo '• data_inizio, data_fine, durata_settimane<br>';
        echo '• note_generali, note_alimentazione, consigli_professionista<br>';
        echo '• stato (bozza/attiva/completata/archiviata)<br>';
        echo '• inviata_email, data_invio_email, pdf_path<br>';
        echo '</div>';

        echo '<h4 style="margin-top: 20px; color: #9c27b0;">🏋️ Tabella: scheda_esercizi</h4>';
        echo '<div class="info">';
        echo '<strong>Scopo:</strong> Memorizza gli esercizi di ogni scheda organizzati per giorno<br><br>';
        echo '<strong>Campi principali:</strong><br>';
        echo '• scheda_allenamento_id (relazione)<br>';
        echo '• giorno_settimana, ordine<br>';
        echo '• nome_esercizio, descrizione, istruzioni_esecuzione<br>';
        echo '• serie, ripetizioni, recupero_secondi<br>';
        echo '• peso_suggerito, durata_minuti, intensita<br>';
        echo '• categoria (forza/cardio/stretching/mobilità)<br>';
        echo '• video_url, immagine_url, muscoli_target<br>';
        echo '</div>';

        echo '</div>';

        // STEP 6: Prossimi passi
        echo '<div class="step">';
        echo '<h3><span class="step-number">6</span> Prossimi Passi</h3>';
        echo '<div class="info">';
        echo '<strong>📌 Cosa fare ora:</strong><br><br>';
        echo '1️⃣ <strong>Accedi al pannello amministratore:</strong><br>';
        echo '&nbsp;&nbsp;&nbsp;&nbsp;<a href="/admin/schede-allenamento" target="_blank">Gestione Schede Allenamento</a><br><br>';
        echo '2️⃣ <strong>Crea la prima scheda personalizzata:</strong><br>';
        echo '&nbsp;&nbsp;&nbsp;&nbsp;• Seleziona una cliente<br>';
        echo '&nbsp;&nbsp;&nbsp;&nbsp;• Inserisci obiettivi e durata<br>';
        echo '&nbsp;&nbsp;&nbsp;&nbsp;• Aggiungi esercizi per ogni giorno<br><br>';
        echo '3️⃣ <strong>Genera PDF e invia via email:</strong><br>';
        echo '&nbsp;&nbsp;&nbsp;&nbsp;• Il PDF verrà generato automaticamente<br>';
        echo '&nbsp;&nbsp;&nbsp;&nbsp;• Email inviata alla cliente con programma allegato<br><br>';
        echo '4️⃣ <strong>Verifica il database:</strong><br>';
        echo '&nbsp;&nbsp;&nbsp;&nbsp;Esegui: <code>SHOW TABLES LIKE \'schede%\';</code><br>';
        echo '</div>';
        echo '</div>';

        // Chiudi connessione
        mysqli_close($conn);
        ?>

        <div class="delete-warning">
            ⚠️ IMPORTANTE: Elimina questo file immediatamente dopo l'esecuzione!<br>
            <small>Per sicurezza, rimuovi il file: <code>public/import-schede-allenamento.php</code></small>
        </div>
    </div>
</div>

</body>
</html>
