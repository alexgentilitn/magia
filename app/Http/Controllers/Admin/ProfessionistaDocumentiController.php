<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Professionista;
use App\Models\ProfessionistaDocumento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

/**
 * Controller: Gestione Documenti Professionista
 * CRUD completo per documenti e allegati professionisti
 */
class ProfessionistaDocumentiController extends Controller
{
    /**
     * Mostra lista documenti professionista
     */
    public function index($professionista_id)
    {
        $professionista = Professionista::with(['documenti' => function($query) {
            $query->orderBy('created_at', 'desc');
        }])->findOrFail($professionista_id);

        // Statistiche documenti
        $statistiche = [
            'totale' => $professionista->documenti->count(),
            'verificati' => $professionista->documenti->where('verificato', true)->count(),
            'in_scadenza' => $professionista->documenti->filter(function($doc) {
                return $doc->data_scadenza &&
                       $doc->data_scadenza->isFuture() &&
                       $doc->data_scadenza->lte(now()->addDays(30));
            })->count(),
            'scaduti' => $professionista->documenti->filter(function($doc) {
                return $doc->isScaduto();
            })->count(),
        ];

        return view('admin.professionisti.documenti.index', compact('professionista', 'statistiche'));
    }

    /**
     * Upload nuovo documento
     */
    public function upload(Request $request, $professionista_id)
    {
        $professionista = Professionista::findOrFail($professionista_id);

        $validated = $request->validate([
            'file' => 'required|file|max:10240', // max 10MB
            'tipo_documento' => 'required|in:certificato,cv,carta_identita,contratto,altro',
            'descrizione' => 'nullable|string|max:500',
            'data_scadenza' => 'nullable|date|after:today',
        ]);

        try {
            $file = $request->file('file');

            // Genera nome file unico
            $nomeFile = time() . '_' . $professionista_id . '_' . $file->getClientOriginalName();

            // Salva file
            $path = $file->storeAs('professionisti/documenti', $nomeFile, 'public');

            // Crea record database
            $documento = ProfessionistaDocumento::create([
                'professionista_id' => $professionista_id,
                'nome_file' => $file->getClientOriginalName(),
                'path' => $path,
                'tipo_documento' => $validated['tipo_documento'],
                'mime_type' => $file->getMimeType(),
                'dimensione' => $file->getSize(),
                'descrizione' => $validated['descrizione'] ?? null,
                'data_scadenza' => $validated['data_scadenza'] ?? null,
                'scaduto' => false,
            ]);

            return redirect()->route('admin.professionisti.documenti.index', $professionista_id)
                ->with('success', 'Documento caricato con successo!');
        } catch (\Exception $e) {
            return redirect()->route('admin.professionisti.documenti.index', $professionista_id)
                ->with('error', 'Errore durante il caricamento: ' . $e->getMessage());
        }
    }

    /**
     * Download documento
     */
    public function download($professionista_id, $documento_id)
    {
        $documento = ProfessionistaDocumento::where('professionista_id', $professionista_id)
                                           ->findOrFail($documento_id);

        if (!Storage::disk('public')->exists($documento->path)) {
            return redirect()->back()->with('error', 'File non trovato.');
        }

        return Storage::disk('public')->download($documento->path, $documento->nome_file);
    }

    /**
     * Elimina documento
     */
    public function destroy($professionista_id, $documento_id)
    {
        $documento = ProfessionistaDocumento::where('professionista_id', $professionista_id)
                                           ->findOrFail($documento_id);

        try {
            // Elimina file fisico
            if (Storage::disk('public')->exists($documento->path)) {
                Storage::disk('public')->delete($documento->path);
            }

            // Elimina record database
            $documento->delete();

            return redirect()->route('admin.professionisti.documenti.index', $professionista_id)
                ->with('success', 'Documento eliminato con successo!');
        } catch (\Exception $e) {
            return redirect()->route('admin.professionisti.documenti.index', $professionista_id)
                ->with('error', 'Errore durante l\'eliminazione: ' . $e->getMessage());
        }
    }

    /**
     * Verifica documento (admin)
     */
    public function verifica($professionista_id, $documento_id)
    {
        $documento = ProfessionistaDocumento::where('professionista_id', $professionista_id)
                                           ->findOrFail($documento_id);

        $documento->update([
            'verificato' => true,
            'verificato_at' => now(),
            'verificato_da' => Auth::id(),
        ]);

        return redirect()->route('admin.professionisti.documenti.index', $professionista_id)
            ->with('success', 'Documento verificato con successo!');
    }

    /**
     * Revoca verifica documento
     */
    public function revocaVerifica($professionista_id, $documento_id)
    {
        $documento = ProfessionistaDocumento::where('professionista_id', $professionista_id)
                                           ->findOrFail($documento_id);

        $documento->update([
            'verificato' => false,
            'verificato_at' => null,
            'verificato_da' => null,
        ]);

        return redirect()->route('admin.professionisti.documenti.index', $professionista_id)
            ->with('success', 'Verifica revocata!');
    }

    /**
     * Aggiorna informazioni documento
     */
    public function update(Request $request, $professionista_id, $documento_id)
    {
        $documento = ProfessionistaDocumento::where('professionista_id', $professionista_id)
                                           ->findOrFail($documento_id);

        $validated = $request->validate([
            'tipo_documento' => 'required|in:certificato,cv,carta_identita,contratto,altro',
            'descrizione' => 'nullable|string|max:500',
            'data_scadenza' => 'nullable|date',
        ]);

        $documento->update($validated);

        return redirect()->route('admin.professionisti.documenti.index', $professionista_id)
            ->with('success', 'Documento aggiornato con successo!');
    }
}
