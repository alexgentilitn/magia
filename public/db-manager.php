<?php
/**
 * DATABASE MANAGER - Controllo completo database MySQL
 *
 * Password di accesso: puoi cambiarla qui sotto
 */

// ==================== SECURITY GUARD ====================
$securityGuard = require_once __DIR__.'/security-guard.php';
// IMPORTANTE: Modifica security-guard.php per aggiungere IP autorizzati alla whitelist!

// ==================== CONFIGURAZIONE ====================
define('DB_MANAGER_PASSWORD', '$Magia2025!'); // Password Database Manager
define('SESSION_TIMEOUT', 3600); // 1 ora

// ==================== AUTENTICAZIONE ====================
session_start();

// Logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: db-manager.php');
    exit;
}

// Login
if (isset($_POST['password'])) {
    if ($_POST['password'] === DB_MANAGER_PASSWORD) {
        $_SESSION['db_manager_auth'] = time();
        $_SESSION['db_manager_logged'] = true;
    } else {
        $error = "Password errata!";
    }
}

// Verifica autenticazione
$isAuthenticated = isset($_SESSION['db_manager_logged']) &&
                   $_SESSION['db_manager_logged'] === true &&
                   (time() - ($_SESSION['db_manager_auth'] ?? 0)) < SESSION_TIMEOUT;

