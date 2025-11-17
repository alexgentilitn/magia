<?php
/**
 * Test: Verifica ruolo Collaboratore
 */

header('Content-Type: text/plain; charset=utf-8');

echo "=== TEST RUOLO COLLABORATORE ===\n\n";

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Ruolo;

try {
    echo "1. Verifica esistenza ruolo Collaboratore...\n";

    $ruoloCollaboratore = Ruolo::where('slug', 'collaboratore')->first();

    if ($ruoloCollaboratore) {
        echo "✅ Ruolo Collaboratore TROVATO\n";
        echo "   ID: {$ruoloCollaboratore->id}\n";
        echo "   Nome: {$ruoloCollaboratore->nome}\n";
        echo "   Slug: {$ruoloCollaboratore->slug}\n";
        echo "   Livello: {$ruoloCollaboratore->livello}\n";
        echo "   Attivo: " . ($ruoloCollaboratore->attivo ? 'Sì' : 'No') . "\n\n";

        // Verifica permessi del ruolo
        $permessi = $ruoloCollaboratore->permessi()->count();
        echo "   Permessi assegnati al ruolo: {$permessi}\n\n";

        echo "✅ TEST OK: Il ruolo verrà assegnato correttamente ai nuovi professionisti\n";
        echo "   ruolo_id = {$ruoloCollaboratore->id}\n\n";

    } else {
        echo "❌ PROBLEMA: Ruolo Collaboratore NON TROVATO\n\n";

        // Lista tutti i ruoli disponibili
        echo "Ruoli disponibili nel database:\n";
        $ruoli = Ruolo::all();

        if ($ruoli->count() > 0) {
            foreach ($ruoli as $r) {
                echo "  - {$r->nome} (slug: {$r->slug}, ID: {$r->id})\n";
            }
        } else {
            echo "  Nessun ruolo trovato nel database!\n";
        }

        echo "\n⚠️ ATTENZIONE: Il controller non potrà assegnare il ruolo ai nuovi professionisti\n";
        echo "   perché il ruolo 'collaboratore' non esiste.\n\n";
        echo "   Soluzione: Esegui il seeder dei ruoli o crea manualmente il ruolo Collaboratore.\n";
    }

} catch (\Exception $e) {
    echo "❌ ERRORE:\n";
    echo "   Messaggio: " . $e->getMessage() . "\n";
    echo "   File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

echo "\n=== FINE ===\n";
