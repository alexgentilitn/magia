<?php
/**
 * Test dati per view permessi
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: text/plain; charset=utf-8');

echo "=== TEST DATI VIEW PERMESSI ===\n\n";

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Utente;
use App\Models\Permesso;

try {
    $utente = Utente::with(['ruolo', 'professionista', 'permessiIndividuali'])
        ->where('tipo_utente', 'professionista')
        ->findOrFail(17);

    echo "✅ Utente trovato: {$utente->nome} {$utente->cognome}\n";
    echo "   Email: {$utente->email}\n";
    echo "   Tipo: {$utente->tipo_utente}\n\n";

    // Verifica ruolo
    echo "=== RUOLO ===\n";
    if ($utente->ruolo) {
        echo "✅ Ruolo: {$utente->ruolo->nome}\n";
        echo "   Slug: {$utente->ruolo->slug}\n";
        echo "   Permessi del ruolo: {$utente->ruolo->permessi->count()}\n\n";
    } else {
        echo "❌ Nessun ruolo associato!\n";
        echo "⚠️ PROBLEMA: L'utente deve avere un ruolo per visualizzare i permessi\n\n";
    }

    // Verifica permessi disponibili
    echo "=== PERMESSI DISPONIBILI ===\n";
    $permessiDisponibili = Permesso::attivi()->ordinati()->get()->groupBy('categoria');

    echo "Permessi totali: " . Permesso::attivi()->count() . "\n";
    echo "Categorie: " . $permessiDisponibili->count() . "\n\n";

    if ($permessiDisponibili->isEmpty()) {
        echo "❌ PROBLEMA: Nessun permesso attivo nel sistema!\n";
        echo "   La view apparirà vuota perché non ci sono permessi da mostrare.\n\n";

        echo "   Soluzione: Esegui la migration dei permessi o inserisci permessi base.\n\n";
    } else {
        echo "Categorie trovate:\n";
        foreach ($permessiDisponibili as $categoria => $permessi) {
            echo "  - {$categoria}: {$permessi->count()} permessi\n";
        }
        echo "\n";
    }

    // Verifica permessi individuali
    echo "=== PERMESSI INDIVIDUALI ===\n";
    $permessiAssegnati = $utente->permessiIndividuali->pluck('id')->toArray();
    echo "Permessi individuali assegnati: " . count($permessiAssegnati) . "\n";

    if (count($permessiAssegnati) > 0) {
        foreach ($utente->permessiIndividuali as $p) {
            echo "  - {$p->nome} ({$p->categoria})\n";
        }
    }
    echo "\n";

    // Verifica permessi del ruolo
    echo "=== PERMESSI DEL RUOLO ===\n";
    if ($utente->ruolo) {
        $permessiRuolo = $utente->ruolo->permessi->pluck('id')->toArray();
        echo "Permessi dal ruolo: " . count($permessiRuolo) . "\n";

        if (count($permessiRuolo) > 0) {
            foreach ($utente->ruolo->permessi->take(5) as $p) {
                echo "  - {$p->nome} ({$p->categoria})\n";
            }
            if ($utente->ruolo->permessi->count() > 5) {
                echo "  ... e altri " . ($utente->ruolo->permessi->count() - 5) . " permessi\n";
            }
        }
    }
    echo "\n";

    // Test rendering view
    echo "=== TEST RENDERING VIEW ===\n";
    try {
        $view = view('admin.professionisti.permessi', [
            'utente' => $utente,
            'permessiDisponibili' => $permessiDisponibili,
            'permessiAssegnati' => $permessiAssegnati,
            'permessiRuolo' => $utente->ruolo ? $utente->ruolo->permessi->pluck('id')->toArray() : [],
        ]);

        echo "✅ View compilata con successo!\n";
        echo "   View path: admin.professionisti.permessi\n";

    } catch (\Exception $e) {
        echo "❌ ERRORE nel rendering view:\n";
        echo "   Messaggio: " . $e->getMessage() . "\n";
        echo "   File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    }

} catch (\Exception $e) {
    echo "❌ ERRORE:\n";
    echo "   Messaggio: " . $e->getMessage() . "\n";
    echo "   File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

echo "\n=== FINE ===\n";
