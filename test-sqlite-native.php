<?php

echo "🔍 TEST SQLITE - Approcci Alternativi\n";
echo "=====================================\n\n";

// Test 1: SQLite3 class (built-in PHP >= 5.3)
echo "Test 1: SQLite3 class...\n";
try {
    if (class_exists('SQLite3')) {
        $db = new SQLite3('database/database.sqlite');
        echo "   ✅ SQLite3 class DISPONIBILE!\n";

        // Test query
        $result = $db->query('SELECT COUNT(*) as count FROM utenti');
        $row = $result->fetchArray(SQLITE3_ASSOC);
        echo "   ✅ Query funziona! Utenti: {$row['count']}\n\n";

        $db->close();
    } else {
        echo "   ❌ SQLite3 class non disponibile\n\n";
    }
} catch (Exception $e) {
    echo "   ❌ Errore: " . $e->getMessage() . "\n\n";
}

// Test 2: PDO SQLite
echo "Test 2: PDO SQLite...\n";
try {
    $pdo = new PDO('sqlite:database/database.sqlite');
    echo "   ✅ PDO SQLite FUNZIONA!\n";

    $stmt = $pdo->query('SELECT COUNT(*) as count FROM utenti');
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "   ✅ Query funziona! Utenti: {$row['count']}\n\n";
} catch (Exception $e) {
    echo "   ❌ Errore: " . $e->getMessage() . "\n\n";
}

// Test 3: Shell sqlite3
echo "Test 3: Comando sqlite3 shell...\n";
$output = shell_exec('sqlite3 database/database.sqlite "SELECT COUNT(*) FROM utenti" 2>&1');
if ($output !== null) {
    echo "   ✅ sqlite3 command disponibile!\n";
    echo "   Risultato: " . trim($output) . "\n\n";
} else {
    echo "   ❌ sqlite3 command non disponibile\n\n";
}

echo "🎯 CONCLUSIONE:\n";
echo "Se uno dei metodi sopra funziona, possiamo usare SQLite!\n";
