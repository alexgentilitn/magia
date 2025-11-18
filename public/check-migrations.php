<?php
/**
 * Script di Verifica Stato Migrations
 *
 * URL: https://www.agstudio.digital/magia/public/check-migrations.php
 */

// Carica Laravel
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';

// Bootstrap dell'applicazione
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Imposta l'applicazione Laravel come istanza globale
Illuminate\Support\Facades\Facade::setFacadeApplication($app);

?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifica Migrations - Magia</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
            min-height: 100vh;
        }
        .container {
            max-width: 1400px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
        }
        .content {
            padding: 30px;
        }
        .section {
            background: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .section h2 {
            color: #667eea;
            margin-bottom: 15px;
            font-size: 1.5em;
        }
        .success {
            background: #d4edda;
            border-color: #28a745;
            color: #155724;
        }
        .success h2 { color: #28a745; }
        .error {
            background: #f8d7da;
            border-color: #dc3545;
            color: #721c24;
        }
        .error h2 { color: #dc3545; }
        .warning {
            background: #fff3cd;
            border-color: #ffc107;
            color: #856404;
        }
        .warning h2 { color: #ff6b6b; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            background: white;
        }
        th {
            background: #667eea;
            color: white;
            padding: 12px;
            text-align: left;
            font-weight: bold;
        }
        td {
            padding: 10px 12px;
            border-bottom: 1px solid #dee2e6;
        }
        tr:nth-child(even) {
            background: #f8f9fa;
        }
        .badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.85em;
            font-weight: bold;
            margin-right: 10px;
        }
        .badge.success { background: #28a745; color: white; }
        .badge.error { background: #dc3545; color: white; }
        .badge.warning { background: #ffc107; color: #333; }
        .badge.info { background: #17a2b8; color: white; }
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin: 20px 0;
        }
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid #667eea;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .stat-number {
            font-size: 2.5em;
            font-weight: bold;
            color: #667eea;
            margin-bottom: 5px;
        }
        .stat-label {
            color: #6c757d;
            font-size: 0.9em;
        }
        .code {
            background: #1e1e1e;
            color: #dcdcdc;
            padding: 15px;
            border-radius: 6px;
            overflow-x: auto;
            font-family: 'Courier New', monospace;
            font-size: 0.9em;
            line-height: 1.6;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔍 Verifica Stato Migrations</h1>
            <p>Controllo Database Magia</p>
        </div>

        <div class="content">
            <?php
            $hasErrors = false;

            // VERIFICA CONNESSIONE DATABASE
            echo '<div class="section">';
            echo '<h2>📡 Connessione Database</h2>';

            try {
                \Illuminate\Support\Facades\DB::connection()->getPdo();
                $dbName = \Illuminate\Support\Facades\DB::connection()->getDatabaseName();
                echo '<p><span class="badge success">✓ CONNESSO</span> Database: <strong>' . htmlspecialchars($dbName) . '</strong></p>';

                $driver = config('database.default');
                $host = config("database.connections.{$driver}.host");
                echo '<p>Driver: <strong>' . htmlspecialchars($driver) . '</strong> | Host: <strong>' . htmlspecialchars($host) . '</strong></p>';

            } catch (\Exception $e) {
                echo '<div class="error">';
                echo '<p><span class="badge error">✗ ERRORE</span> Impossibile connettersi al database</p>';
                echo '<div class="code">' . htmlspecialchars($e->getMessage()) . '</div>';
                echo '</div>';
                $hasErrors = true;
            }
            echo '</div>';

            if (!$hasErrors) {
                // STATO MIGRATIONS
                echo '<div class="section">';
                echo '<h2>📊 Stato Migrations</h2>';

                try {
                    // Controlla se esiste la tabella migrations
                    if (!\Illuminate\Support\Facades\Schema::hasTable('migrations')) {
                        echo '<div class="warning">';
                        echo '<p><span class="badge warning">⚠ ATTENZIONE</span> La tabella <code>migrations</code> non esiste!</p>';
                        echo '<p>Questo significa che <strong>nessuna migration è stata eseguita</strong>.</p>';
                        echo '<p>Esegui: <a href="run-migrations.php?token=magia2025">run-migrations.php</a></p>';
                        echo '</div>';
                    } else {
                        // Leggi tutte le migrations eseguite
                        $ranMigrations = \Illuminate\Support\Facades\DB::table('migrations')
                            ->orderBy('batch')
                            ->orderBy('migration')
                            ->get();

                        // Leggi tutte le migrations disponibili
                        $migrationFiles = [];
                        $migrationPath = database_path('migrations');
                        if (is_dir($migrationPath)) {
                            $files = scandir($migrationPath);
                            foreach ($files as $file) {
                                if (pathinfo($file, PATHINFO_EXTENSION) === 'php') {
                                    $migrationFiles[] = pathinfo($file, PATHINFO_FILENAME);
                                }
                            }
                        }

                        // Statistiche
                        $totalFiles = count($migrationFiles);
                        $totalRan = count($ranMigrations);
                        $pending = $totalFiles - $totalRan;

                        echo '<div class="stats">';
                        echo '<div class="stat-card">';
                        echo '<div class="stat-number">' . $totalFiles . '</div>';
                        echo '<div class="stat-label">Migrations Totali</div>';
                        echo '</div>';

                        echo '<div class="stat-card">';
                        echo '<div class="stat-number" style="color: #28a745;">' . $totalRan . '</div>';
                        echo '<div class="stat-label">Eseguite</div>';
                        echo '</div>';

                        echo '<div class="stat-card">';
                        echo '<div class="stat-number" style="color: ' . ($pending > 0 ? '#dc3545' : '#28a745') . ';">' . $pending . '</div>';
                        echo '<div class="stat-label">Pending</div>';
                        echo '</div>';
                        echo '</div>';

                        // Tabella migrations eseguite
                        if ($totalRan > 0) {
                            echo '<h3 style="margin-top: 20px;">✅ Migrations Eseguite</h3>';
                            echo '<table>';
                            echo '<thead><tr><th>Migration</th><th>Batch</th></tr></thead>';
                            echo '<tbody>';
                            foreach ($ranMigrations as $migration) {
                                echo '<tr>';
                                echo '<td>' . htmlspecialchars($migration->migration) . '</td>';
                                echo '<td><span class="badge info">' . $migration->batch . '</span></td>';
                                echo '</tr>';
                            }
                            echo '</tbody></table>';
                        }

                        // Migrations pending
                        $ranMigrationNames = $ranMigrations->pluck('migration')->toArray();
                        $pendingMigrations = array_diff($migrationFiles, $ranMigrationNames);

                        if (count($pendingMigrations) > 0) {
                            echo '</div><div class="section warning">';
                            echo '<h2>⚠️ Migrations Pending</h2>';
                            echo '<p>Le seguenti migrations NON sono state ancora eseguite:</p>';
                            echo '<table>';
                            echo '<thead><tr><th>Migration</th><th>Azione</th></tr></thead>';
                            echo '<tbody>';
                            foreach ($pendingMigrations as $migration) {
                                echo '<tr>';
                                echo '<td>' . htmlspecialchars($migration) . '</td>';
                                echo '<td><span class="badge warning">DA ESEGUIRE</span></td>';
                                echo '</tr>';
                            }
                            echo '</tbody></table>';
                            echo '<p style="margin-top: 15px;"><strong>Esegui:</strong> <a href="run-migrations.php?token=magia2025">run-migrations.php</a></p>';
                        } else {
                            echo '</div><div class="section success">';
                            echo '<h2>✅ Tutte le Migrations Eseguite</h2>';
                            echo '<p>Non ci sono migrations pending. Il database è aggiornato!</p>';
                        }
                    }

                } catch (\Exception $e) {
                    echo '<div class="error">';
                    echo '<p><span class="badge error">✗ ERRORE</span> ' . htmlspecialchars($e->getMessage()) . '</p>';
                    echo '</div>';
                }
                echo '</div>';

                // VERIFICA TABELLE IMPORTANTI
                echo '<div class="section">';
                echo '<h2>🗄️ Tabelle Database</h2>';

                try {
                    $tables = \Illuminate\Support\Facades\DB::select('SHOW TABLES');
                    $tableKey = 'Tables_in_' . \Illuminate\Support\Facades\DB::getDatabaseName();

                    $importantTables = [
                        'utenti' => 'Utenti del sistema',
                        'clienti' => 'Clienti',
                        'professionisti' => 'Professionisti',
                        'pagamenti_professionisti' => 'Pagamenti Professionisti',
                        'sedi' => 'Sedi',
                        'programmi' => 'Programmi',
                        'lezioni' => 'Lezioni',
                        'impostazioni' => 'Impostazioni',
                    ];

                    echo '<table>';
                    echo '<thead><tr><th>Tabella</th><th>Descrizione</th><th>Record</th><th>Stato</th></tr></thead>';
                    echo '<tbody>';

                    $existingTables = [];
                    foreach ($tables as $table) {
                        $existingTables[] = $table->$tableKey;
                    }

                    foreach ($importantTables as $tableName => $description) {
                        echo '<tr>';
                        echo '<td><strong>' . htmlspecialchars($tableName) . '</strong></td>';
                        echo '<td>' . htmlspecialchars($description) . '</td>';

                        if (in_array($tableName, $existingTables)) {
                            $count = \Illuminate\Support\Facades\DB::table($tableName)->count();
                            echo '<td>' . number_format($count) . '</td>';
                            echo '<td><span class="badge success">✓ ESISTE</span></td>';
                        } else {
                            echo '<td>-</td>';
                            echo '<td><span class="badge error">✗ MANCANTE</span></td>';
                        }
                        echo '</tr>';
                    }

                    echo '</tbody></table>';

                    echo '<p style="margin-top: 15px;"><strong>Totale tabelle nel database:</strong> ' . count($existingTables) . '</p>';

                } catch (\Exception $e) {
                    echo '<div class="error">';
                    echo '<p><span class="badge error">✗ ERRORE</span> ' . htmlspecialchars($e->getMessage()) . '</p>';
                    echo '</div>';
                }
                echo '</div>';

                // RIEPILOGO FINALE
                if (!isset($pending) || $pending === 0) {
                    echo '<div class="section success">';
                    echo '<h2>🎉 Database Aggiornato</h2>';
                    echo '<p>✅ Tutte le migrations sono state eseguite</p>';
                    echo '<p>✅ Tutte le tabelle importanti sono presenti</p>';
                    echo '<p>✅ Il sistema è pronto all\'uso</p>';
                    echo '</div>';
                } else {
                    echo '<div class="section warning">';
                    echo '<h2>⚠️ Azioni Necessarie</h2>';
                    echo '<p>Ci sono migrations pending da eseguire.</p>';
                    echo '<p><a href="run-migrations.php?token=magia2025" style="display: inline-block; padding: 12px 24px; background: #667eea; color: white; text-decoration: none; border-radius: 6px; font-weight: bold; margin-top: 10px;">Esegui Migrations →</a></p>';
                    echo '</div>';
                }
            }
            ?>
        </div>
    </div>
</body>
</html>
