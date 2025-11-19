<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pesata;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;

/**
 * Controller: Gestione Pesate (Admin)
 * Gestisce CRUD pesate per i clienti
 */
class PesateController extends Controller
{
    /**
     * Lista pesate di un cliente con grafici
     */
    public function index($cliente_id)
    {
        $cliente = Cliente::findOrFail($cliente_id);

        $pesate = Pesata::where('cliente_id', $cliente_id)
                       ->orderBy('data_rilevazione', 'desc')
                       ->get();

        $statistiche = Pesata::getStatistiche($cliente_id);
        $grafici = Pesata::getDatiGrafici($cliente_id);

        return view('admin.pesate.index', compact('cliente', 'pesate', 'statistiche', 'grafici'));
    }

    /**
     * Form per aggiungere nuova pesata
     */
    public function create($cliente_id)
    {
        $cliente = Cliente::findOrFail($cliente_id);

        // Lista sedi disponibili
        $sedi = ['Calliano', 'Darè', 'Pieve di Bono', 'Riva', 'Trento'];

        return view('admin.pesate.create', compact('cliente', 'sedi'));
    }

    /**
     * Salva nuova pesata
     */
    public function store(Request $request, $cliente_id)
    {
        // LOG DETTAGLIATO PER DEBUG
        \Log::info('===== INIZIO SALVATAGGIO PESATA =====');
        \Log::info('Cliente ID: ' . $cliente_id);
        \Log::info('Dati ricevuti:', $request->all());
        \Log::info('URL: ' . $request->url());
        \Log::info('Metodo: ' . $request->method());

        try {
            $cliente = Cliente::findOrFail($cliente_id);
            \Log::info('Cliente trovato: ' . $cliente->nome . ' ' . $cliente->cognome);
        } catch (\Exception $e) {
            \Log::error('Cliente non trovato: ' . $e->getMessage());
            return redirect()->back()
                           ->with('error', 'Cliente non trovato');
        }

        $validator = Validator::make($request->all(), [
            'data_rilevazione' => 'required|date',
            'peso' => 'required|numeric',
            'bmi' => 'nullable|numeric',
            'peso_corporeo_senza_grassi' => 'nullable|numeric',
            'muscolo_scheletrico' => 'nullable|numeric',
            'grasso_corporeo' => 'nullable|numeric',
            'grasso_sottocutaneo' => 'nullable|numeric',
            'grasso_viscerale' => 'nullable|integer',
            'acqua_corporea' => 'nullable|numeric',
            'massa_muscolare' => 'nullable|numeric',
            'massa_ossea' => 'nullable|numeric',
            'proteine' => 'nullable|numeric',
            'bmr' => 'nullable|integer',
            'eta_metabolica' => 'nullable|integer',
            'sede' => 'nullable|string|max:100',
            'note' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            \Log::warning('Validazione fallita:', $validator->errors()->toArray());

            return redirect()->back()
                           ->withErrors($validator)
                           ->withInput()
                           ->with('error', 'Errori: ' . implode(', ', $validator->errors()->all()));
        }

        \Log::info('Validazione OK');

        try {
            $data = $validator->validated();
            $data['cliente_id'] = $cliente_id;

            \Log::info('Dati da salvare:', $data);

            $pesata = Pesata::create($data);

            \Log::info('Pesata salvata con ID: ' . $pesata->id);

            if (!$pesata) {
                throw new \Exception('Errore durante il salvataggio');
            }

            \Log::info('===== SALVATAGGIO OK =====');

            return redirect()->route('admin.clienti.pesate.index', $cliente_id)
                           ->with('success', 'Pesata aggiunta con successo!');

        } catch (\Exception $e) {
            \Log::error('===== ERRORE SALVATAGGIO =====');
            \Log::error('Messaggio: ' . $e->getMessage());
            \Log::error('File: ' . $e->getFile() . ':' . $e->getLine());

            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Errore: ' . $e->getMessage());
        }
    }

    /**
     * Form per modificare una pesata
     */
    public function edit($cliente_id, $pesata_id)
    {
        $cliente = Cliente::findOrFail($cliente_id);
        $pesata = Pesata::where('id', $pesata_id)
                       ->where('cliente_id', $cliente_id)
                       ->firstOrFail();

        $sedi = ['Calliano', 'Darè', 'Pieve di Bono', 'Riva', 'Trento'];

        return view('admin.pesate.edit', compact('cliente', 'pesata', 'sedi'));
    }

    /**
     * Aggiorna una pesata
     */
    public function update(Request $request, $cliente_id, $pesata_id)
    {
        $cliente = Cliente::findOrFail($cliente_id);
        $pesata = Pesata::where('id', $pesata_id)
                       ->where('cliente_id', $cliente_id)
                       ->firstOrFail();

        $validator = Validator::make($request->all(), [
            'data_rilevazione' => 'required|date',
            'peso' => 'required|numeric',
            'bmi' => 'nullable|numeric',
            'peso_corporeo_senza_grassi' => 'nullable|numeric',
            'muscolo_scheletrico' => 'nullable|numeric',
            'grasso_corporeo' => 'nullable|numeric',
            'grasso_sottocutaneo' => 'nullable|numeric',
            'grasso_viscerale' => 'nullable|integer',
            'acqua_corporea' => 'nullable|numeric',
            'massa_muscolare' => 'nullable|numeric',
            'massa_ossea' => 'nullable|numeric',
            'proteine' => 'nullable|numeric',
            'bmr' => 'nullable|integer',
            'eta_metabolica' => 'nullable|integer',
            'sede' => 'nullable|string|max:100',
            'note' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                           ->withErrors($validator)
                           ->withInput();
        }

        $pesata->update($request->all());

        return redirect()->route('admin.clienti.pesate.index', $cliente_id)
                       ->with('success', 'Pesata aggiornata con successo!');
    }

    /**
     * Elimina una pesata
     */
    public function destroy($cliente_id, $pesata_id)
    {
        $pesata = Pesata::where('id', $pesata_id)
                       ->where('cliente_id', $cliente_id)
                       ->firstOrFail();

        $pesata->delete();

        return redirect()->route('admin.clienti.pesate.index', $cliente_id)
                       ->with('success', 'Pesata eliminata con successo!');
    }

    /**
     * Lista tutti i clienti con pesate
     */
    public function listaClientiConPesate(Request $request)
    {
        $query = Cliente::query();

        // Ricerca
        if ($request->filled('cerca')) {
            $cerca = $request->cerca;
            $query->where(function($q) use ($cerca) {
                $q->where('nome', 'like', "%$cerca%")
                  ->orWhere('cognome', 'like', "%$cerca%")
                  ->orWhere('email', 'like', "%$cerca%");
            });
        }

        $clienti = $query->orderBy('cognome', 'asc')
                        ->orderBy('nome', 'asc')
                        ->get();

        // Aggiungi conteggio pesate
        $clienti->each(function($cliente) {
            $cliente->totale_pesate = Pesata::where('cliente_id', $cliente->id)->count();
            $ultimaPesata = Pesata::where('cliente_id', $cliente->id)
                                 ->orderBy('data_rilevazione', 'desc')
                                 ->first();
            $cliente->ultima_pesata = $ultimaPesata ? $ultimaPesata->data_rilevazione : null;
        });

        $statistiche = [
            'totale' => $clienti->count(),
            'con_pesate' => $clienti->where('totale_pesate', '>', 0)->count(),
            'senza_pesate' => $clienti->where('totale_pesate', 0)->count(),
        ];

        return view('admin.clienti-pesate.index', compact('clienti', 'statistiche'));
    }

    /**
     * Mostra form per importazione Excel
     */
    public function showImportForm()
    {
        return view('admin.pesate.import');
    }

    /**
     * Processa file Excel e mostra interfaccia mapping colonne
     */
    public function processImport(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
            'sede' => 'required|string|max:100',
        ]);

        try {
            $file = $request->file('file');
            $sede = $request->sede;

            // Salva file temporaneamente
            $filename = 'import_' . time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('temp_imports', $filename);

            $spreadsheet = IOFactory::load($file->getRealPath());
            $worksheet = $spreadsheet->getActiveSheet();
            $highestColumn = $worksheet->getHighestColumn();
            $highestRow = $worksheet->getHighestRow();

            // Leggi headers (assumiamo riga 1 o 2)
            $headers = [];
            for ($col = 'A'; $col <= $highestColumn; $col++) {
                $headers[$col] = trim($worksheet->getCell("{$col}2")->getValue() ?? '');
                if (empty($headers[$col])) {
                    $headers[$col] = trim($worksheet->getCell("{$col}1")->getValue() ?? '');
                }
            }

            // Leggi prime 5 righe di esempio
            $sampleRows = [];
            $startRow = 3; // Assume dati dalla riga 3
            for ($row = $startRow; $row <= min($startRow + 4, $highestRow); $row++) {
                $rowData = [];
                for ($col = 'A'; $col <= $highestColumn; $col++) {
                    $rowData[$col] = $worksheet->getCell("{$col}{$row}")->getValue();
                }
                $sampleRows[] = $rowData;
            }

            // Salva info in sessione
            session([
                'import_file_path' => $path,
                'import_sede' => $sede,
                'import_start_row' => $startRow,
                'import_highest_row' => $highestRow,
            ]);

            return view('admin.pesate.import-mapping', compact('headers', 'sampleRows', 'sede'));

        } catch (\Exception $e) {
            return back()->with('error', 'Errore nella lettura del file: ' . $e->getMessage());
        }
    }

