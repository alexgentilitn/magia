<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Http\Middleware\SuperAdminAuth;

/**
 * Controller: Super Admin
 * Funzionalità avanzate di manutenzione sistema
 */
class SuperAdminController extends Controller
{
    /**
     * Dashboard Super Admin
     */
    public function index()
    {
        $systemInfo = $this->getSystemInfo();
        $cacheInfo = $this->getCacheInfo();
        $logInfo = $this->getLogInfo();
        
        return view('admin.super-admin.index', compact('systemInfo', 'cacheInfo', 'logInfo'));
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
     * Esegui migrations
     */
    public function runMigrations(Request $request)
    {
        try {
            Artisan::call('migrate', ['--force' => true]);
            $output = Artisan::output();
            
            return response()->json([
                'success' => true,
                'message' => 'Migrations eseguite con successo',
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
