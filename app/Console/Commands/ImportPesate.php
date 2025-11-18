<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Models\Pesata;
use App\Models\Cliente;
use Illuminate\Support\Facades\DB;

class ImportPesate extends Command
{
    protected $signature = 'pesate:import {--truncate : Svuota la tabella prima dell\'importazione}';
    protected $description = 'Importa le pesate dai file Excel delle varie sedi nel database MySQL';

    public function handle()
    {
        $this->info('🚀 IMPORTAZIONE PESATE - INIZIO');
        $this->line('================================');
        $this->newLine();

        // Opzione per svuotare la tabella
        if ($this->option('truncate')) {
            if ($this->confirm('⚠️  Vuoi davvero svuotare la tabella pesate?')) {
                DB::table('pesate')->truncate();
                $this->warn('✓ Tabella pesate svuotata');
            }
        }

        $files = [
            ['file' => base_path('GUIDA PROGETTO/Pesate Calliano.xlsx'), 'sede' => 'Calliano'],
            ['file' => base_path('GUIDA PROGETTO/Pesate Pieve di Bono.xlsx'), 'sede' => 'Pieve di Bono'],
            ['file' => base_path('GUIDA PROGETTO/Pesate Riva.xlsx'), 'sede' => 'Riva'],
            ['file' => base_path('GUIDA PROGETTO/Pesate Trento.xlsx'), 'sede' => 'Trento'],
        ];

        // File Darè con caratteri speciali
        $fileDare = glob(base_path('GUIDA PROGETTO/Pesate Dar*.xlsx'));
        if (!empty($fileDare)) {
            $files[] = ['file' => $fileDare[0], 'sede' => 'Darè'];
        }

        $totaleImportate = 0;
        $totaleErrori = 0;
        $clientiNonTrovati = [];

        foreach ($files as $fileInfo) {
            $file = $fileInfo['file'];
            $sede = $fileInfo['sede'];

            if (!file_exists($file)) {
                $this->warn("⚠️  File non trovato: {$file}");
                continue;
            }

            $this->info("📂 Elaboro file: " . basename($file));

            try {
                $spreadsheet = IOFactory::load($file);
                $worksheet = $spreadsheet->getActiveSheet();
                $highestRow = $worksheet->getHighestRow();

                $importateFile = 0;
                $erroriFile = 0;

                // Header nella riga 2, dati dalla riga 3
                for ($row = 3; $row <= $highestRow; $row++) {
                    $cognome = trim($worksheet->getCell("A$row")->getValue() ?? '');
                    $nome = trim($worksheet->getCell("B$row")->getValue() ?? '');

                    if (empty($cognome) && empty($nome)) {
                        continue;
                    }

                    // Cerca cliente per nome e cognome
                    $cliente = Cliente::where('cognome', 'LIKE', $cognome)
                                    ->where('nome', 'LIKE', $nome)
                                    ->first();

                    if (!$cliente) {
                        $clientiNonTrovati[] = "$cognome $nome";
                        $erroriFile++;
                        continue;
                    }

                    // Estrai dati
                    $peso = $this->pulisciNumero($worksheet->getCell("C$row")->getValue());
                    $bmi = $this->pulisciNumero($worksheet->getCell("D$row")->getValue());
                    $pesoSenzaGrassi = $this->pulisciNumero($worksheet->getCell("E$row")->getValue());
                    $muscoloScheletrico = $this->pulisciPercentuale($worksheet->getCell("F$row")->getValue());
                    $grassoCorporeo = $this->pulisciPercentuale($worksheet->getCell("G$row")->getValue());
                    $grassoSottocutaneo = $this->pulisciPercentuale($worksheet->getCell("H$row")->getValue());
                    $grassoViscerale = (int) $worksheet->getCell("I$row")->getValue();
                    $acquaCorporea = $this->pulisciPercentuale($worksheet->getCell("J$row")->getValue());
                    $massaMuscolare = $this->pulisciNumero($worksheet->getCell("K$row")->getValue());
                    $massaOssea = $this->pulisciNumero($worksheet->getCell("L$row")->getValue());
                    $proteine = $this->pulisciPercentuale($worksheet->getCell("M$row")->getValue());
                    $bmr = (int) $worksheet->getCell("N$row")->getValue();
                    $etaMetabolica = (int) $worksheet->getCell("O$row")->getValue();

                    try {
                        Pesata::create([
                            'cliente_id' => $cliente->id,
                            'sede' => $sede,
                            'peso' => $peso,
                            'bmi' => $bmi,
                            'peso_corporeo_senza_grassi' => $pesoSenzaGrassi,
                            'muscolo_scheletrico' => $muscoloScheletrico,
                            'grasso_corporeo' => $grassoCorporeo,
                            'grasso_sottocutaneo' => $grassoSottocutaneo,
                            'grasso_viscerale' => $grassoViscerale,
                            'acqua_corporea' => $acquaCorporea,
                            'massa_muscolare' => $massaMuscolare,
                            'massa_ossea' => $massaOssea,
                            'proteine' => $proteine,
                            'bmr' => $bmr,
                            'eta_metabolica' => $etaMetabolica,
                            'data_rilevazione' => now()->toDateString(),
                        ]);

                        $importateFile++;
                    } catch (\Exception $e) {
                        $this->error("   ❌ Errore riga $row: " . $e->getMessage());
                        $erroriFile++;
                    }
                }

                $this->line("   ✓ Importate: $importateFile | Errori: $erroriFile");
                $totaleImportate += $importateFile;
                $totaleErrori += $erroriFile;

            } catch (\Exception $e) {
                $this->error("❌ Errore nel file {$sede}: " . $e->getMessage());
            }

            $this->newLine();
        }

        // Riepilogo
        $this->line('================================');
        $this->info("✅ IMPORTAZIONE COMPLETATA");
        $this->line("📊 Totale pesate importate: {$totaleImportate}");
        $this->line("⚠️  Totale errori: {$totaleErrori}");

        if (count($clientiNonTrovati) > 0) {
            $this->newLine();
            $this->warn("👤 Clienti non trovati nel database:");
            $clientiUnici = array_unique($clientiNonTrovati);
            foreach ($clientiUnici as $cliente) {
                $this->line("   - {$cliente}");
            }
            $this->newLine();
            $this->comment("💡 Suggerimento: Crea questi clienti prima di importare le pesate.");
        }

        return 0;
    }

    /**
     * Estrae il numero da stringhe come "68.5 kg" o "37.0 %"
     */
    private function pulisciNumero($valore): ?float
    {
        if (empty($valore)) {
            return null;
        }

        if (is_numeric($valore)) {
            return (float) $valore;
        }

        // Rimuovi tutto tranne numeri, punto e virgola
        $valore = preg_replace('/[^0-9.,]/', '', $valore);
        $valore = str_replace(',', '.', $valore);

        return !empty($valore) ? (float) $valore : null;
    }

    /**
     * Estrae la percentuale (già senza il %)
     */
    private function pulisciPercentuale($valore): ?float
    {
        return $this->pulisciNumero($valore);
    }
}