    /**
     * Processa mapping colonne e mostra anteprima
     */
    public function processMappingAndPreview(Request $request)
    {
        $mapping = $request->mapping;
        $sede = session('import_sede');
        $filePath = session('import_file_path');
        $startRow = session('import_start_row', 3);
        $highestRow = session('import_highest_row');

        if (!$filePath || !file_exists(storage_path('app/' . $filePath))) {
            return back()->with('error', 'File di importazione non trovato. Riprova l\'upload.');
        }

        try {
            $spreadsheet = IOFactory::load(storage_path('app/' . $filePath));
            $worksheet = $spreadsheet->getActiveSheet();

            $preview_data = [];
            $clientiTrovati = []; // Clienti esistenti
            $clientiCreati = [];  // Clienti nuovi creati

            for ($row = $startRow; $row <= $highestRow; $row++) {
                // Estrai dati secondo mapping
                $cognome = trim($worksheet->getCell($mapping['cognome'] . $row)->getValue() ?? '');
                $nome = trim($worksheet->getCell($mapping['nome'] . $row)->getValue() ?? '');
                $codiceFiscale = isset($mapping['codice_fiscale']) && $mapping['codice_fiscale'] !== 'skip'
                    ? trim($worksheet->getCell($mapping['codice_fiscale'] . $row)->getValue() ?? '')
                    : null;

                // Salta righe vuote
                if (empty($cognome) && empty($nome) && empty($codiceFiscale)) {
                    continue;
                }

                // Cerca cliente con logica migliorata
                $cliente = $this->findOrCreateCliente($nome, $cognome, $codiceFiscale);

                $rowErrors = [];
                $clienteCreato = false;

                if (!$cliente) {
                    $rowErrors[] = 'Impossibile trovare o creare il cliente';
                } else {
                    // Traccia se è stato appena creato o trovato
                    if ($cliente->wasRecentlyCreated) {
                        $clienteCreato = true;
                        $clientiCreati[$cliente->id] = [
                            'id' => $cliente->id,
                            'nome' => $cliente->nome,
                            'cognome' => $cliente->cognome,
                            'codice_fiscale' => $cliente->codice_fiscale,
                        ];
                    } else {
                        // Cliente esistente trovato
                        if (!isset($clientiTrovati[$cliente->id])) {
                            $clientiTrovati[$cliente->id] = [
                                'id' => $cliente->id,
                                'nome' => $cliente->nome,
                                'cognome' => $cliente->cognome,
                                'codice_fiscale' => $cliente->codice_fiscale,
                                'pesate_count' => 0,
                            ];
                        }
                        $clientiTrovati[$cliente->id]['pesate_count']++;
                    }
                }

                // Estrai peso
                $pesoRaw = $worksheet->getCell($mapping['peso'] . $row)->getValue();
                $peso = $this->pulisciNumero($pesoRaw);

                if (!$peso || $peso < 20 || $peso > 300) {
                    $rowErrors[] = "Peso non valido: '{$pesoRaw}' (deve essere tra 20 e 300 kg)";
                }

                // Data rilevazione: SEMPRE OGGI
                $dataRilevazione = now()->format('Y-m-d');

                $preview_data[] = [
                    'row' => $row,
                    'cliente_id' => $cliente->id ?? null,
                    'cognome' => $cognome,
                    'nome' => $nome,
                    'codice_fiscale' => $codiceFiscale,
                    'cliente_creato' => $clienteCreato,
                    'sede' => $sede,
                    'peso' => $peso,
                    'bmi' => isset($mapping['bmi']) && $mapping['bmi'] !== 'skip'
                        ? $this->validaBMI($this->pulisciNumero($worksheet->getCell($mapping['bmi'] . $row)->getValue()))
                        : null,
                    'peso_corporeo_senza_grassi' => isset($mapping['peso_corporeo_senza_grassi']) && $mapping['peso_corporeo_senza_grassi'] !== 'skip'
                        ? $this->pulisciNumero($worksheet->getCell($mapping['peso_corporeo_senza_grassi'] . $row)->getValue())
                        : null,
                    'muscolo_scheletrico' => isset($mapping['muscolo_scheletrico']) && $mapping['muscolo_scheletrico'] !== 'skip'
                        ? $this->pulisciPercentuale($worksheet->getCell($mapping['muscolo_scheletrico'] . $row)->getValue())
                        : null,
                    'grasso_corporeo' => isset($mapping['grasso_corporeo']) && $mapping['grasso_corporeo'] !== 'skip'
                        ? $this->pulisciPercentuale($worksheet->getCell($mapping['grasso_corporeo'] . $row)->getValue())
                        : null,
                    'grasso_sottocutaneo' => isset($mapping['grasso_sottocutaneo']) && $mapping['grasso_sottocutaneo'] !== 'skip'
                        ? $this->pulisciPercentuale($worksheet->getCell($mapping['grasso_sottocutaneo'] . $row)->getValue())
                        : null,
                    'grasso_viscerale' => isset($mapping['grasso_viscerale']) && $mapping['grasso_viscerale'] !== 'skip'
                        ? $this->validaGrassoViscerale((int) $worksheet->getCell($mapping['grasso_viscerale'] . $row)->getValue())
                        : null,
                    'acqua_corporea' => isset($mapping['acqua_corporea']) && $mapping['acqua_corporea'] !== 'skip'
                        ? $this->pulisciPercentuale($worksheet->getCell($mapping['acqua_corporea'] . $row)->getValue())
                        : null,
                    'massa_muscolare' => isset($mapping['massa_muscolare']) && $mapping['massa_muscolare'] !== 'skip'
                        ? $this->pulisciNumero($worksheet->getCell($mapping['massa_muscolare'] . $row)->getValue())
                        : null,
                    'massa_ossea' => isset($mapping['massa_ossea']) && $mapping['massa_ossea'] !== 'skip'
                        ? $this->pulisciNumero($worksheet->getCell($mapping['massa_ossea'] . $row)->getValue())
                        : null,
                    'proteine' => isset($mapping['proteine']) && $mapping['proteine'] !== 'skip'
                        ? $this->pulisciPercentuale($worksheet->getCell($mapping['proteine'] . $row)->getValue())
                        : null,
                    'bmr' => isset($mapping['bmr']) && $mapping['bmr'] !== 'skip'
                        ? $this->validaBMR((int) $worksheet->getCell($mapping['bmr'] . $row)->getValue())
                        : null,
                    'eta_metabolica' => isset($mapping['eta_metabolica']) && $mapping['eta_metabolica'] !== 'skip'
                        ? $this->validaEtaMetabolica((int) $worksheet->getCell($mapping['eta_metabolica'] . $row)->getValue())
                        : null,
                    'data_rilevazione' => $dataRilevazione,
                    'errors' => $rowErrors,
                ];
            }

            // Converti array associativi in array indicizzati per la view
            $clientiTrovati = array_values($clientiTrovati);
            $clientiCreati = array_values($clientiCreati);

            return view('admin.pesate.import-preview', compact('preview_data', 'sede', 'clientiTrovati', 'clientiCreati'));

        } catch (\Exception $e) {
            \Log::error('Errore import pesate: ' . $e->getMessage());
            return back()->with('error', 'Errore nella lettura del file: ' . $e->getMessage());
        }
    }

