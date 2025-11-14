<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware: Debug Avanzato
 * Funzione: Logga automaticamente errori SQL, query lente, e problemi comuni
 */
class DebugMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // Abilita query logging solo se APP_DEBUG=true
        if (config('app.debug')) {
            DB::enableQueryLog();
        }

        try {
            $response = $next($request);

            // Log query eseguite (solo in debug mode)
            if (config('app.debug')) {
                $queries = DB::getQueryLog();
                
                // Trova query lente (> 1 secondo)
                $slowQueries = collect($queries)->filter(function($query) {
                    return $query['time'] > 1000;
                });

                if ($slowQueries->count() > 0) {
                    Log::warning('⚠️ QUERY LENTE RILEVATE', [
                        'url' => $request->fullUrl(),
                        'slow_queries' => $slowQueries->toArray()
                    ]);
                }

                // Salva ultime 5 query in sessione per il debug panel
                session(['_debug_queries' => collect($queries)->take(5)->pluck('query')->toArray()]);
            }

            return $response;

        } catch (\Illuminate\Database\QueryException $e) {
            // CATTURA ERRORI SQL E FORNISCE SUGGERIMENTI
            
            $errorMessage = $e->getMessage();
            $errorCode = $e->getCode();
            
            Log::error('❌ ERRORE SQL CATTURATO', [
                'message' => $errorMessage,
                'code' => $errorCode,
                'sql' => $e->getSql() ?? 'N/A',
                'bindings' => $e->getBindings() ?? [],
                'url' => $request->fullUrl(),
                'user_id' => auth()->id() ?? 'guest',
                'trace' => $e->getTraceAsString()
            ]);

            // Analizza errore e fornisce suggerimenti
            $suggestion = $this->getSuggestion($errorMessage);

            // Se in produzione, mostra messaggio generico
            if (!config('app.debug')) {
                return back()->with('error', 'Si è verificato un errore. Il team tecnico è stato notificato.');
            }

            // Se in debug, mostra errore dettagliato
            return back()->with('error', 
                "💥 ERRORE SQL: {$errorMessage}\n\n" .
                "💡 SUGGERIMENTO: {$suggestion}\n\n" .
                "📝 Query: " . ($e->getSql() ?? 'N/A')
            )->withInput();
        }
    }

    /**
     * Analizza l'errore e fornisce suggerimenti
     */
    private function getSuggestion(string $error): string
    {
        // Unknown column
        if (str_contains($error, 'Unknown column')) {
            preg_match("/Unknown column '([^']+)'/", $error, $matches);
            $column = $matches[1] ?? 'sconosciuta';
            
            return "⚠️ La colonna '{$column}' non esiste nella tabella!\n" .
                   "✅ SOLUZIONE:\n" .
                   "1. Crea una migration per aggiungere la colonna\n" .
                   "2. Esegui: php artisan make:migration add_{$column}_to_table\n" .
                   "3. Esegui: php artisan migrate";
        }

        // Table doesn't exist
        if (str_contains($error, "doesn't exist") || str_contains($error, 'Table')) {
            preg_match("/Table '([^']+)'/", $error, $matches);
            $table = $matches[1] ?? 'sconosciuta';
            
            return "⚠️ La tabella '{$table}' non esiste!\n" .
                   "✅ SOLUZIONE:\n" .
                   "1. Verifica che le migration siano state eseguite\n" .
                   "2. Esegui: php artisan migrate\n" .
                   "3. Controlla il nome della tabella nel Model";
        }

        // Duplicate entry
        if (str_contains($error, 'Duplicate entry')) {
            return "⚠️ Valore duplicato! Questo record esiste già.\n" .
                   "✅ SOLUZIONE:\n" .
                   "1. Verifica i campi unique nel database\n" .
                   "2. Usa validazione Laravel per evitare duplicati\n" .
                   "3. Esempio: 'email' => 'unique:users,email'";
        }

        // Foreign key constraint
        if (str_contains($error, 'foreign key constraint')) {
            return "⚠️ Vincolo di chiave esterna violato!\n" .
                   "✅ SOLUZIONE:\n" .
                   "1. Verifica che il record padre esista\n" .
                   "2. Controlla le relazioni nel database\n" .
                   "3. Usa onDelete('cascade') se necessario";
        }

        // Column cannot be null
        if (str_contains($error, 'cannot be null')) {
            preg_match("/Column '([^']+)'/", $error, $matches);
            $column = $matches[1] ?? 'sconosciuta';
            
            return "⚠️ La colonna '{$column}' non può essere NULL!\n" .
                   "✅ SOLUZIONE:\n" .
                   "1. Fornisci un valore per questo campo\n" .
                   "2. Oppure rendi la colonna nullable nella migration\n" .
                   "3. Esempio: \$table->string('{$column}')->nullable();";
        }

        // Default
        return "Controlla i log per maggiori dettagli: storage/logs/laravel.log";
    }
}
