<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\BackupService;

class BackupDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:database {--description= : Descrizione del backup}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crea un backup completo del database MySQL';

    /**
     * Execute the console command.
     */
    public function handle(BackupService $backupService)
    {
        $this->info('Avvio backup database...');

        // Verifica disponibilità mysqldump
        if (!$backupService->isMysqldumpAvailable()) {
            $this->error('ERRORE: mysqldump non è disponibile sul sistema');
            return Command::FAILURE;
        }

        $description = $this->option('description');

        if ($description) {
            $this->info("Descrizione: {$description}");
        }

        $this->newLine();
        $bar = $this->output->createProgressBar(3);
        $bar->start();

        $bar->advance();
        $this->newLine();
        $this->comment('→ Esecuzione backup...');

        // Esegui il backup
        $result = $backupService->createBackup($description);

        $bar->advance();
        $this->newLine();

        if ($result['success']) {
            $this->comment('→ Pulizia backup vecchi...');
            $deleted = $backupService->cleanOldBackups();

            $bar->finish();
            $this->newLine(2);

            $this->info('✓ Backup completato con successo!');
            $this->line("  File: {$result['filename']}");
            $this->line("  {$result['message']}");

            if ($deleted > 0) {
                $this->line("  Eliminati {$deleted} backup vecchi");
            }

            return Command::SUCCESS;
        }

        $bar->finish();
        $this->newLine(2);

        $this->error('✗ Errore durante il backup');
        $this->error("  {$result['message']}");

        return Command::FAILURE;
    }
}
