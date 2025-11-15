<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Backup Database - MA.GIA DONNA</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #e91e63 0%, #9c27b0 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .success {
            background: #d4edda;
            border: 1px solid #28a745;
            color: #155724;
            padding: 15px;
            border-radius: 8px;
            margin: 10px 0;
        }
        .error {
            background: #f8d7da;
            border: 1px solid #dc3545;
            color: #721c24;
            padding: 15px;
            border-radius: 8px;
            margin: 10px 0;
        }
        .info {
            background: #d1ecf1;
            border: 1px solid #17a2b8;
            color: #0c5460;
            padding: 15px;
            border-radius: 8px;
            margin: 10px 0;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>💾 Backup Database</h1>
        <p>MA.GIA DONNA</p>
    </div>
    <div style="padding: 30px;">
        <?php
        $db_host = 'localhost';
        $db_user = 'agstudiodiital_agstudiomagia';
        $db_pass = '$Magia2015!';
        $db_name = 'agstudiodiital_magia';
        
        $timestamp = date('Y-m-d_H-i-s');
        $backup_file = "backup_magia_{$timestamp}.sql";
        $backup_path = __DIR__ . '/../storage/backups/';
        
        if (!file_exists($backup_path)) {
            mkdir($backup_path, 0755, true);
        }
        
        $full_path = $backup_path . $backup_file;
        
        $command = sprintf(
            'mysqldump --user=%s --password=%s --host=%s %s > %s 2>&1',
            escapeshellarg($db_user),
            escapeshellarg($db_pass),
            escapeshellarg($db_host),
            escapeshellarg($db_name),
            escapeshellarg($full_path)
        );
        
        exec($command, $output, $return_code);
        
        if ($return_code === 0 && file_exists($full_path)) {
            $file_size_mb = round(filesize($full_path) / 1024 / 1024, 2);
            echo '<div class="success">';
            echo '<strong>✅ Backup completato!</strong><br>';
            echo 'File: ' . htmlspecialchars($backup_file) . '<br>';
            echo 'Dimensione: ' . $file_size_mb . ' MB<br>';
            echo 'Percorso: storage/backups/';
            echo '</div>';
            echo '<div class="info">Scarica il file tramite FTP/cPanel dalla cartella storage/backups/</div>';
        } else {
            echo '<div class="error">❌ Errore backup: codice ' . $return_code . '</div>';
        }
        ?>
        <div style="background: #dc3545; color: white; padding: 20px; border-radius: 10px; margin-top: 30px; text-align: center;">
            ⚠️ Elimina questo file dopo l'uso!
        </div>
    </div>
</div>
</body>
</html>
