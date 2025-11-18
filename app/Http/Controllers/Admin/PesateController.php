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
            'peso' => 'required|numeric|min:20|max:300',
            'bmi' => 'nullable|numeric|min:10|max:60',
            'peso_corporeo_senza_grassi' => 'nullable|numeric|min:0|max:200',
            'muscolo_scheletrico' => 'nullable|numeric|min:0|max:100',
            'grasso_corporeo' => 'nullable|numeric|min:0|max:100',
            'grasso_sottocutaneo' => 'nullable|numeric|min:0|max:100',
            'grasso_viscerale' => 'nullable|integer|min:0|max:30',
            'acqua_corporea' => 'nullable|numeric|min:0|max:100',
            'massa_muscolare' => 'nullable|numeric|min:0|max:200',
            'massa_ossea' => 'nullable|numeric|min:0|max:10',
            'proteine' => 'nullable|numeric|min:0|max:100',
            'bmr' => 'nullable|integer|min:500|max:5000',
            'eta_metabolica' => 'nullable|integer|min:10|max:120',
            'sede' => 'nullable|string|max:100',
            'note' => 'nullable|string|max:1000',
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
            'peso' => 'required|numeric|min:20|max:300',
            'bmi' => 'nullable|numeric|min:10|max:60',
            'peso_corporeo_senza_grassi' => 'nullable|numeric|min:0|max:200',
            'muscolo_scheletrico' => 'nullable|numeric|min:0|max:100',
            'grasso_corporeo' => 'nullable|numeric|min:0|max:100',
            'grasso_sottocutaneo' => 'nullable|numeric|min:0|max:100',
            'grasso_viscerale' => 'nullable|integer|min:0|max:30',
            'acqua_corporea' => 'nullable|numeric|min:0|max:100',
            'massa_muscolare' => 'nullable|numeric|min:0|max:200',
            'massa_ossea' => 'nullable|numeric|min:0|max:10',
            'proteine' => 'nullable|numeric|min:0|max:100',
            'bmr' => 'nullable|integer|min:500|max:5000',
            'eta_metabolica' => 'nullable|integer|min:10|max:120',
            'sede' => 'nullable|string|max:100',
            'note' => 'nullable|string|max:1000',
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
     * Processa file Excel e mostra anteprima
     */
    public function processImport(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls|max:10240',
            'sede' => 'required|string|max:100',
        ]);

        try {
            $file = $request->file('file');
            $sede = $request->sede;

            $spreadsheet = IOFactory::load($file->getRealPath());
            $worksheet = $spreadsheet->getActiveSheet();
            $highestRow = $worksheet->getHighestRow();

            $preview_data = [];
            $errors = [];

            // Header nella riga 2, dati dalla riga 3
            for ($row = 3; $row <= $highestRow; $row++) {
                $cognome = trim($worksheet->getCell("A$row")->getValue() ?? '');
                $nome = trim($worksheet->getCell("B$row")->getValue() ?? '');

                if (empty($cognome) && empty($nome)) {
                    continue;
                }

                // Cerca cliente
                $cliente = Cliente::where('cognome', 'LIKE', $cognome)
                                ->where('nome', 'LIKE', $nome)
                                ->first();

                $rowErrors = [];
                if (!$cliente) {
                    $rowErrors[] = 'Cliente non trovato nel database';
                }

                // Estrai dati pesata
                $peso = $this->pulisciNumero($worksheet->getCell("C$row")->getValue());
                if (!$peso || $peso < 20 || $peso > 300) {
                    $rowErrors[] = 'Peso non valido (deve essere tra 20 e 300 kg)';
                }

                $dataRilevazione = $worksheet->getCell("P$row")->getValue();
                if ($dataRilevazione instanceof \DateTime) {
                    $dataRilevazione = $dataRilevazione->format('Y-m-d');
                } elseif (is_numeric($dataRilevazione)) {
                    // Excel date serial
                    $dataRilevazione = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($dataRilevazione)->format('Y-m-d');
                }

                if (empty($dataRilevazione)) {
                    $rowErrors[] = 'Data rilevazione mancante';
                }

                $preview_data[] = [
                    'row' => $row,
                    'cliente_id' => $cliente->id ?? null,
                    'cognome' => $cognome,
                    'nome' => $nome,
                    'sede' => $sede,
                    'peso' => $peso,
                    'bmi' => $this->pulisciNumero($worksheet->getCell("D$row")->getValue()),
                    'peso_corporeo_senza_grassi' => $this->pulisciNumero($worksheet->getCell("E$row")->getValue()),
                    'muscolo_scheletrico' => $this->pulisciPercentuale($worksheet->getCell("F$row")->getValue()),
                    'grasso_corporeo' => $this->pulisciPercentuale($worksheet->getCell("G$row")->getValue()),
                    'grasso_sottocutaneo' => $this->pulisciPercentuale($worksheet->getCell("H$row")->getValue()),
                    'grasso_viscerale' => (int) $worksheet->getCell("I$row")->getValue(),
                    'acqua_corporea' => $this->pulisciPercentuale($worksheet->getCell("J$row")->getValue()),
                    'massa_muscolare' => $this->pulisciNumero($worksheet->getCell("K$row")->getValue()),
                    'massa_ossea' => $this->pulisciNumero($worksheet->getCell("L$row")->getValue()),
                    'proteine' => $this->pulisciPercentuale($worksheet->getCell("M$row")->getValue()),
                    'bmr' => (int) $worksheet->getCell("N$row")->getValue(),
                    'eta_metabolica' => (int) $worksheet->getCell("O$row")->getValue(),
                    'data_rilevazione' => $dataRilevazione,
                    'errors' => $rowErrors,
                ];
            }

            return view('admin.pesate.import-preview', compact('preview_data', 'sede'));

        } catch (\Exception $e) {
            return back()->with('error', 'Errore nella lettura del file: ' . $e->getMessage());
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

        // Rimuovi simboli e converti
        $numero = str_replace(['%', ','], ['', '.'], $valore);
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

        $numero = str_replace(['%', ','], ['', '.'], $valore);
        return is_numeric($numero) ? (float) $numero : null;
    }
}
