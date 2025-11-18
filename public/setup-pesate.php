<?php
/**
 * Script di Setup Tabella Pesate
 * Crea automaticamente la tabella pesate nel database
 *
 * IMPORTANTE: Elimina questo file dopo l'uso per motivi di sicurezza!
 */

// Carica l'autoloader di Laravel
require __DIR__.'/../vendor/autoload.php';

// Carica l'applicazione Laravel
$app = require_once __DIR__.'/../bootstrap/app.php';

// Avvia Laravel
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup Tabella Pesate - MA.GIA DONNA</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            max-width: 800px;
            width: 100%;
            padding: 40px;
        }
        h1 {
            color: #764ba2;
            margin-bottom: 10px;
            font-size: 28px;
        }
        .subtitle {
            color: #666;
            margin-bottom: 30px;
            font-size: 14px;
        }
        .status-box {
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid;
        }
        .status-box.info {
            background: #e3f2fd;
            border-color: #2196F3;
            color: #1565C0;
        }
        .status-box.success {
            background: #e8f5e9;
            border-color: #4CAF50;
            color: #2E7D32;
        }
        .status-box.error {
            background: #ffebee;
            border-color: #f44336;
            color: #c62828;
        }
        .status-box.warning {
            background: #fff3e0;
            border-color: #ff9800;
            color: #e65100;
        }
        .status-icon {
            font-size: 24px;
            margin-right: 10px;
            vertical-align: middle;
        }
        .btn {
            display: inline-block;
            padding: 12px 30px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            transition: transform 0.2s;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(118, 75, 162, 0.3);
        }
        .btn-secondary {
            background: #6c757d;
        }
        .code {
            background: #f5f5f5;
            padding: 15px;
            border-radius: 6px;
            font-family: 'Courier New', monospace;
            font-size: 14px;
            margin: 15px 0;
            border: 1px solid #ddd;
        }
        .details {
            margin: 20px 0;
            padding: 15px;
            background: #f9f9f9;
            border-radius: 6px;
            font-size: 14px;
        }
        .details strong {
            color: #764ba2;
        }
        .actions {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #f0f0f0;
            text-align: center;
        }
        .warning-banner {
            background: #fff3cd;
            border: 2px solid #ffc107;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
            color: #856404;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>⚙️ Setup Tabella Pesate</h1>
        <p class="subtitle">Sistema di Gestione Pesate Clienti - MA.GIA DONNA</p>

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

$action = $_GET['action'] ?? 'check';

try {
    if ($action === 'check') {
        // VERIFICA SE LA TABELLA ESISTE
        if (Schema::hasTable('pesate')) {
            $count = DB::table('pesate')->count();
            echo '<div class="status-box success">';
            echo '<span class="status-icon">✅</span>';
            echo '<strong>La tabella "pesate" esiste già!</strong><br>';
            echo "Contiene attualmente <strong>{$count}</strong> record.";
            echo '</div>';

            echo '<div class="details">';
            echo '<strong>Stato:</strong> Tutto configurato correttamente<br>';
            echo '<strong>Prossimo passo:</strong> Puoi iniziare a inserire le pesate dei clienti';
            echo '</div>';

            echo '<div class="actions">';
            echo '<a href="' . $_SERVER['PHP_SELF'] . '?action=test" class="btn">🧪 Testa Inserimento</a> ';
            echo '<a href="/magia/public/admin/dashboard" class="btn btn-secondary">← Torna al Dashboard</a>';
            echo '</div>';

        } else {
            echo '<div class="status-box warning">';
            echo '<span class="status-icon">⚠️</span>';
            echo '<strong>La tabella "pesate" NON esiste!</strong><br>';
            echo 'Questo è il motivo per cui il salvataggio delle pesate non funziona.';
            echo '</div>';

            echo '<div class="details">';
            echo '<strong>Problema:</strong> Tabella mancante nel database<br>';
            echo '<strong>Soluzione:</strong> Clicca il pulsante qui sotto per crearla automaticamente<br>';
            echo '<strong>Tempo richiesto:</strong> Circa 2-3 secondi';
            echo '</div>';

            echo '<div class="actions">';
            echo '<a href="' . $_SERVER['PHP_SELF'] . '?action=create" class="btn">🚀 Crea Tabella Ora</a>';
            echo '</div>';
        }

    } elseif ($action === 'create') {
        // CREA LA TABELLA
        echo '<div class="status-box info">';
        echo '<span class="status-icon">⏳</span>';
        echo '<strong>Creazione tabella in corso...</strong>';
        echo '</div>';

        // Esegui la migration
        Artisan::call('migrate', [
            '--path' => 'database/migrations/2025_11_18_160408_create_pesate_table.php',
            '--force' => true
        ]);

        $output = Artisan::output();

        // Verifica che sia stata creata
        if (Schema::hasTable('pesate')) {
            echo '<div class="status-box success">';
            echo '<span class="status-icon">✅</span>';
            echo '<strong>Tabella "pesate" creata con successo!</strong><br>';
            echo 'Il sistema è ora pronto per salvare le pesate dei clienti.';
            echo '</div>';

            echo '<div class="code">' . htmlspecialchars($output) . '</div>';

            echo '<div class="details">';
            echo '<strong>Campi creati:</strong> 17 campi (peso, BMI, grasso corporeo, massa muscolare, ecc.)<br>';
            echo '<strong>Indici:</strong> 3 indici per ottimizzare le performance<br>';
            echo '<strong>Foreign Key:</strong> Collegamento alla tabella clienti';
            echo '</div>';

            echo '<div class="actions">';
            echo '<a href="' . $_SERVER['PHP_SELF'] . '?action=test" class="btn">🧪 Testa Inserimento</a> ';
            echo '<a href="/magia/public/admin/dashboard" class="btn btn-secondary">← Torna al Dashboard</a>';
            echo '</div>';

        } else {
            throw new Exception('La tabella non è stata creata correttamente');
        }

    } elseif ($action === 'test') {
        // TESTA L'INSERIMENTO
        echo '<div class="status-box success">';
        echo '<span class="status-icon">✅</span>';
        echo '<strong>Sistema pronto!</strong>';
        echo '</div>';

        echo '<div class="details">';
        echo '<h3 style="margin-bottom: 15px;">Come testare il salvataggio:</h3>';
        echo '<ol style="line-height: 2; margin-left: 20px;">';
        echo '<li>Vai su <strong>Admin → Clienti</strong></li>';
        echo '<li>Seleziona un cliente qualsiasi</li>';
        echo '<li>Clicca su <strong>"Pesate"</strong></li>';
        echo '<li>Clicca su <strong>"Nuova Pesata"</strong></li>';
        echo '<li>Inserisci:<br>- Data: oggi<br>- Peso: 70.5</li>';
        echo '<li>Clicca <strong>"Salva Pesata"</strong></li>';
        echo '</ol>';
        echo '<p style="margin-top: 15px;"><strong>Risultato atteso:</strong></p>';
        echo '<p>✅ Alert verde con "Pesata aggiunta con successo!"<br>';
        echo '✅ La pesata appare nella lista<br>';
        echo '✅ I grafici si aggiornano</p>';
        echo '</div>';

        echo '<div class="actions">';
        echo '<a href="/magia/public/admin/clienti" class="btn">👥 Vai ai Clienti</a> ';
        echo '<a href="/magia/public/admin/dashboard" class="btn btn-secondary">← Dashboard</a>';
        echo '</div>';
    }

} catch (Exception $e) {
    echo '<div class="status-box error">';
    echo '<span class="status-icon">❌</span>';
    echo '<strong>Errore durante l\'operazione:</strong><br>';
    echo htmlspecialchars($e->getMessage());
    echo '</div>';

    echo '<div class="code">';
    echo '<strong>Dettagli tecnici:</strong><br>';
    echo 'File: ' . $e->getFile() . '<br>';
    echo 'Riga: ' . $e->getLine();
    echo '</div>';

    echo '<div class="actions">';
    echo '<a href="' . $_SERVER['PHP_SELF'] . '" class="btn">🔄 Riprova</a>';
    echo '</div>';
}

?>

        <div class="warning-banner">
            <strong>⚠️ IMPORTANTE:</strong> Per motivi di sicurezza, elimina questo file dopo aver completato il setup:<br>
            <code style="background: white; padding: 5px 10px; border-radius: 4px; display: inline-block; margin-top: 10px;">
                /public/setup-pesate.php
            </code>
        </div>
    </div>
</body>
</html>
