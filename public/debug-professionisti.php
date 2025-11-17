<?php
/**
 * Script Debug - Verifica Professionisti e Permessi
 * URL: https://www.agstudio.digital/magia/public/debug-professionisti.php
 */

// Bootstrap Laravel
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

?>
<!DOCTYPE html>
<html>
<head>
    <title>Debug Professionisti - MA.GIA</title>
    <style>
        body { font-family: monospace; padding: 20px; background: #f5f5f5; }
        .card { background: white; padding: 20px; margin: 10px 0; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
        .warning { color: orange; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        th, td { padding: 8px; text-align: left; border: 1px solid #ddd; }
        th { background: #333; color: white; }
        h2 { color: #333; border-bottom: 2px solid #4CAF50; padding-bottom: 10px; }
        .info { background: #e3f2fd; padding: 10px; border-left: 4px solid #2196F3; margin: 10px 0; }
    </style>
</head>
<body>

<h1>🔍 Debug Professionisti - MA.GIA DONNA</h1>

<?php
try {
    echo '<div class="info">⏰ Timestamp: ' . date('Y-m-d H:i:s') . '</div>';

    // ============================================
    // 1. VERIFICA CONNESSIONE DATABASE
    // ============================================
    echo '<div class="card">';
    echo '<h2>1️⃣ Verifica Connessione Database</h2>';

    try {
        $connection = DB::connection()->getPdo();
        echo '<p class="success">✅ Connessione database OK</p>';
        echo '<p>Driver: ' . DB::connection()->getDriverName() . '</p>';
        echo '<p>Database: ' . DB::connection()->getDatabaseName() . '</p>';
    } catch (Exception $e) {
        echo '<p class="error">❌ Errore connessione: ' . $e->getMessage() . '</p>';
        exit;
    }
    echo '</div>';

    // ============================================
    // 2. VERIFICA TABELLA UTENTI
    // ============================================
    echo '<div class="card">';
    echo '<h2>2️⃣ Verifica Tabella Utenti</h2>';

    if (Schema::hasTable('utenti')) {
        echo '<p class="success">✅ Tabella "utenti" esiste</p>';

        $totalUtenti = DB::table('utenti')->count();
        $professionisti = DB::table('utenti')->where('tipo_utente', 'professionista')->count();
        $amministratori = DB::table('utenti')->where('tipo_utente', 'amministratore')->count();

        echo '<p>• Totale utenti: <strong>' . $totalUtenti . '</strong></p>';
        echo '<p>• Professionisti: <strong>' . $professionisti . '</strong></p>';
        echo '<p>• Amministratori: <strong>' . $amministratori . '</strong></p>';
    } else {
        echo '<p class="error">❌ Tabella "utenti" NON esiste</p>';
    }
    echo '</div>';

    // ============================================
    // 3. VERIFICA UTENTE ID 4
    // ============================================
    echo '<div class="card">';
    echo '<h2>3️⃣ Verifica Utente ID 4</h2>';

    $utente4 = DB::table('utenti')->where('id', 4)->first();

    if ($utente4) {
        echo '<p class="success">✅ Utente ID 4 ESISTE</p>';
        echo '<table>';
        echo '<tr><th>Campo</th><th>Valore</th></tr>';
        foreach ($utente4 as $campo => $valore) {
            if ($campo === 'password') {
                $valore = '****** (nascosto)';
            }
            echo '<tr><td>' . htmlspecialchars($campo) . '</td><td>' . htmlspecialchars($valore ?? 'NULL') . '</td></tr>';
        }
        echo '</table>';

        if ($utente4->tipo_utente === 'professionista') {
            echo '<p class="success">✅ Tipo utente: PROFESSIONISTA (corretto per accedere a /permessi)</p>';
        } else {
            echo '<p class="error">❌ Tipo utente: ' . $utente4->tipo_utente . ' (deve essere "professionista")</p>';
            echo '<p class="warning">⚠️ Questo è il problema! Solo gli utenti con tipo_utente = "professionista" possono accedere alla pagina permessi.</p>';
        }
    } else {
        echo '<p class="error">❌ Utente ID 4 NON ESISTE nel database</p>';
        echo '<p class="warning">⚠️ Questo è il motivo dell\'errore 404!</p>';
    }
    echo '</div>';

    // ============================================
    // 4. LISTA TUTTI I PROFESSIONISTI
    // ============================================
    echo '<div class="card">';
    echo '<h2>4️⃣ Lista Professionisti Disponibili</h2>';

    $professionistiList = DB::table('utenti')
        ->where('tipo_utente', 'professionista')
        ->select('id', 'nome', 'cognome', 'email', 'created_at')
        ->orderBy('id')
        ->get();

    if ($professionistiList->count() > 0) {
        echo '<p class="success">✅ Trovati ' . $professionistiList->count() . ' professionisti</p>';
        echo '<table>';
        echo '<tr><th>ID</th><th>Nome</th><th>Cognome</th><th>Email</th><th>Link Permessi</th></tr>';
        foreach ($professionistiList as $prof) {
            $linkPermessi = 'https://www.agstudio.digital/magia/public/admin/professionisti/' . $prof->id . '/permessi';
            echo '<tr>';
            echo '<td>' . $prof->id . '</td>';
            echo '<td>' . htmlspecialchars($prof->nome ?? '') . '</td>';
            echo '<td>' . htmlspecialchars($prof->cognome ?? '') . '</td>';
            echo '<td>' . htmlspecialchars($prof->email ?? '') . '</td>';
            echo '<td><a href="' . $linkPermessi . '" target="_blank">Apri Permessi</a></td>';
            echo '</tr>';
        }
        echo '</table>';
    } else {
        echo '<p class="error">❌ Nessun professionista trovato</p>';
    }
    echo '</div>';

    // ============================================
    // 5. VERIFICA TABELLE PERMESSI
    // ============================================
    echo '<div class="card">';
    echo '<h2>5️⃣ Verifica Tabelle Sistema Permessi</h2>';

    $tabelle = ['permessi', 'ruoli', 'ruolo_permesso', 'utente_permesso'];

    echo '<table>';
    echo '<tr><th>Tabella</th><th>Esiste?</th><th>Record</th></tr>';
    foreach ($tabelle as $tabella) {
        $esiste = Schema::hasTable($tabella);
        $count = $esiste ? DB::table($tabella)->count() : 0;

        echo '<tr>';
        echo '<td>' . $tabella . '</td>';
        echo '<td>' . ($esiste ? '<span class="success">✅ Sì</span>' : '<span class="error">❌ No</span>') . '</td>';
        echo '<td>' . ($esiste ? $count : 'N/A') . '</td>';
        echo '</tr>';
    }
    echo '</table>';
    echo '</div>';

    // ============================================
    // 6. VERIFICA ROUTES
    // ============================================
    echo '<div class="card">';
    echo '<h2>6️⃣ Verifica Routes Permessi</h2>';

    $routes = [
        'admin.professionisti.permessi.edit' => 'GET /admin/professionisti/{id}/permessi',
        'admin.professionisti.permessi.update' => 'PUT /admin/professionisti/{id}/permessi',
        'admin.professionisti.permessi.reset' => 'DELETE /admin/professionisti/{id}/permessi',
        'admin.professionisti.permessi.api' => 'GET /admin/professionisti/{id}/permessi/api',
    ];

    echo '<table>';
    echo '<tr><th>Nome Route</th><th>URI</th><th>Esiste?</th></tr>';
    foreach ($routes as $nome => $uri) {
        $esiste = Route::has($nome);
        echo '<tr>';
        echo '<td>' . $nome . '</td>';
        echo '<td>' . $uri . '</td>';
        echo '<td>' . ($esiste ? '<span class="success">✅ Sì</span>' : '<span class="error">❌ No</span>') . '</td>';
        echo '</tr>';
    }
    echo '</table>';
    echo '</div>';

    // ============================================
    // 7. DIAGNOSTICA FINALE
    // ============================================
    echo '<div class="card">';
    echo '<h2>7️⃣ Diagnosi e Soluzioni</h2>';

    if (!$utente4) {
        echo '<div class="error">';
        echo '<h3>❌ PROBLEMA: Utente ID 4 non esiste</h3>';
        echo '<p><strong>Soluzione:</strong> Usa un ID valido di un professionista esistente dalla lista sopra.</p>';
        echo '</div>';
    } elseif ($utente4->tipo_utente !== 'professionista') {
        echo '<div class="error">';
        echo '<h3>❌ PROBLEMA: Utente ID 4 non è un professionista</h3>';
        echo '<p><strong>Tipo attuale:</strong> ' . $utente4->tipo_utente . '</p>';
        echo '<p><strong>Soluzione:</strong> Cambia il tipo_utente a "professionista" oppure usa un altro ID dalla lista.</p>';
        echo '</div>';
    } else {
        echo '<div class="success">';
        echo '<h3>✅ Utente ID 4 è corretto</h3>';
        echo '<p>Se stai ancora vedendo l\'errore 404, potrebbe essere un problema di cache.</p>';
        echo '<p><strong>Azioni consigliate:</strong></p>';
        echo '<ul>';
        echo '<li>Pulisci cache Laravel: <a href="/magia/public/clear-cache.php" target="_blank">Clear Cache</a></li>';
        echo '<li>Verifica di essere loggato come amministratore</li>';
        echo '<li>Controlla i log in storage/logs/laravel.log</li>';
        echo '</ul>';
        echo '</div>';
    }
    echo '</div>';

} catch (Exception $e) {
    echo '<div class="card"><div class="error">';
    echo '<h3>💥 ERRORE GENERALE</h3>';
    echo '<p>' . $e->getMessage() . '</p>';
    echo '<pre>' . $e->getTraceAsString() . '</pre>';
    echo '</div></div>';
}
?>

<div class="card">
    <h2>🔗 Link Utili</h2>
    <ul>
        <li><a href="/magia/public/admin/professionisti">Lista Professionisti</a></li>
        <li><a href="/magia/public/clear-cache.php">Clear Cache Laravel</a></li>
        <li><a href="/magia/public/diagnose.php">Diagnostica Sistema</a></li>
        <li><a href="/magia/public/admin/super-admin">Super Admin Panel</a></li>
    </ul>
</div>

<div class="info">
    <strong>📝 Note:</strong> Questo script è sicuro da eseguire in produzione. Non modifica alcun dato, solo lettura.
</div>

</body>
</html>
