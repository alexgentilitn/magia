@extends('layouts.admin')

@section('titolo', 'Nuovo Pagamento')

@section('contenuto')
<div class="p-6">
    <div class="mb-6">
        <a href="{{ route('admin.pagamenti.index') }}" class="text-fucsia-magia hover:text-viola-magia mb-2 inline-block">
            <i class="fas fa-arrow-left mr-2"></i> Torna alla lista
        </a>
        <h1 class="text-3xl font-bold text-gray-800">Nuovo Pagamento</h1>
    </div>

    <form method="POST" action="{{ route('admin.pagamenti.store') }}" class="bg-white rounded-lg shadow p-6">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Cliente -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Cliente *</label>
                <select name="cliente_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent">
                    <option value="">Seleziona cliente</option>
                    @foreach($clienti as $cliente)
                    <option value="{{ $cliente->id }}" {{ old('cliente_id', $clienteSelezionato) == $cliente->id ? 'selected' : '' }}>
                        {{ $cliente->nome }} {{ $cliente->cognome }}
                    </option>
                    @endforeach
                </select>
            </div>

            <!-- Tipo -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tipo *</label>
                <select name="tipo" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent">
                    <option value="programma">Programma</option>
                    <option value="lezione">Lezione</option>
                    <option value="abbonamento">Abbonamento</option>
                    <option value="altro">Altro</option>
                </select>
            </div>

            <!-- Importo -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Importo *</label>
                <input type="number" name="importo" step="0.01" required value="{{ old('importo') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent">
            </div>

            <!-- Metodo -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Metodo Pagamento *</label>
                <select name="metodo" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent">
                    <option value="contanti">Contanti</option>
                    <option value="bonifico">Bonifico</option>
                    <option value="carta">Carta</option>
                    <option value="pos">POS</option>
                    <option value="paypal">PayPal</option>
                    <option value="satispay">Satispay</option>
                    <option value="altro">Altro</option>
                </select>
            </div>

            <!-- Data Emissione -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Data Emissione *</label>
                <input type="date" name="data_emissione" required value="{{ old('data_emissione', now()->format('Y-m-d')) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent">
            </div>

            <!-- Stato -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Stato *</label>
                <select name="stato" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent">
                    <option value="in_attesa">In Attesa</option>
                    <option value="completato">Completato</option>
                    <option value="parziale">Parziale</option>
                </select>
            </div>
        </div>

        <div class="mt-6 flex justify-end space-x-3">
            <a href="{{ route('admin.pagamenti.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                Annulla
            </a>
            <button type="submit" class="px-4 py-2 bg-fucsia-magia text-white rounded-lg hover:bg-viola-magia">
                <i class="fas fa-save mr-2"></i> Salva Pagamento
            </button>
        </div>
    </form>
</div>
@endsection
