<?php
/**
 * CREA PUNTO DI BACKUP COMPLETO
 * Esegue backup database e fornisce comandi Git per creare tag
 * Punto di ripristino completo codice + database
 */

define('SECRET', '$Magia2025!');

if (!isset($_GET['secret']) || $_GET['secret'] !== SECRET) {
    http_response_code(401);
    die('❌ Unauthorized');
}

header('Content-Type: text/plain; charset=utf-8');

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "🎯 CREA PUNTO DI BACKUP COMPLETO\n";
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
    }

    $filename = "db-backup-{$timestamp}.sql";
    $filepath = "{$backupDir}/{$filename}";

    echo "📅 Timestamp: " . date('d/m/Y H:i:s') . "\n";
    echo "🏷️  Tag Git: backup-{$timestamp}\n\n";

    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "STEP 1: BACKUP DATABASE\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

    // Ottieni configurazione database
    $dbName = env('DB_DATABASE');
    $tables = DB::select('SHOW TABLES');
    $tableKey = "Tables_in_{$dbName}";
    $tableCount = count($tables);

    echo "🔍 Database: {$dbName}\n";
    echo "📊 Tabelle: {$tableCount}\n\n";

    // Inizia file SQL
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

        // Struttura
        $createTable = DB::select("SHOW CREATE TABLE `{$tableName}`");
        $createTableSQL = $createTable[0]->{'Create Table'};

        // Conta record
        $count = DB::select("SELECT COUNT(*) as total FROM `{$tableName}`")[0]->total;
        $totalRecords += $count;

        $sqlContent .= "-- {$tableName} ({$count} record)\n";
        $sqlContent .= "DROP TABLE IF EXISTS `{$tableName}`;\n";
        $sqlContent .= $createTableSQL . ";\n\n";

        // Dati
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

    echo "📝 Esegui questi comandi per completare il backup:\n\n";

    echo "# 1. Tag il codice corrente\n";
    echo "git tag -a backup-{$timestamp} -m \"Backup point: {$timestamp} - {$tableCount} tables, {$totalRecords} records\"\n\n";

    echo "# 2. Push del tag su GitHub\n";
    echo "git push origin backup-{$timestamp}\n\n";

    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "RIPRISTINO DA QUESTO BACKUP POINT\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

    echo "♻️  Per ripristinare CODICE + DATABASE:\n\n";

    echo "1. Ripristina codice:\n";
    echo "   git checkout backup-{$timestamp}\n\n";

    echo "2. Ripristina database:\n";
    echo "   https://www.agstudio.digital/magia/public/restore-database.php?secret=\$Magia2025!&file={$filename}&confirm=YES\n\n";

    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "📋 LISTA TUTTI I BACKUP POINTS\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

    echo "Per vedere tutti i backup points disponibili:\n";
    echo "  git tag -l 'backup-*'\n\n";

    echo "Per vedere dettagli di un backup:\n";
    echo "  git show backup-{$timestamp}\n\n";

    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "✅ BACKUP POINT CREATO CON SUCCESSO\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

    echo "💾 File: storage/backups/{$filename}\n";
    echo "🏷️  Tag: backup-{$timestamp}\n";
    echo "📊 Dati: {$tableCount} tabelle, {$totalRecords} record, {$fileSizeMB} MB\n\n";

} catch (\Exception $e) {
    echo "❌ ERRORE: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}
