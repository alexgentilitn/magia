<?php

require __DIR__.'/vendor/autoload.php';

echo "🔄 IMPORTAZIONE DA SQLITE A JSON\n";
echo "=================================\n\n";

try {
    // Connetti al database SQLite
    $sqliteDb = new PDO('sqlite:agstudio.db');
    $sqliteDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "✅ Connesso a agstudio.db\n\n";

    // Ottieni lista tabelle
    $tables = $sqliteDb->query("SELECT name FROM sqlite_master WHERE type='table' ORDER BY name")->fetchAll(PDO::FETCH_COLUMN);

    echo "📋 Tabelle trovate:\n";
    foreach ($tables as $table) {
        echo "   - $table\n";
    }
    echo "\n";

    // Per ogni tabella, mostra struttura e dati
    foreach ($tables as $table) {
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "📊 Tabella: $table\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

        // Ottieni struttura
        $pragma = $sqliteDb->query("PRAGMA table_info($table)")->fetchAll(PDO::FETCH_ASSOC);
        echo "Struttura:\n";
        foreach ($pragma as $col) {
            echo "   - {$col['name']} ({$col['type']}) " . ($col['notnull'] ? "NOT NULL" : "NULL") . "\n";
        }
        echo "\n";

        // Ottieni dati
        $data = $sqliteDb->query("SELECT * FROM $table")->fetchAll(PDO::FETCH_ASSOC);
        $count = count($data);
        echo "Record: $count\n\n";

        if ($count > 0) {
            echo "Dati:\n";
            echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";
        }
    }

    echo "✅ Analisi completata!\n";

} catch (Exception $e) {
    echo "❌ ERRORE: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
