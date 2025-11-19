<?php
/**
 * Script Debug: Test invio email reset password
 */

// Bootstrap Laravel
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "<h1>🔍 Debug Email Reset Password</h1><hr>";

// Test 1: Verifica tabella password_reset_tokens
echo "<h2>1️⃣ Verifica Tabella password_reset_tokens</h2>";
try {
    $hasTable = Schema::hasTable('password_reset_tokens');
    echo $hasTable ? "✅ Tabella esiste<br>" : "❌ Tabella NON esiste - esegui migration!<br>";
} catch (\Exception $e) {
    echo "❌ Errore: " . $e->getMessage() . "<br>";
}

// Test 2: Verifica configurazioni SMTP nel database
echo "<h2>2️⃣ Configurazioni SMTP</h2>";
try {
    $smtp = \App\Models\Impostazione::getSmtpConfig();
    echo "Host: <strong>" . ($smtp['host'] ?? 'NON CONFIGURATO') . "</strong><br>";
    echo "Port: <strong>" . ($smtp['port'] ?? 'NON CONFIGURATO') . "</strong><br>";
    echo "Username: <strong>" . ($smtp['username'] ?? 'NON CONFIGURATO') . "</strong><br>";
    echo "Encryption: <strong>" . ($smtp['encryption'] ?? 'NON CONFIGURATO') . "</strong><br>";
    echo "From Address: <strong>" . ($smtp['from_address'] ?? 'NON CONFIGURATO') . "</strong><br>";
    echo "From Name: <strong>" . ($smtp['from_name'] ?? 'NON CONFIGURATO') . "</strong><br>";

    if (empty($smtp['host']) || empty($smtp['username'])) {
        echo "<div style='color: red; font-weight: bold; margin-top: 10px;'>⚠️ SMTP NON CONFIGURATO - configura in Admin → Impostazioni → Email</div>";
    }
} catch (\Exception $e) {
    echo "❌ Errore: " . $e->getMessage() . "<br>";
}