    /**
     * Trova cliente esistente o crea nuovo cliente
     */
    private function findOrCreateCliente($nome, $cognome, $codiceFiscale = null)
    {
        // Prima cerca per nome e cognome
        $cliente = Cliente::where(function($q) use ($nome, $cognome) {
                $q->whereRaw('LOWER(TRIM(nome)) = ?', [strtolower(trim($nome))])
                  ->whereRaw('LOWER(TRIM(cognome)) = ?', [strtolower(trim($cognome))]);
            })
            ->first();

        // Se non trovato e c'è codice fiscale, cerca per quello
        if (!$cliente && $codiceFiscale) {
            $cliente = Cliente::whereRaw('LOWER(TRIM(codice_fiscale)) = ?', [strtolower(trim($codiceFiscale))])
                ->first();
        }

        // Se ancora non trovato, crea nuovo cliente
        if (!$cliente) {
            try {
                $cliente = Cliente::create([
                    'nome' => ucwords(strtolower(trim($nome))),
                    'cognome' => ucwords(strtolower(trim($cognome))),
                    'codice_fiscale' => $codiceFiscale ? strtoupper(trim($codiceFiscale)) : null,
                    'stato_cliente' => 'attivo',
                    'data_iscrizione' => now(),
                    'utente_id' => auth()->id(),
                ]);

                \Log::info("Nuovo cliente creato durante import: {$cognome} {$nome} (ID: {$cliente->id})");
            } catch (\Exception $e) {
                \Log::error("Errore creazione cliente {$cognome} {$nome}: " . $e->getMessage());
                return null;
            }
        }

        return $cliente;
    }

