<?php
/**
 * Script: Verifica e Crea Tabella utente_permesso
 *
 * Funzione: Controlla se la tabella utente_permesso esiste
 * e la crea se mancante
 */

define('SECRET', '$Magia2025!');

if (!isset($_GET['secret']) || $_GET['secret'] !== SECRET) {
    http_response_code(401);
    die('❌ Unauthorized');
}

header('Content-Type: text/html; charset=utf-8');

echo "<!DOCTYPE html>";
echo "<html><head>";
echo "<title>Verifica Tabella Permessi</title>";
echo "<style>
body { font-family: monospace; max-width: 1200px; margin: 20px auto; padding: 20px; background: #f5f5f5; }
.success { background: #d4edda; border-left: 4px solid #28a745; padding: 15px; margin: 10px 0; }
.error { background: #f8d7da; border-left: 4px solid #dc3545; padding: 15px; margin: 10px 0; }
.info { background: #d1ecf1; border-left: 4px solid #17a2b8; padding: 15px; margin: 10px 0; }
.warning { background: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 10px 0; }
pre { background: #fff; padding: 10px; border: 1px solid #ddd; overflow-x: auto; }
table { width: 100%; border-collapse: collapse; background: #fff; margin: 10px 0; }
th, td { padding: 10px; border: 1px solid #ddd; text-align: left; }
th { background: #007bff; color: white; }
</style>";
echo "</head><body>";

echo "<h1>🔍 Verifica Tabella utente_permesso</h1>";

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

try {
    echo "<div class='info'>";
    echo "<h2>📊 Verifica Database</h2>";
    echo "</div>";

    // 1. Verifica tabella utenti
    echo "<div class='info'>";
    echo "<h3>1. Tabella 'utenti'</h3>";
    if (Schema::hasTable('utenti')) {
        echo "<div class='success'>✅ Tabella 'utenti' esiste</div>";

        // Verifica colonna tipo_utente
        if (Schema::hasColumn('utenti', 'tipo_utente')) {
            echo "<div class='success'>✅ Colonna 'tipo_utente' esiste</div>";
        } else {
            echo "<div class='error'>❌ Colonna 'tipo_utente' NON esiste</div>";
        }

        // Conta professionisti
        $professionisti = DB::table('utenti')->where('tipo_utente', 'professionista')->count();
        echo "<p>👥 Professionisti nel database: <strong>{$professionisti}</strong></p>";
    } else {
        echo "<div class='error'>❌ Tabella 'utenti' NON esiste</div>";
    }
    echo "</div>";

    // 2. Verifica tabella permessi
    echo "<div class='info'>";
    echo "<h3>2. Tabella 'permessi'</h3>";
    if (Schema::hasTable('permessi')) {
        echo "<div class='success'>✅ Tabella 'permessi' esiste</div>";

        $permessi = DB::table('permessi')->count();
        echo "<p>🔐 Permessi disponibili: <strong>{$permessi}</strong></p>";

        if ($permessi > 0) {
            echo "<details><summary>Dettaglio permessi per categoria</summary>";
            echo "<table>";
            echo "<tr><th>Categoria</th><th>Permessi</th></tr>";
            $categorie = DB::table('permessi')
                ->select('categoria', DB::raw('COUNT(*) as totale'))
                ->where('attivo', true)
                ->groupBy('categoria')
                ->get();
            foreach ($categorie as $cat) {
                echo "<tr><td>{$cat->categoria}</td><td>{$cat->totale}</td></tr>";
            }
            echo "</table>";
            echo "</details>";
        }
    } else {
        echo "<div class='error'>❌ Tabella 'permessi' NON esiste</div>";
    }
    echo "</div>";

    // 3. Verifica tabella ruoli
    echo "<div class='info'>";
    echo "<h3>3. Tabella 'ruoli'</h3>";
    if (Schema::hasTable('ruoli')) {
        echo "<div class='success'>✅ Tabella 'ruoli' esiste</div>";

        $ruoli = DB::table('ruoli')->get();
        if ($ruoli->count() > 0) {
            echo "<p>🏷️ Ruoli disponibili:</p>";
            echo "<ul>";
            foreach ($ruoli as $ruolo) {
                echo "<li><strong>{$ruolo->nome}</strong> (slug: {$ruolo->slug})</li>";
            }
            echo "</ul>";
        }
    } else {
        echo "<div class='error'>❌ Tabella 'ruoli' NON esiste</div>";
    }
    echo "</div>";

    // 4. CRITICO: Verifica tabella utente_permesso
    echo "<div class='warning'>";
    echo "<h3>4. ⭐ Tabella 'utente_permesso' (CRITICA)</h3>";

    $tabellaEsiste = Schema::hasTable('utente_permesso');

    if ($tabellaEsiste) {
        echo "<div class='success'>✅ Tabella 'utente_permesso' ESISTE</div>";

        // Verifica struttura
        $columns = Schema::getColumnListing('utente_permesso');
        echo "<p>📋 Colonne presenti: " . implode(', ', $columns) . "</p>";

        // Conta relazioni
        $relazioni = DB::table('utente_permesso')->count();
        echo "<p>🔗 Permessi individuali assegnati: <strong>{$relazioni}</strong></p>";

        if ($relazioni > 0) {
            echo "<details><summary>Dettaglio assegnazioni</summary>";
            $assegnazioni = DB::table('utente_permesso')
                ->join('utenti', 'utente_permesso.utente_id', '=', 'utenti.id')
                ->join('permessi', 'utente_permesso.permesso_id', '=', 'permessi.id')
                ->select('utenti.nome', 'utenti.cognome', 'permessi.nome as permesso', 'utente_permesso.created_at')
                ->get();
            echo "<table>";
            echo "<tr><th>Utente</th><th>Permesso</th><th>Assegnato il</th></tr>";
            foreach ($assegnazioni as $ass) {
                echo "<tr><td>{$ass->nome} {$ass->cognome}</td><td>{$ass->permesso}</td><td>{$ass->created_at}</td></tr>";
            }
            echo "</table>";
            echo "</details>";
        }

    } else {
        echo "<div class='error'>❌ Tabella 'utente_permesso' NON ESISTE - QUESTO È IL PROBLEMA!</div>";

        echo "<div class='warning'>";
        echo "<h4>🔧 Creazione Tabella</h4>";
        echo "<p>Devo creare la tabella 'utente_permesso' per permettere i permessi individuali.</p>";
        echo "<form method='POST'>";
        echo "<button type='submit' name='crea_tabella' style='background: #28a745; color: white; padding: 10px 20px; border: none; cursor: pointer; font-size: 16px;'>➕ Crea Tabella utente_permesso</button>";
        echo "</form>";
        echo "</div>";
    }
    echo "</div>";

    // 5. Verifica tabella ruolo_permesso
    echo "<div class='info'>";
    echo "<h3>5. Tabella 'ruolo_permesso'</h3>";
    if (Schema::hasTable('ruolo_permesso')) {
        echo "<div class='success'>✅ Tabella 'ruolo_permesso' esiste</div>";

        $relazioni = DB::table('ruolo_permesso')->count();
        echo "<p>🔗 Permessi assegnati ai ruoli: <strong>{$relazioni}</strong></p>";
    } else {
        echo "<div class='error'>❌ Tabella 'ruolo_permesso' NON esiste</div>";
    }
    echo "</div>";

    // 6. Verifica migration status
    echo "<div class='info'>";
    echo "<h3>6. Status Migrations</h3>";

    $migrations = DB::table('migrations')->orderBy('batch', 'desc')->get();
    $migrationUtente = $migrations->first(function($m) {
        return strpos($m->migration, 'utente_permesso') !== false;
    });

    if ($migrationUtente) {
        echo "<div class='success'>✅ Migration 'create_utente_permesso_table' ESEGUITA</div>";
        echo "<p>Batch: {$migrationUtente->batch} | Eseguita: {$migrationUtente->migration}</p>";
    } else {
        echo "<div class='error'>❌ Migration 'create_utente_permesso_table' NON ESEGUITA</div>";
    }

    echo "<details><summary>Tutte le migrations (ultime 10)</summary>";
    echo "<table>";
    echo "<tr><th>Migration</th><th>Batch</th></tr>";
    foreach ($migrations->take(10) as $mig) {
        echo "<tr><td>{$mig->migration}</td><td>{$mig->batch}</td></tr>";
    }
    echo "</table>";
    echo "</details>";
    echo "</div>";

    // GESTIONE POST: Creazione tabella
    if (isset($_POST['crea_tabella'])) {
        echo "<div class='warning'>";
        echo "<h3>⚙️ Creazione Tabella in Corso...</h3>";

        try {
            Schema::create('utente_permesso', function ($table) {
                $table->id();
                $table->foreignId('utente_id')->constrained('utenti')->onDelete('cascade');
                $table->foreignId('permesso_id')->constrained('permessi')->onDelete('cascade');
                $table->timestamps();
                $table->unique(['utente_id', 'permesso_id']);
            });

            // Registra la migration
            DB::table('migrations')->insert([
                'migration' => '2025_11_17_create_utente_permesso_table',
                'batch' => DB::table('migrations')->max('batch') + 1
            ]);

            echo "<div class='success'>";
            echo "<h2>✅ TABELLA CREATA CON SUCCESSO!</h2>";
            echo "<p>La tabella 'utente_permesso' è stata creata correttamente.</p>";
            echo "<p><a href='?secret=$Magia2025!' style='display: inline-block; padding: 10px 20px; background: #007bff; color: white; text-decoration: none;'>🔄 Ricarica Pagina</a></p>";
            echo "<p><a href='/magia/public/admin/professionisti/4/permessi' style='display: inline-block; padding: 10px 20px; background: #28a745; color: white; text-decoration: none; margin-left: 10px;'>➡️ Vai ai Permessi</a></p>";
            echo "</div>";

        } catch (\Exception $e) {
            echo "<div class='error'>";
            echo "<h4>❌ Errore durante la creazione:</h4>";
            echo "<p><strong>Messaggio:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
            echo "<p><strong>File:</strong> " . $e->getFile() . ":" . $e->getLine() . "</p>";
            echo "</div>";
        }

        echo "</div>";
    }

    // RIEPILOGO FINALE
    echo "<div class='info'>";
    echo "<h2>📝 Riepilogo</h2>";

    $problemi = [];

    if (!Schema::hasTable('utente_permesso')) {
        $problemi[] = "❌ Tabella 'utente_permesso' mancante (causa del 404)";
    }
    if (!Schema::hasTable('permessi')) {
        $problemi[] = "❌ Tabella 'permessi' mancante";
    }
    if (!Schema::hasTable('ruoli')) {
        $problemi[] = "❌ Tabella 'ruoli' mancante";
    }

    if (empty($problemi)) {
        echo "<div class='success'>";
        echo "<h3>✅ Tutto OK!</h3>";
        echo "<p>Tutte le tabelle necessarie esistono. Il sistema permessi è configurato correttamente.</p>";
        echo "<p><a href='/magia/public/admin/professionisti/4/permessi' style='display: inline-block; padding: 10px 20px; background: #28a745; color: white; text-decoration: none;'>➡️ Vai ai Permessi</a></p>";
        echo "</div>";
    } else {
        echo "<div class='error'>";
        echo "<h3>⚠️ Problemi Rilevati:</h3>";
        echo "<ul>";
        foreach ($problemi as $problema) {
            echo "<li>{$problema}</li>";
        }
        echo "</ul>";
        echo "</div>";
    }

    echo "</div>";

} catch (\Exception $e) {
    echo "<div class='error'>";
    echo "<h2>❌ Errore Critico</h2>";
    echo "<p><strong>Messaggio:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><strong>Tipo:</strong> " . get_class($e) . "</p>";
    echo "<p><strong>File:</strong> " . $e->getFile() . ":" . $e->getLine() . "</p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    echo "</div>";
}

echo "</body></html>";
