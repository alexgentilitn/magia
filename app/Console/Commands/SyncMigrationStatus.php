<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SyncMigrationStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migration:sync {migration : Nome della migration da sincronizzare}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Registra una migration già eseguita nella tabella migrations';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $migrationName = $this->argument('migration');

        // Verifica che la migration non sia già registrata
        $exists = DB::table('migrations')
            ->where('migration', $migrationName)
            ->exists();

        if ($exists) {
            $this->error("La migration '{$migrationName}' è già registrata!");
            return 1;
        }

        // Ottieni l'ultimo batch
        $lastBatch = DB::table('migrations')->max('batch') ?? 0;
        $newBatch = $lastBatch + 1;

        // Registra la migration
        DB::table('migrations')->insert([
            'migration' => $migrationName,
            'batch' => $newBatch
        ]);

        $this->info("✓ Migration '{$migrationName}' registrata con successo (batch {$newBatch})");

        return 0;
    }
}