    /**
     * Helper: parse data da vari formati
     */
    private function parseData($valore)
    {
        if (empty($valore)) {
            return null;
        }

        // Se è già DateTime object
        if ($valore instanceof \DateTime) {
            return $valore->format('Y-m-d');
        }

        // Se è numero seriale Excel
        if (is_numeric($valore) && $valore > 25569) { // Excel date serial inizia da 1900
            try {
                return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($valore)->format('Y-m-d');
            } catch (\Exception $e) {
                \Log::warning("Impossibile convertire serial Excel: {$valore}");
                return null;
            }
        }

        // Prova parsing diretto
        try {
            $data = \Carbon\Carbon::parse($valore);
            return $data->format('Y-m-d');
        } catch (\Exception $e) {
            \Log::warning("Impossibile parsare data: {$valore}");
            return null;
        }
    }

    /**
     * Conferma e importa i dati
     */
    public function confirmImport(Request $request)
    {
        $data = json_decode($request->preview_data, true);
        $sede = $request->sede;

        $imported = 0;
        $skipped = 0;
        $import_errors = [];

        DB::beginTransaction();

        try {
            foreach ($data as $row) {
                // Salta righe con errori
                if (!empty($row['errors']) || !$row['cliente_id']) {
                    $skipped++;
                    $import_errors[] = "Riga {$row['row']}: " . implode(', ', $row['errors']);
                    continue;
                }

                // Crea pesata
                Pesata::create([
                    'cliente_id' => $row['cliente_id'],
                    'sede' => $sede,
                    'peso' => $row['peso'],
                    'bmi' => $row['bmi'],
                    'peso_corporeo_senza_grassi' => $row['peso_corporeo_senza_grassi'],
                    'muscolo_scheletrico' => $row['muscolo_scheletrico'],
                    'grasso_corporeo' => $row['grasso_corporeo'],
                    'grasso_sottocutaneo' => $row['grasso_sottocutaneo'],
                    'grasso_viscerale' => $row['grasso_viscerale'],
                    'acqua_corporea' => $row['acqua_corporea'],
                    'massa_muscolare' => $row['massa_muscolare'],
                    'massa_ossea' => $row['massa_ossea'],
                    'proteine' => $row['proteine'],
                    'bmr' => $row['bmr'],
                    'eta_metabolica' => $row['eta_metabolica'],
                    'data_rilevazione' => $row['data_rilevazione'],
                ]);

                $imported++;
            }

            DB::commit();

            $import_results = [
                'imported' => $imported,
                'skipped' => $skipped,
                'errors' => $import_errors,
            ];

            return view('admin.pesate.import-results', compact('import_results'));

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Errore durante l\'importazione: ' . $e->getMessage());
        }
    }

    /**
     * Helper: pulisce numero da percentuali e formattazione
     */
    private function pulisciNumero($valore)
    {
        if (is_null($valore) || $valore === '') {
            return null;
        }

        // Converti a stringa e pulisci
        $valore = trim(strval($valore));

        // Rimuovi unità di misura comuni (kg, g, cm, m, kcal, etc.)
        $valore = preg_replace('/\s*(kg|g|cm|m|kcal|cal|%)\s*$/i', '', $valore);

        // Rimuovi simboli e converti virgola in punto
        $numero = str_replace([',', ' '], ['.', ''], $valore);

        return is_numeric($numero) ? (float) $numero : null;
    }

    /**
     * Helper: pulisce percentuale
     */
    private function pulisciPercentuale($valore)
    {
        if (is_null($valore) || $valore === '') {
            return null;
        }

        // Converti a stringa e pulisci
        $valore = trim(strval($valore));

        // Rimuovi simbolo percentuale
        $valore = str_replace('%', '', $valore);

        // Sostituisci virgola con punto
        $numero = str_replace([',', ' '], ['.', ''], $valore);

        return is_numeric($numero) ? (float) $numero : null;
    }

    /**
     * Helper: valida valore BMI
     * Se il valore è fuori range (< 10 o > 100), probabilmente è un errore di mapping
     */
    private function validaBMI($valore)
    {
        if (is_null($valore)) {
            return null;
        }

        // BMI valido è generalmente tra 10 e 100
        // Se è fuori range, probabilmente l'utente ha mappato la colonna sbagliata
        if ($valore < 10 || $valore > 100) {
            \Log::warning("BMI fuori range rilevato: {$valore} - valore scartato");
            return null;
        }

        return $valore;
    }

    /**
     * Helper: valida grasso viscerale
     * Valori normali sono tra 1 e 59
     */
    private function validaGrassoViscerale($valore)
    {
        if (is_null($valore)) {
            return null;
        }

        if ($valore < 0 || $valore > 100) {
            \Log::warning("Grasso viscerale fuori range rilevato: {$valore} - valore scartato");
            return null;
        }

        return $valore;
    }

    /**
     * Helper: valida BMR (metabolismo basale)
     * Valori normali sono tra 500 e 5000 kcal
     */
    private function validaBMR($valore)
    {
        if (is_null($valore)) {
            return null;
        }

        if ($valore < 500 || $valore > 5000) {
            \Log::warning("BMR fuori range rilevato: {$valore} - valore scartato");
            return null;
        }

        return $valore;
    }

    /**
     * Helper: valida età metabolica
     * Valori normali sono tra 10 e 120 anni
     */
    private function validaEtaMetabolica($valore)
    {
        if (is_null($valore)) {
            return null;
        }

        if ($valore < 10 || $valore > 120) {
            \Log::warning("Età metabolica fuori range rilevata: {$valore} - valore scartato");
            return null;
        }

        return $valore;
    }
}
