@extends('layouts.admin')

@section('titolo', 'Modifica Sede')

@section('contenuto')
<div class="p-6">
    <div class="mb-6">
        <a href="{{ route('admin.sedi.show', $sede->id) }}" class="text-fucsia-magia hover:text-viola-magia mb-2 inline-block">
            <i class="fas fa-arrow-left mr-2"></i> Torna al dettaglio
        </a>
        <h1 class="text-3xl font-bold text-gray-800">Modifica Sede: {{ $sede->nome }}</h1>
    </div>

    <form method="POST" action="{{ route('admin.sedi.update', $sede->id) }}" class="bg-white rounded-lg shadow p-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Nome -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Nome Sede *</label>
                <input type="text" name="nome" required value="{{ old('nome', $sede->nome) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent" placeholder="Es: MA.GIA DONNA - Sede Centrale">
            </div>

            <!-- Descrizione -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Descrizione</label>
                <textarea name="descrizione" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent" placeholder="Breve descrizione della sede...">{{ old('descrizione', $sede->descrizione) }}</textarea>
            </div>

            <!-- Indirizzo Via -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Indirizzo *</label>
                <input type="text" name="indirizzo_via" required value="{{ old('indirizzo_via', $sede->indirizzo_via) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent" placeholder="Via, numero civico">
            </div>

            <!-- Città -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Città *</label>
                <input type="text" name="indirizzo_citta" required value="{{ old('indirizzo_citta', $sede->indirizzo_citta) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent" placeholder="Es: Milano">
            </div>

            <!-- Provincia -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Provincia *</label>
                <input type="text" name="indirizzo_provincia" required maxlength="2" value="{{ old('indirizzo_provincia', $sede->indirizzo_provincia) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent" placeholder="Es: MI" style="text-transform: uppercase;">
            </div>

            <!-- CAP -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">CAP *</label>
                <input type="text" name="indirizzo_cap" required value="{{ old('indirizzo_cap', $sede->indirizzo_cap) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent" placeholder="Es: 20100">
            </div>

            <!-- Capienza -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Capienza Massima</label>
                <input type="number" name="capienza_massima" min="1" value="{{ old('capienza_massima', $sede->capienza_massima) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent" placeholder="Es: 30">
            </div>

            <!-- Telefono -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Telefono</label>
                <input type="text" name="telefono" value="{{ old('telefono', $sede->telefono) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent" placeholder="Es: 02 1234567">
            </div>

            <!-- Email -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                <input type="email" name="email" value="{{ old('email', $sede->email) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent" placeholder="Es: sede@magiadonna.it">
            </div>

            <!-- Latitudine -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Latitudine</label>
                <input type="number" name="latitudine" step="0.000001" value="{{ old('latitudine', $sede->latitudine) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent" placeholder="Es: 45.4642">
            </div>

            <!-- Longitudine -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Longitudine</label>
                <input type="number" name="longitudine" step="0.000001" value="{{ old('longitudine', $sede->longitudine) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent" placeholder="Es: 9.1900">
            </div>

            <!-- Ordine -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Ordine di Visualizzazione</label>
                <input type="number" name="ordine" min="0" value="{{ old('ordine', $sede->ordine) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent">
                <p class="text-xs text-gray-500 mt-1">Numero più basso = visualizzato per primo</p>
            </div>
        </div>

        <!-- Flags -->
        <div class="mt-6 space-y-3">
            <div class="flex items-center">
                <input type="checkbox" name="attiva" id="attiva" value="1" {{ old('attiva', $sede->attiva) ? 'checked' : '' }} class="rounded border-gray-300 text-fucsia-magia">
                <label for="attiva" class="ml-2 text-sm text-gray-700">Sede Attiva</label>
            </div>

            <div class="flex items-center">
                <input type="checkbox" name="visibile_pubblico" id="visibile_pubblico" value="1" {{ old('visibile_pubblico', $sede->visibile_pubblico) ? 'checked' : '' }} class="rounded border-gray-300 text-fucsia-magia">
                <label for="visibile_pubblico" class="ml-2 text-sm text-gray-700">Visibile al Pubblico</label>
            </div>

            <div class="flex items-center">
                <input type="checkbox" name="sede_principale" id="sede_principale" value="1" {{ old('sede_principale', $sede->sede_principale) ? 'checked' : '' }} class="rounded border-gray-300 text-fucsia-magia">
                <label for="sede_principale" class="ml-2 text-sm text-gray-700">Imposta come Sede Principale</label>
            </div>
        </div>

        <div class="mt-6 flex justify-end space-x-3">
            <a href="{{ route('admin.sedi.show', $sede->id) }}" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                Annulla
            </a>
            <button type="submit" class="px-4 py-2 bg-fucsia-magia text-white rounded-lg hover:bg-viola-magia">
                <i class="fas fa-save mr-2"></i> Salva Modifiche
            </button>
        </div>
    </form>
</div>
@endsection
