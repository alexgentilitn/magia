<?php
/**
 * Script: Verifica Permessi Disponibili
 *
 * Funzione: Controlla se ci sono permessi nel database
 * e perché la pagina permessi appare bianca
 */

header('Content-Type: text/html; charset=utf-8');

echo "<!DOCTYPE html>";
echo "<html><head>";
echo "<title>Verifica Permessi</title>";
echo "<style>
body { font-family: Arial, sans-serif; max-width: 1200px; margin: 20px auto; padding: 20px; background: #f5f5f5; }
h1, h2, h3 { color: #333; }
.success { background: #d4edda; border-left: 4px solid #28a745; padding: 15px; margin: 10px 0; }
.error { background: #f8d7da; border-left: 4px solid #dc3545; padding: 15px; margin: 10px 0; }
.warning { background: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 10px 0; }
.info { background: #d1ecf1; border-left: 4px solid #17a2b8; padding: 15px; margin: 10px 0; }
table { width: 100%; border-collapse: collapse; background: #fff; margin: 10px 0; }
th, td { padding: 10px; border: 1px solid #ddd; text-align: left; }
th { background: #007bff; color: white; }
</style>";
echo "</head><body>";

echo "<h1>🔐 Verifica Permessi Disponibili</h1>";

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Permesso;
use App\Models\Utente;
use App\Models\Ruolo;

try {
    // 1. Verifica permessi nel database
    echo "<div class='info'>";
    echo "<h2>1️⃣ Permessi nel Database</h2>";

    $permessiTotali = Permesso::count();
    $permessiAttivi = Permesso::where('attivo', true)->count();

    if ($permessiTotali === 0) {
        echo "<div class='error'>";
        echo "<h3>❌ PROBLEMA CRITICO: Nessun Permesso nel Database!</h3>";
        echo "<p>La pagina appare <strong>bianca</strong> perché <strong>non ci sono permessi</strong> da visualizzare.</p>";
        echo "<p><strong>Soluzione:</strong> Devi eseguire il seeder dei permessi o crearli manualmente.</p>";
        echo "</div>";

        echo "<div class='warning'>";
        echo "<h4>🔧 Come risolvere:</h4>";
        echo "<pre>php artisan db:seed --class=PermessiSeeder</pre>";
        echo "<p>Oppure crea manualmente alcuni permessi di test nella tabella 'permessi'.</p>";
        echo "</div>";

    } else {
        echo "<div class='success'>";
        echo "✅ Permessi totali nel database: <strong>{$permessiTotali}</strong><br>";
        echo "✅ Permessi attivi: <strong>{$permessiAttivi}</strong>";
        echo "</div>";

        // Raggruppa per categoria
        $permessiPerCategoria = Permesso::where('attivo', true)
            ->orderBy('categoria')
            ->orderBy('ordine')
            ->get()
            ->groupBy('categoria');

        echo "<h3>Permessi per Categoria:</h3>";
        echo "<table>";
        echo "<tr><th>Categoria</th><th>Permessi</th></tr>";

        foreach ($permessiPerCategoria as $categoria => $permessi) {
            echo "<tr>";
            echo "<td><strong>{$categoria}</strong></td>";
            echo "<td>{$permessi->count()}</td>";
            echo "</tr>";
        }
        echo "</table>";

        echo "<details><summary>Dettaglio tutti i permessi</summary>";
        echo "<table>";
        echo "<tr><th>ID</th><th>Nome</th><th>Slug</th><th>Categoria</th><th>Attivo</th></tr>";
        foreach (Permesso::orderBy('categoria')->orderBy('ordine')->get() as $p) {
            $attivo = $p->attivo ? '✅ Sì' : '❌ No';
            echo "<tr><td>{$p->id}</td><td>{$p->nome}</td><td>{$p->slug}</td><td>{$p->categoria}</td><td>{$attivo}</td></tr>";
        }
        echo "</table>";
        echo "</details>";
    }
    echo "</div>";

    // 2. Verifica ruoli e permessi assegnati
    echo "<div class='info'>";
    echo "<h2>2️⃣ Ruoli e Permessi Assegnati</h2>";

    $ruoli = Ruolo::with('permessi')->get();

    if ($ruoli->count() > 0) {
        echo "<table>";
        echo "<tr><th>Ruolo</th><th>Slug</th><th>Permessi Assegnati</th></tr>";

        foreach ($ruoli as $ruolo) {
            $permessiCount = $ruolo->permessi()->count();
            echo "<tr>";
            echo "<td>{$ruolo->nome}</td>";
            echo "<td>{$ruolo->slug}</td>";
            echo "<td><strong>{$permessiCount}</strong></td>";
            echo "</tr>";
        }
        echo "</table>";

        // Dettaglio permessi del ruolo Collaboratore
        $collaboratore = Ruolo::where('slug', 'collaboratore')->first();
        if ($collaboratore) {
            $permessiCollaboratore = $collaboratore->permessi()->count();

            if ($permessiCollaboratore === 0) {
                echo "<div class='warning'>";
                echo "<h4>⚠️ Il ruolo Collaboratore NON ha permessi assegnati</h4>";
                echo "<p>Questo è il motivo per cui la pagina appare vuota!</p>";
                echo "<p>Il ruolo esiste ma non ha permessi collegati nella tabella ruolo_permesso.</p>";
                echo "</div>";
            } else {
                echo "<div class='success'>";
                echo "✅ Ruolo Collaboratore ha <strong>{$permessiCollaboratore}</strong> permessi assegnati";
                echo "</div>";
            }
        }

    } else {
        echo "<div class='error'>❌ Nessun ruolo nel database</div>";
    }
    echo "</div>";

    // 3. Test utente ID 18
    echo "<div class='info'>";
    echo "<h2>3️⃣ Test Utente ID 18</h2>";

    $utente = Utente::with(['ruolo.permessi', 'permessiIndividuali'])->find(18);

    if ($utente) {
        echo "<div class='success'>";
        echo "✅ Utente trovato: {$utente->nome} {$utente->cognome}<br>";
        echo "Email: {$utente->email}<br>";
        echo "Tipo: {$utente->tipo_utente}";
        echo "</div>";

        if ($utente->ruolo) {
            echo "<p><strong>Ruolo:</strong> {$utente->ruolo->nome}</p>";
            echo "<p><strong>Permessi dal ruolo:</strong> {$utente->ruolo->permessi->count()}</p>";
            echo "<p><strong>Permessi individuali:</strong> {$utente->permessiIndividuali->count()}</p>";

            if ($utente->ruolo->permessi->count() === 0 && $utente->permessiIndividuali->count() === 0) {
                echo "<div class='warning'>";
                echo "<h4>⚠️ Questo utente non ha permessi!</h4>";
                echo "<p>La view sarà vuota perché:</p>";
                echo "<ul>";
                echo "<li>Il ruolo Collaboratore non ha permessi assegnati</li>";
                echo "<li>L'utente non ha permessi individuali</li>";
                echo "</ul>";
                echo "</div>";
            }

        } else {
            echo "<div class='error'>❌ L'utente non ha un ruolo assegnato</div>";
        }

    } else {
        echo "<div class='error'>❌ Utente ID 18 non trovato</div>";
    }
    echo "</div>";

    // 4. Verifica tabelle pivot
    echo "<div class='info'>";
    echo "<h2>4️⃣ Tabelle Pivot</h2>";

    $ruoloPermessoCount = DB::table('ruolo_permesso')->count();
    $utentePermessoCount = DB::table('utente_permesso')->count();

    echo "<table>";
    echo "<tr><th>Tabella</th><th>Record</th></tr>";
    echo "<tr><td>ruolo_permesso</td><td><strong>{$ruoloPermessoCount}</strong></td></tr>";
    echo "<tr><td>utente_permesso</td><td><strong>{$utentePermessoCount}</strong></td></tr>";
    echo "</table>";

    if ($ruoloPermessoCount === 0) {
        echo "<div class='error'>";
        echo "<h4>❌ PROBLEMA: Tabella ruolo_permesso vuota!</h4>";
        echo "<p>I ruoli esistono ma non hanno permessi collegati.</p>";
        echo "<p><strong>Soluzione:</strong> Assegna permessi ai ruoli tramite seeder o manualmente.</p>";
        echo "</div>";
    }
    echo "</div>";

    // RIEPILOGO
    echo "<hr>";
    echo "<div class='info'>";
    echo "<h2>📝 RIEPILOGO</h2>";

    $problemi = [];

    if ($permessiTotali === 0) {
        $problemi[] = "❌ Nessun permesso nel database";
    }
    if ($ruoloPermessoCount === 0) {
        $problemi[] = "❌ Nessun permesso assegnato ai ruoli";
    }

    if (count($problemi) > 0) {
        echo "<div class='error'>";
        echo "<h3>🔴 Cause della Pagina Bianca:</h3>";
        echo "<ol>";
        foreach ($problemi as $problema) {
            echo "<li>{$problema}</li>";
        }
        echo "</ol>";
        echo "</div>";

        echo "<div class='warning'>";
        echo "<h3>🔧 Soluzioni:</h3>";
        echo "<ol>";
        echo "<li>Esegui il seeder dei permessi:<br><pre>php artisan db:seed --class=PermessiSeeder</pre></li>";
        echo "<li>Oppure assegna manualmente permessi ai ruoli nella tabella ruolo_permesso</li>";
        echo "<li>Verifica che i permessi siano attivi (campo 'attivo' = 1)</li>";
        echo "</ol>";
        echo "</div>";

    } else {
        echo "<div class='success'>";
        echo "<h3>✅ Tutti i dati sono presenti</h3>";
        echo "<p>Se la pagina appare ancora bianca, potrebbe essere un problema di rendering della view.</p>";
        echo "</div>";
    }
    echo "</div>";

} catch (\Exception $e) {
    echo "<div class='error'>";
    echo "<h2>❌ Errore</h2>";
    echo "<p><strong>Messaggio:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><strong>File:</strong> " . $e->getFile() . ":" . $e->getLine() . "</p>";
    echo "</div>";
}

echo "</body></html>";
