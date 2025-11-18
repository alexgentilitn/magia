<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Models\PesataJson;
use App\Models\ClienteJson;

class ImportPesate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pesate:import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Importa le pesate dai file Excel delle varie sedi';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 IMPORTAZIONE PESATE - INIZIO');
        $this->line('================================');
        $this->newLine();

        // Inizializza tabelle
        $this->info('📋 Inizializzazione tabelle...');
        PesataJson::initTable();
        ClienteJson::initTable();

        $files = [
            ['file' => base_path('GUIDA PROGETTO/Pesate Calliano.xlsx'), 'sede' => 'Calliano'],
            ['file' => base_path('GUIDA PROGETTO/Pesate Pieve di Bono.xlsx'), 'sede' => 'Pieve di Bono'],
            ['file' => base_path('GUIDA PROGETTO/Pesate Riva.xlsx'), 'sede' => 'Riva'],
            ['file' => base_path('GUIDA PROGETTO/Pesate Trento.xlsx'), 'sede' => 'Trento'],
        ];

        // Prova anche con il file Darè che ha caratteri speciali
        $fileDare = glob(base_path('GUIDA PROGETTO/Pesate Dar*.xlsx'));
        if (!empty($fileDare)) {
            $files[] = ['file' => $fileDare[0], 'sede' => 'Darè'];
        }

        $totaleImportate = 0;
        $totaleErrori = 0;
        $clientiNonTrovati = [];
        $clientiCreati = [];

        // FASE 1: Raccogli tutti i nomi unici dai file
        $this->info('📝 Fase 1: Raccolta clienti unici...');
        $clientiUnici = [];

        foreach ($files as $fileInfo) {
            $file = $fileInfo['file'];
            $sede = $fileInfo['sede'];

            if (!file_exists($file)) {
                continue;
            }

            try {
                $spreadsheet = IOFactory::load($file);
                $worksheet = $spreadsheet->getActiveSheet();
                $highestRow = $worksheet->getHighestRow();

                for ($row = 3; $row <= $highestRow; $row++) {
                    $cognome = trim($worksheet->getCell("A$row")->getValue() ?? '');
                    $nome = trim($worksheet->getCell("B$row")->getValue() ?? '');

                    if (empty($cognome) && empty($nome)) {
                        continue;
                    }

                    $key = strtolower($cognome . '|' . $nome);
                    if (!isset($clientiUnici[$key])) {
                        $clientiUnici[$key] = [
                            'nome' => $nome,
                            'cognome' => $cognome,
                            'sede' => $sede,
                        ];
                    }
                }
            } catch (\Exception $e) {
                // Ignora errori in questa fase
            }
        }

        $this->line("  Trovati " . count($clientiUnici) . " clienti unici");

        // FASE 2: Crea tutti i clienti che non esistono
        $this->newLine();
        $this->info('👥 Fase 2: Creazione clienti mancanti...');

        foreach ($clientiUnici as $key => $datiCliente) {
            $cliente = ClienteJson::findByNomeCognome($datiCliente['nome'], $datiCliente['cognome']);

            if (!$cliente) {
                $nuovoCliente = [
                    'nome' => $datiCliente['nome'],
                    'cognome' => $datiCliente['cognome'],
                    'email' => strtolower($datiCliente['nome'] . '.' . $datiCliente['cognome']) . '@cliente.magia.local',
                    'password' => password_hash('password123', PASSWORD_DEFAULT),
                    'attivo' => true,
                    'sede_principale' => $datiCliente['sede'],
                    'stato_cliente' => 'attivo',
                ];

                if (ClienteJson::create($nuovoCliente)) {
                    $clientiCreati[] = "{$datiCliente['cognome']} {$datiCliente['nome']}";
                    $this->line("  ✅ Creato: {$datiCliente['cognome']} {$datiCliente['nome']}");
                }
            }
        }

        $this->newLine();
        $this->info('📊 Fase 3: Importazione pesate...');

        // FASE 3: Importa le pesate
        foreach ($files as $fileInfo) {
            $file = $fileInfo['file'];
            $sede = $fileInfo['sede'];

            if (!file_exists($file)) {
                $this->warn("⚠️  File non trovato: $file");
                continue;
            }

            $this->newLine();
            $this->info("📊 Elaborazione: " . basename($file) . " (Sede: $sede)");
            $this->line('-----------------------------------');

            try {
                $spreadsheet = IOFactory::load($file);
                $worksheet = $spreadsheet->getActiveSheet();
                $highestRow = $worksheet->getHighestRow();

                // Salta la riga 1 (Tabella 1) e la riga 2 (intestazioni)
                for ($row = 3; $row <= $highestRow; $row++) {
                    // Leggi i dati dalla riga
                    $cognome = trim($worksheet->getCell("A$row")->getValue() ?? '');
                    $nome = trim($worksheet->getCell("B$row")->getValue() ?? '');

                    // Salta righe vuote
                    if (empty($cognome) && empty($nome)) {
                        continue;
                    }

                    // Cerca il cliente (dovrebbe esistere dopo la Fase 2)
                    $cliente = ClienteJson::findByNomeCognome($nome, $cognome);

                    if (!$cliente) {
                        $clientiNonTrovati[] = "$cognome $nome";
                        $this->error("  ❌ Cliente non trovato: $cognome $nome");
                        $totaleErrori++;
                        continue;
                    }

                    // Estrai i valori dalla riga
                    $peso = $worksheet->getCell("C$row")->getValue();
                    $bmi = $worksheet->getCell("D$row")->getValue();
                    $pesoCorpSenzaGrassi = $worksheet->getCell("E$row")->getValue();
                    $muscoloScheletrico = $worksheet->getCell("F$row")->getValue();
                    $grassoCorporeo = $worksheet->getCell("G$row")->getValue();
                    $grassoSottocutaneo = $worksheet->getCell("H$row")->getValue();
                    $grassoViscerale = $worksheet->getCell("I$row")->getValue();
                    $acquaCorporea = $worksheet->getCell("J$row")->getValue();
                    $massaMuscolare = $worksheet->getCell("K$row")->getValue();
                    $massaOssea = $worksheet->getCell("L$row")->getValue();
                    $proteine = $worksheet->getCell("M$row")->getValue();
                    $bmr = $worksheet->getCell("N$row")->getValue();
                    $etaMetabolica = $worksheet->getCell("O$row")->getValue();

                    // Crea la pesata
                    $pesata = [
                        'cliente_id' => $cliente['id'],
                        'cognome' => $cognome,
                        'nome' => $nome,
                        'sede' => $sede,
                        'peso' => $peso,
                        'bmi' => $this->pulisciNumero($bmi),
                        'peso_corporeo_senza_grassi' => $pesoCorpSenzaGrassi,
                        'muscolo_scheletrico' => $this->pulisciPercentuale($muscoloScheletrico),
                        'grasso_corporeo' => $this->pulisciPercentuale($grassoCorporeo),
                        'grasso_sottocutaneo' => $this->pulisciPercentuale($grassoSottocutaneo),
                        'grasso_viscerale' => $this->pulisciNumero($grassoViscerale),
                        'acqua_corporea' => $this->pulisciPercentuale($acquaCorporea),
                        'massa_muscolare' => $massaMuscolare,
                        'massa_ossea' => $massaOssea,
                        'proteine' => $this->pulisciPercentuale($proteine),
                        'bmr' => $this->pulisciNumero($bmr),
                        'eta_metabolica' => $this->pulisciNumero($etaMetabolica),
                        'data_rilevazione' => date('Y-m-d'),
                    ];

                    if (PesataJson::create($pesata)) {
                        $totaleImportate++;
                        $this->line("  ✓ Importata pesata: $cognome $nome");
                    } else {
                        $totaleErrori++;
                        $this->error("  ✗ Errore importazione: $cognome $nome");
                    }
                }

                $this->line("  Righe elaborate: " . ($highestRow - 2));

            } catch (\Exception $e) {
                $this->error("❌ Errore: " . $e->getMessage());
                $totaleErrori++;
            }
        }

        $this->newLine(2);
        $this->line('================================');
        $this->info('📊 RIEPILOGO IMPORTAZIONE');
        $this->line('================================');
        $this->info("✅ Pesate importate: $totaleImportate");
        $this->line("❌ Errori: $totaleErrori");
        $this->line("👤 Clienti creati: " . count($clientiCreati));

        if (!empty($clientiCreati)) {
            $this->newLine();
            $this->info('📝 Clienti creati:');
            foreach ($clientiCreati as $cliente) {
                $this->line("  - $cliente");
            }
        }

        if (!empty($clientiNonTrovati)) {
            $this->newLine();
            $this->warn('⚠️  Clienti non trovati/creati:');
            foreach ($clientiNonTrovati as $cliente) {
                $this->line("  - $cliente");
            }
        }

        $this->newLine();
        $this->info('🎉 IMPORTAZIONE COMPLETATA!');
        $this->newLine();

        return Command::SUCCESS;
    }

    /**
     * Pulisce un valore numerico (rimuove unità, converte formato)
     */
    private function pulisciNumero($valore)
    {
        if ($valore === null || $valore === '') {
            return null;
        }

        // Se è già un numero, restituiscilo
        if (is_numeric($valore)) {
            return (float) $valore;
        }

        // Rimuovi unità (kg, kcal, etc.)
        $numero = preg_replace('/[^0-9.,]/', '', $valore);

        // Converti virgola in punto
        $numero = str_replace(',', '.', $numero);

        return $numero !== '' ? (float) $numero : null;
    }

    /**
     * Pulisce una percentuale (rimuove %, converte formato)
     */
    private function pulisciPercentuale($valore)
    {
        if ($valore === null || $valore === '') {
            return null;
        }

        // Se è già un numero, restituiscilo
        if (is_numeric($valore)) {
            return (float) $valore;
        }

        // Rimuovi % e altri caratteri
        $numero = preg_replace('/[^0-9.,]/', '', $valore);

        // Converti virgola in punto
        $numero = str_replace(',', '.', $numero);

        return $numero !== '' ? (float) $numero : null;
    }
}
