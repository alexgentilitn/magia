<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Command: Diagnostica Database
 * Funzione: Verifica struttura database e identifica problemi comuni
 * Uso: php artisan db:diagnose
 */
class DiagnoseDatabase extends Command
{
    protected $signature = 'db:diagnose {--fix : Tenta di correggere i problemi automaticamente}';
    protected $description = 'Diagnostica problemi comuni nel database';

    public function handle()
    {
        $this->info('🔍 Inizio diagnostica database...');
        $this->newLine();

        $problems = 0;

        // 1. Verifica connessione database
        $this->info('1️⃣ Test connessione database...');
        try {
            DB::connection()->getPdo();
            $this->line('   ✅ Connessione OK');
        } catch (\Exception $e) {
            $this->error('   ❌ Connessione fallita: ' . $e->getMessage());
            return 1;
        }

        // 2. Verifica tabelle esistenti
        $this->newLine();
        $this->info('2️⃣ Verifica tabelle...');
        
        $expectedTables = [
            'users' => 'Utenti',
            'utenti' => 'Utenti (legacy)',
            'clienti' => 'Clienti',
            'ruoli' => 'Ruoli',
            'permessi' => 'Permessi',
            'migrations' => 'Migrations'
        ];

        foreach ($expectedTables as $table => $description) {
            if (Schema::hasTable($table)) {
                $count = DB::table($table)->count();
                $this->line("   ✅ {$description} ({$table}): {$count} record");
            } else {
                $this->warn("   ⚠️ {$description} ({$table}): MANCANTE");
                $problems++;
            }
        }

        // 3. Verifica colonne critiche nella tabella clienti
        $this->newLine();
        $this->info('3️⃣ Verifica struttura tabella clienti...');
        
        if (Schema::hasTable('clienti')) {
            $requiredColumns = [
                'id', 'nome', 'cognome', 'email', 'telefono_mobile',
                'data_nascita', 'codice_fiscale', 'stato_cliente',
                'indirizzo_via', 'indirizzo_citta', 'indirizzo_cap', 'indirizzo_provincia',
                'created_at', 'updated_at'
            ];

            foreach ($requiredColumns as $column) {
                if (Schema::hasColumn('clienti', $column)) {
                    $this->line("   ✅ Colonna '{$column}'");
                } else {
                    $this->error("   ❌ Colonna '{$column}' MANCANTE!");
                    $problems++;
                    
                    if ($this->option('fix')) {
                        $this->warn("   🔧 Tentativo correzione automatica non implementato per questa colonna");
                        $this->warn("   💡 Esegui: php artisan make:migration add_{$column}_to_clienti");
                    }
                }
            }
        }

        // 4. Verifica indici e chiavi
        $this->newLine();
        $this->info('4️⃣ Verifica indici e chiavi...');
        
        if (Schema::hasTable('clienti')) {
            try {
                $indexes = DB::select("SHOW INDEX FROM clienti");
                $this->line('   ✅ Trovati ' . count($indexes) . ' indici');
                
                foreach ($indexes as $index) {
                    if ($index->Key_name === 'PRIMARY') {
                        $this->line("   ✅ Primary Key: {$index->Column_name}");
                    }
                }
            } catch (\Exception $e) {
                $this->warn('   ⚠️ Impossibile verificare indici');
            }
        }

        // 5. Verifica dati di test
        $this->newLine();
        $this->info('5️⃣ Verifica dati...');
        
        if (Schema::hasTable('utenti')) {
            $adminCount = DB::table('utenti')->where('tipo_utente', 'admin')->count();
            if ($adminCount > 0) {
                $this->line("   ✅ Utenti admin: {$adminCount}");
            } else {
                $this->warn("   ⚠️ Nessun utente admin trovato!");
                $problems++;
            }
        }

        if (Schema::hasTable('clienti')) {
            $clientiCount = DB::table('clienti')->count();
            $this->line("   📊 Clienti totali: {$clientiCount}");
        }

        // 6. Verifica permessi storage
        $this->newLine();
        $this->info('6️⃣ Verifica permessi cartelle...');
        
        $directories = [
            storage_path('logs'),
            storage_path('framework/cache'),
            storage_path('framework/sessions'),
            storage_path('framework/views'),
        ];

        foreach ($directories as $dir) {
            if (is_writable($dir)) {
                $this->line("   ✅ " . basename($dir) . ": scrivibile");
            } else {
                $this->error("   ❌ " . basename($dir) . ": NON scrivibile");
                $this->warn("   💡 Esegui: chmod -R 775 storage/");
                $problems++;
            }
        }

        // RIEPILOGO
        $this->newLine();
        $this->line('═══════════════════════════════════════');
        
        if ($problems === 0) {
            $this->info('✅ Nessun problema rilevato!');
            $this->info('Il database è configurato correttamente.');
        } else {
            $this->error("❌ Rilevati {$problems} problemi!");
            $this->warn('Segui i suggerimenti sopra per risolverli.');
            
            if (!$this->option('fix')) {
                $this->newLine();
                $this->info('💡 TIP: Usa --fix per tentare correzioni automatiche');
                $this->info('   Esempio: php artisan db:diagnose --fix');
            }
        }
        
        $this->newLine();

        return $problems === 0 ? 0 : 1;
    }
}
