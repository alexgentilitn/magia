<?php
/**
 * Fix Problema Foreign Key Segnalazioni -> Users
 * Verifica e risolve il problema della foreign key
 *
 * ELIMINA QUESTO FILE dopo l'uso!
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Carica Laravel
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fix Foreign Key Segnalazioni - MA.GIA DONNA</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.3);
            max-width: 900px;
            margin: 0 auto;
            padding: 40px;
        }
        h1 {
            color: #764ba2;
            margin-bottom: 10px;
            font-size: 28px;
        }
        .step {
            background: #f9fafb;
            border-left: 4px solid #667eea;
            padding: 15px 20px;
            margin: 15px 0;
            border-radius: 6px;
        }
        .step.success {
            background: #ecfdf5;
            border-color: #10b981;
        }
        .step.error {
            background: #fef2f2;
            border-color: #ef4444;
        }
        .step.warning {
            background: #fefce8;
            border-color: #eab308;
        }
        .step-title {
            font-weight: bold;
            margin-bottom: 8px;
            font-size: 16px;
        }
        .step-content {
            font-size: 14px;
            color: #555;
            line-height: 1.6;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            font-size: 14px;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }
        th {
            background: #f9fafb;
            font-weight: bold;
            color: #374151;
        }
        .code {
            background: #2d2d2d;
            color: #f8f8f2;
            padding: 10px;
            border-radius: 6px;
            font-family: 'Courier New', monospace;
            font-size: 13px;
            overflow-x: auto;
            margin: 10px 0;
        }
        .btn {
            display: inline-block;
            padding: 12px 30px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            margin: 10px 5px;
            border: none;
            cursor: pointer;
            font-size: 16px;
        }
        .warning-box {
            background: #fff3cd;
            border: 2px solid #ffc107;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            text-align: center;
            color: #856404;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 Fix Foreign Key Segnalazioni</h1>
        <p style="color: #666; margin-bottom: 30px; font-size: 14px;">
            Diagnosi e risoluzione del problema foreign key utente_id
        </p>

<?php

try {
    // 1. Verifica utente loggato
    echo '<div class="step">';
    echo '<div class="step-title">👤 1. Utente Loggato</div>';
    echo '<div class="step-content">';

    if (Auth::check()) {
        $currentUser = Auth::user();
        echo '<strong>Utente corrente:</strong><br>';
        echo 'ID: ' . $currentUser->id . '<br>';
        echo 'Nome: ' . $currentUser->nome . ' ' . $currentUser->cognome . '<br>';
        echo 'Email: ' . $currentUser->email . '<br>';
        echo 'Tipo: ' . $currentUser->tipo_utente . '<br>';
    } else {
        echo '<strong style="color: #ef4444;">⚠️ Nessun utente loggato!</strong><br>';
        echo 'Devi essere loggato per usare il sistema di segnalazioni.';
    }

    echo '</div>';
    echo '</div>';

    // 2. Lista tutti gli utenti
    echo '<div class="step">';
    echo '<div class="step-title">📋 2. Utenti nel Database</div>';

    $users = DB::table('users')->select('id', 'nome', 'cognome', 'email', 'tipo_utente')->get();

    if ($users->count() > 0) {
        echo '<table>';
        echo '<thead><tr><th>ID</th><th>Nome</th><th>Email</th><th>Tipo</th></tr></thead>';
        echo '<tbody>';
        foreach ($users as $user) {
            echo '<tr>';
            echo '<td><strong>' . $user->id . '</strong></td>';
            echo '<td>' . htmlspecialchars($user->nome . ' ' . $user->cognome) . '</td>';
            echo '<td>' . htmlspecialchars($user->email) . '</td>';
            echo '<td>' . htmlspecialchars($user->tipo_utente) . '</td>';
            echo '</tr>';
        }
        echo '</tbody></table>';

        echo '<div class="step-content">';
        echo '<strong>Totale utenti:</strong> ' . $users->count();
        echo '</div>';
    } else {
        echo '<div class="step-content" style="color: #ef4444;">';
        echo '<strong>⚠️ Nessun utente trovato nel database!</strong>';
        echo '</div>';
    }

    echo '</div>';

    // 3. Verifica tabella segnalazioni
    echo '<div class="step">';
    echo '<div class="step-title">📊 3. Tabella Segnalazioni</div>';

    if (Schema::hasTable('segnalazioni')) {
        $segnalazioni = DB::table('segnalazioni')
            ->select('id', 'utente_id', 'titolo', 'stato', 'created_at')
            ->get();

        if ($segnalazioni->count() > 0) {
            echo '<table>';
            echo '<thead><tr><th>ID</th><th>Utente ID</th><th>Titolo</th><th>Stato</th><th>Data</th></tr></thead>';
            echo '<tbody>';
            foreach ($segnalazioni as $seg) {
                echo '<tr>';
                echo '<td>' . $seg->id . '</td>';
                echo '<td>' . $seg->utente_id . '</td>';
                echo '<td>' . htmlspecialchars($seg->titolo) . '</td>';
                echo '<td>' . $seg->stato . '</td>';
                echo '<td>' . date('d/m/Y H:i', strtotime($seg->created_at)) . '</td>';
                echo '</tr>';
            }
            echo '</tbody></table>';
        } else {
            echo '<div class="step-content">';
            echo '<strong>Nessuna segnalazione presente.</strong>';
            echo '</div>';
        }
    } else {
        echo '<div class="step-content" style="color: #ef4444;">';
        echo '<strong>⚠️ La tabella segnalazioni non esiste!</strong>';
        echo '</div>';
    }

    echo '</div>';

    // 4. Verifica foreign keys
    echo '<div class="step">';
    echo '<div class="step-title">🔗 4. Foreign Keys</div>';

    $foreignKeys = DB::select("
        SELECT
            CONSTRAINT_NAME,
            TABLE_NAME,
            COLUMN_NAME,
            REFERENCED_TABLE_NAME,
            REFERENCED_COLUMN_NAME
        FROM information_schema.KEY_COLUMN_USAGE
        WHERE TABLE_SCHEMA = DATABASE()
        AND TABLE_NAME = 'segnalazioni'
        AND REFERENCED_TABLE_NAME IS NOT NULL
    ");

    if (count($foreignKeys) > 0) {
        echo '<table>';
        echo '<thead><tr><th>Constraint</th><th>Colonna</th><th>Riferimento</th></tr></thead>';
        echo '<tbody>';
        foreach ($foreignKeys as $fk) {
            echo '<tr>';
            echo '<td>' . $fk->CONSTRAINT_NAME . '</td>';
            echo '<td>' . $fk->COLUMN_NAME . '</td>';
            echo '<td>' . $fk->REFERENCED_TABLE_NAME . '.' . $fk->REFERENCED_COLUMN_NAME . '</td>';
            echo '</tr>';
        }
        echo '</tbody></table>';
    }

    echo '</div>';

    // 5. Diagnosi problema
    echo '<div class="step warning">';
    echo '<div class="step-title">🔍 5. Diagnosi Problema</div>';
    echo '<div class="step-content">';

    if (!Auth::check()) {
        echo '<strong style="color: #ef4444;">PROBLEMA IDENTIFICATO:</strong><br>';
        echo 'Non sei loggato! Devi accedere al pannello admin prima di creare segnalazioni.<br><br>';
        echo '<strong>Soluzione:</strong><br>';
        echo '1. Fai il login al pannello admin<br>';
        echo '2. Vai su Impostazioni → Segnalazioni<br>';
        echo '3. Crea la segnalazione';
    } else if ($users->count() == 0) {
        echo '<strong style="color: #ef4444;">PROBLEMA IDENTIFICATO:</strong><br>';
        echo 'Nessun utente nel database! Questo è molto strano.<br><br>';
        echo '<strong>Contatta lo sviluppatore</strong>';
    } else {
        $userIds = $users->pluck('id')->toArray();
        $currentUserId = Auth::check() ? Auth::id() : null;

        echo '<strong style="color: #10b981;">✅ TUTTO SEMBRA OK</strong><br><br>';
        echo '<strong>Utenti disponibili (ID validi):</strong> ' . implode(', ', $userIds) . '<br>';
        echo '<strong>Utente loggato (ID):</strong> ' . ($currentUserId ?? 'Non loggato') . '<br><br>';

        if ($currentUserId && in_array($currentUserId, $userIds)) {
            echo '<span style="color: #10b981;">✅ Il tuo ID utente esiste nel database</span><br>';
            echo '<span style="color: #10b981;">✅ Puoi creare segnalazioni senza problemi</span>';
        } else if ($currentUserId) {
            echo '<span style="color: #ef4444;">❌ Il tuo ID (' . $currentUserId . ') NON esiste nella tabella users!</span><br>';
            echo '<strong>Questo è un problema grave - contatta lo sviluppatore</strong>';
        }
    }

    echo '</div>';
    echo '</div>';

    // 6. Soluzione
    echo '<div class="step success">';
    echo '<div class="step-title">💡 6. Come Usare le Segnalazioni</div>';
    echo '<div class="step-content">';
    echo '<ol style="margin-left: 20px; line-height: 2;">';
    echo '<li><strong>Accedi al pannello admin</strong> (se non sei già loggato)</li>';
    echo '<li>Vai su <strong>Impostazioni → Tab "Segnalazioni"</strong></li>';
    echo '<li>Compila il form con:</li>';
    echo '<ul style="margin-left: 20px; margin-top: 5px;">';
    echo '<li>Tipo (Bug/Suggerimento/Altro)</li>';
    echo '<li>Priorità (Bassa/Media/Alta)</li>';
    echo '<li>Titolo e descrizione</li>';
    echo '<li>Email per notifiche (opzionale)</li>';
    echo '</ul>';
    echo '<li>Clicca <strong>"Invia Segnalazione"</strong></li>';
    echo '</ol>';
    echo '</div>';
    echo '</div>';

} catch (\Exception $e) {
    echo '<div class="step error">';
    echo '<div class="step-title">❌ Errore</div>';
    echo '<div class="step-content">';
    echo '<strong>Si è verificato un errore:</strong><br>';
    echo htmlspecialchars($e->getMessage());
    echo '</div>';
    echo '</div>';
}

?>

        <div style="margin-top: 30px; text-align: center;">
            <a href="/magia/public/admin/impostazioni" class="btn">
                📊 Vai alle Impostazioni
            </a>
            <a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="btn">
                🔄 Ricarica
            </a>
        </div>

        <div class="warning-box">
            <strong>⚠️ ELIMINA QUESTO FILE</strong> dopo aver risolto:<br>
            <code>/public/fix-segnalazioni-users.php</code>
        </div>
    </div>
</body>
</html>
