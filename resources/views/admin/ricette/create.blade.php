@extends('layouts.admin')

@section('titolo', 'Nuova Ricetta')

@section('contenuto')
<div class="p-6">

    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Nuova Ricetta</h2>
            <p class="text-gray-600 mt-1">Crea una nuova ricetta alimentare</p>
        </div>
        <a href="{{ route('admin.ricette.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
            <i class="fas fa-arrow-left mr-2"></i>Torna alla Lista
        </a>
    </div>

    <form action="{{ route('admin.ricette.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="bg-white rounded-lg shadow p-6 space-y-6">

            <!-- Info Base -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Titolo <span class="text-red-500">*</span></label>
                    <input type="text" name="titolo" value="{{ old('titolo') }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-viola-magia @error('titolo') border-red-500 @enderror"
                           placeholder="es: Insalata Proteica Estiva">
                    @error('titolo')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Descrizione</label>
                    <textarea name="descrizione" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-viola-magia"
                              placeholder="Breve descrizione della ricetta...">{{ old('descrizione') }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Categoria</label>
                    <select name="categoria" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-viola-magia">
                        <option value="">Seleziona...</option>
                        @foreach(\App\Models\Ricetta::getCategorie() as $key => $label)
                            <option value="{{ $key }}" {{ old('categoria') == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Difficoltà</label>
                    <select name="difficolta" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-viola-magia">
                        @foreach(\App\Models\Ricetta::getDifficolta() as $key => $label)
                            <option value="{{ $key }}" {{ old('difficolta', 'facile') == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tempo Preparazione (minuti)</label>
                    <input type="number" name="tempo_preparazione" value="{{ old('tempo_preparazione') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-viola-magia"
                           placeholder="30">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Calorie</label>
                    <input type="number" name="calorie" value="{{ old('calorie') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-viola-magia"
                           placeholder="250">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Immagine</label>
                    <input type="file" name="immagine" accept="image/*"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-viola-magia">
                </div>
            </div>

            <!-- Ingredienti e Procedimento -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Ingredienti <span class="text-red-500">*</span></label>
                <textarea name="ingredienti" rows="6" required
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-viola-magia @error('ingredienti') border-red-500 @enderror"
                          placeholder="Inserisci gli ingredienti (uno per riga)&#10;- 200g di pollo&#10;- 150g di insalata&#10;- 50g di pomodorini">{{ old('ingredienti') }}</textarea>
                @error('ingredienti')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Procedimento <span class="text-red-500">*</span></label>
                <textarea name="procedimento" rows="8" required
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-viola-magia @error('procedimento') border-red-500 @enderror"
                          placeholder="Descrivi il procedimento passo per passo...">{{ old('procedimento') }}</textarea>
                @error('procedimento')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Note</label>
                <textarea name="note" rows="3"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-viola-magia">{{ old('note') }}</textarea>
            </div>

            <!-- Pubblicazione -->
            <div class="border-t pt-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Pubblicazione</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Data Pubblicazione</label>
                        <input type="date" name="data_pubblicazione" value="{{ old('data_pubblicazione') }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-viola-magia">
                        <p class="text-xs text-gray-500 mt-1">Lascia vuoto per pubblicazione immediata</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Ordine Pubblicazione</label>
                        <input type="number" name="ordine_pubblicazione" value="{{ old('ordine_pubblicazione', 0) }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-viola-magia">
                        <p class="text-xs text-gray-500 mt-1">Ordine di distribuzione (0 = primo)</p>
                    </div>
                </div>

                <div class="mt-4">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="pubblicata" value="1" {{ old('pubblicata') ? 'checked' : '' }}
                               class="w-5 h-5 text-viola-magia border-gray-300 rounded focus:ring-viola-magia">
                        <span class="ml-3">
                            <span class="font-medium text-gray-900">Pubblica Immediatamente</span>
                            <span class="block text-sm text-gray-500">La ricetta sarà visibile ai clienti</span>
                        </span>
                    </label>
                </div>
            </div>

            <!-- Azioni -->
            <div class="flex justify-end gap-4 pt-6 border-t">
                <a href="{{ route('admin.ricette.index') }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                    <i class="fas fa-times mr-2"></i>Annulla
                </a>
                <button type="submit" class="px-6 py-2 bg-gradient-to-r from-viola-magia to-fucsia-magia text-white rounded-lg hover:shadow-lg transition">
                    <i class="fas fa-save mr-2"></i>Salva Ricetta
                </button>
            </div>

        </div>
    </form>

</div>
@endsection
