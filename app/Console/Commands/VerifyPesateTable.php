<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

class VerifyPesateTable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pesate:verify {--create : Crea automaticamente la tabella se non esiste}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verifica se la tabella pesate esiste e opzionalmente la crea';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Verifica tabella pesate...');

        // Verifica se la tabella esiste
        if (Schema::hasTable('pesate')) {
            $this->info('✓ La tabella "pesate" esiste già nel database.');

            // Mostra il numero di record
            $count = DB::table('pesate')->count();
            $this->info("  Contiene {$count} record.");

            return 0;
        }

        $this->warn('✗ La tabella "pesate" NON esiste nel database.');
        $this->newLine();

        // Se è stata passata l'opzione --create, crea la tabella
        if ($this->option('create')) {
            $this->info('Creazione tabella in corso...');

            try {
                Artisan::call('migrate', [
                    '--path' => 'database/migrations/2025_11_18_160408_create_pesate_table.php',
                    '--force' => true
                ]);

                $this->info('✓ Tabella "pesate" creata con successo!');
                return 0;

            } catch (\Exception $e) {
                $this->error('✗ Errore durante la creazione della tabella:');
                $this->error($e->getMessage());
                return 1;
            }
        }

        // Mostra le istruzioni per creare la tabella
        $this->newLine();
        $this->info('Per creare la tabella, hai 2 opzioni:');
        $this->newLine();
        $this->info('OPZIONE 1 - Usando il comando artisan:');
        $this->line('  php artisan pesate:verify --create');
        $this->newLine();
        $this->info('OPZIONE 2 - Usando la migration:');
        $this->line('  php artisan migrate --path=database/migrations/2025_11_18_160408_create_pesate_table.php');
        $this->newLine();
        $this->info('OPZIONE 3 - Manualmente con SQL:');
        $this->line('  Esegui il file SQL_PESATE.sql in phpMyAdmin');

        return 1;
    }
}
