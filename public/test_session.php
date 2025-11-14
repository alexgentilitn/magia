<?php
/**
 * FILE: diagnostica-completa.php
 * POSIZIONE: Carica in /public/
 * URL: https://www.agstudio.digital/magia/public/diagnostica-completa.php
 * 
 * DIAGNOSTICA COMPLETA MA.GIA DONNA
 * Verifica: File, Sessioni, Database, Permessi, Configurazioni
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Percorsi base
$basePath = dirname(__DIR__);
$publicPath = __DIR__;

// Inizio sessione per test
session_start();
if (!isset($_SESSION['test_visits'])) {
    $_SESSION['test_visits'] = 0;
}
$_SESSION['test_visits']++;
$_SESSION['test_time'] = date('H:i:s');

?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diagnostica Completa - MA.GIA DONNA</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
            color: #333;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        h1 {
            color: white;
            text-align: center;
            margin-bottom: 30px;
            font-size: 2.5em;
            text-shadow: 0 2px 10px rgba(0,0,0,0.3);
        }
        .section {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        .section h2 {
            color: #667eea;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #f0f0f0;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .emoji { font-size: 1.3em; }
        .test-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px;
            border-bottom: 1px solid #f5f5f5;
        }
        .test-row:last-child { border-bottom: none; }
        .test-label {
            font-weight: 600;
            color: #555;
            flex: 1;
        }
        .test-value {
            font-family: 'Courier New', monospace;
            background: #f8f9fa;
            padding: 6px 12px;
            border-radius: 5px;
            font-size: 0.9em;
            max-width: 60%;
            word-break: break-all;
        }
        .status {
            display: inline-flex;
            align-items: center;
            padding: 6px 15px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 0.85em;
            gap: 5px;
        }
        .status.ok { background: #10b981; color: white; }
        .status.warning { background: #f59e0b; color: white; }
        .status.error { background: #ef4444; color: white; }
        .file-list {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            max-height: 300px;
            overflow-y: auto;
            font-family: 'Courier New', monospace;
            font-size: 0.85em;
        }
        .file-ok { color: #10b981; }
        .file-missing { color: #ef4444; }
        .summary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            font-size: 1.2em;
            margin-bottom: 20px;
        }
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }
        .summary-item {
            background: rgba(255,255,255,0.2);
            padding: 15px;
            border-radius: 8px;
        }
        .summary-number {
            font-size: 2em;
            font-weight: bold;
        }
        pre {
            background: #1f2937;
            color: #10b981;
            padding: 15px;
            border-radius: 8px;
            overflow-x: auto;
            font-size: 0.85em;
        }
        .alert {
            background: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 15px;
            border-radius: 8px;
            margin: 15px 0;
        }
        .alert h3 {
            color: #92400e;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

<div class="container">
    
    <h1>🔍 Diagnostica Completa Sistema</h1>

    <?php
    // Contatori per summary
    $totalTests = 0;
    $passedTests = 0;
    $failedTests = 0;
    $warnings = 0;
    $problems = [];

    // Test functions
    function testStatus($condition, $label, &$total, &$passed, &$failed) {
        $total++;
        if ($condition) {
            $passed++;
            return '<span class="status ok">✅ OK</span>';
        } else {
            $failed++;
            return '<span class="status error">❌ ERRORE</span>';
        }
    }

    function warningStatus($condition, $label, &$warnings) {
        if (!$condition) {
            $warnings++;
            return '<span class="status warning">⚠️ ATTENZIONE</span>';
        }
        return '<span class="status ok">✅ OK</span>';
    }
    ?>

    <!-- SUMMARY -->
    <div class="summary">
        <div style="font-size: 1.5em; margin-bottom: 10px;">📊 Riepilogo Test</div>
        <div class="summary-grid">
            <div class="summary-item">
                <div style="opacity: 0.8;">Test Totali</div>
                <div class="summary-number" id="total-tests">0</div>
            </div>
            <div class="summary-item">
                <div style="opacity: 0.8;">✅ Superati</div>
                <div class="summary-number" id="passed-tests">0</div>
            </div>
            <div class="summary-item">
                <div style="opacity: 0.8;">❌ Falliti</div>
                <div class="summary-number" id="failed-tests">0</div>
            </div>
            <div class="summary-item">
                <div style="opacity: 0.8;">⚠️ Warning</div>
                <div class="summary-number" id="warnings">0</div>
            </div>
        </div>
    </div>

    <!-- 1. TEST SESSIONI PHP -->
    <div class="section">
        <h2><span class="emoji">🍪</span> Test Sessioni PHP</h2>
        <div class="test-row">
            <span class="test-label">Sessione avviata</span>
            <?php echo testStatus(session_status() === PHP_SESSION_ACTIVE, 'session', $totalTests, $passedTests, $failedTests); ?>
        </div>
        <div class="test-row">
            <span class="test-label">Session ID</span>
            <span class="test-value"><?php echo session_id(); ?></span>
        </div>
        <div class="test-row">
            <span class="test-label">Visite questa pagina</span>
            <span class="test-value"><?php echo $_SESSION['test_visits']; ?></span>
        </div>
        <div class="test-row">
            <span class="test-label">Ora ultima visita</span>
            <span class="test-value"><?php echo $_SESSION['test_time']; ?></span>
        </div>
        <div class="test-row">
            <span class="test-label">Save Handler</span>
            <span class="test-value"><?php echo ini_get('session.save_handler'); ?></span>
        </div>
        <div class="test-row">
            <span class="test-label">Save Path</span>
            <span class="test-value"><?php echo ini_get('session.save_path'); ?></span>
        </div>
        <div class="test-row">
            <span class="test-label">Cookie Lifetime</span>
            <span class="test-value"><?php echo ini_get('session.cookie_lifetime'); ?> sec</span>
        </div>
    </div>

    <!-- 2. TEST FILE .ENV -->
    <div class="section">
        <h2><span class="emoji">⚙️</span> Configurazione .env</h2>
        <?php
        $envPath = $basePath . '/.env';
        $envExists = file_exists($envPath);
        $envConfig = [];
        
        if ($envExists) {
            $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
                    list($key, $value) = explode('=', $line, 2);
                    $envConfig[trim($key)] = trim($value, '"');
                }
            }
        }
        ?>
        <div class="test-row">
            <span class="test-label">File .env esiste</span>
            <?php echo testStatus($envExists, 'env', $totalTests, $passedTests, $failedTests); ?>
        </div>
        <?php if ($envExists): ?>
        <div class="test-row">
            <span class="test-label">SESSION_DRIVER</span>
            <span class="test-value"><?php echo $envConfig['SESSION_DRIVER'] ?? 'non trovato'; ?></span>
        </div>
        <div class="test-row">
            <span class="test-label">SESSION_LIFETIME</span>
            <span class="test-value"><?php echo $envConfig['SESSION_LIFETIME'] ?? 'non trovato'; ?> minuti</span>
        </div>
        <div class="test-row">
            <span class="test-label">APP_URL</span>
            <span class="test-value"><?php echo $envConfig['APP_URL'] ?? 'non trovato'; ?></span>
        </div>
        <div class="test-row">
            <span class="test-label">APP_DEBUG</span>
            <span class="test-value"><?php echo $envConfig['APP_DEBUG'] ?? 'non trovato'; ?></span>
        </div>
        <?php endif; ?>
    </div>

    <!-- 3. TEST DATABASE -->
    <div class="section">
        <h2><span class="emoji">🗄️</span> Connessione Database</h2>
        <?php
        $dbHost = $envConfig['DB_HOST'] ?? 'localhost';
        $dbName = $envConfig['DB_DATABASE'] ?? '';
        $dbUser = $envConfig['DB_USERNAME'] ?? '';
        $dbPass = $envConfig['DB_PASSWORD'] ?? '';
        
        $dbConnected = false;
        $dbError = '';
        $tablesCount = 0;
        
        if ($dbName && $dbUser) {
            try {
                $pdo = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUser, $dbPass);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $dbConnected = true;
                
                $stmt = $pdo->query("SHOW TABLES");
                $tablesCount = $stmt->rowCount();
            } catch (PDOException $e) {
                $dbError = $e->getMessage();
            }
        }
        ?>
        <div class="test-row">
            <span class="test-label">Connessione DB</span>
            <?php echo testStatus($dbConnected, 'db', $totalTests, $passedTests, $failedTests); ?>
        </div>
        <?php if ($dbConnected): ?>
        <div class="test-row">
            <span class="test-label">Database</span>
            <span class="test-value"><?php echo $dbName; ?></span>
        </div>
        <div class="test-row">
            <span class="test-label">Numero tabelle</span>
            <span class="test-value"><?php echo $tablesCount; ?></span>
        </div>
        <?php else: ?>
        <div class="alert">
            <h3>Errore Connessione Database</h3>
            <p><?php echo htmlspecialchars($dbError); ?></p>
        </div>
        <?php $problems[] = "Database non connesso: " . $dbError; ?>
        <?php endif; ?>
    </div>

    <!-- 4. TEST FILE CRITICI -->
    <div class="section">
        <h2><span class="emoji">📁</span> File Critici Laravel</h2>
        <?php
        $criticalFiles = [
            'composer.json' => $basePath . '/composer.json',
            'artisan' => $basePath . '/artisan',
            'routes/web.php' => $basePath . '/routes/web.php',
            'app/Http/Kernel.php' => $basePath . '/app/Http/Kernel.php',
            'app/Models/Cliente.php' => $basePath . '/app/Models/Cliente.php',
            'app/Models/Utente.php' => $basePath . '/app/Models/Utente.php',
            'app/Http/Controllers/Admin/ClientiController.php' => $basePath . '/app/Http/Controllers/Admin/ClientiController.php',
            'resources/views/layouts/admin.blade.php' => $basePath . '/resources/views/layouts/admin.blade.php',
            'resources/views/admin/clienti/index.blade.php' => $basePath . '/resources/views/admin/clienti/index.blade.php',
        ];
        
        $filesOk = 0;
        $filesMissing = 0;
        ?>
        <div class="file-list">
        <?php foreach ($criticalFiles as $name => $path): ?>
            <?php 
            $exists = file_exists($path);
            $size = $exists ? filesize($path) : 0;
            if ($exists) {
                $filesOk++;
                echo '<div class="file-ok">✅ ' . $name . ' (' . number_format($size) . ' bytes)</div>';
            } else {
                $filesMissing++;
                echo '<div class="file-missing">❌ ' . $name . ' - MANCANTE!</div>';
                $problems[] = "File mancante: " . $name;
            }
            ?>
        <?php endforeach; ?>
        </div>
        <div class="test-row">
            <span class="test-label">File presenti</span>
            <span class="test-value"><?php echo $filesOk; ?> / <?php echo count($criticalFiles); ?></span>
        </div>
        <?php 
        $totalTests++;
        if ($filesMissing == 0) {
            $passedTests++;
        } else {
            $failedTests++;
        }
        ?>
    </div>

    <!-- 5. TEST PERMESSI CARTELLE -->
    <div class="section">
        <h2><span class="emoji">🔐</span> Permessi Cartelle</h2>
        <?php
        $directories = [
            'storage' => $basePath . '/storage',
            'storage/framework' => $basePath . '/storage/framework',
            'storage/framework/cache' => $basePath . '/storage/framework/cache',
            'storage/framework/sessions' => $basePath . '/storage/framework/sessions',
            'storage/framework/views' => $basePath . '/storage/framework/views',
            'storage/logs' => $basePath . '/storage/logs',
            'bootstrap/cache' => $basePath . '/bootstrap/cache',
        ];
        
        foreach ($directories as $name => $path):
            $exists = is_dir($path);
            $writable = $exists && is_writable($path);
            $perms = $exists ? substr(sprintf('%o', fileperms($path)), -4) : 'N/A';
        ?>
        <div class="test-row">
            <span class="test-label"><?php echo $name; ?></span>
            <div>
                <?php if ($exists): ?>
                    <?php echo warningStatus($writable, $name, $warnings); ?>
                    <span class="test-value" style="margin-left: 10px;"><?php echo $perms; ?></span>
                <?php else: ?>
                    <span class="status error">❌ NON ESISTE</span>
                    <?php 
                    $failedTests++;
                    $totalTests++;
                    $problems[] = "Cartella mancante: " . $name;
                    ?>
                <?php endif; ?>
            </div>
        </div>
        <?php 
        if ($exists) {
            $totalTests++;
            if ($writable) $passedTests++; else $failedTests++;
        }
        endforeach; 
        ?>
    </div>

    <!-- 6. TEST LARAVEL -->
    <div class="section">
        <h2><span class="emoji">🚀</span> Laravel Framework</h2>
        <?php
        $vendorExists = is_dir($basePath . '/vendor');
        $autoloadExists = file_exists($basePath . '/vendor/autoload.php');
        $laravelInstalled = $vendorExists && $autoloadExists;
        ?>
        <div class="test-row">
            <span class="test-label">Vendor directory</span>
            <?php echo testStatus($vendorExists, 'vendor', $totalTests, $passedTests, $failedTests); ?>
        </div>
        <div class="test-row">
            <span class="test-label">Autoload.php</span>
            <?php echo testStatus($autoloadExists, 'autoload', $totalTests, $passedTests, $failedTests); ?>
        </div>
        <?php if ($laravelInstalled): ?>
        <div class="test-row">
            <span class="test-label">Composer packages</span>
            <span class="test-value"><?php echo count(glob($basePath . '/vendor/*/*', GLOB_ONLYDIR)); ?></span>
        </div>
        <?php else: ?>
        <div class="alert">
            <h3>⚠️ Laravel non completamente installato</h3>
            <p>Esegui <code>composer install</code> dal terminal</p>
        </div>
        <?php $problems[] = "Laravel vendor mancante - esegui composer install"; ?>
        <?php endif; ?>
    </div>

    <!-- 7. PROBLEMI TROVATI -->
    <?php if (count($problems) > 0): ?>
    <div class="section">
        <h2><span class="emoji">🚨</span> Problemi Trovati</h2>
        <?php foreach ($problems as $problem): ?>
        <div class="alert">
            <strong>❌</strong> <?php echo htmlspecialchars($problem); ?>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- 8. SOLUZIONI SUGGERITE -->
    <div class="section">
        <h2><span class="emoji">💡</span> Comandi di Riparazione</h2>
        <p style="margin-bottom: 15px;">Esegui questi comandi nel Terminal cPanel:</p>
        <pre>cd ~/public_html/magia

