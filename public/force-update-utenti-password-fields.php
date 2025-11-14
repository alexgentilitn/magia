<?php
/**
 * Script di Migrazione: Aggiorna tabella utenti con campi password temporanea
 *
 * Esegui questo script DOPO aver fatto il deploy del codice:
 * https://tuosito.com/force-update-utenti-password-fields.php
 *
 * Campi aggiunti:
 * - password_temp_expires_at: timestamp scadenza password temporanea
 * - deve_cambiare_password: flag per forzare cambio password al login
 */

// Carica Laravel
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

echo "<pre>";
echo "==============================================\n";
echo "  AGGIORNAMENTO TABELLA UTENTI\n";
echo "  Campi Password Temporanea con Scadenza\n";
echo "==============================================\n\n";

try {
    // Verifica se i campi esistono già
    $hasPasswordTempExpires = Schema::hasColumn('utenti', 'password_temp_expires_at');
    $hasDeveChangePassword = Schema::hasColumn('utenti', 'deve_cambiare_password');

    if ($hasPasswordTempExpires && $hasDeveChangePassword) {
        echo "✓ I campi esistono già nella tabella 'utenti'!\n";
        echo "  - password_temp_expires_at: PRESENTE\n";
        echo "  - deve_cambiare_password: PRESENTE\n\n";
        echo "Nessuna modifica necessaria.\n";
    } else {
        echo "Aggiungo i nuovi campi alla tabella 'utenti'...\n\n";

        Schema::table('utenti', function (Blueprint $table) use ($hasPasswordTempExpires, $hasDeveChangePassword) {
            if (!$hasPasswordTempExpires) {
                $table->timestamp('password_temp_expires_at')->nullable()->after('password');
                echo "✓ Aggiunto campo: password_temp_expires_at\n";
            }

            if (!$hasDeveChangePassword) {
                $table->boolean('deve_cambiare_password')->default(false)->after('password_temp_expires_at');
                echo "✓ Aggiunto campo: deve_cambiare_password\n";
            }
        });

        echo "\n✓ Tabella 'utenti' aggiornata con successo!\n";
    }

    echo "\n==============================================\n";
    echo "RIEPILOGO CAMPI:\n";
    echo "==============================================\n";

    $columns = DB::select("DESCRIBE utenti");
    echo "\nCampi relativi alla password:\n";
    foreach ($columns as $column) {
        if (str_contains($column->Field, 'password') || $column->Field === 'deve_cambiare_password') {
            echo "  • {$column->Field} ({$column->Type}) - {$column->Null}\n";
        }
    }

    echo "\n==============================================\n";
    echo "FUNZIONALITÀ ABILITATE:\n";
    echo "==============================================\n";
    echo "✓ Password temporanee con scadenza automatica (24 ore)\n";
    echo "✓ Invio email automatico con credenziali ai professionisti\n";
    echo "✓ Reset password da admin con notifica email\n";
    echo "✓ Layout email standardizzato per tutte le comunicazioni\n";

    echo "\n==============================================\n";
    echo "✅ MIGRAZIONE COMPLETATA CON SUCCESSO!\n";
    echo "==============================================\n";

} catch (\Exception $e) {
    echo "\n❌ ERRORE durante la migrazione:\n";
    echo $e->getMessage() . "\n";
    echo "\nStack trace:\n";
    echo $e->getTraceAsString();
}

echo "</pre>";
