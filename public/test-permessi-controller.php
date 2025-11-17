<?php
/**
 * Test diretto controller permessi
 */

header('Content-Type: text/plain; charset=utf-8');

echo "=== TEST CONTROLLER PERMESSI ===\n\n";

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Http\Controllers\Admin\PermessiCollaboratoreController;
use Illuminate\Http\Request;

try {
    echo "1. Verifica classe controller...\n";
    if (class_exists('App\Http\Controllers\Admin\PermessiCollaboratoreController')) {
        echo "✅ Classe PermessiCollaboratoreController esiste\n\n";
    } else {
        echo "❌ Classe PermessiCollaboratoreController NON esiste\n";
        exit(1);
    }

    echo "2. Verifica metodo edit...\n";
    $controller = new PermessiCollaboratoreController();
    if (method_exists($controller, 'edit')) {
        echo "✅ Metodo edit() esiste\n\n";
    } else {
        echo "❌ Metodo edit() NON esiste\n";
        exit(1);
    }

    echo "3. Test chiamata metodo edit con utente ID 4...\n\n";

    try {
        $request = Request::create('/admin/professionisti/4/permessi', 'GET');
        $response = $controller->edit(4);

        echo "✅ Metodo edit(4) eseguito senza errori!\n";
        echo "Tipo risposta: " . get_class($response) . "\n";

        if (method_exists($response, 'getStatusCode')) {
            echo "Status code: " . $response->getStatusCode() . "\n";
        }

    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        echo "❌ ERRORE: Utente professionista con ID 4 non trovato nel database\n";
        echo "Messaggio: " . $e->getMessage() . "\n\n";

        echo "Verifica utenti professionisti disponibili:\n";
        $professionisti = \App\Models\Utente::where('tipo_utente', 'professionista')->get(['id', 'nome', 'cognome', 'email']);

        if ($professionisti->count() > 0) {
            foreach ($professionisti as $p) {
                echo "  - ID: {$p->id} - {$p->nome} {$p->cognome} ({$p->email})\n";
            }
        } else {
            echo "  Nessun professionista trovato!\n";
        }

    } catch (\Exception $e) {
        echo "❌ ERRORE durante esecuzione metodo:\n";
        echo "Tipo: " . get_class($e) . "\n";
        echo "Messaggio: " . $e->getMessage() . "\n";
        echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
        echo "\nTrace:\n" . $e->getTraceAsString() . "\n";
    }

} catch (\Exception $e) {
    echo "❌ ERRORE CRITICO:\n";
    echo "Messaggio: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

echo "\n=== FINE ===\n";