// Test 3: Mostra tutti gli utenti admin/professionisti
echo "<h2>3️⃣ Elenco Utenti Admin/Professionisti</h2>";
try {
    $utenti = \App\Models\Utente::whereIn('tipo_utente', ['amministratore', 'professionista'])
        ->orderBy('email')
        ->get();

    if ($utenti->count() > 0) {
        echo "<table border='1' cellpadding='8' cellspacing='0' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>Email</th><th>Nome</th><th>Tipo</th><th>Attivo</th></tr>";
        foreach ($utenti as $u) {
            $attivo = $u->attivo ? '✅' : '❌';
            echo "<tr>";
            echo "<td><strong>{$u->email}</strong></td>";
            echo "<td>{$u->nome} {$u->cognome}</td>";
            echo "<td>{$u->tipo_utente}</td>";
            echo "<td>{$attivo}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "❌ Nessun utente admin/professionista trovato nel database<br>";
    }
} catch (\Exception $e) {
    echo "❌ Errore: " . $e->getMessage() . "<br>";
}

// Test 4: Verifica utente admin specifico
echo "<h2>4️⃣ Verifica Utente Admin Specifico</h2>";
$email = isset($_GET['email']) ? trim($_GET['email']) : '';
if ($email) {
    try {
        // Prima cerca l'utente senza filtro tipo_utente per vedere se esiste (case-insensitive)
        $utenteQualsiasi = \App\Models\Utente::whereRaw('LOWER(email) = ?', [strtolower($email)])->first();

        if ($utenteQualsiasi) {
            echo "ℹ️ Utente trovato nel database:<br>";
            echo "Email: <strong>{$utenteQualsiasi->email}</strong><br>";
            echo "Nome: <strong>{$utenteQualsiasi->nome} {$utenteQualsiasi->cognome}</strong><br>";
            echo "Tipo: <strong>{$utenteQualsiasi->tipo_utente}</strong><br>";
            echo "Attivo: <strong>" . ($utenteQualsiasi->attivo ? 'Sì' : 'No') . "</strong><br><br>";

            // Poi verifica se è admin/professionista
            if (in_array($utenteQualsiasi->tipo_utente, ['amministratore', 'professionista'])) {
                echo "✅ <strong style='color: green;'>L'utente è un {$utenteQualsiasi->tipo_utente} - può recuperare la password</strong><br>";
            } else {
                echo "❌ <strong style='color: red;'>L'utente è un '{$utenteQualsiasi->tipo_utente}' - solo amministratori e professionisti possono recuperare la password</strong><br>";
            }
        } else {
            echo "❌ Nessun utente trovato con email: {$email}<br>";
            echo "ℹ️ Controlla che l'email sia scritta correttamente<br>";
        }
    } catch (\Exception $e) {
        echo "❌ Errore: " . $e->getMessage() . "<br>";
    }
} else {
    echo "ℹ️ Aggiungi ?email=tuaemail@domain.com all'URL per verificare un utente specifico<br>";
}

// Test 5: Test invio email (solo se richiesto esplicitamente)
if (isset($_GET['test_send']) && $_GET['test_send'] === 'yes' && $email) {
    echo "<h2>5️⃣ Test Invio Email</h2>";
    try {
        // Trova utente (case-insensitive)
        $utente = \App\Models\Utente::whereRaw('LOWER(email) = ?', [strtolower($email)])->first();

        if (!$utente) {
            echo "❌ Utente non trovato con email: {$email}<br>";
        } elseif (!in_array($utente->tipo_utente, ['amministratore', 'professionista'])) {
            echo "❌ L'utente è un '{$utente->tipo_utente}' - solo amministratori e professionisti possono recuperare la password<br>";
        } else {
            // Applica config SMTP
            \App\Models\Impostazione::applySmtpConfig();
            echo "✅ Configurazioni SMTP applicate<br>";

            // Genera token di test
            $token = \Str::random(64);
            // Salva token (per test) - usa email dal database per mantenere il case originale
            \DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $utente->email],
                [
                    'email' => $utente->email,
                    'token' => \Hash::make($token),
                    'created_at' => now(),
                ]
            );
            echo "✅ Token generato e salvato<br>";

            // Invia email - usa email dal database
            \Mail::to($utente->email)->send(new \App\Mail\ResetPasswordMail($utente, $token));
            echo "✅ <strong style='color: green;'>EMAIL INVIATA CON SUCCESSO!</strong><br>";
            echo "Controlla la casella di posta di: <strong>{$utente->email}</strong><br>";
        }
    } catch (\Exception $e) {
        echo "❌ <strong style='color: red;'>ERRORE INVIO EMAIL:</strong><br>";
        echo "<pre style='background: #fee; padding: 10px; border-radius: 5px;'>" . $e->getMessage() . "</pre>";
        echo "<strong>Stack trace:</strong><br>";
        echo "<pre style='background: #fee; padding: 10px; border-radius: 5px; font-size: 11px;'>" . $e->getTraceAsString() . "</pre>";
    }
}

echo "<hr>";
echo "<h2>🧪 Azioni Disponibili</h2>";
echo "<ul>";
echo "<li><a href='?email=tuaemail@domain.com'>Verifica utente</a> (sostituisci con la tua email)</li>";
echo "<li><a href='?email=tuaemail@domain.com&test_send=yes' style='color: orange; font-weight: bold;'>TEST INVIO EMAIL</a> (sostituisci con la tua email)</li>";
echo "</ul>";

echo "<hr>";
echo "<p style='color: red;'><strong>⚠️ ELIMINA QUESTO FILE dopo il debug!</strong></p>";
if (isset($_GET['delete'])) {
    unlink(__FILE__);
    echo "✅ File eliminato!";
    exit;
}
echo "<a href='?delete=1' style='color: red;'>Elimina questo file</a>";
