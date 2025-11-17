#!/usr/bin/env php
<?php
/**
 * CREA BACKUP POINT - Versione CLI
 * Eseguibile da linea di comando senza parametri
 */

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "🎯 CREA PUNTO DI BACKUP COMPLETO\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

try {
    $timestamp = date('Y-m-d_H-i-s');
    $backupDir = __DIR__ . '/../storage/backups';

    if (!is_dir($backupDir)) {
        mkdir($backupDir, 0755, true);
    }

    $filename = "db-backup-{$timestamp}.sql";
    $filepath = "{$backupDir}/{$filename}";

    echo "📅 Timestamp: " . date('d/m/Y H:i:s') . "\n";
    echo "🏷️  Tag Git: backup-{$timestamp}\n\n";

    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "STEP 1: BACKUP DATABASE\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

    $dbName = env('DB_DATABASE');
    $tables = DB::select('SHOW TABLES');
    $tableKey = "Tables_in_{$dbName}";
    $tableCount = count($tables);

    echo "🔍 Database: {$dbName}\n";
    echo "📊 Tabelle: {$tableCount}\n\n";

    $sqlContent = "-- ==========================================\n";
    $sqlContent .= "-- BACKUP POINT: backup-{$timestamp}\n";
    $sqlContent .= "-- Database: {$dbName}\n";
    $sqlContent .= "-- Data: " . date('d/m/Y H:i:s') . "\n";
    $sqlContent .= "-- Tabelle: {$tableCount}\n";
    $sqlContent .= "-- ==========================================\n\n";
    $sqlContent .= "SET FOREIGN_KEY_CHECKS=0;\n";
    $sqlContent .= "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\n";
    $sqlContent .= "START TRANSACTION;\n";
    $sqlContent .= "SET time_zone = \"+00:00\";\n\n";

    $totalRecords = 0;

    echo "Esportazione tabelle:\n";

    foreach ($tables as $index => $table) {
        $tableName = $table->$tableKey;
        $num = $index + 1;

        echo "  [{$num}/{$tableCount}] {$tableName}... ";

        $createTable = DB::select("SHOW CREATE TABLE `{$tableName}`");
        $createTableSQL = $createTable[0]->{'Create Table'};

        $count = DB::select("SELECT COUNT(*) as total FROM `{$tableName}`")[0]->total;
        $totalRecords += $count;

        $sqlContent .= "-- {$tableName} ({$count} record)\n";
        $sqlContent .= "DROP TABLE IF EXISTS `{$tableName}`;\n";
        $sqlContent .= $createTableSQL . ";\n\n";

        if ($count > 0) {
            $chunkSize = 100;
            $offset = 0;

            while ($offset < $count) {
                $rows = DB::select("SELECT * FROM `{$tableName}` LIMIT {$chunkSize} OFFSET {$offset}");

                foreach ($rows as $row) {
                    $values = [];
                    foreach ($row as $value) {
                        $values[] = ($value === null) ? 'NULL' : "'" . addslashes($value) . "'";
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

    file_put_contents($filepath, $sqlContent);
    $fileSize = filesize($filepath);
    $fileSizeMB = round($fileSize / 1024 / 1024, 2);

    echo "\n✅ Database esportato: {$fileSizeMB} MB\n";
    echo "   {$totalRecords} record totali\n\n";

    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "STEP 2: COMANDI GIT PER BACKUP POINT\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

    $tagMessage = "Backup point: {$timestamp} - {$tableCount} tables, {$totalRecords} records - Drag&drop calendario + Sistema backup";

    echo "Esegui questi comandi:\n\n";
    echo "git tag -a backup-{$timestamp} -m \"{$tagMessage}\"\n";
    echo "git push origin backup-{$timestamp}\n\n";

    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "✅ BACKUP POINT CREATO\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

    echo "💾 File SQL: storage/backups/{$filename}\n";
    echo "🏷️  Tag Git: backup-{$timestamp}\n";
    echo "📊 Dati: {$tableCount} tabelle, {$totalRecords} record, {$fileSizeMB} MB\n\n";

    echo "🔄 RIPRISTINO:\n";
    echo "  Codice: git checkout backup-{$timestamp}\n";
    echo "  Database: restore-database.php?file={$filename}&confirm=YES\n\n";

    // Ritorna il timestamp per uso in script esterni
    echo "TIMESTAMP={$timestamp}\n";

} catch (\Exception $e) {
    echo "❌ ERRORE: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    exit(1);
}
