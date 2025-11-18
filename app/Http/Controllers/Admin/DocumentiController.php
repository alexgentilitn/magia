<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Documento;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

/**
 * Controller: Documenti
 * Funzione: Gestisce upload, visualizzazione ed eliminazione documenti clienti
 */
class DocumentiController extends Controller
{
    /**
     * Lista documenti di un cliente
     */
    public function index($cliente_id)
    {
        $cliente = Cliente::with(['documenti' => function($query) {
            $query->orderBy('created_at', 'desc');
        }])->findOrFail($cliente_id);

        $documenti = $cliente->documenti;

        return view('admin.clienti.documenti.index', compact('cliente', 'documenti'));
    }

    /**
     * Form upload nuovo documento
     */
    public function create($cliente_id)
    {
        $cliente = Cliente::findOrFail($cliente_id);
        $tipi_documento = Documento::getTipiDocumento();
        $stati = Documento::getStati();

        return view('admin.clienti.documenti.create', compact('cliente', 'tipi_documento', 'stati'));
    }

    /**
     * Salva nuovo documento
     */
    public function store(Request $request, $cliente_id)
    {
        $cliente = Cliente::findOrFail($cliente_id);

        $validated = $request->validate([
            'tipo_documento' => 'required|string',
            'file' => 'required|file|max:10240|mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx',
            'descrizione' => 'nullable|string|max:500',
            'data_scadenza' => 'nullable|date',
            'stato' => 'nullable|string',
            'note' => 'nullable|string',
            'visibile_cliente' => 'nullable|boolean',
        ], [
            'file.required' => 'Il file è obbligatorio',
            'file.max' => 'Il file non può superare 10MB',
            'file.mimes' => 'Formato file non consentito. Formati accettati: PDF, JPG, PNG, DOC, DOCX, XLS, XLSX',
        ]);

        try {
            // Upload file
            $file = $request->file('file');
            $nome_originale = $file->getClientOriginalName();
            $estensione = $file->getClientOriginalExtension();

            // Genera nome file univoco
            $nome_file = Str::slug(pathinfo($nome_originale, PATHINFO_FILENAME)) . '_' . time() . '.' . $estensione;

            // Salva in storage/app/documenti/cliente_{id}/
            $path = $file->storeAs(
                'documenti/cliente_' . $cliente_id,
                $nome_file,
                'private' // Storage privato, non accessibile pubblicamente
            );

            // Crea record documento
            $documento = Documento::create([
                'cliente_id' => $cliente_id,
                'caricato_da_utente_id' => Auth::id(),
                'tipo_documento' => $validated['tipo_documento'],
                'nome_file' => $nome_file,
                'nome_originale' => $nome_originale,
                'path' => $path,
                'mime_type' => $file->getMimeType(),
                'dimensione' => $file->getSize(),
                'descrizione' => $validated['descrizione'] ?? null,
                'data_scadenza' => $validated['data_scadenza'] ?? null,
                'stato' => $validated['stato'] ?? 'valido',
                'note' => $validated['note'] ?? null,
                'visibile_cliente' => $validated['visibile_cliente'] ?? false,
            ]);

            // Se è un certificato medico, aggiorna anche il cliente
            if ($validated['tipo_documento'] === 'certificato_medico' && $validated['data_scadenza']) {
                $cliente->update([
                    'certificato_medico_valido' => true,
                    'certificato_scadenza' => $validated['data_scadenza'],
                ]);
            }

            return redirect()
                ->route('admin.clienti.documenti.index', $cliente_id)
                ->with('success', 'Documento caricato con successo');

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Errore durante il caricamento del documento: ' . $e->getMessage());
        }
    }

    /**
     * Download documento
     */
    public function download($id)
    {
        $documento = Documento::findOrFail($id);

        // Verifica che il file esista
        if (!Storage::disk('private')->exists($documento->path)) {
            return redirect()
                ->back()
                ->with('error', 'File non trovato');
        }

        return Storage::disk('private')->download(
            $documento->path,
            $documento->nome_originale
        );
    }

    /**
     * Visualizza documento (in browser)
     */
    public function show($id)
    {
        $documento = Documento::with('cliente', 'caricatoDa')->findOrFail($id);

        // Verifica che il file esista
        if (!Storage::disk('private')->exists($documento->path)) {
            return redirect()
                ->back()
                ->with('error', 'File non trovato');
        }

        // Se è PDF o immagine, mostra in browser
        if (str_contains($documento->mime_type, 'pdf') || str_contains($documento->mime_type, 'image')) {
            return response()->file(
                Storage::disk('private')->path($documento->path)
            );
        }

        // Altrimenti forza download
        return $this->download($id);
    }

    /**
     * Form modifica documento
     */
    public function edit($id)
    {
        $documento = Documento::with('cliente')->findOrFail($id);
        $tipi_documento = Documento::getTipiDocumento();
        $stati = Documento::getStati();

        return view('admin.clienti.documenti.edit', compact('documento', 'tipi_documento', 'stati'));
    }

    /**
     * Aggiorna documento
     */
    public function update(Request $request, $id)
    {
        $documento = Documento::findOrFail($id);

        $validated = $request->validate([
            'tipo_documento' => 'required|string',
            'descrizione' => 'nullable|string|max:500',
            'data_scadenza' => 'nullable|date',
            'stato' => 'nullable|string',
            'note' => 'nullable|string',
            'visibile_cliente' => 'nullable|boolean',
        ]);

        try {
            $documento->update($validated);

            // Se è un certificato medico, aggiorna anche il cliente
            if ($validated['tipo_documento'] === 'certificato_medico' && $validated['data_scadenza']) {
                $documento->cliente->update([
                    'certificato_medico_valido' => true,
                    'certificato_scadenza' => $validated['data_scadenza'],
                ]);
            }

            return redirect()
                ->route('admin.clienti.documenti.index', $documento->cliente_id)
                ->with('success', 'Documento aggiornato con successo');

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Errore durante l\'aggiornamento del documento: ' . $e->getMessage());
        }
    }

    /**
     * Elimina documento
     */
    public function destroy($id)
    {
        $documento = Documento::findOrFail($id);
        $cliente_id = $documento->cliente_id;

        try {
            // Il file fisico viene eliminato automaticamente dal boot() del Model
            $documento->delete();

            return redirect()
                ->route('admin.clienti.documenti.index', $cliente_id)
                ->with('success', 'Documento eliminato con successo');

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Errore durante l\'eliminazione del documento: ' . $e->getMessage());
        }
    }

    /**
     * Lista tutti i documenti in scadenza (dashboard alert)
     */
    public function scadenza()
    {
        $documenti_in_scadenza = Documento::with('cliente')
            ->inScadenza(30)
            ->orderBy('data_scadenza', 'asc')
            ->get();

        $documenti_scaduti = Documento::with('cliente')
            ->scaduti()
            ->orderBy('data_scadenza', 'desc')
            ->get();

        return view('admin.documenti.scadenza', compact('documenti_in_scadenza', 'documenti_scaduti'));
    }
}
