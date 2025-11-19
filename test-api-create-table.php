<?php

echo "🚀 TEST API DATABASE - Creazione Tabella Automatica\n";
echo "===================================================\n\n";

$apiUrl = 'https://www.agstudio.digital/magia/public/db-execute.php';
$secret = '$Magia2025!';

function executeSQL($url, $secret, $sql, $description) {
    echo "📝 {$description}\n";
    echo "   SQL: " . substr($sql, 0, 80) . (strlen($sql) > 80 ? '...' : '') . "\n";

    $data = json_encode([
        'secret' => $secret,
        'sql' => $sql
    ]);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Content-Length: ' . strlen($data)
    ]);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Per testing

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    if ($error) {
        echo "   ❌ CURL Error: {$error}\n\n";
        return false;
    }

    echo "   HTTP Code: {$httpCode}\n";

    $result = json_decode($response, true);
    if ($result) {
        if (isset($result['success']) && $result['success']) {
            echo "   ✅ Success!\n";
            if (isset($result['rows'])) {
                echo "   📊 Rows: {$result['rows']}\n";
            }
            if (isset($result['affected'])) {
                echo "   📝 Affected: {$result['affected']}\n";
            }
            if (isset($result['message'])) {
                echo "   💬 {$result['message']}\n";
            }
            if (isset($result['data']) && is_array($result['data'])) {
                echo "   📋 Data:\n";
                foreach (array_slice($result['data'], 0, 5) as $row) {
                    echo "      " . json_encode($row) . "\n";
                }
            }
        } else {
            echo "   ❌ Error: " . ($result['error'] ?? 'Unknown error') . "\n";
            return false;
        }
    } else {
        echo "   ⚠️  Response: {$response}\n";
    }

    echo "\n";
    return $result;
}

// Test 1: Crea tabella
executeSQL($apiUrl, $secret,
    "CREATE TABLE test_claude (
        id INT PRIMARY KEY AUTO_INCREMENT,
        nome VARCHAR(100),
        descrizione TEXT,
        data_creazione TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",
    "Step 1: Creazione tabella test_claude"
);

// Test 2: Verifica esistenza
executeSQL($apiUrl, $secret,
    "SHOW TABLES LIKE 'test_claude'",
    "Step 2: Verifica esistenza tabella"
);

// Test 3: Inserisci dati
executeSQL($apiUrl, $secret,
    "INSERT INTO test_claude (nome, descrizione) VALUES
    ('Test 1', 'Primo record creato automaticamente da Claude'),
    ('Test 2', 'Secondo record creato automaticamente da Claude'),
    ('Test 3', 'Terzo record creato automaticamente da Claude')",
    "Step 3: Inserimento 3 record di test"
);

// Test 4: Leggi dati
$result = executeSQL($apiUrl, $secret,
    "SELECT * FROM test_claude",
    "Step 4: Lettura dati inseriti"
);

// Test 5: Elimina tabella
executeSQL($apiUrl, $secret,
    "DROP TABLE test_claude",
    "Step 5: Eliminazione tabella test_claude"
);

// Test 6: Verifica eliminazione
executeSQL($apiUrl, $secret,
    "SHOW TABLES LIKE 'test_claude'",
    "Step 6: Verifica eliminazione (dovrebbe essere vuoto)"
);

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "✅ TEST COMPLETATO!\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "\n";
echo "Claude può ora eseguire qualsiasi operazione SQL sul database\n";
echo "in modo completamente automatico, senza intervento manuale!\n";
