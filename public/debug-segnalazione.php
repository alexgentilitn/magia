<?php
/**
 * DIAGNOSTIC SCRIPT: Debug Segnalazione Issues
 * Controlla permessi utente e configurazione email
 *
 * ELIMINA DOPO L'USO!
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Carica Laravel
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debug Segnalazione #1</title>
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
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px;
        }
        h1 { color: #764ba2; margin-bottom: 20px; }
        h2 { color: #667eea; margin: 30px 0 15px; border-bottom: 2px solid #667eea; padding-bottom: 10px; }
        .box {
            background: #f9fafb;
            border-left: 4px solid #667eea;
            padding: 15px 20px;
            margin: 15px 0;
            border-radius: 6px;
        }
        .success { border-left-color: #10b981; background: #d1fae5; }
        .error { border-left-color: #ef4444; background: #fee2e2; }
        .warning { border-left-color: #f59e0b; background: #fef3c7; }
        .info { border-left-color: #3b82f6; background: #dbeafe; }
        .code {
            background: #2d2d2d;
            color: #f8f8f2;
            padding: 15px;
            border-radius: 6px;
            font-family: 'Courier New', monospace;
            font-size: 13px;
            overflow-x: auto;
            margin: 10px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }
        th {
            background: #f9fafb;
            font-weight: bold;
        }
        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: bold;
        }
        .badge-success { background: #d1fae5; color: #065f46; }
        .badge-error { background: #fee2e2; color: #991b1b; }
        .badge-warning { background: #fef3c7; color: #92400e; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Debug Segnalazione #1</h1>
        <p>Diagnostica problemi: 403 Forbidden + Email non inviata</p>

<?php

try {
    // ============================================
    // 1. CHECK AUTENTICAZIONE
    // ============================================
    echo '<h2>1️⃣ Autenticazione Utente</h2>';

    if (Auth::check()) {
        $user = Auth::user();
        echo '<div class="box success">';
        echo '<strong>✅ Utente autenticato:</strong><br>';
        echo '<table>';
        echo '<tr><th>Campo</th><th>Valore</th></tr>';
        echo '<tr><td><strong>ID</strong></td><td>' . $user->id . '</td></tr>';
        echo '<tr><td><strong>Name</strong></td><td>' . htmlspecialchars($user->name) . '</td></tr>';
        echo '<tr><td><strong>Email</strong></td><td>' . htmlspecialchars($user->email) . '</td></tr>';
        echo '<tr><td><strong>Tipo Utente</strong></td><td><span class="badge badge-success">' . htmlspecialchars($user->tipo_utente) . '</span></td></tr>';
        echo '</table>';
        echo '</div>';

        $isSuperAdmin = $user->tipo_utente === 'super_admin';

        if ($isSuperAdmin) {
            echo '<div class="box success">✅ L\'utente è <strong>SUPER ADMIN</strong> - può vedere tutte le segnalazioni</div>';
        } else {
            echo '<div class="box warning">⚠️ L\'utente è <strong>ADMIN</strong> - può vedere solo le proprie segnalazioni</div>';
        }

    } else {
        echo '<div class="box error">❌ <strong>NESSUN UTENTE AUTENTICATO</strong> - questo è il problema!</div>';
        echo '<p>Devi essere loggato per accedere alle segnalazioni.</p>';
        die();
    }

    // ============================================
    // 2. CHECK SEGNALAZIONE #1
    // ============================================
    echo '<h2>2️⃣ Segnalazione #1</h2>';

    $segnalazione = \App\Models\Segnalazione::with(['utente', 'risolutore'])->find(1);

    if (!$segnalazione) {
        echo '<div class="box error">❌ Segnalazione #1 non trovata nel database</div>';
    } else {
        echo '<div class="box success">✅ Segnalazione #1 trovata</div>';

        echo '<table>';
        echo '<tr><th>Campo</th><th>Valore</th></tr>';
        echo '<tr><td><strong>ID</strong></td><td>' . $segnalazione->id . '</td></tr>';
        echo '<tr><td><strong>Titolo</strong></td><td>' . htmlspecialchars($segnalazione->titolo) . '</td></tr>';
        echo '<tr><td><strong>Tipo</strong></td><td>' . htmlspecialchars($segnalazione->tipo) . '</td></tr>';
        echo '<tr><td><strong>Stato</strong></td><td><span class="badge badge-warning">' . htmlspecialchars($segnalazione->stato) . '</span></td></tr>';
        echo '<tr><td><strong>Priorità</strong></td><td>' . htmlspecialchars($segnalazione->priorita) . '</td></tr>';
        echo '<tr><td><strong>Email Notifica</strong></td><td>' . htmlspecialchars($segnalazione->email_notifica ?? 'NON IMPOSTATA') . '</td></tr>';
        echo '<tr><td><strong>Utente ID (creatore)</strong></td><td>' . $segnalazione->utente_id . '</td></tr>';

        if ($segnalazione->utente) {
            echo '<tr><td><strong>Nome Creatore</strong></td><td>' . htmlspecialchars($segnalazione->utente->name) . '</td></tr>';
        } else {
            echo '<tr><td><strong>Nome Creatore</strong></td><td><span class="badge badge-error">UTENTE NON TROVATO</span></td></tr>';
        }

        echo '<tr><td><strong>Risposta</strong></td><td>' . htmlspecialchars($segnalazione->risposta ?? 'Nessuna risposta') . '</td></tr>';
        echo '<tr><td><strong>Creata il</strong></td><td>' . $segnalazione->created_at->format('d/m/Y H:i:s') . '</td></tr>';
        echo '</table>';

        // ============================================
        // 3. CHECK PERMESSI
        // ============================================
        echo '<h2>3️⃣ Verifica Permessi</h2>';

        echo '<div class="box info">';
        echo '<strong>Logica controller (riga 73):</strong><br>';
        echo '<div class="code">';
        echo 'if (Auth::user()->tipo_utente !== \'super_admin\' && $segnalazione->utente_id !== Auth::id()) {<br>';
        echo '&nbsp;&nbsp;&nbsp;&nbsp;abort(403, \'Non hai i permessi...\');<br>';
        echo '}';
        echo '</div>';
        echo '</div>';

        echo '<table>';
        echo '<tr><th>Condizione</th><th>Valore</th><th>Risultato</th></tr>';

        $condizioneA = $user->tipo_utente !== 'super_admin';
        $condizioneB = $segnalazione->utente_id !== $user->id;

        echo '<tr>';
        echo '<td>tipo_utente !== \'super_admin\'</td>';
        echo '<td>"' . $user->tipo_utente . '" !== "super_admin"</td>';
        echo '<td>' . ($condizioneA ? '<span class="badge badge-warning">TRUE</span>' : '<span class="badge badge-success">FALSE</span>') . '</td>';
        echo '</tr>';

        echo '<tr>';
        echo '<td>segnalazione->utente_id !== Auth::id()</td>';
        echo '<td>' . $segnalazione->utente_id . ' !== ' . $user->id . '</td>';
        echo '<td>' . ($condizioneB ? '<span class="badge badge-warning">TRUE</span>' : '<span class="badge badge-success">FALSE</span>') . '</td>';
        echo '</tr>';

        echo '<tr style="background: #fef3c7;">';
        echo '<td><strong>Condizione AND (entrambe TRUE = 403)</strong></td>';
        echo '<td>' . ($condizioneA ? 'TRUE' : 'FALSE') . ' AND ' . ($condizioneB ? 'TRUE' : 'FALSE') . '</td>';

        $risultato = $condizioneA && $condizioneB;

        if ($risultato) {
            echo '<td><span class="badge badge-error">TRUE = ERRORE 403</span></td>';
        } else {
            echo '<td><span class="badge badge-success">FALSE = ACCESSO CONSENTITO</span></td>';
        }
        echo '</tr>';
        echo '</table>';

        if ($risultato) {
            echo '<div class="box error">';
            echo '<strong>❌ PROBLEMA IDENTIFICATO:</strong><br>';
            echo 'L\'utente loggato (ID: ' . $user->id . ') NON è super_admin E NON è il creatore (ID: ' . $segnalazione->utente_id . ')<br>';
            echo '<strong>Soluzione:</strong> Solo il creatore o un super admin possono vedere questa segnalazione.';
            echo '</div>';
        } else {
            echo '<div class="box success">';
            echo '<strong>✅ PERMESSI OK:</strong><br>';
            echo 'L\'utente può visualizzare questa segnalazione.';
            echo '</div>';
        }

        // ============================================
        // 4. CHECK CONFIGURAZIONE EMAIL
        // ============================================
        echo '<h2>4️⃣ Configurazione Email</h2>';

        if (!$segnalazione->email_notifica) {
            echo '<div class="box warning">';
            echo '⚠️ <strong>Email notifica NON impostata</strong> per questa segnalazione.<br>';
            echo 'Nessuna email verrà inviata anche se lo stato cambia.';
            echo '</div>';
        } else {
            echo '<div class="box success">';
            echo '✅ Email notifica impostata: <strong>' . htmlspecialchars($segnalazione->email_notifica) . '</strong>';
            echo '</div>';

            // Check SMTP config
            echo '<div class="box info">';
            echo '<strong>Configurazione SMTP attuale:</strong><br>';
            echo '<table>';
            echo '<tr><th>Parametro</th><th>Valore</th></tr>';
            echo '<tr><td>MAIL_MAILER</td><td>' . htmlspecialchars(config('mail.default')) . '</td></tr>';
            echo '<tr><td>MAIL_HOST</td><td>' . htmlspecialchars(config('mail.mailers.smtp.host')) . '</td></tr>';
            echo '<tr><td>MAIL_PORT</td><td>' . htmlspecialchars(config('mail.mailers.smtp.port')) . '</td></tr>';
            echo '<tr><td>MAIL_USERNAME</td><td>' . htmlspecialchars(config('mail.mailers.smtp.username') ?: 'NON IMPOSTATO') . '</td></tr>';
            echo '<tr><td>MAIL_ENCRYPTION</td><td>' . htmlspecialchars(config('mail.mailers.smtp.encryption') ?: 'none') . '</td></tr>';
            echo '<tr><td>MAIL_FROM_ADDRESS</td><td>' . htmlspecialchars(config('mail.from.address')) . '</td></tr>';
            echo '<tr><td>MAIL_FROM_NAME</td><td>' . htmlspecialchars(config('mail.from.name')) . '</td></tr>';
            echo '</table>';
            echo '</div>';

            // Check se Impostazione model può sovrascrivere config
            if (class_exists(\App\Models\Impostazione::class)) {
                echo '<div class="box info">';
                echo '✅ Model Impostazione esiste - controlliamo le impostazioni SMTP salvate nel DB<br><br>';

                $impostazioni = \App\Models\Impostazione::pluck('valore', 'chiave')->toArray();

                if (isset($impostazioni['smtp_host'])) {
                    echo '<strong>SMTP dal database (sovrascrive .env):</strong><br>';
                    echo '<table>';
                    echo '<tr><th>Chiave</th><th>Valore</th></tr>';
                    echo '<tr><td>smtp_host</td><td>' . htmlspecialchars($impostazioni['smtp_host'] ?? 'NON IMPOSTATO') . '</td></tr>';
                    echo '<tr><td>smtp_port</td><td>' . htmlspecialchars($impostazioni['smtp_port'] ?? 'NON IMPOSTATO') . '</td></tr>';
                    echo '<tr><td>smtp_username</td><td>' . htmlspecialchars($impostazioni['smtp_username'] ?? 'NON IMPOSTATO') . '</td></tr>';
                    echo '<tr><td>smtp_encryption</td><td>' . htmlspecialchars($impostazioni['smtp_encryption'] ?? 'NON IMPOSTATO') . '</td></tr>';
                    echo '<tr><td>smtp_from_address</td><td>' . htmlspecialchars($impostazioni['smtp_from_address'] ?? 'NON IMPOSTATO') . '</td></tr>';
                    echo '<tr><td>smtp_from_name</td><td>' . htmlspecialchars($impostazioni['smtp_from_name'] ?? 'NON IMPOSTATO') . '</td></tr>';
                    echo '</table>';
                } else {
                    echo '<div class="box warning">⚠️ SMTP non configurato nel database - usa valori da .env</div>';
                }
                echo '</div>';
            }
        }

        // ============================================
        // 5. TEST INVIO EMAIL (opzionale)
        // ============================================
        echo '<h2>5️⃣ Test Invio Email</h2>';

        if (isset($_GET['test_email']) && $_GET['test_email'] == '1' && $segnalazione->email_notifica) {
            echo '<div class="box warning">⚙️ Tentativo invio email di test...</div>';

            try {
                // Applica config SMTP
                if (class_exists(\App\Models\Impostazione::class)) {
                    \App\Models\Impostazione::applySmtpConfig();
                }

                // Invia email
                Mail::to($segnalazione->email_notifica)->send(
                    new \App\Mail\SegnalazioneStatoCambiatoMail($segnalazione, 'aperta', 'in_lavorazione')
                );

                echo '<div class="box success">✅ <strong>Email inviata con successo!</strong><br>';
                echo 'Controlla la casella: ' . htmlspecialchars($segnalazione->email_notifica);
                echo '</div>';

            } catch (\Exception $e) {
                echo '<div class="box error">❌ <strong>Errore invio email:</strong><br>';
                echo htmlspecialchars($e->getMessage());
                echo '</div>';
            }
        } else {
            if ($segnalazione->email_notifica) {
                echo '<div class="box info">';
                echo '💡 Vuoi testare l\'invio email?<br>';
                echo '<a href="?test_email=1" style="display: inline-block; margin-top: 10px; padding: 10px 20px; background: #667eea; color: white; text-decoration: none; border-radius: 6px;">📧 Invia Email di Test</a>';
                echo '</div>';
            }
        }
    }

} catch (\Exception $e) {
    echo '<div class="box error">';
    echo '<strong>❌ ERRORE:</strong><br>';
    echo htmlspecialchars($e->getMessage());
    echo '<br><br><pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
    echo '</div>';
}

?>

        <div style="margin-top: 40px; padding: 20px; background: #fff3cd; border: 2px solid #ffc107; border-radius: 8px; text-align: center;">
            <strong>⚠️ ELIMINA QUESTO FILE</strong> dopo il debug:<br>
            <code>/public/debug-segnalazione.php</code>
        </div>

        <div style="margin-top: 20px; text-align: center;">
            <a href="/magia/public/admin/impostazioni" style="display: inline-block; padding: 10px 20px; background: #667eea; color: white; text-decoration: none; border-radius: 6px;">⬅️ Torna a Impostazioni</a>
            <a href="?" style="display: inline-block; padding: 10px 20px; background: #10b981; color: white; text-decoration: none; border-radius: 6px; margin-left: 10px;">🔄 Ricarica</a>
        </div>

    </div>
</body>
</html>
