<?php
/**
 * Script per creare la tabella PESATE nel database MySQL
 *
 * ISTRUZIONI:
 * 1. Carica questo file nella root del progetto
 * 2. Apri il browser: http://tuosito.it/setup-pesate-mysql.php
 * 3. Controlla i risultati
 * 4. ELIMINA questo file dopo l'uso per sicurezza
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Carica configurazione Laravel
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Ottieni configurazione database
$dbConfig = config('database.connections.mysql');

?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup Tabella PESATE - MA.GIA DONNA</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #db2777 0%, #9333ea 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 { font-size: 32px; margin-bottom: 10px; }
        .header p { opacity: 0.9; }
        .content { padding: 30px; }
        .step {
            background: #f8fafc;
            border-left: 4px solid #db2777;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 8px;
        }
        .step h2 {
            color: #1e293b;
            font-size: 20px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
        }
        .step-number {
            background: #db2777;
            color: white;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
            font-weight: bold;
        }
        .success {
            background: #dcfce7;
            border-left-color: #16a34a;
            color: #166534;
        }
        .error {
            background: #fee2e2;
            border-left-color: #dc2626;
            color: #991b1b;
        }
        .warning {
            background: #fef3c7;
            border-left-color: #f59e0b;
            color: #92400e;
        }
        .info {
            background: #dbeafe;
            border-left-color: #3b82f6;
            color: #1e40af;
        }
        .code {
            background: #1e293b;
            color: #e2e8f0;
            padding: 15px;
            border-radius: 8px;
            font-family: 'Courier New', monospace;
            font-size: 13px;
            overflow-x: auto;
            margin: 10px 0;
        }
        .btn {
            display: inline-block;
            background: linear-gradient(135deg, #db2777 0%, #9333ea 100%);
            color: white;
            padding: 12px 24px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            margin-top: 10px;
        }
        .btn:hover { opacity: 0.9; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }
        th {
            background: #f1f5f9;
            font-weight: 600;
            color: #475569;
        }
        .icon { margin-right: 8px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🏋️ Setup Tabella PESATE</h1>
            <p>Installazione sistema gestione pesate e parametri corporei</p>
        </div>
        <div class="content">

<?php

try {
    // Connessione al database
    $pdo = new PDO(
        "mysql:host={$dbConfig['host']};port={$dbConfig['port']};dbname={$dbConfig['database']};charset=utf8mb4",
        $dbConfig['username'],
        $dbConfig['password'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    echo '<div class="step success">';
    echo '<h2><span class="step-number">✓</span> Connessione Database</h2>';
    echo '<p>Connesso a: <strong>' . htmlspecialchars($dbConfig['database']) . '</strong></p>';
    echo '</div>';

    // Verifica se tabella esiste già
    $stmt = $pdo->query("SHOW TABLES LIKE 'pesate'");
    $tabellaEsiste = $stmt->rowCount() > 0;

    if ($tabellaEsiste) {
        echo '<div class="step warning">';
        echo '<h2><span class="step-number">⚠</span> Tabella Esistente</h2>';
        echo '<p>La tabella <code>pesate</code> esiste già nel database.</p>';

        $count = $pdo->query("SELECT COUNT(*) FROM pesate")->fetchColumn();
        echo "<p>Record presenti: <strong>{$count}</strong></p>";
        echo '</div>';
    } else {
        // Crea tabella pesate
        $sql = "CREATE TABLE IF NOT EXISTS `pesate` (
            `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            `cliente_id` bigint(20) UNSIGNED NOT NULL,
            `sede` varchar(100) DEFAULT NULL COMMENT 'Sede dove è stata effettuata la pesata',
            `peso` decimal(5,2) NOT NULL COMMENT 'Peso in kg',
            `bmi` decimal(5,2) DEFAULT NULL COMMENT 'Body Mass Index',
            `peso_corporeo_senza_grassi` decimal(5,2) DEFAULT NULL COMMENT 'Massa magra in kg',
            `muscolo_scheletrico` decimal(5,2) DEFAULT NULL COMMENT 'Percentuale muscolo scheletrico',
            `grasso_corporeo` decimal(5,2) DEFAULT NULL COMMENT 'Percentuale grasso corporeo',
            `grasso_sottocutaneo` decimal(5,2) DEFAULT NULL COMMENT 'Percentuale grasso sottocutaneo',
            `grasso_viscerale` int(11) DEFAULT NULL COMMENT 'Livello grasso viscerale',
            `acqua_corporea` decimal(5,2) DEFAULT NULL COMMENT 'Percentuale acqua corporea',
            `massa_muscolare` decimal(5,2) DEFAULT NULL COMMENT 'Massa muscolare in kg',
            `massa_ossea` decimal(5,2) DEFAULT NULL COMMENT 'Massa ossea in kg',
            `proteine` decimal(5,2) DEFAULT NULL COMMENT 'Percentuale proteine',
            `bmr` int(11) DEFAULT NULL COMMENT 'Metabolismo basale (calorie)',
            `eta_metabolica` int(11) DEFAULT NULL COMMENT 'Età metabolica in anni',
            `data_rilevazione` date NOT NULL COMMENT 'Data della pesata',
            `note` text DEFAULT NULL COMMENT 'Note aggiuntive',
            `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `idx_cliente_id` (`cliente_id`),
            KEY `idx_data_rilevazione` (`data_rilevazione`),
            KEY `idx_sede` (`sede`),
            CONSTRAINT `fk_pesate_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `clienti` (`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Storico pesate e parametri corporei clienti'";

        $pdo->exec($sql);

        echo '<div class="step success">';
        echo '<h2><span class="step-number">✓</span> Tabella Creata</h2>';
        echo '<p>La tabella <code>pesate</code> è stata creata con successo!</p>';
        echo '</div>';
    }

    // Mostra struttura tabella
    echo '<div class="step info">';
    echo '<h2><span class="step-number">ℹ</span> Struttura Tabella</h2>';
    echo '<table>';
    echo '<tr><th>Campo</th><th>Tipo</th><th>Descrizione</th></tr>';

    $campi = [
        ['cliente_id', 'bigint(20)', 'ID cliente (FK)'],
        ['sede', 'varchar(100)', 'Sede rilevazione'],
        ['peso', 'decimal(5,2)', 'Peso corporeo (kg)'],
        ['bmi', 'decimal(5,2)', 'Body Mass Index'],
        ['grasso_corporeo', 'decimal(5,2)', 'Grasso corporeo (%)'],
        ['muscolo_scheletrico', 'decimal(5,2)', 'Muscolo scheletrico (%)'],
        ['acqua_corporea', 'decimal(5,2)', 'Acqua corporea (%)'],
        ['massa_muscolare', 'decimal(5,2)', 'Massa muscolare (kg)'],
        ['bmr', 'int(11)', 'Metabolismo basale (cal)'],
        ['data_rilevazione', 'date', 'Data della pesata'],
    ];

    foreach ($campi as $campo) {
        echo "<tr><td><strong>{$campo[0]}</strong></td><td><code>{$campo[1]}</code></td><td>{$campo[2]}</td></tr>";
    }
    echo '</table>';
    echo '</div>';

    // Prossimi passi
    echo '<div class="step">';
    echo '<h2><span class="step-number">📋</span> Prossimi Passi</h2>';
    echo '<ol style="margin-left: 20px; line-height: 1.8;">';
    echo '<li>Importa i dati dai file Excel: <code>php artisan pesate:import</code></li>';
    echo '<li>Accedi al pannello admin: <code>/admin/clienti-pesate</code></li>';
    echo '<li>Visualizza le pesate di un cliente specifico</li>';
    echo '<li><strong>IMPORTANTE:</strong> Elimina questo file dopo l\'uso!</li>';
    echo '</ol>';
    echo '</div>';

} catch (PDOException $e) {
    echo '<div class="step error">';
    echo '<h2><span class="step-number">✗</span> Errore Database</h2>';
    echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
    echo '<div class="code">' . htmlspecialchars($e->getTraceAsString()) . '</div>';
    echo '</div>';
} catch (Exception $e) {
    echo '<div class="step error">';
    echo '<h2><span class="step-number">✗</span> Errore</h2>';
    echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
    echo '</div>';
}

?>

            <div class="step warning">
                <h2><span class="step-number">⚠</span> Sicurezza</h2>
                <p><strong>ATTENZIONE:</strong> Elimina questo file dopo l'installazione per motivi di sicurezza!</p>
                <div class="code">rm setup-pesate-mysql.php</div>
            </div>

        </div>
    </div>
</body>
</html>
