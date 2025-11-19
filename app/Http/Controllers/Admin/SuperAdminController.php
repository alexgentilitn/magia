<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Http\Middleware\SuperAdminAuth;
use App\Services\BackupService;

/**
 * Controller: Super Admin
 * Funzionalità avanzate di manutenzione sistema
 */
class SuperAdminController extends Controller
{
    /**
     * Dashboard Super Admin
     */
    public function index(BackupService $backupService)
    {
        $systemInfo = $this->getSystemInfo();
        $cacheInfo = $this->getCacheInfo();
        $logInfo = $this->getLogInfo();
        $backupStats = $backupService->getBackupStats();
        $backupList = $backupService->listBackups();
        $mysqldumpAvailable = $backupService->isMysqldumpAvailable();

        return view('admin.super-admin.index', compact(
            'systemInfo',
            'cacheInfo',
            'logInfo',
            'backupStats',
            'backupList',
            'mysqldumpAvailable'
        ));
    }

    /**
     * Toggle Debug Mode
     */
    public function toggleDebug(Request $request)
    {
        $envFile = base_path('.env');
        
        if (!File::exists($envFile)) {
            return response()->json([
                'success' => false,
                'message' => 'File .env non trovato'
            ], 404);
        }

        $envContent = File::get($envFile);
        
        // Verifica stato attuale
        $isDebugEnabled = preg_match('/^APP_DEBUG=true$/m', $envContent);
        
        // Toggle
        if ($isDebugEnabled) {
            $envContent = preg_replace('/^APP_DEBUG=true$/m', 'APP_DEBUG=false', $envContent);
            $newState = false;
            $message = 'Debug mode DISABILITATO';
        } else {
            $envContent = preg_replace('/^APP_DEBUG=false$/m', 'APP_DEBUG=true', $envContent);
            $newState = true;
            $message = 'Debug mode ABILITATO';
        }
        
        File::put($envFile, $envContent);
        
        // Cancella cache config
        $this->clearConfigCache();
        
        return response()->json([
            'success' => true,
            'message' => $message,
            'debug_enabled' => $newState
        ]);
    }

