<?php
/**
 * Script: Diagnosi Completa Errore 404
 *
 * Funzione: Verifica tutte le possibili cause del 404
 */

header('Content-Type: text/html; charset=utf-8');

echo "<!DOCTYPE html>";
echo "<html><head>";
echo "<title>Diagnosi 404 - Permessi</title>";
echo "<style>
body { font-family: Arial, sans-serif; max-width: 1200px; margin: 20px auto; padding: 20px; background: #f5f5f5; }
h1, h2, h3 { color: #333; }
.success { background: #d4edda; border-left: 4px solid #28a745; padding: 15px; margin: 10px 0; }
.error { background: #f8d7da; border-left: 4px solid #dc3545; padding: 15px; margin: 10px 0; }
.warning { background: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 10px 0; }
.info { background: #d1ecf1; border-left: 4px solid #17a2b8; padding: 15px; margin: 10px 0; }
pre { background: #fff; padding: 10px; border: 1px solid #ddd; overflow-x: auto; }
table { width: 100%; border-collapse: collapse; background: #fff; margin: 10px 0; }
th, td { padding: 10px; border: 1px solid #ddd; text-align: left; }
th { background: #007bff; color: white; }
.code { font-family: monospace; background: #f8f9fa; padding: 2px 6px; border-radius: 3px; }
</style>";
echo "</head><body>";

echo "<h1>🔍 Diagnosi Completa Errore 404 - Permessi</h1>";
echo "<p><em>Generato: " . date('Y-m-d H:i:s') . "</em></p>";

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Utente;
use App\Http\Controllers\Admin\PermessiCollaboratoreController;
use Illuminate\Support\Facades\Route;

$problemi = [];
$ok = [];

try {
    // 1. Verifica Controller
    echo "<div class='info'>";
    echo "<h2>1️⃣ Verifica Controller</h2>";

    if (class_exists('App\Http\Controllers\Admin\PermessiCollaboratoreController')) {
        echo "<div class='success'>✅ Classe PermessiCollaboratoreController ESISTE</div>";
        $ok[] = "Controller class exists";

        $controller = new PermessiCollaboratoreController();
        if (method_exists($controller, 'edit')) {
            echo "<div class='success'>✅ Metodo edit() ESISTE</div>";
            $ok[] = "Method edit() exists";
        } else {
            echo "<div class='error'>❌ Metodo edit() NON ESISTE</div>";
            $problemi[] = "Metodo edit() mancante nel controller";
        }
    } else {
        echo "<div class='error'>❌ Classe PermessiCollaboratoreController NON ESISTE</div>";
        $problemi[] = "Controller class non trovata";
    }
    echo "</div>";

    // 2. Verifica Routes
    echo "<div class='info'>";
    echo "<h2>2️⃣ Verifica Routes Registrate</h2>";

    $routes = Route::getRoutes();
    $foundPermessiEdit = false;

    foreach ($routes as $route) {
        $name = $route->getName();
        if ($name === 'admin.professionisti.permessi.edit') {
            $foundPermessiEdit = true;
            echo "<div class='success'>";
            echo "✅ Route 'admin.professionisti.permessi.edit' REGISTRATA<br>";
            echo "URI Pattern: <span class='code'>" . $route->uri() . "</span><br>";
            echo "Methods: <span class='code'>" . implode(', ', $route->methods()) . "</span><br>";
            echo "Action: <span class='code'>" . $route->getActionName() . "</span>";
            echo "</div>";
            $ok[] = "Route registered";
            break;
        }
    }

    if (!$foundPermessiEdit) {
        echo "<div class='error'>❌ Route 'admin.professionisti.permessi.edit' NON TROVATA</div>";
        $problemi[] = "Route non registrata";

        echo "<div class='warning'>";
        echo "<h4>Routes professionisti trovate:</h4>";
        echo "<ul>";
        foreach ($routes as $route) {
            $name = $route->getName();
            if ($name && strpos($name, 'professionisti') !== false) {
                echo "<li><span class='code'>{$name}</span> → {$route->uri()}</li>";
            }
        }
        echo "</ul>";
        echo "</div>";
    }
    echo "</div>";

    // 3. Verifica Professionisti Disponibili
    echo "<div class='info'>";
    echo "<h2>3️⃣ Professionisti Disponibili</h2>";

    $professionisti = Utente::with(['ruolo'])
        ->where('tipo_utente', 'professionista')
        ->get();

    if ($professionisti->count() > 0) {
        echo "<div class='success'>✅ Trovati {$professionisti->count()} professionisti</div>";

        echo "<table>";
        echo "<tr><th>ID</th><th>Nome</th><th>Email</th><th>Ruolo</th><th>URL Test</th></tr>";
        foreach ($professionisti as $p) {
            $ruolo = $p->ruolo ? $p->ruolo->nome : '❌ Nessuno';
            $testUrl = "/magia/public/admin/professionisti/{$p->id}/permessi";
            echo "<tr>";
            echo "<td><strong>{$p->id}</strong></td>";
            echo "<td>{$p->nome} {$p->cognome}</td>";
            echo "<td>{$p->email}</td>";
            echo "<td>{$ruolo}</td>";
            echo "<td><a href='{$testUrl}' target='_blank'>{$testUrl}</a></td>";
            echo "</tr>";
        }
        echo "</table>";
        $ok[] = "Professionisti trovati: " . $professionisti->count();
    } else {
        echo "<div class='error'>❌ NESSUN PROFESSIONISTA NEL DATABASE</div>";
        $problemi[] = "Nessun utente con tipo_utente='professionista'";
    }
    echo "</div>";

    // 4. Test Controller con ID esistente
    if ($professionisti->count() > 0) {
        echo "<div class='info'>";
        echo "<h2>4️⃣ Test Controller</h2>";

        $primoProfessionista = $professionisti->first();
        $testId = $primoProfessionista->id;

        echo "<p>Testing con ID: <strong>{$testId}</strong> ({$primoProfessionista->nome} {$primoProfessionista->cognome})</p>";

        try {
            $controller = new PermessiCollaboratoreController();
            $response = $controller->edit($testId);

            echo "<div class='success'>";
            echo "✅ Controller->edit({$testId}) eseguito SENZA ERRORI<br>";
            echo "Tipo risposta: <span class='code'>" . get_class($response) . "</span><br>";

            if (method_exists($response, 'getStatusCode')) {
                echo "Status code: <span class='code'>" . $response->getStatusCode() . "</span>";
            }
            echo "</div>";
            $ok[] = "Controller test passed";

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            echo "<div class='error'>";
            echo "❌ ModelNotFoundException: Professionista ID {$testId} non trovato dal controller<br>";
            echo "Questo è strano perché l'utente esiste nel database...";
            echo "</div>";
            $problemi[] = "Controller non trova l'utente anche se esiste";

        } catch (\Exception $e) {
            echo "<div class='error'>";
            echo "❌ ERRORE nel controller:<br>";
            echo "Tipo: <span class='code'>" . get_class($e) . "</span><br>";
            echo "Messaggio: " . htmlspecialchars($e->getMessage()) . "<br>";
            echo "File: " . $e->getFile() . ":" . $e->getLine();
            echo "</div>";
            $problemi[] = "Errore controller: " . $e->getMessage();
        }
        echo "</div>";
    }

    // 5. Verifica File View
    echo "<div class='info'>";
    echo "<h2>5️⃣ Verifica File View</h2>";

    $viewPath = resource_path('views/admin/professionisti/permessi.blade.php');
    if (file_exists($viewPath)) {
        echo "<div class='success'>";
        echo "✅ View file ESISTE<br>";
        echo "Path: <span class='code'>{$viewPath}</span><br>";
        echo "Size: " . filesize($viewPath) . " bytes<br>";
        echo "Modified: " . date('Y-m-d H:i:s', filemtime($viewPath));
        echo "</div>";
        $ok[] = "View file exists";
    } else {
        echo "<div class='error'>❌ View file NON ESISTE: {$viewPath}</div>";
        $problemi[] = "File view mancante";
    }
    echo "</div>";

    // 6. Verifica Cache
    echo "<div class='info'>";
    echo "<h2>6️⃣ Verifica Cache Laravel</h2>";

    $cacheFiles = [
        'Route Cache' => base_path('bootstrap/cache/routes-v7.php'),
        'Config Cache' => base_path('bootstrap/cache/config.php'),
        'View Cache' => storage_path('framework/views'),
    ];

    foreach ($cacheFiles as $name => $path) {
        if (file_exists($path)) {
            echo "<div class='warning'>⚠️ {$name} ESISTE: {$path}</div>";
        } else {
            echo "<div class='success'>✅ {$name} non presente (OK)</div>";
        }
    }
    echo "</div>";

    // RIEPILOGO
    echo "<hr>";
    echo "<div class='info'>";
    echo "<h2>📝 RIEPILOGO</h2>";

    if (count($problemi) === 0) {
        echo "<div class='success'>";
        echo "<h3>✅ NESSUN PROBLEMA TECNICO RILEVATO</h3>";
        echo "<p>Tutti i componenti sono presenti e funzionanti:</p>";
        echo "<ul>";
        foreach ($ok as $item) {
            echo "<li>✅ {$item}</li>";
        }
        echo "</ul>";

        echo "<h4>💡 Se ricevi ancora 404, verifica:</h4>";
        echo "<ol>";
        echo "<li>Che il deployment FTP sia completato (controlla GitHub Actions)</li>";
        echo "<li>Che stai usando un ID professionista valido (vedi tabella sopra)</li>";
        echo "<li>Che il server remoto abbia le ultime modifiche</li>";
        echo "<li>Svuota cache browser (CTRL+F5)</li>";
        echo "</ol>";
        echo "</div>";

    } else {
        echo "<div class='error'>";
        echo "<h3>❌ PROBLEMI RILEVATI:</h3>";
        echo "<ol>";
        foreach ($problemi as $problema) {
            echo "<li>{$problema}</li>";
        }
        echo "</ol>";
        echo "</div>";

        echo "<div class='warning'>";
        echo "<h4>🔧 Possibili Soluzioni:</h4>";
        echo "<ul>";
        if (in_array("Route non registrata", $problemi)) {
            echo "<li>Controlla il file routes/web.php</li>";
            echo "<li>Pulisci la cache delle route: <span class='code'>php artisan route:clear</span></li>";
        }
        if (in_array("Controller class non trovata", $problemi)) {
            echo "<li>Verifica che il file controller sia stato deployato</li>";
            echo "<li>Controlla GitHub Actions per errori di deploy</li>";
        }
        if (strpos(implode(' ', $problemi), 'professionista') !== false) {
            echo "<li>Crea un nuovo professionista dall'interfaccia admin</li>";
            echo "<li>Usa lo script <a href='/magia/public/lista-professionisti.php'>lista-professionisti.php</a> per trovare ID validi</li>";
        }
        echo "</ul>";
        echo "</div>";
    }

    echo "</div>";

} catch (\Exception $e) {
    echo "<div class='error'>";
    echo "<h2>❌ ERRORE CRITICO</h2>";
    echo "<p><strong>Messaggio:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><strong>Tipo:</strong> " . get_class($e) . "</p>";
    echo "<p><strong>File:</strong> " . $e->getFile() . ":" . $e->getLine() . "</p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    echo "</div>";
}

echo "</body></html>";
