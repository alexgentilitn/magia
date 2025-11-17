<?php
/**
 * Script: Verifica Veloce Permessi e Ruoli
 * Versione semplificata senza colonna 'ordine'
 */

header('Content-Type: text/plain; charset=utf-8');
echo "=== VERIFICA PERMESSI E RUOLI ===\n\n";

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Permesso;
use App\Models\Ruolo;
use Illuminate\Support\Facades\DB;

try {
    // 1. PERMESSI
    echo "1️⃣ PERMESSI NEL DATABASE\n";
    echo "─────────────────────────\n";

    $permessiTotali = Permesso::count();
    $permessiAttivi = Permesso::where('attivo', true)->count();

    echo "Totali: {$permessiTotali}\n";
    echo "Attivi: {$permessiAttivi}\n\n";

    if ($permessiAttivi > 0) {
        echo "✅ OK - Ci sono permessi nel database\n\n";
    } else {
        echo "❌ PROBLEMA - Nessun permesso attivo!\n\n";
    }

    // 2. RUOLI
    echo "2️⃣ RUOLI NEL DATABASE\n";
    echo "─────────────────────────\n";

    $ruoli = Ruolo::all();
    foreach ($ruoli as $r) {
        $permessiCount = DB::table('ruolo_permesso')->where('ruolo_id', $r->id)->count();
        echo "• {$r->nome} (ID: {$r->id}, slug: {$r->slug})\n";
        echo "  Permessi assegnati: {$permessiCount}\n";
    }
    echo "\n";

    // 3. TABELLA PIVOT ruolo_permesso
    echo "3️⃣ TABELLA RUOLO_PERMESSO\n";
    echo "──────────────────────────\n";

    $ruoloPermessoCount = DB::table('ruolo_permesso')->count();
    echo "Record totali: {$ruoloPermessoCount}\n\n";

    if ($ruoloPermessoCount === 0) {
        echo "❌ PROBLEMA CRITICO!\n";
        echo "La tabella ruolo_permesso è VUOTA.\n";
        echo "Questo è il motivo per cui la pagina permessi appare bianca!\n\n";
        echo "SOLUZIONE:\n";
        echo "Devi assegnare permessi ai ruoli.\n";
        echo "Esempio SQL per assegnare tutti i permessi al ruolo Collaboratore:\n\n";

        $collaboratore = Ruolo::where('slug', 'collaboratore')->first();
        if ($collaboratore) {
            echo "INSERT INTO ruolo_permesso (ruolo_id, permesso_id, created_at, updated_at)\n";
            echo "SELECT {$collaboratore->id}, id, NOW(), NOW() FROM permessi WHERE attivo = 1;\n";
        }
    } else {
        echo "✅ OK - Ci sono permessi assegnati ai ruoli\n";

        // Dettaglio per ogni ruolo
        echo "\nDettaglio assegnazioni:\n";
        $assegnazioni = DB::table('ruolo_permesso')
            ->select('ruolo_id', DB::raw('COUNT(*) as count'))
            ->groupBy('ruolo_id')
            ->get();

        foreach ($assegnazioni as $ass) {
            $ruolo = Ruolo::find($ass->ruolo_id);
            if ($ruolo) {
                echo "  • {$ruolo->nome}: {$ass->count} permessi\n";
            }
        }
    }

    echo "\n";

    // 4. TABELLA PIVOT utente_permesso
    echo "4️⃣ TABELLA UTENTE_PERMESSO\n";
    echo "───────────────────────────\n";

    $utentePermessoCount = DB::table('utente_permesso')->count();
    echo "Permessi individuali assegnati: {$utentePermessoCount}\n\n";

    // 5. DIAGNOSI FINALE
    echo "═════════════════════════════════════\n";
    echo "📋 DIAGNOSI\n";
    echo "═════════════════════════════════════\n\n";

    $problemi = [];

    if ($permessiAttivi === 0) {
        $problemi[] = "❌ Nessun permesso attivo nel database";
    }

    if ($ruoloPermessoCount === 0) {
        $problemi[] = "❌ Nessun permesso assegnato ai ruoli (tabella ruolo_permesso vuota)";
    }

    if (count($problemi) > 0) {
        echo "PROBLEMI RILEVATI:\n";
        foreach ($problemi as $p) {
            echo "{$p}\n";
        }
        echo "\n";
        echo "➡️ La pagina permessi appare BIANCA perché non ci sono permessi da visualizzare.\n";
        echo "   Anche se l'utente ha un ruolo, il ruolo non ha permessi collegati.\n\n";

        echo "PROSSIMI PASSI:\n";
        echo "1. Esegui il seeder dei permessi (se disponibile)\n";
        echo "2. Oppure usa la query SQL sopra per assegnare i permessi\n";
        echo "3. Ricarica la pagina permessi\n";

    } else {
        echo "✅ TUTTO OK!\n";
        echo "Permessi: {$permessiAttivi}\n";
        echo "Assegnazioni ruoli: {$ruoloPermessoCount}\n\n";
        echo "Se la pagina permessi appare ancora bianca, potrebbe essere un problema\n";
        echo "di rendering della view o di JavaScript.\n";
    }

} catch (\Exception $e) {
    echo "\n❌ ERRORE:\n";
    echo $e->getMessage() . "\n";
    echo $e->getFile() . ":" . $e->getLine() . "\n";
}

echo "\n=== FINE ===\n";
