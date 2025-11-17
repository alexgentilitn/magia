<?php
/**
 * Test connessione database come Laravel
 */

define('TEST_TOKEN', 'magia2025');

if (!isset($_GET['token']) || $_GET['token'] !== TEST_TOKEN) {
    die('<h1>Accesso Negato</h1><p>Usa: ?token=' . TEST_TOKEN . '</p>');
}

error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Test Laravel Database</title>
    <style>
        body { font-family: Arial; padding: 20px; background: #f5f5f5; }
        .container { max-width: 900px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; }
        .test { margin: 15px 0; padding: 15px; border-left: 4px solid #2196F3; background: #f9f9f9; }
        .success { border-left-color: #4CAF50; background: #f1f8f4; }
        .error { border-left-color: #f44336; background: #fef1f0; }
        .warning { border-left-color: #ff9800; background: #fff8f0; }
        pre { background: #272822; color: #f8f8f2; padding: 10px; border-radius: 4px; overflow-x: auto; }
        h2 { margin-top: 0; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Test Connessione Database Laravel</h1>

<?php

// Carica .env come fa Laravel
$envPath = __DIR__ . '/../.env';
$env = [];

if (file_exists($envPath)) {
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0 || strpos($line, '=') === false) continue;
        list($key, $value) = explode('=', $line, 2);
        $env[trim($key)] = trim($value, '"\'');
    }
}

echo '<div class="test">';
echo '<h2>📄 Step 1: File .env</h2>';
echo '<p><strong>Path:</strong> ' . htmlspecialchars($envPath) . '</p>';
echo '<p><strong>Esiste:</strong> ' . (file_exists($envPath) ? '✅ Sì' : '❌ No') . '</p>';
echo '<p><strong>DB_HOST:</strong> ' . htmlspecialchars($env['DB_HOST'] ?? 'NON TROVATO') . '</p>';
echo '<p><strong>DB_PORT:</strong> ' . htmlspecialchars($env['DB_PORT'] ?? 'NON TROVATO') . '</p>';
echo '<p><strong>DB_DATABASE:</strong> ' . htmlspecialchars($env['DB_DATABASE'] ?? 'NON TROVATO') . '</p>';
echo '<p><strong>DB_USERNAME:</strong> ' . htmlspecialchars($env['DB_USERNAME'] ?? 'NON TROVATO') . '</p>';
echo '<p><strong>DB_PASSWORD:</strong> ' . (isset($env['DB_PASSWORD']) ? '****** (impostata)' : 'NON TROVATA') . '</p>';
echo '</div>';

// Test connessione PDO
echo '<div class="test">';
echo '<h2>🔌 Step 2: Test Connessione PDO</h2>';

try {
    $dsn = sprintf(
        'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
        $env['DB_HOST'] ?? 'localhost',
        $env['DB_PORT'] ?? '3306',
        $env['DB_DATABASE'] ?? ''
    );
    
    echo '<p><strong>DSN:</strong> <code>' . htmlspecialchars($dsn) . '</code></p>';
    
    $pdo = new PDO(
        $dsn,
        $env['DB_USERNAME'] ?? '',
        $env['DB_PASSWORD'] ?? '',
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
        ]
    );
    
    echo '<p class="success">✅ <strong>CONNESSIONE RIUSCITA!</strong></p>';
    
    // Test query
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM professionisti");
    $result = $stmt->fetch();
    echo '<p>✅ Query test riuscita: <strong>' . $result['count'] . ' professionisti</strong> nel database</p>';
    
} catch (PDOException $e) {
    echo '<p class="error">❌ <strong>ERRORE CONNESSIONE:</strong></p>';
    echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
    echo '<p><strong>Codice errore:</strong> ' . $e->getCode() . '</p>';
}
echo '</div>';

// Test bootstrap cache
echo '<div class="test">';
echo '<h2>📦 Step 3: Cache Bootstrap</h2>';
$bootstrapCache = dirname(__DIR__) . '/bootstrap/cache';
$cacheFiles = [
    'config.php',
    'routes-v7.php',
    'services.php',
    'events.php'
];

foreach ($cacheFiles as $file) {
    $path = $bootstrapCache . '/' . $file;
    $exists = file_exists($path);
    echo '<p><strong>' . $file . ':</strong> ' . ($exists ? '⚠️ ESISTE (potrebbe causare problemi)' : '✅ Non esiste') . '</p>';
}
echo '</div>';

// Test directory storage
echo '<div class="test">';
echo '<h2>📁 Step 4: Directory Storage</h2>';
$storagePath = dirname(__DIR__) . '/storage/app/public/professionisti';
echo '<p><strong>Path:</strong> ' . htmlspecialchars($storagePath) . '</p>';
echo '<p><strong>Esiste:</strong> ' . (is_dir($storagePath) ? '✅ Sì' : '❌ No') . '</p>';
echo '<p><strong>Scrivibile:</strong> ' . (is_writable($storagePath) ? '✅ Sì' : '❌ No') . '</p>';

// Test scrittura file
$testFile = $storagePath . '/test-' . time() . '.txt';
if (file_put_contents($testFile, 'test')) {
    echo '<p class="success">✅ <strong>Test scrittura riuscito!</strong></p>';
    unlink($testFile);
} else {
    echo '<p class="error">❌ <strong>Test scrittura fallito!</strong></p>';
}
echo '</div>';

// Test symlink
echo '<div class="test">';
echo '<h2>🔗 Step 5: Symlink public/storage</h2>';
$publicStorage = dirname(__DIR__) . '/public/storage';
echo '<p><strong>Path:</strong> ' . htmlspecialchars($publicStorage) . '</p>';
echo '<p><strong>Esiste:</strong> ' . (file_exists($publicStorage) ? '✅ Sì' : '❌ No') . '</p>';
echo '<p><strong>È symlink:</strong> ' . (is_link($publicStorage) ? '✅ Sì' : '❌ No') . '</p>';
if (is_link($publicStorage)) {
    echo '<p><strong>Punta a:</strong> ' . htmlspecialchars(readlink($publicStorage)) . '</p>';
}
echo '</div>';

// Info PHP
echo '<div class="test">';
echo '<h2>ℹ️ Step 6: Info PHP</h2>';
echo '<p><strong>PHP Version:</strong> ' . phpversion() . '</p>';
echo '<p><strong>PHP SAPI:</strong> ' . php_sapi_name() . '</p>';
echo '<p><strong>PDO MySQL:</strong> ' . (extension_loaded('pdo_mysql') ? '✅ Caricato' : '❌ Non caricato') . '</p>';
echo '<p><strong>Memory Limit:</strong> ' . ini_get('memory_limit') . '</p>';
echo '<p><strong>Max Upload Size:</strong> ' . ini_get('upload_max_filesize') . '</p>';
echo '<p><strong>Post Max Size:</strong> ' . ini_get('post_max_size') . '</p>';
echo '</div>';

?>
        <div class="test warning">
            <h2>⚠️ IMPORTANTE</h2>
            <p>Elimina questo file dopo aver visto i risultati:</p>
            <pre>rm <?php echo __FILE__; ?></pre>
        </div>
    </div>
</body>
</html>
