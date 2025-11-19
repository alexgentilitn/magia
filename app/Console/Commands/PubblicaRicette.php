<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Ricetta;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

/**
 * Command: Pubblica Ricette Schedulate
 *
 * Funzione: Pubblica automaticamente le ricette schedulate
 * Esecuzione: Ogni ora via cron
 */
class PubblicaRicette extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ricette:pubblica {--force : Forza pubblicazione anche se data futura}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pubblica automaticamente le ricette schedulate per la data/ora corrente';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Controllo ricette da pubblicare...');

        $now = Carbon::now();

        // Query ricette schedulate
        $query = Ricetta::where('stato', 'schedulata')
            ->whereNotNull('data_pubblicazione');

        if (!$this->option('force')) {
            $query->where('data_pubblicazione', '<=', $now);
        }

        $ricetteDaPubblicare = $query->get();

        if ($ricetteDaPubblicare->isEmpty()) {
            $this->info('✅ Nessuna ricetta da pubblicare al momento.');
            Log::info('PubblicaRicette: Nessuna ricetta trovata', [
                'data_controllo' => $now->toDateTimeString()
            ]);
            return 0;
        }

        $this->info("📋 Trovate {$ricetteDaPubblicare->count()} ricette da pubblicare:");

        $pubblicateCount = 0;
        $erroriCount = 0;

        foreach ($ricetteDaPubblicare as $ricetta) {
            try {
                $this->line("   → Pubblicando: {$ricetta->titolo}");

                // Cambia stato a pubblicata
                $ricetta->stato = 'pubblicata';
                $ricetta->save();

                $pubblicateCount++;

                Log::info('PubblicaRicette: Ricetta pubblicata', [
                    'ricetta_id' => $ricetta->id,
                    'titolo' => $ricetta->titolo,
                    'data_schedulata' => $ricetta->data_pubblicazione,
                    'data_effettiva' => $now->toDateTimeString()
                ]);

                $this->info("     ✅ Pubblicata con successo!");

            } catch (\Exception $e) {
                $erroriCount++;

                Log::error('PubblicaRicette: Errore pubblicazione ricetta', [
                    'ricetta_id' => $ricetta->id,
                    'titolo' => $ricetta->titolo,
                    'errore' => $e->getMessage()
                ]);

                $this->error("     ❌ Errore: {$e->getMessage()}");
            }
        }

        // Riepilogo
        $this->newLine();
        $this->info("📊 Riepilogo:");
        $this->info("   ✅ Pubblicate: {$pubblicateCount}");
        if ($erroriCount > 0) {
            $this->error("   ❌ Errori: {$erroriCount}");
        }

        return $erroriCount > 0 ? 1 : 0;
    }
}
