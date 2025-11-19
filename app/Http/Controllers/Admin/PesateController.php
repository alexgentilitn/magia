<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pesata;
use App\Models\Cliente;
use App\Models\Sede;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;

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
        // Pulisci errori dettagliati dalle sessioni precedenti
        session()->forget('import_errors_detail');

        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv,txt|max:10240',
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
                    $cell = $worksheet->getCell("{$col}{$row}");
                    // Usa leggiValoreCella() per gestire automaticamente formati Data
                    $rowData[$col] = $this->leggiValoreCella($cell);
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

                // Cerca possibili clienti - RILEVA OMONIMI
                $risultatoRicerca = $this->findPossibiliClienti($nome, $cognome, $codiceFiscale, $sede);

                $rowErrors = [];
                $clienteCreato = false;
                $cliente = null;
                $hasOmonimia = false;
                $possibiliClienti = [];

                if ($risultatoRicerca['match_type'] === 'nessuno') {
                    // Nuovo cliente - nessuna scelta da fare
                    $clienteCreato = true;
                    $clientiCreati["new_{$row}"] = [
                        'nome' => $nome,
                        'cognome' => $cognome,
                        'codice_fiscale' => $codiceFiscale,
                    ];
                } elseif ($risultatoRicerca['match_type'] === 'unico' || $risultatoRicerca['match_type'] === 'codice_fiscale') {
                    // Un solo match - MOSTRA COMUNQUE LA SCELTA all'utente
                    $cliente = $risultatoRicerca['clienti'][0];

                    // Prepara dati per scelta utente (cliente esistente o crea nuovo)
                    $hasOmonimia = true; // Mostra radio buttons
                    $possibiliClienti = collect($risultatoRicerca['clienti'])->map(function($c) {
                        return [
                            'id' => $c->id,
                            'nome' => $c->nome,
                            'cognome' => $c->cognome,
                            'codice_fiscale' => $c->codice_fiscale,
                            'data_iscrizione' => $c->data_iscrizione,
                            'email' => $c->email,
                            'telefono' => $c->telefono,
                        ];
                    })->toArray();

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
                } elseif ($risultatoRicerca['match_type'] === 'multiplo') {
                    // OMONIMIA MULTIPLA! Prepara dati per scelta utente
                    $hasOmonimia = true;
                    $possibiliClienti = collect($risultatoRicerca['clienti'])->map(function($c) {
                        return [
                            'id' => $c->id,
                            'nome' => $c->nome,
                            'cognome' => $c->cognome,
                            'codice_fiscale' => $c->codice_fiscale,
                            'data_iscrizione' => $c->data_iscrizione,
                            'email' => $c->email,
                            'telefono' => $c->telefono,
                        ];
                    })->toArray();

                    // Usa il primo come default ma segnala omonimia
                    $cliente = $risultatoRicerca['clienti'][0];
                }

                // Estrai peso - usa leggiValoreCella per gestire formati Data
                $pesoRaw = $this->leggiValoreCella($worksheet->getCell($mapping['peso'] . $row));
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
                    'has_omonimia' => $hasOmonimia,
                    'possibili_clienti' => $possibiliClienti,
                    'sede' => $sede,
                    'peso' => $peso,
                    // Usa sistema universale per tutti i campi numerici
                    'bmi' => isset($mapping['bmi']) && $mapping['bmi'] !== 'skip'
                        ? $this->validaBMI($this->leggiValoreCellaUniversale($worksheet->getCell($mapping['bmi'] . $row)))
                        : null,
                    'peso_corporeo_senza_grassi' => isset($mapping['peso_corporeo_senza_grassi']) && $mapping['peso_corporeo_senza_grassi'] !== 'skip'
                        ? $this->leggiValoreCellaUniversale($worksheet->getCell($mapping['peso_corporeo_senza_grassi'] . $row))
                        : null,
                    'muscolo_scheletrico' => isset($mapping['muscolo_scheletrico']) && $mapping['muscolo_scheletrico'] !== 'skip'
                        ? $this->normalizzaPercentuale($this->leggiValoreCellaUniversale($worksheet->getCell($mapping['muscolo_scheletrico'] . $row)))
                        : null,
                    'grasso_corporeo' => isset($mapping['grasso_corporeo']) && $mapping['grasso_corporeo'] !== 'skip'
                        ? $this->normalizzaPercentuale($this->leggiValoreCellaUniversale($worksheet->getCell($mapping['grasso_corporeo'] . $row)))
                        : null,
                    'grasso_sottocutaneo' => isset($mapping['grasso_sottocutaneo']) && $mapping['grasso_sottocutaneo'] !== 'skip'
                        ? $this->normalizzaPercentuale($this->leggiValoreCellaUniversale($worksheet->getCell($mapping['grasso_sottocutaneo'] . $row)))
                        : null,
                    'grasso_viscerale' => isset($mapping['grasso_viscerale']) && $mapping['grasso_viscerale'] !== 'skip'
                        ? $this->validaGrassoViscerale((int) $this->leggiValoreCellaUniversale($worksheet->getCell($mapping['grasso_viscerale'] . $row)))
                        : null,
                    'acqua_corporea' => isset($mapping['acqua_corporea']) && $mapping['acqua_corporea'] !== 'skip'
                        ? $this->normalizzaPercentuale($this->leggiValoreCellaUniversale($worksheet->getCell($mapping['acqua_corporea'] . $row)))
                        : null,
                    'massa_muscolare' => isset($mapping['massa_muscolare']) && $mapping['massa_muscolare'] !== 'skip'
                        ? $this->leggiValoreCellaUniversale($worksheet->getCell($mapping['massa_muscolare'] . $row))
                        : null,
                    'massa_ossea' => isset($mapping['massa_ossea']) && $mapping['massa_ossea'] !== 'skip'
                        ? $this->leggiValoreCellaUniversale($worksheet->getCell($mapping['massa_ossea'] . $row))
                        : null,
                    'proteine' => isset($mapping['proteine']) && $mapping['proteine'] !== 'skip'
                        ? $this->normalizzaPercentuale($this->leggiValoreCellaUniversale($worksheet->getCell($mapping['proteine'] . $row)))
                        : null,
                    'bmr' => isset($mapping['bmr']) && $mapping['bmr'] !== 'skip'
                        ? $this->validaBMR((int) $this->leggiValoreCellaUniversale($worksheet->getCell($mapping['bmr'] . $row)))
                        : null,
                    'eta_metabolica' => isset($mapping['eta_metabolica']) && $mapping['eta_metabolica'] !== 'skip'
                        ? $this->validaEtaMetabolica((int) $this->leggiValoreCellaUniversale($worksheet->getCell($mapping['eta_metabolica'] . $row)))
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
     * Cerca TUTTI i clienti che matchano nome/cognome nella sede
     * Ritorna array di possibili match per gestione omonimi
     */
    private function findPossibiliClienti($nome, $cognome, $codiceFiscale = null, $nomeSede = null)
    {
        // Trova ID della sede dal nome
        $sedeId = null;
        if ($nomeSede) {
            $sede = Sede::where('nome', $nomeSede)->first();
            $sedeId = $sede ? $sede->id : null;
        }

        $possibiliMatch = [];

        // Cerca per codice fiscale (match più preciso)
        if ($codiceFiscale) {
            $query = Cliente::whereRaw('LOWER(TRIM(codice_fiscale)) = ?', [strtolower(trim($codiceFiscale))]);

            if ($sedeId) {
                $query->where('sede_preferita_id', $sedeId);
            }

            $clienteCF = $query->first();

            if ($clienteCF) {
                return [
                    'match_type' => 'codice_fiscale',
                    'clienti' => [$clienteCF],
                    'is_duplicato' => false
                ];
            }
        }

        // Cerca per nome e cognome NELLA SEDE
        $query = Cliente::where(function($q) use ($nome, $cognome) {
            $q->whereRaw('LOWER(TRIM(nome)) = ?', [strtolower(trim($nome))])
              ->whereRaw('LOWER(TRIM(cognome)) = ?', [strtolower(trim($cognome))]);
        });

        if ($sedeId) {
            $query->where('sede_preferita_id', $sedeId);
        }

        $clienti = $query->get();

        if ($clienti->count() === 0) {
            // Nessun match - nuovo cliente
            return [
                'match_type' => 'nessuno',
                'clienti' => [],
                'is_duplicato' => false
            ];
        } elseif ($clienti->count() === 1) {
            // Un solo match - sicuro
            return [
                'match_type' => 'unico',
                'clienti' => $clienti->all(),
                'is_duplicato' => false
            ];
        } else {
            // Multipli match - OMONIMIA!
            return [
                'match_type' => 'multiplo',
                'clienti' => $clienti->all(),
                'is_duplicato' => true
            ];
        }
    }

    /**
     * Crea nuovo cliente (usato in confirmImport dopo scelta utente)
     */
    private function creaCliente($nome, $cognome, $codiceFiscale = null, $nomeSede = null)
    {
        // Trova ID della sede dal nome
        $sedeId = null;
        if ($nomeSede) {
            $sede = Sede::where('nome', $nomeSede)->first();
            $sedeId = $sede ? $sede->id : null;
        }

        try {
            $datiCliente = [
                'nome' => ucwords(strtolower(trim($nome))),
                'cognome' => ucwords(strtolower(trim($cognome))),
                'codice_fiscale' => $codiceFiscale ? strtoupper(trim($codiceFiscale)) : null,
                'stato_cliente' => 'attivo',
                'tipo_cliente' => 'effettiva',
                'data_iscrizione' => now(),
                'utente_id' => auth()->id(),
                'sede_preferita_id' => $sedeId,
            ];

            \Log::info("Creazione nuovo cliente", $datiCliente);

            $cliente = Cliente::create($datiCliente);

            \Log::info("Nuovo cliente creato: ID {$cliente->id}");

            return $cliente;
        } catch (\Exception $e) {
            \Log::error("Errore creazione cliente: " . $e->getMessage());
            return null;
        }
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
        $sede = $request->sede;
        $totalRows = $request->total_rows;

        \Log::info("=== INIZIO IMPORT PESATE (NUOVA VERSIONE CON RISOLUZIONE OMONIMI) ===");
        \Log::info("Sede: {$sede}");
        \Log::info("Numero righe da importare: {$totalRows}");

        $imported = 0;
        $skipped = 0;
        $import_errors = [];

        // Ogni pesata è indipendente per evitare rollback di tutto

        for ($index = 0; $index < $totalRows; $index++) {
            $rowNumber = $request->input("row_number_{$index}");

            \Log::info("Processando riga #{$rowNumber} (index {$index})");

            // Salta righe con errori
            $hasErrors = $request->input("has_errors_{$index}") === '1';
            if ($hasErrors) {
                $skipped++;
                $errorMsg = "Riga {$rowNumber}: Contiene errori di validazione";
                $import_errors[] = $errorMsg;
                \Log::warning("Riga saltata: {$errorMsg}");
                continue;
            }

            // Leggi dati cliente
            $nome = $request->input("nome_{$index}");
            $cognome = $request->input("cognome_{$index}");
            $codiceFiscale = $request->input("codice_fiscale_{$index}");
            $hasOmonimia = $request->input("has_omonimia_{$index}") === '1';

            // Determina cliente_id
            $clienteIdInput = $request->input("cliente_id_{$index}");

            try {
                $clienteId = null;

                // Se utente ha scelto "new" (crea nuovo cliente)
                if ($clienteIdInput === 'new') {
                    \Log::info("Riga {$rowNumber}: Creazione nuovo cliente '{$cognome} {$nome}' in sede {$sede}");

                    $nuovoCliente = $this->creaCliente($nome, $cognome, $codiceFiscale, $sede);
                    $clienteId = $nuovoCliente->id;

                    \Log::info("Riga {$rowNumber}: Nuovo cliente creato con ID {$clienteId}");
                }
                // Altrimenti usa l'ID esistente selezionato
                elseif ($clienteIdInput) {
                    $clienteId = (int) $clienteIdInput;

                    // Verifica che il cliente esista
                    $clienteEsiste = Cliente::where('id', $clienteId)->exists();
                    if (!$clienteEsiste) {
                        $skipped++;
                        $errorMsg = "Riga {$rowNumber}: Cliente ID {$clienteId} non esiste nel database";
                        $import_errors[] = $errorMsg;
                        \Log::error($errorMsg);
                        continue;
                    }

                    \Log::info("Riga {$rowNumber}: Usando cliente esistente ID {$clienteId}");
                }
                else {
                    // Nessun cliente selezionato
                    $skipped++;
                    $errorMsg = "Riga {$rowNumber}: Nessun cliente selezionato";
                    $import_errors[] = $errorMsg;
                    \Log::warning($errorMsg);
                    continue;
                }

                // Leggi tutti i 13 campi misurazione (modificabili dall'utente)
                $peso = $request->input("peso_{$index}");
                $bmi = $request->input("bmi_{$index}");
                $grassoCorporeo = $request->input("grasso_corporeo_{$index}");
                $grassoSottocutaneo = $request->input("grasso_sottocutaneo_{$index}");
                $grassoViscerale = $request->input("grasso_viscerale_{$index}");
                $muscoloScheletrico = $request->input("muscolo_scheletrico_{$index}");
                $massaMuscolare = $request->input("massa_muscolare_{$index}");
                $massaOssea = $request->input("massa_ossea_{$index}");
                $bmr = $request->input("bmr_{$index}");
                $acquaCorporea = $request->input("acqua_corporea_{$index}");
                $pesoCorporeoSenzaGrassi = $request->input("peso_corporeo_senza_grassi_{$index}");
                $proteine = $request->input("proteine_{$index}");
                $etaMetabolica = $request->input("eta_metabolica_{$index}");
                $dataRilevazione = $request->input("data_rilevazione_{$index}");

                // Prepara dati pesata
                $pesataData = [
                    'cliente_id' => $clienteId,
                    'sede' => $sede,
                    'peso' => $peso ?: null,
                    'bmi' => $bmi ?: null,
                    'peso_corporeo_senza_grassi' => $pesoCorporeoSenzaGrassi ?: null,
                    'muscolo_scheletrico' => $muscoloScheletrico ?: null,
                    'grasso_corporeo' => $grassoCorporeo ?: null,
                    'grasso_sottocutaneo' => $grassoSottocutaneo ?: null,
                    'grasso_viscerale' => $grassoViscerale ?: null,
                    'acqua_corporea' => $acquaCorporea ?: null,
                    'massa_muscolare' => $massaMuscolare ?: null,
                    'massa_ossea' => $massaOssea ?: null,
                    'proteine' => $proteine ?: null,
                    'bmr' => $bmr ?: null,
                    'eta_metabolica' => $etaMetabolica ?: null,
                    'data_rilevazione' => $dataRilevazione ?: now()->format('Y-m-d'),
                ];

                \Log::info("Riga {$rowNumber}: Tentativo creazione pesata con dati:", $pesataData);

                // Crea pesata
                $pesata = Pesata::create($pesataData);

                \Log::info("Riga {$rowNumber}: Pesata creata con ID: {$pesata->id}");

                $imported++;

            } catch (\Exception $e) {
                // Gestisci errore a livello di singola pesata (non rollback globale)
                $skipped++;
                $errorMsg = "Riga {$rowNumber}: Errore inserimento - " . $e->getMessage();
                $import_errors[] = $errorMsg;
                \Log::error("Errore inserimento pesata riga {$rowNumber}: " . $e->getMessage());
                \Log::error("Stack trace: " . $e->getTraceAsString());
                // Continua con la prossima riga
                continue;
            }
        }

        \Log::info("=== IMPORT COMPLETATO ===");
        \Log::info("Importate: {$imported}, Saltate: {$skipped}");

        $import_results = [
            'imported' => $imported,
            'skipped' => $skipped,
            'errors' => $import_errors,
        ];

        return view('admin.pesate.import-results', compact('import_results'));
    }

    /**
     * Helper: legge valore da cella Excel in modo intelligente
     * Se la cella è formattata come Data ma contiene un numero,
     * legge il valore con massima precisione senza arrotondamenti
     */
    private function leggiValoreCella($cell)
    {
        if (!$cell) {
            return null;
        }

        $rawValue = $cell->getValue();

        // Se il valore è nullo o vuoto, ritorna subito
        if (is_null($rawValue) || $rawValue === '') {
            return null;
        }

        // Controlla se la cella ha formato Data
        $isDateFormat = Date::isDateTimeFormat($cell->getStyle()->getNumberFormat());

        // Se è formato Data ma il valore è un numero alto (>1000),
        // probabilmente è un numero seriale di data Excel applicato per errore
        if ($isDateFormat && is_numeric($rawValue) && $rawValue > 1000) {
            // SOLUZIONE: Excel ha interpretato il valore come Data (es. 19.09 → 19:09)
            // Converte il numero seriale in DateTime e estrae ore:minuti per ricostruire il valore originale

            try {
                // Converti numero seriale Excel in DateTime
                $dateTime = Date::excelToDateTimeObject($rawValue);

                // Estrai ore e minuti
                $ore = (int) $dateTime->format('H');
                $minuti = (int) $dateTime->format('i');

                // Ricostruisci valore originale: 19.09 (ore.minuti come decimale)
                $valoreRicostruito = $ore + ($minuti / 100);

                \Log::info("Cella con formato Data - valore ricostruito da ore:minuti", [
                    'coordinate' => $cell->getCoordinate(),
                    'getValue' => $rawValue,
                    'dateTime' => $dateTime->format('Y-m-d H:i:s'),
                    'ore' => $ore,
                    'minuti' => $minuti,
                    'valoreRicostruito' => $valoreRicostruito,
                    'formatCode' => $cell->getStyle()->getNumberFormat()->getFormatCode()
                ]);

                // Verifica che il valore ricostruito sia plausibile (range 5-100 per valori corporei)
                if ($valoreRicostruito >= 5 && $valoreRicostruito <= 100) {
                    return $valoreRicostruito;
                }

                \Log::warning("Valore ricostruito fuori range, uso fallback", [
                    'valoreRicostruito' => $valoreRicostruito
                ]);

            } catch (\Exception $e) {
                \Log::error("Errore conversione data Excel", [
                    'coordinate' => $cell->getCoordinate(),
                    'rawValue' => $rawValue,
                    'error' => $e->getMessage()
                ]);
            }

            // Fallback: prova con getFormattedValue()
            $formattedValue = $cell->getFormattedValue();
            $cleanValue = preg_replace('/[^\d,\.\-]/', '', $formattedValue);
            $cleanValue = str_replace(',', '.', $cleanValue);

            if (is_numeric($cleanValue) && abs($cleanValue) < 1000) {
                \Log::info("Cella con formato Data - uso getFormattedValue() pulito (fallback)", [
                    'coordinate' => $cell->getCoordinate(),
                    'formattedValue' => $formattedValue,
                    'cleanValue' => $cleanValue,
                ]);
                return $cleanValue;
            }

            // Ultimo fallback
            return $formattedValue;
        }

        // Altrimenti usa getValue() per massima precisione
        return $rawValue;
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
     * Rileva anche numeri seriali Excel (date) e li scarta
     */
    private function validaBMI($valore)
    {
        if (is_null($valore)) {
            return null;
        }

        // Rileva numeri seriali Excel (date) - valori > 1000 sono sicuramente date
        // (BMI non supera mai 100 in condizioni normali)
        if (is_numeric($valore) && $valore > 1000) {
            \Log::warning("BMI: rilevato numero seriale Excel (cella formattata come data): {$valore}");
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

    /**
     * =====================================================
     * SISTEMA IMPORT UNIVERSALE - Multi-formato intelligente
     * =====================================================
     * Gestisce file da diverse bilance con formati diversi
     */

    /**
     * Helper: Rileva automaticamente il separatore per file CSV/TXT
     * Testa vari separatori comuni e sceglie il più probabile
     */
    private function rilevaSeparatoreCSV($filePath, $rigaDaTestare = 5)
    {
        $handle = fopen($filePath, 'r');
        if (!$handle) {
            return ','; // Default
        }

        $separatoriPossibili = [
            ',' => 0,
            ';' => 0,
            "\t" => 0,
            '|' => 0,
        ];

        $rigaCount = 0;
        while (($riga = fgets($handle)) && $rigaCount < $rigaDaTestare) {
            foreach ($separatoriPossibili as $sep => $count) {
                $separatoriPossibili[$sep] += substr_count($riga, $sep);
            }
            $rigaCount++;
        }
        fclose($handle);

        // Ritorna il separatore più frequente
        arsort($separatoriPossibili);
        $separatore = key($separatoriPossibili);

        \Log::info("Separatore CSV rilevato", [
            'separatore' => $separatore === "\t" ? 'TAB' : $separatore,
            'conteggi' => $separatoriPossibili
        ]);

        return $separatore;
    }

    /**
     * Helper: Rileva encoding del file
     * Supporta UTF-8, ISO-8859-1, Windows-1252
     */
    private function rilevaEncoding($filePath)
    {
        $handle = fopen($filePath, 'r');
        if (!$handle) {
            return 'UTF-8';
        }

        $sample = fread($handle, 4096);
        fclose($handle);

        $encoding = mb_detect_encoding($sample, ['UTF-8', 'ISO-8859-1', 'Windows-1252', 'ASCII'], true);

        return $encoding ?: 'UTF-8';
    }

    /**
     * Helper: Pulisce valore numerico da QUALSIASI formato
     * Gestisce: percentuali (27.5%), valute (€15.50), spazi, virgole, etc.
     */
    private function pulisciValoreUniversale($valore)
    {
        if (is_null($valore) || $valore === '') {
            return null;
        }

        // Se è già un numero, ritornalo
        if (is_numeric($valore)) {
            return (float) $valore;
        }

        // Converti a stringa e trim
        $valore = trim(strval($valore));

        // Rimuovi simboli comuni: €, $, £, %, kg, g, cm, m, kcal, etc.
        $valore = preg_replace('/[€$£¥₹%kgcmKcal]/iu', '', $valore);

        // Rimuovi spazi
        $valore = str_replace(' ', '', $valore);

        // Gestisci formato europeo (1.234,56) vs americano (1,234.56)
        // Se ci sono sia virgole che punti, identifica quale è decimale
        $hasComma = strpos($valore, ',') !== false;
        $hasDot = strpos($valore, '.') !== false;

        if ($hasComma && $hasDot) {
            // Identifica quale viene per ultimo (quello è il decimale)
            $lastComma = strrpos($valore, ',');
            $lastDot = strrpos($valore, '.');

            if ($lastComma > $lastDot) {
                // Formato europeo: 1.234,56
                $valore = str_replace('.', '', $valore); // Rimuovi separatore migliaia
                $valore = str_replace(',', '.', $valore); // Converti decimale
            } else {
                // Formato americano: 1,234.56
                $valore = str_replace(',', '', $valore); // Rimuovi separatore migliaia
            }
        } elseif ($hasComma) {
            // Solo virgola - probabilmente decimale europeo
            $valore = str_replace(',', '.', $valore);
        }
        // Se solo punto, è già ok

        // Rimuovi eventuali caratteri rimanenti tranne numeri, punto, segno meno
        $valore = preg_replace('/[^0-9.\-]/', '', $valore);

        return is_numeric($valore) ? (float) $valore : null;
    }

    /**
     * Helper: Legge valore da cella con rilevamento formato universale
     * Estende leggiValoreCella per gestire anche formati percentuale, valuta, etc.
     */
    private function leggiValoreCellaUniversale($cell)
    {
        if (!$cell) {
            return null;
        }

        // Usa la funzione esistente leggiValoreCella per gestione formato Data
        $valore = $this->leggiValoreCella($cell);

        if (is_null($valore) || $valore === '') {
            return null;
        }

        // Se è già un numero pulito, ritornalo
        if (is_numeric($valore) && $valore < 1000) {
            return $valore;
        }

        // Altrimenti applica pulizia universale
        return $this->pulisciValoreUniversale($valore);
    }

    /**
     * Helper: Converte formato percentuale
     * Gestisce sia "27.5%" che "27.5" che "0.275"
     */
    private function normalizzaPercentuale($valore)
    {
        if (is_null($valore)) {
            return null;
        }

        $pulito = $this->pulisciValoreUniversale($valore);

        if (is_null($pulito)) {
            return null;
        }

        // Se il valore è tra 0 e 1, probabilmente è già in formato decimale (0.275)
        // Converti in percentuale
        if ($pulito > 0 && $pulito < 1) {
            return $pulito * 100;
        }

        // Altrimenti è già percentuale (27.5)
        return $pulito;
    }

    /**
     * Helper: Log diagnostico import
     * Registra informazioni utili per troubleshooting
     */
    private function logImportInfo($tipo, $dati)
    {
        \Log::info("Import Pesate - {$tipo}", array_merge($dati, [
            'timestamp' => now()->toISOString(),
            'user_id' => auth()->id(),
        ]));
    }
}
