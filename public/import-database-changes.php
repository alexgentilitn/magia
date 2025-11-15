<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Importazione Modifiche Database - MA.GIA DONNA</title>
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
        .sql-output {
            background: #2d2d2d;
            color: #f8f8f2;
            padding: 15px;
            border-radius: 8px;
            font-family: 'Courier New', monospace;
            font-size: 13px;
            overflow-x: auto;
            margin: 10px 0;
        }
        .field-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 10px;
            margin: 15px 0;
        }
        .field-item {
            background: white;
            padding: 10px;
            border-radius: 6px;
            border: 1px solid #ddd;
            font-size: 13px;
        }
        .field-item strong {
            color: #e91e63;
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
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h1>🚀 Importazione Modifiche Database</h1>
        <p>Sistema di aggiornamento diretto MySQL - MA.GIA DONNA</p>
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
        $skipped = [];

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
        echo 'Server: <strong>' . htmlspecialchars($db_host) . ':' . $db_port . '</strong><br>';
        echo 'Charset: <strong>utf8mb4</strong>';
        echo '</div>';
        echo '</div>';

        // STEP 2: Verifica tabella clienti
        echo '<div class="step">';
        echo '<h3><span class="step-number">2</span> Verifica Tabella Clienti</h3>';

        $result = mysqli_query($conn, "SHOW TABLES LIKE 'clienti'");
        if (mysqli_num_rows($result) === 0) {
            echo '<div class="error">';
            echo '<strong>❌ Tabella "clienti" non trovata!</strong><br>';
            echo 'Impossibile procedere con le modifiche.';
            echo '</div>';
            echo '</div></div></body></html>';
            mysqli_close($conn);
            exit;
        }

        echo '<div class="success">';
        echo '<strong>✅ Tabella "clienti" trovata!</strong>';
        echo '</div>';
        echo '</div>';

        // STEP 3: Esecuzione modifiche database
        echo '<div class="step">';
        echo '<h3><span class="step-number">3</span> Esecuzione Modifiche</h3>';

        // Array di tutte le modifiche da applicare
        $alterations = [
            // CONSENSI GDPR (10 campi)
            "ALTER TABLE clienti ADD COLUMN privacy_accettata TINYINT(1) DEFAULT 0 COMMENT 'Accettazione Privacy Policy'",
            "ALTER TABLE clienti ADD COLUMN privacy_accettata_at TIMESTAMP NULL COMMENT 'Data/ora accettazione privacy'",
            "ALTER TABLE clienti ADD COLUMN termini_accettati TINYINT(1) DEFAULT 0 COMMENT 'Accettazione Termini e Condizioni'",
            "ALTER TABLE clienti ADD COLUMN termini_accettati_at TIMESTAMP NULL COMMENT 'Data/ora accettazione termini'",
            "ALTER TABLE clienti ADD COLUMN marketing_accettato TINYINT(1) DEFAULT 0 COMMENT 'Consenso comunicazioni marketing'",
            "ALTER TABLE clienti ADD COLUMN marketing_accettato_at TIMESTAMP NULL COMMENT 'Data/ora consenso marketing'",
            "ALTER TABLE clienti ADD COLUMN consenso_dati_sensibili TINYINT(1) DEFAULT 0 COMMENT 'Consenso trattamento dati sensibili salute'",
            "ALTER TABLE clienti ADD COLUMN consenso_dati_sensibili_at TIMESTAMP NULL COMMENT 'Data/ora consenso dati sensibili'",
            "ALTER TABLE clienti ADD COLUMN ip_registrazione VARCHAR(45) NULL COMMENT 'Indirizzo IP al momento della registrazione'",
            "ALTER TABLE clienti ADD COLUMN note_consensi TEXT NULL COMMENT 'Note aggiuntive sui consensi'",

            // DATI ANAGRAFICI (3 campi)
            "ALTER TABLE clienti ADD COLUMN data_nascita DATE NULL COMMENT 'Data di nascita'",
            "ALTER TABLE clienti ADD COLUMN eta INT NULL COMMENT 'Età calcolata'",
            "ALTER TABLE clienti ADD COLUMN sesso ENUM('F', 'M', 'Altro') NULL COMMENT 'Sesso biologico'",

            // OBIETTIVI (2 campi)
            "ALTER TABLE clienti ADD COLUMN obiettivi_personali TEXT NULL COMMENT 'Obiettivi di fitness e benessere'",
            "ALTER TABLE clienti ADD COLUMN livello_attivita ENUM('sedentario', 'leggero', 'moderato', 'intenso', 'molto_intenso') NULL COMMENT 'Livello di attività fisica'",

            // DATI MEDICI (5 campi)
            "ALTER TABLE clienti ADD COLUMN note_mediche TEXT NULL COMMENT 'Note mediche e anamnesi'",
            "ALTER TABLE clienti ADD COLUMN allergie_intolleranze TEXT NULL COMMENT 'Allergie e intolleranze alimentari'",
            "ALTER TABLE clienti ADD COLUMN patologie TEXT NULL COMMENT 'Patologie e condizioni mediche'",
            "ALTER TABLE clienti ADD COLUMN farmaci_assunti TEXT NULL COMMENT 'Farmaci regolarmente assunti'",
            "ALTER TABLE clienti ADD COLUMN certificato_medico_presente TINYINT(1) DEFAULT 0 COMMENT 'Certificato medico sportivo presente'",

            // ALIMENTAZIONE (3 campi)
            "ALTER TABLE clienti ADD COLUMN preferenze_alimentari TEXT NULL COMMENT 'Preferenze e abitudini alimentari'",
            "ALTER TABLE clienti ADD COLUMN regime_alimentare ENUM('onnivoro', 'vegetariano', 'vegano', 'pescetariano', 'altro') NULL COMMENT 'Tipo di regime alimentare seguito'",
            "ALTER TABLE clienti ADD COLUMN cibi_da_evitare TEXT NULL COMMENT 'Cibi da evitare per scelta o salute'",

            // PARAMETRI CORPOREI (3 campi)
            "ALTER TABLE clienti ADD COLUMN peso DECIMAL(5,2) NULL COMMENT 'Peso corporeo in kg'",
            "ALTER TABLE clienti ADD COLUMN altezza DECIMAL(5,2) NULL COMMENT 'Altezza in cm'",
            "ALTER TABLE clienti ADD COLUMN bmi DECIMAL(4,2) NULL COMMENT 'Indice di massa corporea calcolato'",

            // CIRCONFERENZE (7 campi)
            "ALTER TABLE clienti ADD COLUMN circonferenza_vita DECIMAL(5,2) NULL COMMENT 'Circonferenza vita in cm'",
            "ALTER TABLE clienti ADD COLUMN circonferenza_fianchi DECIMAL(5,2) NULL COMMENT 'Circonferenza fianchi in cm'",
            "ALTER TABLE clienti ADD COLUMN circonferenza_petto DECIMAL(5,2) NULL COMMENT 'Circonferenza petto in cm'",
            "ALTER TABLE clienti ADD COLUMN circonferenza_braccio_dx DECIMAL(5,2) NULL COMMENT 'Circonferenza braccio destro in cm'",
            "ALTER TABLE clienti ADD COLUMN circonferenza_braccio_sx DECIMAL(5,2) NULL COMMENT 'Circonferenza braccio sinistro in cm'",
            "ALTER TABLE clienti ADD COLUMN circonferenza_coscia_dx DECIMAL(5,2) NULL COMMENT 'Circonferenza coscia destra in cm'",
            "ALTER TABLE clienti ADD COLUMN circonferenza_coscia_sx DECIMAL(5,2) NULL COMMENT 'Circonferenza coscia sinistra in cm'",

            // TIMESTAMP MISURAZIONI (2 campi)
            "ALTER TABLE clienti ADD COLUMN ultima_pesata TIMESTAMP NULL COMMENT 'Data/ora ultima pesata'",
            "ALTER TABLE clienti ADD COLUMN ultima_misurazione TIMESTAMP NULL COMMENT 'Data/ora ultima misurazione circonferenze'",

            // NOTE (1 campo)
            "ALTER TABLE clienti ADD COLUMN note_professionista TEXT NULL COMMENT 'Note riservate del professionista'"
        ];

        $total_fields = count($alterations);
        $executed = 0;

        foreach ($alterations as $index => $sql) {
            $field_number = $index + 1;

            // Estrai il nome del campo dalla query
            preg_match('/ADD COLUMN (\w+)/', $sql, $matches);
            $field_name = $matches[1] ?? "campo_{$field_number}";

            // Esegui la query
            $result = @mysqli_query($conn, $sql);

            if ($result) {
                $successes[] = $field_name;
                $executed++;
                echo '<div class="success">✅ <strong>' . htmlspecialchars($field_name) . '</strong> aggiunto con successo</div>';
            } else {
                $error_msg = mysqli_error($conn);

                // Se il campo esiste già, non è un errore critico
                if (strpos($error_msg, 'Duplicate column name') !== false) {
                    $skipped[] = $field_name;
                    echo '<div class="warning">⚠️  <strong>' . htmlspecialchars($field_name) . '</strong> - già esistente (saltato)</div>';
                } else {
                    $errors[] = ['field' => $field_name, 'error' => $error_msg];
                    echo '<div class="error">❌ <strong>' . htmlspecialchars($field_name) . '</strong> - Errore: ' . htmlspecialchars($error_msg) . '</div>';
                }
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
        echo '<div class="number">' . $total_fields . '</div>';
        echo '<div class="label">Campi Totali</div>';
        echo '</div>';

        echo '<div class="stat-card">';
        echo '<div class="number">' . $executed . '</div>';
        echo '<div class="label">Aggiunti</div>';
        echo '</div>';

        echo '<div class="stat-card">';
        echo '<div class="number">' . count($skipped) . '</div>';
        echo '<div class="label">Già Presenti</div>';
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

        if ($executed > 0) {
            echo '<div class="success">';
            echo '<strong>🎉 Operazione completata!</strong><br>';
            echo 'Sono stati aggiunti <strong>' . $executed . '</strong> nuovi campi alla tabella clienti.';
            echo '</div>';
        }

        if (count($skipped) > 0) {
            echo '<div class="info">';
            echo '<strong>ℹ️ Campi già presenti:</strong> ' . count($skipped) . '<br>';
            echo 'Questi campi esistevano già nel database e sono stati saltati.';
            echo '</div>';
        }

        if (count($errors) > 0) {
            echo '<div class="error">';
            echo '<strong>⚠️ Attenzione:</strong> si sono verificati ' . count($errors) . ' errori durante l\'esecuzione.<br>';
            echo 'Controlla i log sopra per i dettagli.';
            echo '</div>';
        }

        echo '</div>';

        // STEP 5: Dettaglio campi aggiunti
        if ($executed > 0 || count($skipped) > 0) {
            echo '<div class="step">';
            echo '<h3><span class="step-number">5</span> Dettaglio Campi Aggiunti</h3>';

            echo '<h4 style="margin-top: 20px; color: #9c27b0;">📋 Consensi GDPR (10 campi)</h4>';
            echo '<div class="field-list">';
            echo '<div class="field-item"><strong>privacy_accettata</strong><br><small>Accettazione Privacy Policy</small></div>';
            echo '<div class="field-item"><strong>privacy_accettata_at</strong><br><small>Timestamp accettazione</small></div>';
            echo '<div class="field-item"><strong>termini_accettati</strong><br><small>Accettazione T&C</small></div>';
            echo '<div class="field-item"><strong>termini_accettati_at</strong><br><small>Timestamp accettazione</small></div>';
            echo '<div class="field-item"><strong>marketing_accettato</strong><br><small>Consenso marketing</small></div>';
            echo '<div class="field-item"><strong>marketing_accettato_at</strong><br><small>Timestamp consenso</small></div>';
            echo '<div class="field-item"><strong>consenso_dati_sensibili</strong><br><small>Consenso dati salute</small></div>';
            echo '<div class="field-item"><strong>consenso_dati_sensibili_at</strong><br><small>Timestamp consenso</small></div>';
            echo '<div class="field-item"><strong>ip_registrazione</strong><br><small>IP registrazione</small></div>';
            echo '<div class="field-item"><strong>note_consensi</strong><br><small>Note consensi</small></div>';
            echo '</div>';

            echo '<h4 style="margin-top: 20px; color: #9c27b0;">👤 Dati Anagrafici (3 campi)</h4>';
            echo '<div class="field-list">';
            echo '<div class="field-item"><strong>data_nascita</strong><br><small>Data di nascita</small></div>';
            echo '<div class="field-item"><strong>eta</strong><br><small>Età calcolata</small></div>';
            echo '<div class="field-item"><strong>sesso</strong><br><small>Sesso biologico</small></div>';
            echo '</div>';

            echo '<h4 style="margin-top: 20px; color: #9c27b0;">🎯 Obiettivi (2 campi)</h4>';
            echo '<div class="field-list">';
            echo '<div class="field-item"><strong>obiettivi_personali</strong><br><small>Obiettivi fitness</small></div>';
            echo '<div class="field-item"><strong>livello_attivita</strong><br><small>Livello attività fisica</small></div>';
            echo '</div>';

            echo '<h4 style="margin-top: 20px; color: #9c27b0;">🏥 Dati Medici (5 campi)</h4>';
            echo '<div class="field-list">';
            echo '<div class="field-item"><strong>note_mediche</strong><br><small>Anamnesi medica</small></div>';
            echo '<div class="field-item"><strong>allergie_intolleranze</strong><br><small>Allergie alimentari</small></div>';
            echo '<div class="field-item"><strong>patologie</strong><br><small>Patologie note</small></div>';
            echo '<div class="field-item"><strong>farmaci_assunti</strong><br><small>Farmaci regolari</small></div>';
            echo '<div class="field-item"><strong>certificato_medico_presente</strong><br><small>Certificato sportivo</small></div>';
            echo '</div>';

            echo '<h4 style="margin-top: 20px; color: #9c27b0;">🍎 Alimentazione (3 campi)</h4>';
            echo '<div class="field-list">';
            echo '<div class="field-item"><strong>preferenze_alimentari</strong><br><small>Preferenze dieta</small></div>';
            echo '<div class="field-item"><strong>regime_alimentare</strong><br><small>Tipo regime</small></div>';
            echo '<div class="field-item"><strong>cibi_da_evitare</strong><br><small>Esclusioni alimentari</small></div>';
            echo '</div>';

            echo '<h4 style="margin-top: 20px; color: #9c27b0;">⚖️ Parametri Corporei (3 campi)</h4>';
            echo '<div class="field-list">';
            echo '<div class="field-item"><strong>peso</strong><br><small>Peso in kg</small></div>';
            echo '<div class="field-item"><strong>altezza</strong><br><small>Altezza in cm</small></div>';
            echo '<div class="field-item"><strong>bmi</strong><br><small>Indice massa corporea</small></div>';
            echo '</div>';

            echo '<h4 style="margin-top: 20px; color: #9c27b0;">📏 Circonferenze Corporee (7 campi)</h4>';
            echo '<div class="field-list">';
            echo '<div class="field-item"><strong>circonferenza_vita</strong><br><small>Vita in cm</small></div>';
            echo '<div class="field-item"><strong>circonferenza_fianchi</strong><br><small>Fianchi in cm</small></div>';
            echo '<div class="field-item"><strong>circonferenza_petto</strong><br><small>Petto in cm</small></div>';
            echo '<div class="field-item"><strong>circonferenza_braccio_dx</strong><br><small>Braccio destro in cm</small></div>';
            echo '<div class="field-item"><strong>circonferenza_braccio_sx</strong><br><small>Braccio sinistro in cm</small></div>';
            echo '<div class="field-item"><strong>circonferenza_coscia_dx</strong><br><small>Coscia destra in cm</small></div>';
            echo '<div class="field-item"><strong>circonferenza_coscia_sx</strong><br><small>Coscia sinistra in cm</small></div>';
            echo '</div>';

            echo '<h4 style="margin-top: 20px; color: #9c27b0;">🕐 Timestamp Misurazioni (2 campi)</h4>';
            echo '<div class="field-list">';
            echo '<div class="field-item"><strong>ultima_pesata</strong><br><small>Data ultima pesata</small></div>';
            echo '<div class="field-item"><strong>ultima_misurazione</strong><br><small>Data misurazioni</small></div>';
            echo '</div>';

            echo '<h4 style="margin-top: 20px; color: #9c27b0;">📝 Note (1 campo)</h4>';
            echo '<div class="field-list">';
            echo '<div class="field-item"><strong>note_professionista</strong><br><small>Note riservate</small></div>';
            echo '</div>';

            echo '</div>';
        }

        // STEP 6: Prossimi passi
        echo '<div class="step">';
        echo '<h3><span class="step-number">6</span> Prossimi Passi</h3>';
        echo '<div class="info">';
        echo '<strong>📌 Cosa fare ora:</strong><br><br>';
        echo '1️⃣ <strong>Testa il form di registrazione:</strong><br>';
        echo '&nbsp;&nbsp;&nbsp;&nbsp;<a href="/registrazione" target="_blank">https://www.agstudio.digital/magia/public/registrazione</a><br><br>';
        echo '2️⃣ <strong>Verifica le pagine legali:</strong><br>';
        echo '&nbsp;&nbsp;&nbsp;&nbsp;<a href="/privacy-policy" target="_blank">Privacy Policy</a> | ';
        echo '<a href="/termini-condizioni" target="_blank">Termini e Condizioni</a><br><br>';
        echo '3️⃣ <strong>Controlla il pannello admin:</strong><br>';
        echo '&nbsp;&nbsp;&nbsp;&nbsp;<a href="/admin/clienti" target="_blank">Gestione Clienti</a><br><br>';
        echo '4️⃣ <strong>Verifica il database:</strong><br>';
        echo '&nbsp;&nbsp;&nbsp;&nbsp;Esegui: <code>DESCRIBE clienti;</code> per vedere tutti i campi<br>';
        echo '</div>';
        echo '</div>';

        // Chiudi connessione
        mysqli_close($conn);
        ?>

        <div class="delete-warning">
            ⚠️ IMPORTANTE: Elimina questo file immediatamente dopo l'esecuzione!<br>
            <small>Per sicurezza, rimuovi il file: <code>public/import-database-changes.php</code></small>
        </div>
    </div>
</div>

</body>
</html>
