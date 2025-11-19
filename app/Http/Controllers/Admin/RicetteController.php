<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ricetta;
use App\Models\Programma;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RicetteController extends Controller
{
    public function index()
    {
        $ricette = Ricetta::orderBy('ordine_pubblicazione', 'asc')
                         ->orderBy('created_at', 'desc')
                         ->paginate(20);

        return view('admin.ricette.index', compact('ricette'));
    }

    public function create()
    {
        $programmi = Programma::attivi()->get();
        $categorie = Ricetta::getCategorie();
        $difficolta = Ricetta::getDifficolta();

        return view('admin.ricette.create', compact('programmi', 'categorie', 'difficolta'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'titolo' => 'required|string|max:255',
            'descrizione' => 'nullable|string',
            'ingredienti' => 'required|string',
            'procedimento' => 'required|string',
            'tempo_preparazione' => 'nullable|integer',
            'difficolta' => 'required|in:facile,media,difficile',
            'calorie' => 'nullable|integer',
            'categoria' => 'nullable|string',
            'immagine' => 'nullable|image|max:2048',
            'ordine_pubblicazione' => 'nullable|integer',
            'data_pubblicazione' => 'nullable|date',
            'pubblicata' => 'boolean',
            'visibile_programmi' => 'nullable|array',
        ]);

        // Upload immagine
        if ($request->hasFile('immagine')) {
            $validated['immagine'] = $request->file('immagine')->store('ricette', 'public');
        }

        Ricetta::create($validated);

        return redirect()->route('admin.ricette.index')
                        ->with('success', 'Ricetta creata con successo');
    }

    public function edit($id)
    {
        $ricetta = Ricetta::findOrFail($id);
        $programmi = Programma::attivi()->get();
        $categorie = Ricetta::getCategorie();
        $difficolta = Ricetta::getDifficolta();

        return view('admin.ricette.edit', compact('ricetta', 'programmi', 'categorie', 'difficolta'));
    }

    public function update(Request $request, $id)
    {
        $ricetta = Ricetta::findOrFail($id);

        $validated = $request->validate([
            'titolo' => 'required|string|max:255',
            'descrizione' => 'nullable|string',
            'ingredienti' => 'required|string',
            'procedimento' => 'required|string',
            'tempo_preparazione' => 'nullable|integer',
            'difficolta' => 'required|in:facile,media,difficile',
            'calorie' => 'nullable|integer',
            'categoria' => 'nullable|string',
            'immagine' => 'nullable|image|max:2048',
            'ordine_pubblicazione' => 'nullable|integer',
            'data_pubblicazione' => 'nullable|date',
            'pubblicata' => 'boolean',
            'visibile_programmi' => 'nullable|array',
        ]);

        if ($request->hasFile('immagine')) {
            if ($ricetta->immagine) {
                Storage::disk('public')->delete($ricetta->immagine);
            }
            $validated['immagine'] = $request->file('immagine')->store('ricette', 'public');
        }

        $ricetta->update($validated);

        return redirect()->route('admin.ricette.index')
                        ->with('success', 'Ricetta aggiornata');
    }

    public function destroy($id)
    {
        $ricetta = Ricetta::findOrFail($id);

        if ($ricetta->immagine) {
            Storage::disk('public')->delete($ricetta->immagine);
        }

        $ricetta->delete();

        return redirect()->route('admin.ricette.index')
                        ->with('success', 'Ricetta eliminata');
    }

    public function pubblica($id)
    {
        $ricetta = Ricetta::findOrFail($id);
        $ricetta->update([
            'pubblicata' => true,
            'data_pubblicazione' => $ricetta->data_pubblicazione ?? now(),
        ]);

        return redirect()->back()->with('success', 'Ricetta pubblicata');
    }
}
