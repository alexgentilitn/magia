<?php
// Debug page per verificare configurazione server
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Debug Info - AGstudio CRM</title>
    <style>
        body { font-family: monospace; margin: 20px; background: #f4f4f4; }
        .section { background: white; padding: 15px; margin-bottom: 15px; border-radius: 5px; }
        .ok { color: green; }
        .error { color: red; }
        .warning { color: orange; }
        h2 { margin-top: 0; border-bottom: 2px solid #333; padding-bottom: 10px; }
    </style>
</head>
<body>
    <h1>AGstudio CRM - Debug Information</h1>

    <div class="section">
        <h2>PHP Version</h2>
        <p><strong>Version:</strong> <?php echo PHP_VERSION; ?></p>
        <p><strong>Required:</strong> 7.4+ <?php echo version_compare(PHP_VERSION, '7.4', '>=') ? '<span class="ok">✓ OK</span>' : '<span class="error">✗ TOO OLD</span>'; ?></p>
    </div>

    <div class="section">
        <h2>File Permissions</h2>
        <?php
        $files = [
            'smtp_settings.php',
            'email_templates.php',
            'config.php',
            'agstudio.db',
            'PHPMailer/PHPMailer.php'
        ];

        foreach ($files as $file) {
            if (file_exists($file)) {
                $perms = substr(sprintf('%o', fileperms($file)), -4);
                echo "<p><span class='ok'>✓</span> $file (permissions: $perms)</p>";
            } else {
                echo "<p><span class='error'>✗</span> $file <strong>NOT FOUND</strong></p>";
            }
        }
        ?>
    </div>

    <div class="section">
        <h2>Database Connection</h2>
        <?php
        try {
            $db = new SQLite3('agstudio.db');
            echo "<p class='ok'>✓ Database connected</p>";

            $tables = ['smtp_config', 'email_templates', 'email_queue', 'users'];
            foreach ($tables as $table) {
                $result = $db->query("SELECT COUNT(*) as count FROM $table");
                $row = $result->fetchArray(SQLITE3_ASSOC);
                echo "<p>✓ Table '<strong>$table</strong>': {$row['count']} rows</p>";
            }
        } catch (Exception $e) {
            echo "<p class='error'>✗ Database error: " . $e->getMessage() . "</p>";
        }
        ?>
    </div>

    <div class="section">
        <h2>Required Extensions</h2>
        <?php
        $extensions = ['pdo', 'sqlite3', 'mbstring', 'openssl', 'curl'];
        foreach ($extensions as $ext) {
            if (extension_loaded($ext)) {
                echo "<p class='ok'>✓ $ext</p>";
            } else {
                echo "<p class='error'>✗ $ext <strong>NOT LOADED</strong></p>";
            }
        }
        ?>
    </div>

    <div class="section">
        <h2>PHPMailer Status</h2>
        <?php
        if (file_exists('PHPMailer/PHPMailer.php')) {
            echo "<p class='ok'>✓ PHPMailer found</p>";
            try {
                require_once 'PHPMailer/PHPMailer.php';
                require_once 'PHPMailer/SMTP.php';
                require_once 'PHPMailer/Exception.php';
                $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
                echo "<p class='ok'>✓ PHPMailer can be instantiated</p>";
            } catch (Throwable $e) {
                echo "<p class='error'>✗ PHPMailer error: " . $e->getMessage() . "</p>";
            }
        } else {
            echo "<p class='error'>✗ PHPMailer NOT found</p>";
            echo "<p class='warning'>⚠ PHPMailer cartella mancante - email non funzioneranno!</p>";
        }
        ?>
    </div>

    <div class="section">
        <h2>Writable Directories</h2>
        <?php
        $dirs = ['uploads/', 'logs/'];
        foreach ($dirs as $dir) {
            if (is_dir($dir)) {
                if (is_writable($dir)) {
                    echo "<p class='ok'>✓ $dir is writable</p>";
                } else {
                    echo "<p class='error'>✗ $dir is NOT writable</p>";
                }
            } else {
                echo "<p class='warning'>⚠ $dir does not exist</p>";
            }
        }
        ?>
    </div>

    <div class="section">
        <h2>Error Reporting</h2>
        <p><strong>display_errors:</strong> <?php echo ini_get('display_errors') ? 'ON' : 'OFF'; ?></p>
        <p><strong>error_reporting:</strong> <?php echo error_reporting(); ?></p>
        <p><strong>log_errors:</strong> <?php echo ini_get('log_errors') ? 'ON' : 'OFF'; ?></p>
        <p><strong>error_log:</strong> <?php echo ini_get('error_log') ?: 'default'; ?></p>
    </div>

    <div class="section">
        <h2>Test Pages</h2>
        <p><a href="test_simple_smtp.php">→ Test Simple SMTP Page</a> (verifica base funzionamento)</p>
        <p><a href="smtp_settings.php">→ SMTP Settings (pagina reale)</a></p>
        <p><a href="email_templates.php">→ Email Templates (pagina reale)</a></p>
    </div>

    <div class="section">
        <h2>Server Info</h2>
        <p><strong>Server Software:</strong> <?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'; ?></p>
        <p><strong>Document Root:</strong> <?php echo $_SERVER['DOCUMENT_ROOT'] ?? 'Unknown'; ?></p>
        <p><strong>Script Filename:</strong> <?php echo __FILE__; ?></p>
        <p><strong>Current Directory:</strong> <?php echo getcwd(); ?></p>
    </div>

    <hr>
    <p style="text-align: center; color: #666;">
        Dopo aver verificato questa pagina, <a href="dashboard.php">torna alla Dashboard</a>
    </p>
</body>
</html>