    /**
     * Pulisci tutta la cache
     */
    public function clearAllCache(Request $request)
    {
        $results = [];
        $totalDeleted = 0;
        $errors = [];
        $hasErrors = false;

        // 1. Cache configurazione bootstrap
        try {
            $bootstrapCache = base_path('bootstrap/cache');

            // Verifica permessi directory
            if (!File::exists($bootstrapCache)) {
                $results['bootstrap'] = 'Directory non esiste';
            } elseif (!is_writable($bootstrapCache)) {
                $results['bootstrap'] = 'ERRORE: Directory non scrivibile (permessi)';
                $errors[] = 'Bootstrap cache: permessi insufficienti';
                $hasErrors = true;
            } else {
                $files = [
                    base_path('bootstrap/cache/config.php'),
                    base_path('bootstrap/cache/routes-v7.php'),
                    base_path('bootstrap/cache/services.php'),
                    base_path('bootstrap/cache/events.php'),
                ];

                $deleted = 0;
                $skipped = 0;
                foreach ($files as $file) {
                    if (File::exists($file)) {
                        if (is_writable($file)) {
                            File::delete($file);
                            $deleted++;
                        } else {
                            $skipped++;
                            $errors[] = "File non cancellabile: " . basename($file);
                        }
                    }
                }

                $results['bootstrap'] = "OK ($deleted eliminati, $skipped saltati)";
                $totalDeleted += $deleted;

                if ($skipped > 0) {
                    $hasErrors = true;
                }
            }
        } catch (\Exception $e) {
            $results['bootstrap'] = 'ERRORE: ' . $e->getMessage();
            $errors[] = 'Bootstrap: ' . $e->getMessage();
            $hasErrors = true;
        }

        // 2. Cache view
        try {
            $viewsPath = storage_path('framework/views');
            $deleted = 0;
            $skipped = 0;

            if (!File::exists($viewsPath)) {
                $results['views'] = 'Directory non esiste';
            } elseif (!is_writable($viewsPath)) {
                $results['views'] = 'ERRORE: Directory non scrivibile (permessi)';
                $errors[] = 'Views cache: permessi insufficienti';
                $hasErrors = true;
            } else {
                $files = File::files($viewsPath);
                foreach ($files as $file) {
                    if (pathinfo($file, PATHINFO_EXTENSION) === 'php') {
                        if (is_writable($file)) {
                            File::delete($file);
                            $deleted++;
                        } else {
                            $skipped++;
                        }
                    }
                }

                $results['views'] = "OK ($deleted eliminati, $skipped saltati)";
                $totalDeleted += $deleted;

                if ($skipped > 0) {
                    $hasErrors = true;
                }
            }
        } catch (\Exception $e) {
            $results['views'] = 'ERRORE: ' . $e->getMessage();
            $errors[] = 'Views: ' . $e->getMessage();
            $hasErrors = true;
        }

        // 3. Cache framework
        try {
            $cachePath = storage_path('framework/cache/data');
            $deleted = 0;
            $skipped = 0;

            if (!File::exists($cachePath)) {
                $results['framework'] = 'Directory non esiste';
            } elseif (!is_writable($cachePath)) {
                $results['framework'] = 'ERRORE: Directory non scrivibile (permessi)';
                $errors[] = 'Framework cache: permessi insufficienti';
                $hasErrors = true;
            } else {
                $dirs = File::directories($cachePath);
                foreach ($dirs as $dir) {
                    if (is_writable($dir)) {
                        $files = File::files($dir);
                        foreach ($files as $file) {
                            if (is_writable($file)) {
                                File::delete($file);
                                $deleted++;
                            } else {
                                $skipped++;
                            }
                        }
                    } else {
                        $skipped++;
                    }
                }

                $results['framework'] = "OK ($deleted eliminati, $skipped saltati)";
                $totalDeleted += $deleted;

                if ($skipped > 0) {
                    $hasErrors = true;
                }
            }
        } catch (\Exception $e) {
            $results['framework'] = 'ERRORE: ' . $e->getMessage();
            $errors[] = 'Framework: ' . $e->getMessage();
            $hasErrors = true;
        }

        // Prepara messaggio di risposta
        if ($hasErrors && $totalDeleted === 0) {
            return response()->json([
                'success' => false,
                'message' => 'Errore pulizia cache: ' . implode(', ', $errors),
                'results' => $results,
                'total_deleted' => $totalDeleted,
                'errors' => $errors
            ], 500);
        }

        $message = "Cache pulita con successo! $totalDeleted file eliminati";
        if ($hasErrors) {
            $message .= " (alcuni errori riscontrati)";
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'results' => $results,
            'total_deleted' => $totalDeleted,
            'errors' => $errors
        ]);
    }

    /**
     * Pulisci solo cache configurazione
     */
    public function clearConfigCache()
    {
        try {
            $configCache = base_path('bootstrap/cache/config.php');
            $deleted = false;

            if (File::exists($configCache)) {
                File::delete($configCache);
                $deleted = true;
            }

            return response()->json([
                'success' => true,
                'message' => $deleted ? 'Cache configurazione pulita' : 'Nessuna cache da pulire'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Errore: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Pulisci log
     */
    public function clearLogs(Request $request)
    {
        try {
            $logPath = storage_path('logs/laravel.log');

            // Crea directory se non esiste
            $logDir = dirname($logPath);
            if (!File::exists($logDir)) {
                File::makeDirectory($logDir, 0755, true);
            }

            // Pulisci o crea il file vuoto
            File::put($logPath, '');

            return response()->json([
                'success' => true,
                'message' => 'Log pulito con successo'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Errore pulizia log: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Visualizza log
     */
    public function viewLogs(Request $request)
    {
        $logPath = storage_path('logs/laravel.log');

        if (!File::exists($logPath)) {
            return response()->json([
                'success' => true,
                'content' => 'Nessun file di log presente',
                'size' => 0,
                'modified' => null
            ]);
        }

        try {
            // Prendi le ultime 100 righe
            $lines = $request->input('lines', 100);
            $content = File::get($logPath);

            if (empty($content)) {
                return response()->json([
                    'success' => true,
                    'content' => 'Log vuoto',
                    'size' => 0,
                    'modified' => File::lastModified($logPath)
                ]);
            }

            $logLines = array_slice(explode("\n", $content), -$lines);

            return response()->json([
                'success' => true,
                'content' => implode("\n", $logLines),
                'size' => File::size($logPath),
                'modified' => File::lastModified($logPath)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Errore lettura log: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Info database
     */
    public function databaseInfo(Request $request)
    {
        try {
            $tables = DB::select('SHOW TABLES');
            $dbName = DB::getDatabaseName();
            
            $tableInfo = [];
            foreach ($tables as $table) {
                $tableName = $table->{'Tables_in_' . $dbName};
                $count = DB::table($tableName)->count();
                $tableInfo[] = [
                    'name' => $tableName,
                    'rows' => $count
                ];
            }
            
            return response()->json([
                'success' => true,
                'database' => $dbName,
                'tables' => $tableInfo,
                'total_tables' => count($tableInfo)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Errore: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Ottimizza database
     */
    public function optimizeDatabase(Request $request)
    {
        try {
            $tables = DB::select('SHOW TABLES');
            $dbName = DB::getDatabaseName();
            $optimized = [];
            
            foreach ($tables as $table) {
                $tableName = $table->{'Tables_in_' . $dbName};
                DB::statement("OPTIMIZE TABLE `{$tableName}`");
                $optimized[] = $tableName;
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Database ottimizzato con successo',
                'tables_optimized' => count($optimized)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Errore: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Ottieni status di tutte le migrations
     */
    public function getMigrationsStatus(Request $request)
    {
        try {
            $migrationsPath = database_path('migrations');
            $migrationFiles = File::glob($migrationsPath . '/*.php');

            // Ottieni migrations eseguite dal database
            $ranMigrations = DB::table('migrations')
                ->orderBy('batch', 'desc')
                ->orderBy('migration', 'asc')
                ->get()
                ->keyBy('migration');

            $migrations = [];

            foreach ($migrationFiles as $file) {
                $filename = basename($file);
                $migrationName = pathinfo($filename, PATHINFO_FILENAME);

                $isRan = isset($ranMigrations[$migrationName]);

                $migrations[] = [
                    'name' => $migrationName,
                    'filename' => $filename,
                    'status' => $isRan ? 'ran' : 'pending',
                    'batch' => $isRan ? $ranMigrations[$migrationName]->batch : null,
                    'executed_at' => $isRan ? $ranMigrations[$migrationName]->created_at ?? null : null,
                    'file_path' => $file,
                ];
            }

            // Ordina: pending prima, poi ran (più recenti prima)
            usort($migrations, function($a, $b) {
                if ($a['status'] !== $b['status']) {
                    return $a['status'] === 'pending' ? -1 : 1;
                }
                if ($a['status'] === 'ran') {
                    return ($b['batch'] ?? 0) - ($a['batch'] ?? 0);
                }
                return strcmp($a['name'], $b['name']);
            });

            return response()->json([
                'success' => true,
                'migrations' => $migrations,
                'total' => count($migrations),
                'pending' => count(array_filter($migrations, fn($m) => $m['status'] === 'pending')),
                'ran' => count(array_filter($migrations, fn($m) => $m['status'] === 'ran')),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Errore: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Esegui migrations (tutte o selettive)
     */
    public function runMigrations(Request $request)
    {
        try {
            // Verifica connessione database prima di eseguire
            try {
                DB::connection()->getPdo();
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Errore connessione database: ' . $e->getMessage(),
                    'debug' => [
                        'host' => config('database.connections.mysql.host'),
                        'database' => config('database.connections.mysql.database'),
                        'error_type' => get_class($e)
                    ]
                ], 500);
            }

            $migrations = $request->input('migrations', []); // Array di migration names

            if (empty($migrations)) {
                // Esegui tutte le migrations pending
                $exitCode = Artisan::call('migrate', ['--force' => true]);
                $output = Artisan::output();

                return response()->json([
                    'success' => $exitCode === 0,
                    'message' => $exitCode === 0
                        ? 'Tutte le migrations sono state eseguite'
                        : 'Alcune migrations sono fallite',
                    'output' => $output,
                    'exit_code' => $exitCode
                ]);
            }

            // Esegui migrations specifiche
            $results = [];
            foreach ($migrations as $migration) {
                try {
                    // Verifica che il file esista
                    $migrationPath = database_path('migrations/' . $migration . '.php');
                    if (!file_exists($migrationPath)) {
                        $results[$migration] = [
                            'success' => false,
                            'error' => 'File migration non trovato: ' . $migrationPath
                        ];
                        continue;
                    }

                    // Esegui migration specifica usando path
                    $exitCode = Artisan::call('migrate', [
                        '--path' => 'database/migrations/' . $migration . '.php',
                        '--force' => true
                    ]);

                    $output = Artisan::output();

                    $results[$migration] = [
                        'success' => $exitCode === 0,
                        'output' => $output,
                        'exit_code' => $exitCode
                    ];

                    if ($exitCode !== 0) {
                        $results[$migration]['error'] = 'Migration fallita (exit code: ' . $exitCode . ')';
                    }

                } catch (\Exception $e) {
                    $results[$migration] = [
                        'success' => false,
                        'error' => $e->getMessage(),
                        'error_type' => get_class($e),
                        'trace' => config('app.debug') ? $e->getTraceAsString() : 'Enable debug mode per vedere trace'
                    ];
                }
            }

            $successCount = count(array_filter($results, fn($r) => $r['success']));
            $failCount = count($results) - $successCount;

            // Crea messaggio dettagliato
            $message = "{$successCount} migration(s) eseguita/e con successo";
            if ($failCount > 0) {
                $message .= ", {$failCount} fallita/e";

                // Aggiungi dettagli errori
                $errors = [];
                foreach ($results as $name => $result) {
                    if (!$result['success']) {
                        $errors[] = $name . ': ' . ($result['error'] ?? 'Errore sconosciuto');
                    }
                }
                $message .= "\n\nDettagli:\n" . implode("\n", $errors);
            }

            return response()->json([
                'success' => $failCount === 0,
                'message' => $message,
                'results' => $results,
                'summary' => [
                    'total' => count($migrations),
                    'success' => $successCount,
                    'failed' => $failCount
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Errore esecuzione migrations: ' . $e->getMessage(),
                'error_type' => get_class($e),
                'trace' => config('app.debug') ? $e->getTraceAsString() : 'Enable debug mode per vedere trace completo'
            ], 500);
        }
    }

    /**
     * Rollback migrations specifiche
     */
    public function rollbackMigration(Request $request)
    {
        try {
            $migration = $request->input('migration');

            if (!$migration) {
                return response()->json([
                    'success' => false,
                    'message' => 'Migration non specificata'
                ], 400);
            }

            // Verifica che la migration sia stata eseguita
            $exists = DB::table('migrations')
                ->where('migration', $migration)
                ->exists();

            if (!$exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Migration non trovata nel database'
                ], 404);
            }

            // Esegui rollback per batch specifico
            $batch = DB::table('migrations')
                ->where('migration', $migration)
                ->value('batch');

            Artisan::call('migrate:rollback', [
                '--step' => 1,
                '--force' => true
            ]);

            $output = Artisan::output();

            return response()->json([
                'success' => true,
                'message' => 'Rollback eseguito con successo',
                'output' => $output
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Errore: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Sincronizza migration già eseguita
     */
    public function syncMigration(Request $request)
    {
        try {
            $migration = $request->input('migration');

            if (!$migration) {
                return response()->json([
                    'success' => false,
                    'message' => 'Migration non specificata'
                ], 400);
            }

            // Verifica che la migration non sia già registrata
            $exists = DB::table('migrations')
                ->where('migration', $migration)
                ->exists();

            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Migration già registrata nella tabella migrations'
                ], 400);
            }

            // Ottieni l'ultimo batch
            $lastBatch = DB::table('migrations')->max('batch') ?? 0;
            $newBatch = $lastBatch + 1;

            // Registra la migration
            DB::table('migrations')->insert([
                'migration' => $migration,
                'batch' => $newBatch
            ]);

            return response()->json([
                'success' => true,
                'message' => "Migration '{$migration}' registrata con successo (batch {$newBatch})"
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Errore: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Elimina file migration
     */
    public function deleteMigrationFile(Request $request)
    {
        try {
            $migration = $request->input('migration');

            if (!$migration) {
                return response()->json([
                    'success' => false,
                    'message' => 'Migration non specificata'
                ], 400);
            }

            // Verifica che la migration NON sia stata eseguita
            $isRan = DB::table('migrations')
                ->where('migration', $migration)
                ->exists();

            if ($isRan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Impossibile eliminare: migration già eseguita. Esegui prima il rollback.'
                ], 400);
            }

            $filePath = database_path('migrations/' . $migration . '.php');

            if (!File::exists($filePath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'File migration non trovato'
                ], 404);
            }

            File::delete($filePath);

            return response()->json([
                'success' => true,
                'message' => 'File migration eliminato con successo'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Errore: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Crea un backup del database
     */
    public function createBackup(Request $request, BackupService $backupService)
    {
        $request->validate([
            'description' => 'nullable|string|max:255'
        ]);

        $result = $backupService->createBackup($request->input('description'));

        return response()->json($result);
    }

    /**
     * Lista tutti i backup disponibili
     */
    public function listBackups(BackupService $backupService)
    {
        $backups = $backupService->listBackups();

        return response()->json([
            'success' => true,
            'backups' => $backups
        ]);
    }

    /**
     * Download di un backup
     */
    public function downloadBackup(Request $request, BackupService $backupService)
    {
        $filename = $request->input('filename');

        if (!$filename) {
            return response()->json([
                'success' => false,
                'message' => 'Nome file non specificato'
            ], 400);
        }

        // Validazione del nome file (sicurezza)
        if (!preg_match('/^backup_\d{4}-\d{2}-\d{2}_\d{2}-\d{2}-\d{2}\.sql$/', $filename)) {
            return response()->json([
                'success' => false,
                'message' => 'Nome file non valido'
            ], 400);
        }

        $download = $backupService->downloadBackup($filename);

        if (!$download) {
            return response()->json([
                'success' => false,
                'message' => 'Backup non trovato'
            ], 404);
        }

        return $download;
    }

    /**
     * Elimina un backup
     */
    public function deleteBackup(Request $request, BackupService $backupService)
    {
        $filename = $request->input('filename');

        if (!$filename) {
            return response()->json([
                'success' => false,
                'message' => 'Nome file non specificato'
            ], 400);
        }

        // Validazione del nome file (sicurezza)
        if (!preg_match('/^backup_\d{4}-\d{2}-\d{2}_\d{2}-\d{2}-\d{2}\.sql$/', $filename)) {
            return response()->json([
                'success' => false,
                'message' => 'Nome file non valido'
            ], 400);
        }

        $success = $backupService->deleteBackup($filename);

        if ($success) {
            return response()->json([
                'success' => true,
                'message' => 'Backup eliminato con successo'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Errore durante l\'eliminazione del backup'
        ], 500);
    }

    /**
     * Pulisce i backup vecchi
     */
    public function cleanOldBackups(BackupService $backupService)
    {
        $deleted = $backupService->cleanOldBackups();

        return response()->json([
            'success' => true,
            'message' => "Eliminati $deleted backup vecchi",
            'deleted' => $deleted
        ]);
    }

    /**
     * Gestione Segnalazioni (Super Admin)
     * Lista tutte le segnalazioni con filtri
     */
    public function segnalazioni(Request $request)
    {
        $query = \App\Models\Segnalazione::with(['utente', 'risolutore'])->orderBy('created_at', 'desc');

        // Filtro per stato
        if ($request->filled('stato')) {
            $query->where('stato', $request->stato);
        }

        // Filtro per tipo
        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        // Filtro per priorità
        if ($request->filled('priorita')) {
            $query->where('priorita', $request->priorita);
        }

        // Filtro per ricerca testo
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('titolo', 'like', "%{$search}%")
                  ->orWhere('descrizione', 'like', "%{$search}%");
            });
        }

        $segnalazioni = $query->paginate(20);

        // Statistiche riepilogo
        $stats = [
            'totale' => \App\Models\Segnalazione::count(),
            'aperte' => \App\Models\Segnalazione::where('stato', 'aperta')->count(),
            'in_lavorazione' => \App\Models\Segnalazione::where('stato', 'in_lavorazione')->count(),
            'risolte' => \App\Models\Segnalazione::where('stato', 'risolta')->count(),
            'bug' => \App\Models\Segnalazione::where('tipo', 'bug')->count(),
            'suggerimenti' => \App\Models\Segnalazione::where('tipo', 'suggerimento')->count(),
            'alta_priorita' => \App\Models\Segnalazione::where('priorita', 'alta')->count(),
        ];

        return view('admin.super-admin.segnalazioni', compact('segnalazioni', 'stats'));
    }

    /**
     * Logout Super Admin
     */
    public function logout(Request $request)
    {
        SuperAdminAuth::logout();

        return redirect()->route('admin.dashboard')
            ->with('success', 'Logout Super Admin effettuato');
    }

    /**
     * Info sistema
     */
    private function getSystemInfo()
    {
        $envFile = base_path('.env');
        $envContent = File::exists($envFile) ? File::get($envFile) : '';
        $isDebugEnabled = preg_match('/^APP_DEBUG=true$/m', $envContent);

        return [
            'php_version' => phpversion(),
            'laravel_version' => app()->version(),
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'N/A',
            'environment' => config('app.env'),
            'debug_mode' => $isDebugEnabled,
            'timezone' => config('app.timezone'),
            'locale' => config('app.locale'),
            'memory_limit' => ini_get('memory_limit'),
            'max_upload' => ini_get('upload_max_filesize'),
            'max_post' => ini_get('post_max_size'),
        ];
    }

    /**
     * Info cache
     */
    private function getCacheInfo()
    {
        $bootstrapCache = base_path('bootstrap/cache');
        $cacheFiles = [
            'config.php' => File::exists($bootstrapCache . '/config.php'),
            'routes-v7.php' => File::exists($bootstrapCache . '/routes-v7.php'),
            'services.php' => File::exists($bootstrapCache . '/services.php'),
            'events.php' => File::exists($bootstrapCache . '/events.php'),
        ];

        $viewsCache = storage_path('framework/views');
        $viewsCacheCount = File::exists($viewsCache) ? count(File::files($viewsCache)) : 0;

        return [
            'files' => $cacheFiles,
            'views_cached' => $viewsCacheCount,
        ];
    }

    /**
     * Info log
     */
    private function getLogInfo()
    {
        $logPath = storage_path('logs/laravel.log');
        
        if (!File::exists($logPath)) {
            return [
                'exists' => false,
                'size' => 0,
                'modified' => null,
            ];
        }

        return [
            'exists' => true,
            'size' => File::size($logPath),
            'size_human' => $this->formatBytes(File::size($logPath)),
            'modified' => File::lastModified($logPath),
            'modified_human' => date('d/m/Y H:i:s', File::lastModified($logPath)),
        ];
    }

    /**
     * Formatta bytes in formato leggibile
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
}
