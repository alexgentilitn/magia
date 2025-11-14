@extends('layouts.admin')

@section('titolo', 'Modifica Impostazione Sistema')

@section('contenuto')
<div class="p-6 max-w-4xl mx-auto">

    <!-- Header -->
    <div class="mb-6">
        <a href="{{ route('admin.impostazioni-sistema.index') }}"
           class="text-fucsia-magia hover:text-viola-magia mb-2 inline-block">
            <i class="fas fa-arrow-left mr-2"></i> Torna alle impostazioni
        </a>
        <h2 class="text-2xl font-bold text-gray-800">Modifica Impostazione Sistema</h2>
        <p class="text-gray-600 mt-1">{{ $impostazione->etichetta }}</p>
    </div>

    <!-- Form -->
    <form method="POST" action="{{ route('admin.impostazioni-sistema.update', $impostazione->id) }}"
          class="bg-white rounded-lg shadow p-6">
        @csrf
        @method('PUT')

        <!-- Categoria e Chiave (solo se non è di sistema) -->
        @if(!$impostazione->di_sistema)
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Categoria <span class="text-red-500">*</span>
                </label>
                <select name="categoria"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent"
                        required>
                    @foreach($categorie as $key => $nome)
                    <option value="{{ $key }}" {{ $impostazione->categoria === $key ? 'selected' : '' }}>
                        {{ $nome }}
                    </option>
                    @endforeach
                </select>
                @error('categoria')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Chiave <span class="text-red-500">*</span>
                </label>
                <input type="text"
                       name="chiave"
                       value="{{ old('chiave', $impostazione->chiave) }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent"
                       placeholder="es: nuova_tipologia"
                       pattern="[a-z0-9_]+"
                       required>
                <p class="text-xs text-gray-500 mt-1">Solo lettere minuscole, numeri e underscore</p>
                @error('chiave')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>
        @else
        <!-- Mostra categoria e chiave come solo lettura per impostazioni di sistema -->
        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6 rounded-r-lg">
            <div class="flex items-start">
                <i class="fas fa-info-circle text-blue-500 mt-1 mr-3"></i>
                <div class="flex-1">
                    <p class="font-medium text-blue-900">Impostazione di sistema</p>
                    <p class="text-sm text-blue-700 mt-1">
                        Categoria: <code class="bg-blue-100 px-2 py-1 rounded">{{ $categorie[$impostazione->categoria] ?? $impostazione->categoria }}</code> •
                        Chiave: <code class="bg-blue-100 px-2 py-1 rounded">{{ $impostazione->chiave }}</code>
                    </p>
                    <p class="text-xs text-blue-600 mt-2">
                        Non è possibile modificare categoria e chiave delle impostazioni di sistema
                    </p>
                </div>
            </div>
        </div>
        @endif

        <!-- Etichetta -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Etichetta <span class="text-red-500">*</span>
            </label>
            <input type="text"
                   name="etichetta"
                   value="{{ old('etichetta', $impostazione->etichetta) }}"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent"
                   placeholder="es: Lezione Individuale"
                   required>
            <p class="text-xs text-gray-500 mt-1">Nome visualizzato nelle select e nell'interfaccia</p>
            @error('etichetta')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Descrizione -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Descrizione
            </label>
            <textarea name="descrizione"
                      rows="3"
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent"
                      placeholder="Descrizione opzionale dell'impostazione">{{ old('descrizione', $impostazione->descrizione) }}</textarea>
            @error('descrizione')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Colore e Icona -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Colore
                </label>
                <div class="flex gap-2">
                    <input type="color"
                           name="colore"
                           value="{{ old('colore', $impostazione->colore ?? '#764ba2') }}"
                           class="h-10 w-20 border border-gray-300 rounded-lg cursor-pointer">
                    <input type="text"
                           name="colore_text"
                           value="{{ old('colore', $impostazione->colore ?? '#764ba2') }}"
                           class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent"
                           placeholder="#764ba2"
                           pattern="#[0-9A-Fa-f]{6}"
                           onchange="document.querySelector('input[type=color]').value = this.value">
                </div>
                <p class="text-xs text-gray-500 mt-1">Colore esadecimale per badge e visualizzazione</p>
                @error('colore')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Icona FontAwesome
                </label>
                <div class="flex gap-2">
                    <input type="text"
                           name="icona"
                           value="{{ old('icona', $impostazione->icona) }}"
                           class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent"
                           placeholder="fa-user">
                    @if($impostazione->icona)
                    <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center text-viola-magia">
                        <i class="fas {{ $impostazione->icona }}"></i>
                    </div>
                    @endif
                </div>
                <p class="text-xs text-gray-500 mt-1">
                    Classe FontAwesome (es: fa-user, fa-calendar)
                    <a href="https://fontawesome.com/icons" target="_blank" class="text-fucsia-magia hover:underline">Cerca icone</a>
                </p>
                @error('icona')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Ordinamento -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Ordinamento
            </label>
            <input type="number"
                   name="ordinamento"
                   value="{{ old('ordinamento', $impostazione->ordinamento ?? 0) }}"
                   min="0"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent"
                   placeholder="0">
            <p class="text-xs text-gray-500 mt-1">Ordine di visualizzazione nelle select (0 = primo)</p>
            @error('ordinamento')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Attivo -->
        <div class="mb-6">
            <label class="flex items-center cursor-pointer">
                <input type="checkbox"
                       name="attivo"
                       value="1"
                       {{ old('attivo', $impostazione->attivo) ? 'checked' : '' }}
                       class="rounded text-fucsia-magia focus:ring-fucsia-magia mr-3">
                <div>
                    <span class="text-sm font-medium text-gray-700">Impostazione attiva</span>
                    <p class="text-xs text-gray-500">Solo le impostazioni attive sono disponibili nelle select</p>
                </div>
            </label>
        </div>

        <!-- Pulsanti -->
        <div class="flex gap-3 pt-4 border-t">
            <button type="submit"
                    class="px-6 py-2.5 bg-gradient-to-r from-viola-magia to-fucsia-magia text-white rounded-lg hover:shadow-lg transition-all font-medium">
                <i class="fas fa-save mr-2"></i>
                Salva Modifiche
            </button>
            <a href="{{ route('admin.impostazioni-sistema.index') }}"
               class="px-6 py-2.5 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-medium">
                <i class="fas fa-times mr-2"></i>
                Annulla
            </a>
        </div>
    </form>

</div>

<script>
// Sincronizza input color e text
document.querySelector('input[type=color]').addEventListener('input', function() {
    document.querySelector('input[name=colore_text]').value = this.value;
});
</script>
@endsection