if (!$isAuthenticated && !isset($_POST['password'])) {
    // Mostra form login
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>DB Manager - Login</title>
        <style>
            body { font-family: Arial; background: #f0f0f0; padding: 50px; }
            .login-box { max-width: 400px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
            input[type="password"] { width: 100%; padding: 12px; margin: 10px 0; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
            button { width: 100%; padding: 12px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; }
            button:hover { background: #0056b3; }
            .error { color: red; margin: 10px 0; }
            h2 { text-align: center; color: #333; }
        </style>
    </head>
    <body>
        <div class="login-box">
            <h2>🔐 Database Manager</h2>
            <?php if (isset($error)): ?>
                <div class="error"><?= $error ?></div>
            <?php endif; ?>
            <form method="POST">
                <input type="password" name="password" placeholder="Inserisci password" required autofocus>
                <button type="submit">Accedi</button>
            </form>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// ==================== BOOTSTRAP LARAVEL ====================
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// ==================== AZIONI ====================
$result = null;
$error = null;
$success = null;

// Esegui query SQL
if (isset($_POST['execute_sql'])) {
    try {
        $sql = trim($_POST['sql']);

        if (stripos($sql, 'SELECT') === 0 || stripos($sql, 'SHOW') === 0 || stripos($sql, 'DESCRIBE') === 0) {
            // Query SELECT
            $result = DB::select($sql);
            $success = "Query eseguita! " . count($result) . " righe restituite.";
        } else {
            // Query INSERT/UPDATE/DELETE/CREATE/ALTER/DROP
            DB::statement($sql);
            $success = "Query eseguita con successo!";
        }
    } catch (\Exception $e) {
        $error = "Errore: " . $e->getMessage();
    }
}

// Ottieni lista tabelle
$tables = DB::select('SHOW TABLES');
$databaseName = DB::connection()->getDatabaseName();
$tableKey = "Tables_in_" . $databaseName;

// Vista tabella specifica
$selectedTable = $_GET['table'] ?? null;
$tableData = null;
$tableStructure = null;

if ($selectedTable) {
    try {
        $tableData = DB::table($selectedTable)->limit(100)->get();
        $tableStructure = DB::select("DESCRIBE `{$selectedTable}`");
    } catch (\Exception $e) {
        $error = "Errore caricamento tabella: " . $e->getMessage();
    }
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Database Manager - <?= $databaseName ?></title>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f5f5f5; }

        .header { background: #2c3e50; color: white; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .header h1 { font-size: 24px; }
        .header a { color: white; text-decoration: none; padding: 8px 15px; background: #e74c3c; border-radius: 4px; }
        .header a:hover { background: #c0392b; }

        .container { display: flex; height: calc(100vh - 60px); }

        .sidebar { width: 250px; background: white; border-right: 1px solid #ddd; overflow-y: auto; }
        .sidebar h3 { padding: 15px; background: #ecf0f1; border-bottom: 1px solid #ddd; font-size: 14px; text-transform: uppercase; color: #555; }
        .sidebar ul { list-style: none; }
        .sidebar li { border-bottom: 1px solid #f0f0f0; }
        .sidebar a { display: block; padding: 12px 15px; color: #333; text-decoration: none; transition: background 0.2s; }
        .sidebar a:hover, .sidebar a.active { background: #3498db; color: white; }

        .main { flex: 1; padding: 30px; overflow-y: auto; }

        .panel { background: white; padding: 25px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .panel h2 { margin-bottom: 20px; color: #2c3e50; font-size: 20px; border-bottom: 2px solid #3498db; padding-bottom: 10px; }

        textarea { width: 100%; min-height: 150px; padding: 15px; border: 1px solid #ddd; border-radius: 4px; font-family: 'Courier New', monospace; font-size: 14px; resize: vertical; }

        button { padding: 12px 25px; background: #3498db; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 14px; font-weight: bold; }
        button:hover { background: #2980b9; }
        button.danger { background: #e74c3c; }
        button.danger:hover { background: #c0392b; }

        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th { background: #34495e; color: white; padding: 12px; text-align: left; font-weight: 600; font-size: 13px; }
        td { padding: 10px 12px; border-bottom: 1px solid #ecf0f1; font-size: 13px; }
        tr:hover { background: #f8f9fa; }

        .alert { padding: 15px; border-radius: 4px; margin-bottom: 20px; }
        .alert-success { background: #d4edda; border: 1px solid #c3e6cb; color: #155724; }
        .alert-error { background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; }

        .badge { display: inline-block; padding: 4px 8px; border-radius: 3px; font-size: 11px; font-weight: bold; }
        .badge-primary { background: #3498db; color: white; }
        .badge-warning { background: #f39c12; color: white; }

        .query-examples { background: #ecf0f1; padding: 15px; border-radius: 4px; margin-top: 15px; }
        .query-examples h4 { margin-bottom: 10px; color: #555; font-size: 14px; }
        .query-examples code { display: block; background: white; padding: 8px; margin: 5px 0; border-radius: 3px; font-size: 12px; cursor: pointer; }
        .query-examples code:hover { background: #3498db; color: white; }

        .stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 20px; }
        .stat-card { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; border-radius: 8px; }
        .stat-card h3 { font-size: 32px; margin-bottom: 5px; }
        .stat-card p { opacity: 0.9; font-size: 14px; }
    </style>
    <script>
        function fillQuery(query) {
            document.getElementById('sql').value = query;
            document.getElementById('sql').focus();
        }
    </script>
</head>
<body>
    <div class="header">
        <h1>🗄️ Database Manager - <?= $databaseName ?></h1>
        <a href="?logout=1">Logout</a>
    </div>

    <div class="container">
        <div class="sidebar">
            <h3>📊 Tabelle (<?= count($tables) ?>)</h3>
            <ul>
                <?php foreach ($tables as $table): ?>
                    <?php $tableName = $table->$tableKey; ?>
                    <li>
                        <a href="?table=<?= urlencode($tableName) ?>"
                           class="<?= $selectedTable === $tableName ? 'active' : '' ?>">
                            <?= htmlspecialchars($tableName) ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <div class="main">
            <?php if ($success): ?>
                <div class="alert alert-success">✅ <?= htmlspecialchars($success) ?></div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert alert-error">❌ <?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <!-- Query SQL Executor -->
            <div class="panel">
                <h2>⚡ Esegui Query SQL</h2>
                <form method="POST">
                    <textarea name="sql" id="sql" placeholder="Scrivi qui la tua query SQL..."><?= isset($_POST['sql']) ? htmlspecialchars($_POST['sql']) : '' ?></textarea>
                    <br><br>
                    <button type="submit" name="execute_sql">▶️ Esegui Query</button>
                </form>

                <div class="query-examples">
                    <h4>📝 Query di esempio (clicca per usare):</h4>
                    <code onclick="fillQuery('SHOW TABLES;')">SHOW TABLES;</code>
                    <code onclick="fillQuery('SELECT * FROM utenti LIMIT 10;')">SELECT * FROM utenti LIMIT 10;</code>
                    <code onclick="fillQuery('DESCRIBE utenti;')">DESCRIBE utenti;</code>
                    <code onclick="fillQuery('SELECT COUNT(*) as totale FROM clienti;')">SELECT COUNT(*) as totale FROM clienti;</code>
                    <code onclick="fillQuery('SHOW CREATE TABLE utenti;')">SHOW CREATE TABLE utenti;</code>
                </div>
            </div>

            <!-- Risultati Query -->
            <?php if ($result !== null && is_array($result) && count($result) > 0): ?>
                <div class="panel">
                    <h2>📋 Risultati Query (<?= count($result) ?> righe)</h2>
                    <div style="overflow-x: auto;">
                        <table>
                            <thead>
                                <tr>
                                    <?php foreach (array_keys((array)$result[0]) as $column): ?>
                                        <th><?= htmlspecialchars($column) ?></th>
                                    <?php endforeach; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($result as $row): ?>
                                    <tr>
                                        <?php foreach ((array)$row as $value): ?>
                                            <td><?= htmlspecialchars($value ?? 'NULL') ?></td>
                                        <?php endforeach; ?>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Vista Tabella Selezionata -->
            <?php if ($selectedTable): ?>
                <div class="panel">
                    <h2>🔍 Tabella: <?= htmlspecialchars($selectedTable) ?></h2>

                    <?php if ($tableStructure): ?>
                        <h3 style="margin-top: 20px; margin-bottom: 10px; color: #555;">Struttura:</h3>
                        <table>
                            <thead>
                                <tr>
                                    <th>Campo</th>
                                    <th>Tipo</th>
                                    <th>Null</th>
                                    <th>Key</th>
                                    <th>Default</th>
                                    <th>Extra</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($tableStructure as $col): ?>
                                    <tr>
                                        <td><strong><?= htmlspecialchars($col->Field) ?></strong></td>
                                        <td><span class="badge badge-primary"><?= htmlspecialchars($col->Type) ?></span></td>
                                        <td><?= htmlspecialchars($col->Null) ?></td>
                                        <td><?= htmlspecialchars($col->Key) ?></td>
                                        <td><?= htmlspecialchars($col->Default ?? 'NULL') ?></td>
                                        <td><?= htmlspecialchars($col->Extra) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>

                    <?php if ($tableData && count($tableData) > 0): ?>
                        <h3 style="margin-top: 30px; margin-bottom: 10px; color: #555;">
                            Dati (primi 100):
                            <span class="badge badge-warning"><?= count($tableData) ?> righe</span>
                        </h3>
                        <div style="overflow-x: auto;">
                            <table>
                                <thead>
                                    <tr>
                                        <?php foreach (array_keys((array)$tableData[0]) as $column): ?>
                                            <th><?= htmlspecialchars($column) ?></th>
                                        <?php endforeach; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($tableData as $row): ?>
                                        <tr>
                                            <?php foreach ((array)$row as $value): ?>
                                                <td><?= htmlspecialchars(substr($value ?? 'NULL', 0, 100)) ?></td>
                                            <?php endforeach; ?>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
