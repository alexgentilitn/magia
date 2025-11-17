<?php
/**
 * Test utente ID 5 e controller permessi
 */

header('Content-Type: text/plain; charset=utf-8');

echo "=== TEST UTENTE ID 5 ===\n\n";

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Utente;
use App\Models\Permesso;
use App\Http\Controllers\Admin\PermessiCollaboratoreController;

try {
    echo "1. Verifica utente ID 5...\n";

    $utente = Utente::find(5);

    if ($utente) {
        echo "✅ Utente ID 5 ESISTE\n";
        echo "   Nome: {$utente->nome} {$utente->cognome}\n";
        echo "   Email: {$utente->email}\n";
        echo "   Tipo: {$utente->tipo_utente}\n";
        echo "   Ruolo ID: {$utente->ruolo_id}\n\n";

        if ($utente->tipo_utente !== 'professionista') {
            echo "⚠️ ATTENZIONE: L'utente NON è un professionista!\n";
            echo "   Il controller richiede tipo_utente = 'professionista'\n\n";
        }

        // Verifica ruolo
        if ($utente->ruolo) {
            echo "✅ Ruolo associato: {$utente->ruolo->nome}\n\n";
        } else {
            echo "❌ Nessun ruolo associato\n\n";
        }

        // Verifica professionista
        if ($utente->professionista) {
            echo "✅ Record professionista associato\n";
            echo "   ID professionista: {$utente->professionista->id}\n\n";
        } else {
            echo "⚠️ Nessun record nella tabella 'professionisti'\n\n";
        }

    } else {
        echo "❌ Utente ID 5 NON ESISTE\n\n";
    }

    echo "2. Lista tutti i professionisti...\n";
    $professionisti = Utente::where('tipo_utente', 'professionista')->get();

    if ($professionisti->count() > 0) {
        foreach ($professionisti as $p) {
            echo "  - ID: {$p->id} - {$p->nome} {$p->cognome} ({$p->email}) - Ruolo: " . ($p->ruolo ? $p->ruolo->nome : 'nessuno') . "\n";
        }
    } else {
        echo "  Nessun professionista trovato!\n";
    }

    echo "\n3. Verifica permessi disponibili...\n";
    $permessi = Permesso::attivi()->count();
    echo "  Permessi attivi nel sistema: {$permessi}\n\n";

    if ($utente && $utente->tipo_utente === 'professionista') {
        echo "4. Test controller con ID 5...\n";

        try {
            $controller = new PermessiCollaboratoreController();
            $response = $controller->edit(5);

            echo "✅ Controller eseguito con successo!\n";
            echo "   Tipo risposta: " . get_class($response) . "\n";

            if (method_exists($response, 'getStatusCode')) {
                echo "   Status: " . $response->getStatusCode() . "\n";
            }

        } catch (\Exception $e) {
            echo "❌ ERRORE nel controller:\n";
            echo "   Tipo: " . get_class($e) . "\n";
            echo "   Messaggio: " . $e->getMessage() . "\n";
            echo "   File: " . $e->getFile() . ":" . $e->getLine() . "\n\n";
            echo "   Stack trace:\n";

            $trace = $e->getTrace();
            foreach (array_slice($trace, 0, 5) as $i => $t) {
                $file = isset($t['file']) ? basename($t['file']) : 'unknown';
                $line = isset($t['line']) ? $t['line'] : '?';
                $function = isset($t['function']) ? $t['function'] : 'unknown';
                echo "     #{$i} {$file}:{$line} {$function}()\n";
            }
        }
    }

} catch (\Exception $e) {
    echo "❌ ERRORE CRITICO:\n";
    echo "   Messaggio: " . $e->getMessage() . "\n";
    echo "   File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

echo "\n=== FINE ===\n";
