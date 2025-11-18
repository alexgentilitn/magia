<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Professionista;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

/**
 * Controller: Gestione Galleria Foto Professionista
 * Upload multiplo foto per galleria professionista
 */
class ProfessionistaGalleriaController extends Controller
{
    /**
     * Mostra galleria foto professionista
     */
    public function index($professionista_id)
    {
        $professionista = Professionista::findOrFail($professionista_id);

        $galleria = $professionista->galleria_foto ?? [];

        return view('admin.professionisti.galleria.index', compact('professionista', 'galleria'));
    }

    /**
     * Upload nuove foto
     */
    public function upload(Request $request, $professionista_id)
    {
        $professionista = Professionista::findOrFail($professionista_id);

        $request->validate([
            'foto' => 'required|array|max:10',
            'foto.*' => 'image|mimes:jpeg,png,jpg|max:5120', // max 5MB per foto
        ]);

        try {
            $galleria = $professionista->galleria_foto ?? [];

            foreach ($request->file('foto') as $foto) {
                // Genera nome file unico
                $nomeFile = time() . '_' . uniqid() . '.' . $foto->extension();

                // Salva foto
                $path = $foto->storeAs('professionisti/galleria', $nomeFile, 'public');

                // Aggiungi all'array galleria
                $galleria[] = [
                    'path' => $path,
                    'nome_file' => $foto->getClientOriginalName(),
                    'caricato_il' => now()->toDateTimeString(),
                ];
            }

            // Aggiorna professionista
            $professionista->update(['galleria_foto' => $galleria]);

            return redirect()->route('admin.professionisti.galleria.index', $professionista_id)
                ->with('success', count($request->file('foto')) . ' foto caricate con successo!');
        } catch (\Exception $e) {
            return redirect()->route('admin.professionisti.galleria.index', $professionista_id)
                ->with('error', 'Errore durante il caricamento: ' . $e->getMessage());
        }
    }

    /**
     * Elimina foto dalla galleria
     */
    public function destroy($professionista_id, $index)
    {
        $professionista = Professionista::findOrFail($professionista_id);

        try {
            $galleria = $professionista->galleria_foto ?? [];

            if (!isset($galleria[$index])) {
                return redirect()->back()->with('error', 'Foto non trovata.');
            }

            $foto = $galleria[$index];

            // Elimina file fisico
            if (isset($foto['path']) && Storage::disk('public')->exists($foto['path'])) {
                Storage::disk('public')->delete($foto['path']);
            }

            // Rimuovi dall'array
            array_splice($galleria, $index, 1);

            // Aggiorna professionista
            $professionista->update(['galleria_foto' => $galleria]);

            return redirect()->route('admin.professionisti.galleria.index', $professionista_id)
                ->with('success', 'Foto eliminata con successo!');
        } catch (\Exception $e) {
            return redirect()->route('admin.professionisti.galleria.index', $professionista_id)
                ->with('error', 'Errore durante l\'eliminazione: ' . $e->getMessage());
        }
    }

    /**
     * Imposta foto come principale (foto profilo)
     */
    public function impostaCopertina($professionista_id, $index)
    {
        $professionista = Professionista::findOrFail($professionista_id);

        $galleria = $professionista->galleria_foto ?? [];

        if (!isset($galleria[$index])) {
            return redirect()->back()->with('error', 'Foto non trovata.');
        }

        $foto = $galleria[$index];

        // Copia foto come foto profilo
        if (isset($foto['path']) && Storage::disk('public')->exists($foto['path'])) {
            // Elimina foto profilo vecchia
            if ($professionista->foto_profilo && Storage::disk('public')->exists($professionista->foto_profilo)) {
                Storage::disk('public')->delete($professionista->foto_profilo);
            }

            // Copia nuova foto come profilo
            $nuovoPath = 'professionisti/' . time() . '_profilo_' . $professionista_id . '.jpg';
            Storage::disk('public')->copy($foto['path'], $nuovoPath);

            $professionista->update(['foto_profilo' => $nuovoPath]);

            return redirect()->route('admin.professionisti.galleria.index', $professionista_id)
                ->with('success', 'Foto impostata come foto profilo!');
        }

        return redirect()->back()->with('error', 'Errore durante l\'impostazione.');
    }

    /**
     * Scarica tutte le foto come ZIP
     */
    public function downloadAll($professionista_id)
    {
        $professionista = Professionista::findOrFail($professionista_id);

        $galleria = $professionista->galleria_foto ?? [];

        if (empty($galleria)) {
            return redirect()->back()->with('error', 'Nessuna foto da scaricare.');
        }

        try {
            $zipName = 'galleria_' . $professionista->cognome . '_' . time() . '.zip';
            $zipPath = storage_path('app/temp/' . $zipName);

            // Crea directory temp se non esiste
            if (!file_exists(storage_path('app/temp'))) {
                mkdir(storage_path('app/temp'), 0755, true);
            }

            $zip = new \ZipArchive();

            if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === true) {
                foreach ($galleria as $index => $foto) {
                    if (isset($foto['path']) && Storage::disk('public')->exists($foto['path'])) {
                        $filePath = storage_path('app/public/' . $foto['path']);
                        $fileName = $foto['nome_file'] ?? 'foto_' . ($index + 1) . '.jpg';
                        $zip->addFile($filePath, $fileName);
                    }
                }

                $zip->close();

                return response()->download($zipPath)->deleteFileAfterSend(true);
            }

            return redirect()->back()->with('error', 'Errore nella creazione del file ZIP.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Errore: ' . $e->getMessage());
        }
    }
}
