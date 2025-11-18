<?php
/**
 * RIPRISTINO DATABASE DA BACKUP
 * Importa un dump SQL precedentemente creato
 * ⚠️ ATTENZIONE: Sovrascrive TUTTI i dati attuali!
 */

define('SECRET', '$Magia2025!');

if (!isset($_GET['secret']) || $_GET['secret'] !== SECRET) {
    http_response_code(401);
    die('❌ Unauthorized');
}

header('Content-Type: text/plain; charset=utf-8');

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "♻️  RIPRISTINO DATABASE DA BACKUP\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    $backupDir = __DIR__ . '/../storage/backups';

    // Verifica directory backups
    if (!is_dir($backupDir)) {
        echo "❌ Directory backups non trovata!\n";
        echo "   Percorso: {$backupDir}\n\n";
        echo "⚠️  Nessun backup disponibile.\n";
        exit;
    }

    // Lista backups disponibili
    $backups = glob("{$backupDir}/db-backup-*.sql");

    if (empty($backups)) {
        echo "❌ Nessun backup disponibile!\n\n";
        echo "⚠️  Crea prima un backup usando:\n";
        echo "   https://www.agstudio.digital/magia/public/backup-database.php?secret=\$Magia2025!\n";
        exit;
    }

    // Ordina per data (più recente prima)
    rsort($backups);

    echo "📋 Backups disponibili:\n\n";
    foreach ($backups as $index => $backup) {
        $filename = basename($backup);
        $size = filesize($backup);
        $sizeMB = round($size / 1024 / 1024, 2);
        $date = date('d/m/Y H:i:s', filemtime($backup));
        echo "  " . ($index + 1) . ". {$filename}\n";
        echo "     Data: {$date} | Dimensione: {$sizeMB} MB\n\n";
    }

    // Verifica se è stato specificato un file
    if (!isset($_GET['file'])) {
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
        echo "⚠️  NESSUN FILE SPECIFICATO\n\n";
        echo "Per ripristinare un backup, aggiungi il parametro 'file':\n\n";
        echo "Esempio:\n";
        $latestFile = basename($backups[0]);
        echo "  ?secret=\$Magia2025!&file={$latestFile}\n\n";
        echo "🔔 Il backup più recente è: {$latestFile}\n";
        exit;
    }

    $requestedFile = $_GET['file'];
    $filepath = "{$backupDir}/{$requestedFile}";

    // Verifica esistenza file
    if (!file_exists($filepath)) {
        echo "❌ File backup non trovato!\n";
        echo "   File richiesto: {$requestedFile}\n";
        exit;
    }

    $fileSize = filesize($filepath);
    $fileSizeMB = round($fileSize / 1024 / 1024, 2);
    $fileDate = date('d/m/Y H:i:s', filemtime($filepath));

    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "⚠️  ATTENZIONE - OPERAZIONE PERICOLOSA!\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

    echo "Stai per ripristinare:\n";
    echo "  📁 File: {$requestedFile}\n";
    echo "  📅 Data backup: {$fileDate}\n";
    echo "  💾 Dimensione: {$fileSizeMB} MB\n\n";

    echo "⚠️  Questa operazione:\n";
    echo "  ❌ Eliminerà TUTTE le tabelle attuali\n";
    echo "  ❌ Sovrascriverà TUTTI i dati\n";
    echo "  ❌ NON può essere annullata\n\n";

    // Richiedi conferma esplicita
    if (!isset($_GET['confirm']) || $_GET['confirm'] !== 'YES') {
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
        echo "🛑 RIPRISTINO BLOCCATO\n\n";
        echo "Per procedere, aggiungi il parametro di conferma:\n\n";
        echo "  &confirm=YES\n\n";
        echo "URL completo:\n";
        echo "  ?secret=\$Magia2025!&file={$requestedFile}&confirm=YES\n\n";
        exit;
    }

    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "🔄 RIPRISTINO IN CORSO...\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

    // Leggi file SQL
    $sqlContent = file_get_contents($filepath);
    $sqlStatements = explode(";\n", $sqlContent);

    $executed = 0;
    $errors = 0;
    $startTime = microtime(true);

    foreach ($sqlStatements as $statement) {
        $statement = trim($statement);

        // Salta commenti e righe vuote
        if (empty($statement) || substr($statement, 0, 2) === '--') {
            continue;
        }

        try {
            DB::statement($statement);
            $executed++;

            // Mostra progresso ogni 50 query
            if ($executed % 50 === 0) {
                echo "  ✓ Eseguite {$executed} query...\n";
            }
        } catch (\Exception $e) {
            $errors++;
            echo "  ⚠️  Errore query: " . substr($statement, 0, 100) . "...\n";
            echo "      Messaggio: " . $e->getMessage() . "\n";
        }
    }

    $endTime = microtime(true);
    $duration = round($endTime - $startTime, 2);

    echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "✅ RIPRISTINO COMPLETATO\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

    echo "📊 Statistiche:\n";
    echo "  - Query eseguite: {$executed}\n";
    echo "  - Errori: {$errors}\n";
    echo "  - Tempo impiegato: {$duration} secondi\n\n";

    // Verifica database
    $tables = DB::select('SHOW TABLES');
    $tableCount = count($tables);

    echo "✅ Database ripristinato:\n";
    echo "  - Tabelle presenti: {$tableCount}\n\n";

    echo "🔍 Verifica i dati nell'applicazione per confermare il ripristino.\n\n";

} catch (\Exception $e) {
    echo "❌ ERRORE CRITICO: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n\n";
    echo "⚠️  Il database potrebbe essere in uno stato inconsistente!\n";
    echo "   Si consiglia di ripristinare nuovamente da backup.\n";
}

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
