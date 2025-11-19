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

// Test 3: Verifica utente admin
echo "<h2>3️⃣ Verifica Utente Admin</h2>";
$email = isset($_GET['email']) ? $_GET['email'] : '';
if ($email) {
    try {
        $utente = \App\Models\Utente::where('email', $email)
            ->whereIn('tipo_utente', ['amministratore', 'professionista'])
            ->first();

        if ($utente) {
            echo "✅ Utente trovato: <strong>{$utente->nome} {$utente->cognome}</strong><br>";
            echo "Email: <strong>{$utente->email}</strong><br>";
            echo "Tipo: <strong>{$utente->tipo_utente}</strong><br>";
        } else {
            echo "❌ Nessun admin/professionista trovato con email: {$email}<br>";
        }
    } catch (\Exception $e) {
        echo "❌ Errore: " . $e->getMessage() . "<br>";
    }
} else {
    echo "ℹ️ Aggiungi ?email=tuaemail@domain.com all'URL per verificare un utente specifico<br>";
}

// Test 4: Test invio email (solo se richiesto esplicitamente)
if (isset($_GET['test_send']) && $_GET['test_send'] === 'yes' && $email) {
    echo "<h2>4️⃣ Test Invio Email</h2>";
    try {
        // Applica config SMTP
        \App\Models\Impostazione::applySmtpConfig();
        echo "✅ Configurazioni SMTP applicate<br>";

        // Genera token di test
        $token = \Str::random(64);

        // Trova utente
        $utente = \App\Models\Utente::where('email', $email)
            ->whereIn('tipo_utente', ['amministratore', 'professionista'])
            ->first();

        if (!$utente) {
            echo "❌ Utente non trovato<br>";
        } else {
            // Salva token (per test)
            \DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $email],
                [
                    'email' => $email,
                    'token' => \Hash::make($token),
                    'created_at' => now(),
                ]
            );
            echo "✅ Token generato e salvato<br>";

            // Invia email
            \Mail::to($email)->send(new \App\Mail\ResetPasswordMail($utente, $token));
            echo "✅ <strong style='color: green;'>EMAIL INVIATA CON SUCCESSO!</strong><br>";
            echo "Controlla la casella di posta di: <strong>{$email}</strong><br>";
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
