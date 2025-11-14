<?php
/**
 * Progetto Magia Donna - Homepage
 *
 * Pagina principale del progetto
 */

require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            padding: 50px;
            max-width: 600px;
            text-align: center;
        }
        h1 {
            color: #667eea;
            margin-bottom: 20px;
            font-size: 2.5em;
        }
        p {
            color: #666;
            line-height: 1.6;
            margin-bottom: 30px;
        }
        .info-box {
            background: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 20px;
            margin: 20px 0;
            text-align: left;
        }
        .info-box h3 {
            color: #667eea;
            margin-bottom: 10px;
        }
        .info-box ul {
            list-style: none;
            padding-left: 0;
        }
        .info-box li {
            padding: 8px 0;
            border-bottom: 1px solid #e0e0e0;
        }
        .info-box li:last-child {
            border-bottom: none;
        }
        .btn {
            display: inline-block;
            background: #667eea;
            color: white;
            padding: 15px 30px;
            border-radius: 50px;
            text-decoration: none;
            margin: 10px;
            transition: all 0.3s;
        }
        .btn:hover {
            background: #764ba2;
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        }
        .version {
            color: #999;
            font-size: 0.9em;
            margin-top: 30px;
        }
        .status {
            display: inline-block;
            background: #4caf50;
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.9em;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <span class="status">✓ Sistema Attivo</span>
        <h1>🌟 <?php echo SITE_NAME; ?></h1>
        <p>Benvenuto nel sistema di gestione Progetto Magia Donna</p>

        <div class="info-box">
            <h3>📋 Informazioni Sistema</h3>
            <ul>
                <li><strong>Versione:</strong> <?php echo SITE_VERSION; ?></li>
                <li><strong>Ambiente:</strong> Produzione</li>
                <li><strong>Database:</strong> <?php
                    try {
                        echo $pdo ? '✅ Connesso' : '❌ Non connesso';
                    } catch (Exception $e) {
                        echo '❌ Errore connessione';
                    }
                ?></li>
                <li><strong>Timezone:</strong> <?php echo date_default_timezone_get(); ?></li>
            </ul>
        </div>

        <div class="info-box">
            <h3>🚀 Prossimi Passi</h3>
            <ul>
                <li>1. Configurare le credenziali del database in <code>config.php</code></li>
                <li>2. Eseguire <code>database_setup.php</code> per inizializzare il DB</li>
                <li>3. Caricare il PDF delle linee guida del progetto</li>
                <li>4. Iniziare lo sviluppo delle funzionalità</li>
            </ul>
        </div>

        <a href="admin/" class="btn">🔐 Area Admin</a>
        <a href="database_setup.php" class="btn">⚙️ Setup Database</a>

        <p class="version">
            Progetto Magia Donna v<?php echo SITE_VERSION; ?><br>
            Deploy automatico attivo via GitHub Actions
        </p>
    </div>
</body>
</html>