# Crea cartelle mancanti
mkdir -p storage/framework/sessions
mkdir -p storage/framework/cache/data
mkdir -p storage/framework/views
mkdir -p storage/logs

# Sistema permessi
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/

# Pulisci cache Laravel
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Reinstalla dipendenze (se necessario)
composer install --no-dev</pre>
    </div>

    <!-- INFO SISTEMA -->
    <div class="section">
        <h2><span class="emoji">💻</span> Informazioni Sistema</h2>
        <div class="test-row">
            <span class="test-label">PHP Version</span>
            <span class="test-value"><?php echo phpversion(); ?></span>
        </div>
        <div class="test-row">
            <span class="test-label">Server Software</span>
            <span class="test-value"><?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'N/A'; ?></span>
        </div>
        <div class="test-row">
            <span class="test-label">Document Root</span>
            <span class="test-value"><?php echo $_SERVER['DOCUMENT_ROOT'] ?? 'N/A'; ?></span>
        </div>
        <div class="test-row">
            <span class="test-label">Base Path</span>
            <span class="test-value"><?php echo $basePath; ?></span>
        </div>
    </div>

</div>

<script>
document.getElementById('total-tests').textContent = <?php echo $totalTests; ?>;
document.getElementById('passed-tests').textContent = <?php echo $passedTests; ?>;
document.getElementById('failed-tests').textContent = <?php echo $failedTests; ?>;
document.getElementById('warnings').textContent = <?php echo $warnings; ?>;
</script>

<div style="text-align: center; color: white; padding: 20px; margin-top: 20px;">
    <strong>⚠️ IMPORTANTE:</strong> Elimina questo file dopo aver risolto i problemi!<br>
    <small>File: diagnostica-completa.php</small>
</div>

</body>
</html>