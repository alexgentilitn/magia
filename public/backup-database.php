<?php
/**
 * BACKUP COMPLETO DATABASE
 * Esporta tutte le tabelle del database in formato SQL
 * Utilizzabile per ripristino in caso di emergenza
 */

define('SECRET', '$Magia2025!');

if (!isset($_GET['secret']) || $_GET['secret'] !== SECRET) {
    http_response_code(401);
    die('❌ Unauthorized');
}

header('Content-Type: text/plain; charset=utf-8');

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "💾 BACKUP DATABASE COMPLETO\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    $timestamp = date('Y-m-d_H-i-s');
    $backupDir = __DIR__ . '/../storage/backups';

    // Crea directory se non esiste
    if (!is_dir($backupDir)) {
        mkdir($backupDir, 0755, true);
        echo "✅ Directory backups creata\n\n";
    }

    $filename = "db-backup-{$timestamp}.sql";
    $filepath = "{$backupDir}/{$filename}";

    echo "📅 Timestamp: {$timestamp}\n";
    echo "📁 File: {$filename}\n\n";

    // Ottieni configurazione database
    $dbHost = env('DB_HOST', '127.0.0.1');
    $dbPort = env('DB_PORT', '3306');
    $dbName = env('DB_DATABASE');
    $dbUser = env('DB_USERNAME');
    $dbPass = env('DB_PASSWORD');

    echo "🔍 Database: {$dbName}\n";
    echo "🖥️  Host: {$dbHost}:{$dbPort}\n\n";

    // Ottieni lista tabelle
    $tables = DB::select('SHOW TABLES');
    $tableKey = "Tables_in_{$dbName}";
    $tableCount = count($tables);

    echo "📊 Tabelle trovate: {$tableCount}\n\n";

    // Inizia file SQL
    $sqlContent = "-- ==========================================\n";
    $sqlContent .= "-- BACKUP DATABASE: {$dbName}\n";
    $sqlContent .= "-- Data: {$timestamp}\n";
    $sqlContent .= "-- Tabelle: {$tableCount}\n";
    $sqlContent .= "-- ==========================================\n\n";
    $sqlContent .= "SET FOREIGN_KEY_CHECKS=0;\n";
    $sqlContent .= "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\n";
    $sqlContent .= "START TRANSACTION;\n";
    $sqlContent .= "SET time_zone = \"+00:00\";\n\n";

    $totalRecords = 0;
    $processedTables = 0;

    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "📋 BACKUP TABELLE\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

    foreach ($tables as $table) {
        $tableName = $table->$tableKey;
        $processedTables++;

        echo "  [{$processedTables}/{$tableCount}] {$tableName}... ";

        // Ottieni struttura tabella
        $createTable = DB::select("SHOW CREATE TABLE `{$tableName}`");
        $createTableSQL = $createTable[0]->{'Create Table'};

        // Conta record
        $count = DB::select("SELECT COUNT(*) as total FROM `{$tableName}`")[0]->total;
        $totalRecords += $count;

        // Aggiungi DROP TABLE
        $sqlContent .= "-- ==========================================\n";
        $sqlContent .= "-- Tabella: {$tableName} ({$count} record)\n";
        $sqlContent .= "-- ==========================================\n";
        $sqlContent .= "DROP TABLE IF EXISTS `{$tableName}`;\n";
        $sqlContent .= $createTableSQL . ";\n\n";

        // Esporta dati se presenti
        if ($count > 0) {
            $sqlContent .= "-- Dati tabella {$tableName}\n";

            // Ottieni dati in blocchi per evitare memory issues
            $chunkSize = 100;
            $offset = 0;

            while ($offset < $count) {
                $rows = DB::select("SELECT * FROM `{$tableName}` LIMIT {$chunkSize} OFFSET {$offset}");

                foreach ($rows as $row) {
                    $values = [];
                    foreach ($row as $value) {
                        if ($value === null) {
                            $values[] = 'NULL';
                        } else {
                            $values[] = "'" . addslashes($value) . "'";
                        }
                    }

                    $sqlContent .= "INSERT INTO `{$tableName}` VALUES (" . implode(', ', $values) . ");\n";
                }

                $offset += $chunkSize;
            }

            $sqlContent .= "\n";
        }

        echo "✅ {$count} record\n";
    }

    $sqlContent .= "SET FOREIGN_KEY_CHECKS=1;\n";
    $sqlContent .= "COMMIT;\n";

    // Salva file
    file_put_contents($filepath, $sqlContent);
    $fileSize = filesize($filepath);
    $fileSizeMB = round($fileSize / 1024 / 1024, 2);

    echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "✅ BACKUP COMPLETATO\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

    echo "📊 Statistiche:\n";
    echo "  - Tabelle processate: {$processedTables}\n";
    echo "  - Record totali: {$totalRecords}\n";
    echo "  - Dimensione file: {$fileSizeMB} MB\n";
    echo "  - Percorso: storage/backups/{$filename}\n\n";

    echo "📝 Prossimi step:\n";
    echo "  1. Committare il file backup su Git\n";
    echo "  2. Creare tag Git: backup-{$timestamp}\n";
    echo "  3. Push su GitHub\n\n";

    echo "⚠️  NOTA: Per motivi di sicurezza, questo file NON sarà committato\n";
    echo "   automaticamente. Dovrai aggiungerlo manualmente se necessario.\n\n";

    echo "🔄 Per ripristinare questo backup:\n";
    echo "   https://www.agstudio.digital/magia/public/restore-database.php?secret=\$Magia2025!&file={$filename}\n\n";

} catch (\Exception $e) {
    echo "❌ ERRORE: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
