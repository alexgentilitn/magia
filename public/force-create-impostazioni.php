<?php
/**
 * Script di Migrazione: Crea tabella impostazioni
 *
 * Esegui questo script DOPO aver fatto il deploy del codice:
 * https://tuosito.com/force-create-impostazioni.php
 *
 * Funzionalità:
 * - Crea tabella impostazioni con schema completo
 * - Inserisce configurazioni SMTP di default
 * - Gestisce criptazione password
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
echo "  CREAZIONE TABELLA IMPOSTAZIONI\n";
echo "  Sistema Configurazione SMTP\n";
echo "==============================================\n\n";

try {
    // Verifica se la tabella esiste già
    if (Schema::hasTable('impostazioni')) {
        echo "✓ La tabella 'impostazioni' esiste già!\n\n";

        $count = DB::table('impostazioni')->count();
        echo "Record presenti: {$count}\n";

        echo "\nNessuna modifica necessaria.\n";
    } else {
        echo "Creazione tabella 'impostazioni'...\n\n";

        // Crea tabella
        Schema::create('impostazioni', function (Blueprint $table) {
            $table->id();
            $table->string('chiave', 100)->unique();
            $table->text('valore')->nullable();
            $table->string('gruppo', 50)->default('generale')->index();
            $table->string('tipo', 20)->default('testo');
            $table->text('descrizione')->nullable();
            $table->boolean('criptata')->default(false);
            $table->timestamps();
        });

        echo "✓ Tabella 'impostazioni' creata con successo!\n\n";

        // Inserisci impostazioni SMTP di default
        echo "Inserimento configurazioni SMTP di default...\n\n";

        DB::table('impostazioni')->insert([
            [
                'chiave' => 'smtp_host',
                'valore' => 'smtp.gmail.com',
                'gruppo' => 'smtp',
                'tipo' => 'testo',
                'descrizione' => 'Host del server SMTP',
                'criptata' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'chiave' => 'smtp_porta',
                'valore' => '587',
                'gruppo' => 'smtp',
                'tipo' => 'numero',
                'descrizione' => 'Porta del server SMTP',
                'criptata' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'chiave' => 'smtp_username',
                'valore' => '',
                'gruppo' => 'smtp',
                'tipo' => 'email',
                'descrizione' => 'Username/Email per autenticazione SMTP',
                'criptata' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'chiave' => 'smtp_password',
                'valore' => '',
                'gruppo' => 'smtp',
                'tipo' => 'password',
                'descrizione' => 'Password per autenticazione SMTP',
                'criptata' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'chiave' => 'smtp_encryption',
                'valore' => 'tls',
                'gruppo' => 'smtp',
                'tipo' => 'testo',
                'descrizione' => 'Tipo di encryption (tls, ssl, none)',
                'criptata' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'chiave' => 'mail_from_address',
                'valore' => 'noreply@magiadonna.it',
                'gruppo' => 'smtp',
                'tipo' => 'email',
                'descrizione' => 'Indirizzo email mittente',
                'criptata' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'chiave' => 'mail_from_name',
                'valore' => 'MA.GIA DONNA',
                'gruppo' => 'smtp',
                'tipo' => 'testo',
                'descrizione' => 'Nome mittente',
                'criptata' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        echo "✓ Configurazioni SMTP inserite!\n";
    }

    echo "\n==============================================\n";
    echo "RIEPILOGO CONFIGURAZIONI:\n";
    echo "==============================================\n";

    $impostazioni = DB::table('impostazioni')->where('gruppo', 'smtp')->get();
    foreach ($impostazioni as $imp) {
        $valore = $imp->criptata ? '[CRIPTATA]' : ($imp->valore ?: '[VUOTO]');
        echo "  • {$imp->chiave}: {$valore}\n";
    }

    echo "\n==============================================\n";
    echo "PROSSIMI PASSI:\n";
    echo "==============================================\n";
    echo "1. Vai su: Area Admin → Impostazioni → SMTP\n";
    echo "2. Configura i dati del tuo server SMTP\n";
    echo "3. Usa il pulsante 'Test Email' per verificare\n";
    echo "4. Le email saranno inviate automaticamente dal sistema\n";

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
