<?php
/**
 * Script di aggiornamento database per importazione pesate
 *
 * Questo script rende nullable i campi non essenziali della tabella clienti
 * per permettere la creazione di clienti con dati minimi durante l'importazione
 *
 * UTILIZZO:
 * - Via CLI: php update_database.php
 * - Via Browser: https://www.agstudio.digital/magia/update_database.php
 */

// Configurazione
$host = 'localhost';
$database = 'agstudiodiital_magia';
$username = 'agstudiodiital_agstudiomagia';
$password = '$Magia2015!';

echo "=============================================================\n";
echo "AGGIORNAMENTO DATABASE - Sistema Importazione Pesate\n";
echo "=============================================================\n\n";

try {
    // Connessione al database
    echo "📡 Connessione al database...\n";
    $pdo = new PDO(
        "mysql:host={$host};dbname={$database};charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
    echo "✅ Connessione riuscita!\n\n";

    // Verifica esistenza tabella
    echo "🔍 Verifica tabella 'clienti'...\n";
    $stmt = $pdo->query("SHOW TABLES LIKE 'clienti'");
    if ($stmt->rowCount() === 0) {
        throw new Exception("Tabella 'clienti' non trovata nel database!");
    }
    echo "✅ Tabella 'clienti' trovata\n\n";

    // Lista modifiche da applicare
    $modifiche = [
        [
            'campo' => 'codice_fiscale',
            'sql' => "ALTER TABLE clienti MODIFY COLUMN codice_fiscale VARCHAR(16) NULL UNIQUE",
            'descrizione' => 'Codice Fiscale → NULL'
        ],
        [
            'campo' => 'email',
            'sql' => "ALTER TABLE clienti MODIFY COLUMN email VARCHAR(255) NULL",
            'descrizione' => 'Email → NULL'
        ],
        [
            'campo' => 'telefono_mobile',
            'sql' => "ALTER TABLE clienti MODIFY COLUMN telefono_mobile VARCHAR(20) NULL",
            'descrizione' => 'Telefono Mobile → NULL'
        ],
        [
            'campo' => 'data_nascita',
            'sql' => "ALTER TABLE clienti MODIFY COLUMN data_nascita DATE NULL",
            'descrizione' => 'Data Nascita → NULL'
        ],
        [
            'campo' => 'indirizzo',
            'sql' => "ALTER TABLE clienti MODIFY COLUMN indirizzo VARCHAR(200) NULL",
            'descrizione' => 'Indirizzo → NULL'
        ],
        [
            'campo' => 'citta',
            'sql' => "ALTER TABLE clienti MODIFY COLUMN citta VARCHAR(100) NULL",
            'descrizione' => 'Città → NULL'
        ],
        [
            'campo' => 'provincia',
            'sql' => "ALTER TABLE clienti MODIFY COLUMN provincia VARCHAR(2) NULL",
            'descrizione' => 'Provincia → NULL'
        ],
        [
            'campo' => 'cap',
            'sql' => "ALTER TABLE clienti MODIFY COLUMN cap VARCHAR(5) NULL",
            'descrizione' => 'CAP → NULL'
        ],
    ];

    echo "📋 Modifiche da applicare:\n";
    echo "-----------------------------------------------------------\n";
    foreach ($modifiche as $index => $modifica) {
        echo ($index + 1) . ". " . $modifica['descrizione'] . "\n";
    }
    echo "\n";

    // Nota: ALTER TABLE in MySQL fa autocommit, quindi non usiamo transazioni
    echo "🔄 Inizio applicazione modifiche...\n";

    $modificheApplicate = 0;
    $modificheSkippate = 0;
    $errori = [];

    // Applica ogni modifica
    foreach ($modifiche as $modifica) {
        try {
            // Verifica se il campo esiste
            $stmt = $pdo->query("SHOW COLUMNS FROM clienti LIKE '{$modifica['campo']}'");

            if ($stmt->rowCount() === 0) {
                echo "⚠️  Campo '{$modifica['campo']}' non esiste - SKIP\n";
                $modificheSkippate++;
                continue;
            }

            // Esegui modifica
            $pdo->exec($modifica['sql']);
            echo "✅ {$modifica['descrizione']}\n";
            $modificheApplicate++;

        } catch (PDOException $e) {
            // Se l'errore è perché il campo è già nullable, è ok
            if (strpos($e->getMessage(), 'Duplicate column name') !== false ||
                strpos($e->getMessage(), 'already') !== false) {
                echo "ℹ️  Campo '{$modifica['campo']}' già configurato - SKIP\n";
                $modificheSkippate++;
            } else {
                $errori[] = "Errore su campo '{$modifica['campo']}': " . $e->getMessage();
                echo "❌ ERRORE: {$modifica['descrizione']} - " . $e->getMessage() . "\n";
            }
        }
    }

    echo "\n";

    // Verifica risultati
    if (count($errori) > 0) {
        echo "⚠️  Trovati errori durante l'aggiornamento\n";
        echo "❌ Alcune modifiche potrebbero non essere state applicate\n\n";

        echo "ERRORI RILEVATI:\n";
        echo "-----------------------------------------------------------\n";
        foreach ($errori as $errore) {
            echo "• " . $errore . "\n";
        }
        echo "\n";
        echo "Nota: Le modifiche ALTER TABLE fanno autocommit, quindi alcune\n";
        echo "      potrebbero essere state salvate prima dell'errore.\n";
        echo "      Verifica manualmente lo stato del database.\n\n";
        exit(1);
    }

    // Nota: Non serve commit perché ALTER TABLE fa autocommit
    echo "💾 Modifiche salvate automaticamente\n";

    echo "\n";
    echo "=============================================================\n";
    echo "✅ AGGIORNAMENTO COMPLETATO CON SUCCESSO!\n";
    echo "=============================================================\n\n";

    echo "📊 RIEPILOGO:\n";
    echo "   • Modifiche applicate: {$modificheApplicate}\n";
    echo "   • Modifiche skippate: {$modificheSkippate}\n";
    echo "   • Errori: 0\n\n";

    echo "🎉 Il database è ora pronto per l'importazione pesate!\n";
    echo "   → I clienti possono essere creati con SOLO nome + cognome\n";
    echo "   → L'import accetta valori con unità di misura (es: '57.50 kg')\n\n";

    // Mostra struttura aggiornata dei campi modificati
    echo "📋 VERIFICA CAMPI NULLABLE:\n";
    echo "-----------------------------------------------------------\n";
    $stmt = $pdo->query("
        SELECT COLUMN_NAME, IS_NULLABLE, DATA_TYPE, COLUMN_TYPE
        FROM INFORMATION_SCHEMA.COLUMNS
        WHERE TABLE_SCHEMA = '{$database}'
        AND TABLE_NAME = 'clienti'
        AND COLUMN_NAME IN ('codice_fiscale', 'email', 'telefono_mobile', 'data_nascita', 'indirizzo', 'citta', 'provincia', 'cap')
        ORDER BY ORDINAL_POSITION
    ");

    while ($row = $stmt->fetch()) {
        $nullable = $row['IS_NULLABLE'] === 'YES' ? '✅ NULL' : '❌ NOT NULL';
        echo sprintf("%-20s %-15s %s\n", $row['COLUMN_NAME'], $row['DATA_TYPE'], $nullable);
    }

    echo "\n";
    echo "=============================================================\n";
    echo "TUTTO OK! Puoi ora eliminare questo file per sicurezza:\n";
    echo "rm /home/user/magia/update_database.php\n";
    echo "=============================================================\n";

} catch (PDOException $e) {
    echo "\n";
    echo "=============================================================\n";
    echo "❌ ERRORE DI CONNESSIONE AL DATABASE\n";
    echo "=============================================================\n\n";
    echo "Messaggio: " . $e->getMessage() . "\n";
    echo "Codice: " . $e->getCode() . "\n\n";

    echo "POSSIBILI SOLUZIONI:\n";
    echo "1. Verifica che le credenziali siano corrette\n";
    echo "2. Verifica che MySQL sia in esecuzione\n";
    echo "3. Verifica che l'utente abbia i permessi ALTER TABLE\n\n";

    exit(1);

} catch (Exception $e) {
    echo "\n";
    echo "=============================================================\n";
    echo "❌ ERRORE GENERICO\n";
    echo "=============================================================\n\n";
    echo "Messaggio: " . $e->getMessage() . "\n\n";

    exit(1);
}
