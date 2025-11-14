@extends('layouts.admin')

@section('titolo', 'Modifica Pagamento')

@section('contenuto')
<div class="p-6">
    <div class="mb-6">
        <a href="{{ route('admin.pagamenti.show', $pagamento->id) }}" class="text-fucsia-magia hover:text-viola-magia mb-2 inline-block">
            <i class="fas fa-arrow-left mr-2"></i> Torna al dettaglio
        </a>
        <h1 class="text-3xl font-bold text-gray-800">Modifica Pagamento #{{ $pagamento->id }}</h1>
    </div>

    <form method="POST" action="{{ route('admin.pagamenti.update', $pagamento->id) }}" class="bg-white rounded-lg shadow p-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Cliente -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Cliente *</label>
                <select name="cliente_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent">
                    @foreach($clienti as $cliente)
                    <option value="{{ $cliente->id }}" {{ $pagamento->cliente_id == $cliente->id ? 'selected' : '' }}>
                        {{ $cliente->nome }} {{ $cliente->cognome }}
                    </option>
                    @endforeach
                </select>
            </div>

            <!-- Tipo -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tipo *</label>
                <select name="tipo" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent">
                    <option value="programma" {{ $pagamento->tipo == 'programma' ? 'selected' : '' }}>Programma</option>
                    <option value="lezione" {{ $pagamento->tipo == 'lezione' ? 'selected' : '' }}>Lezione</option>
                    <option value="abbonamento" {{ $pagamento->tipo == 'abbonamento' ? 'selected' : '' }}>Abbonamento</option>
                    <option value="altro" {{ $pagamento->tipo == 'altro' ? 'selected' : '' }}>Altro</option>
                </select>
            </div>

            <!-- Importo -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Importo *</label>
                <input type="number" name="importo" step="0.01" required value="{{ $pagamento->importo }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent">
            </div>

            <!-- Importo Pagato -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Importo Pagato</label>
                <input type="number" name="importo_pagato" step="0.01" value="{{ $pagamento->importo_pagato }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent">
            </div>

            <!-- Metodo -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Metodo Pagamento *</label>
                <select name="metodo" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent">
                    <option value="contanti" {{ $pagamento->metodo == 'contanti' ? 'selected' : '' }}>Contanti</option>
                    <option value="bonifico" {{ $pagamento->metodo == 'bonifico' ? 'selected' : '' }}>Bonifico</option>
                    <option value="carta" {{ $pagamento->metodo == 'carta' ? 'selected' : '' }}>Carta</option>
                    <option value="pos" {{ $pagamento->metodo == 'pos' ? 'selected' : '' }}>POS</option>
                    <option value="paypal" {{ $pagamento->metodo == 'paypal' ? 'selected' : '' }}>PayPal</option>
                    <option value="satispay" {{ $pagamento->metodo == 'satispay' ? 'selected' : '' }}>Satispay</option>
                    <option value="altro" {{ $pagamento->metodo == 'altro' ? 'selected' : '' }}>Altro</option>
                </select>
            </div>

            <!-- Stato -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Stato *</label>
                <select name="stato" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent">
                    <option value="in_attesa" {{ $pagamento->stato == 'in_attesa' ? 'selected' : '' }}>In Attesa</option>
                    <option value="parziale" {{ $pagamento->stato == 'parziale' ? 'selected' : '' }}>Parziale</option>
                    <option value="completato" {{ $pagamento->stato == 'completato' ? 'selected' : '' }}>Completato</option>
                    <option value="scaduto" {{ $pagamento->stato == 'scaduto' ? 'selected' : '' }}>Scaduto</option>
                    <option value="cancellato" {{ $pagamento->stato == 'cancellato' ? 'selected' : '' }}>Cancellato</option>
                </select>
            </div>

            <!-- Data Emissione -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Data Emissione *</label>
                <input type="date" name="data_emissione" required value="{{ $pagamento->data_emissione->format('Y-m-d') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent">
            </div>

            <!-- Data Pagamento -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Data Pagamento</label>
                <input type="date" name="data_pagamento" value="{{ $pagamento->data_pagamento?->format('Y-m-d') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent">
            </div>
        </div>

        <div class="mt-6 flex justify-end space-x-3">
            <a href="{{ route('admin.pagamenti.show', $pagamento->id) }}" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                Annulla
            </a>
            <button type="submit" class="px-4 py-2 bg-fucsia-magia text-white rounded-lg hover:bg-viola-magia">
                <i class="fas fa-save mr-2"></i> Salva Modifiche
            </button>
        </div>
    </form>
</div>
@endsection
