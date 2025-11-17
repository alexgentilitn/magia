<?php
/**
 * Verifica estensioni PHP richieste da Laravel
 */

define('CHECK_TOKEN', 'magia2025');

if (!isset($_GET['token']) || $_GET['token'] !== CHECK_TOKEN) {
    die('<h1>Accesso Negato</h1><p>Usa: ?token=' . CHECK_TOKEN . '</p>');
}

?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Verifica Estensioni PHP</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
            margin: 0;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            padding: 30px;
        }
        h1 {
            color: #333;
            border-bottom: 3px solid #667eea;
            padding-bottom: 15px;
        }
        .extension {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px;
            margin: 8px 0;
            border-radius: 6px;
            background: #f8f9fa;
        }
        .enabled {
            background: #d4edda;
            border-left: 4px solid #28a745;
        }
        .disabled {
            background: #f8d7da;
            border-left: 4px solid #dc3545;
        }
        .badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 0.85em;
        }
        .badge.ok { background: #28a745; color: white; }
        .badge.error { background: #dc3545; color: white; }
        .instructions {
            background: #fff3cd;
            border: 2px solid #ffc107;
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
        }
        .code {
            background: #1e1e1e;
            color: #dcdcdc;
            padding: 15px;
            border-radius: 6px;
            font-family: monospace;
            overflow-x: auto;
            margin: 10px 0;
        }
        ol li { margin: 10px 0; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Verifica Estensioni PHP</h1>

        <h2>📦 Estensioni Richieste da Laravel</h2>

        <?php
        $requiredExtensions = [
            'fileinfo' => 'Richiesta per upload file (MIME type detection)',
            'pdo' => 'Richiesta per connessione database',
            'pdo_mysql' => 'Richiesta per MySQL',
            'mbstring' => 'Richiesta per gestione stringhe multibyte',
            'openssl' => 'Richiesta per crittografia',
            'tokenizer' => 'Richiesta per parsing PHP',
            'xml' => 'Richiesta per gestione XML',
            'ctype' => 'Richiesta per validazione caratteri',
            'json' => 'Richiesta per gestione JSON',
            'bcmath' => 'Opzionale per calcoli precisione',
            'curl' => 'Richiesta per richieste HTTP',
            'gd' => 'Opzionale per manipolazione immagini',
            'zip' => 'Opzionale per gestione file ZIP',
        ];

        $missingCritical = [];
        $missingOptional = [];

        foreach ($requiredExtensions as $ext => $description) {
            $loaded = extension_loaded($ext);
            $isCritical = in_array($ext, ['fileinfo', 'pdo', 'pdo_mysql', 'mbstring', 'openssl', 'tokenizer', 'xml', 'ctype', 'json']);
            
            echo '<div class="extension ' . ($loaded ? 'enabled' : 'disabled') . '">';
            echo '<div>';
            echo '<strong>' . htmlspecialchars($ext) . '</strong>';
            echo '<div style="font-size: 0.85em; color: #666; margin-top: 4px;">' . htmlspecialchars($description) . '</div>';
            echo '</div>';
            echo '<span class="badge ' . ($loaded ? 'ok' : 'error') . '">';
            echo $loaded ? '✅ INSTALLATA' : '❌ MANCANTE';
            echo '</span>';
            echo '</div>';
            
            if (!$loaded) {
                if ($isCritical) {
                    $missingCritical[] = $ext;
                } else {
                    $missingOptional[] = $ext;
                }
            }
        }
        ?>

        <?php if (!empty($missingCritical)): ?>
        <div class="instructions">
            <h2>⚠️ AZIONE RICHIESTA</h2>
            <p><strong>Le seguenti estensioni CRITICHE mancano:</strong></p>
            <ul>
                <?php foreach ($missingCritical as $ext): ?>
                <li><strong><?php echo htmlspecialchars($ext); ?></strong></li>
                <?php endforeach; ?>
            </ul>

            <h3>🔧 Come Abilitare l'Estensione Fileinfo</h3>
            
            <p><strong>Se hai accesso a cPanel / Plesk:</strong></p>
            <ol>
                <li>Vai su <strong>cPanel → Select PHP Version</strong> (o Plesk → PHP Settings)</li>
                <li>Cerca l'estensione <strong>fileinfo</strong></li>
                <li>Spunta la checkbox per attivarla</li>
                <li>Clicca su <strong>Save</strong> o <strong>Apply</strong></li>
                <li>Ricarica questa pagina per verificare</li>
            </ol>

            <p><strong>Se hai accesso SSH:</strong></p>
            <div class="code">
# Per Debian/Ubuntu
sudo apt-get install php-fileinfo
sudo systemctl restart apache2

# Per CentOS/RHEL
sudo yum install php-fileinfo
sudo systemctl restart httpd
            </div>

            <p><strong>Se hai un php.ini personalizzato:</strong></p>
            <div class="code">
# Aggiungi o decommenta questa riga in php.ini:
extension=fileinfo
            </div>

            <p><strong>Contatta il tuo hosting provider se non riesci ad attivarla.</strong></p>
        </div>
        <?php elseif (!empty($missingOptional)): ?>
        <div class="instructions" style="border-color: #17a2b8; background: #d1ecf1;">
            <h2>ℹ️ Estensioni Opzionali Mancanti</h2>
            <p>Le seguenti estensioni sono opzionali ma consigliate:</p>
            <ul>
                <?php foreach ($missingOptional as $ext): ?>
                <li><strong><?php echo htmlspecialchars($ext); ?></strong></li>
                <?php endforeach; ?>
            </ul>
            <p>L'applicazione funzionerà comunque, ma alcune funzionalità potrebbero essere limitate.</p>
        </div>
        <?php else: ?>
        <div class="instructions" style="border-color: #28a745; background: #d4edda;">
            <h2>✅ Tutte le Estensioni Installate!</h2>
            <p>Il tuo server PHP ha tutte le estensioni richieste da Laravel.</p>
            <p><strong>Ora puoi provare a caricare la foto profilo!</strong></p>
            <p><a href="../admin/professionisti">→ Vai ai Professionisti</a></p>
        </div>
        <?php endif; ?>

        <h2>📊 Informazioni Server</h2>
        <div class="extension enabled">
            <strong>PHP Version:</strong>
            <span><?php echo phpversion(); ?></span>
        </div>
        <div class="extension enabled">
            <strong>Server Software:</strong>
            <span><?php echo htmlspecialchars($_SERVER['SERVER_SOFTWARE'] ?? 'N/A'); ?></span>
        </div>
        <div class="extension enabled">
            <strong>PHP SAPI:</strong>
            <span><?php echo php_sapi_name(); ?></span>
        </div>

        <div style="margin-top: 30px; padding: 15px; background: #f8d7da; border-radius: 6px;">
            <p><strong>⚠️ IMPORTANTE:</strong> Elimina questo file dopo aver verificato:</p>
            <div class="code">rm <?php echo __FILE__; ?></div>
        </div>
    </div>
</body>
</html>
