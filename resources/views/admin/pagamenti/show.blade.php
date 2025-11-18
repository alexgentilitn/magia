@extends('layouts.admin')

@section('titolo', 'Dettaglio Pagamento')

@section('contenuto')
<div class="p-6">
    <div class="mb-6">
        <a href="{{ route('admin.pagamenti.index') }}" class="text-fucsia-magia hover:text-viola-magia mb-2 inline-block">
            <i class="fas fa-arrow-left mr-2"></i> Torna alla lista
        </a>
        <h1 class="text-3xl font-bold text-gray-800">Dettaglio Pagamento #{{ $pagamento->id }}</h1>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Info Principale -->
        <div class="lg:col-span-2 bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold mb-4">Informazioni Pagamento</h2>
            
            <dl class="grid grid-cols-2 gap-4">
                <div>
                    <dt class="text-sm text-gray-600">Cliente</dt>
                    <dd class="text-base font-medium">{{ $pagamento->cliente->nome }} {{ $pagamento->cliente->cognome }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-gray-600">Oggetto</dt>
                    <dd class="text-base font-medium">{{ $pagamento->descrizione_oggetto }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-gray-600">Importo Totale</dt>
                    <dd class="text-xl font-bold text-gray-800">€ {{ number_format($pagamento->importo, 2) }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-gray-600">Importo Pagato</dt>
                    <dd class="text-xl font-bold text-green-600">€ {{ number_format($pagamento->importo_pagato, 2) }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-gray-600">Stato</dt>
                    <dd><span class="px-3 py-1 rounded-full {{ $pagamento->badge_stato }}">{{ ucfirst($pagamento->stato) }}</span></dd>
                </div>
                <div>
                    <dt class="text-sm text-gray-600">Metodo</dt>
                    <dd><span class="px-3 py-1 rounded {{ $pagamento->badge_metodo }}"><i class="fas {{ $pagamento->icona_metodo }} mr-1"></i>{{ ucfirst($pagamento->metodo) }}</span></dd>
                </div>
                <div>
                    <dt class="text-sm text-gray-600">Data Emissione</dt>
                    <dd>{{ $pagamento->data_emissione->format('d/m/Y') }}</dd>
                </div>
                @if($pagamento->data_scadenza)
                <div>
                    <dt class="text-sm text-gray-600">Data Scadenza</dt>
                    <dd>{{ $pagamento->data_scadenza->format('d/m/Y') }}</dd>
                </div>
                @endif
            </dl>
        </div>

        <!-- Azioni Rapide -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold mb-4">Azioni</h2>
            <div class="space-y-2">
                <a href="{{ route('admin.pagamenti.edit', $pagamento->id) }}" class="block w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-center">
                    <i class="fas fa-edit mr-2"></i> Modifica
                </a>
                
                @if($pagamento->stato !== 'completato')
                <form method="POST" action="{{ route('admin.pagamenti.marca-completato', $pagamento->id) }}" class="block">
                    @csrf
                    <input type="hidden" name="data_pagamento" value="{{ now()->format('Y-m-d') }}">
                    <input type="hidden" name="metodo" value="{{ $pagamento->metodo }}">
                    <button type="submit" class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                        <i class="fas fa-check mr-2"></i> Marca Completato
                    </button>
                </form>
                @endif
                
                <form method="POST" action="{{ route('admin.pagamenti.destroy', $pagamento->id) }}" class="block" id="delete-pagamento-form">
                    @csrf
                    @method('DELETE')
                    <button type="button" onclick="confermaEliminazione('delete-pagamento-form', 'Eliminare il pagamento?', 'Il pagamento di €{{ number_format($pagamento->importo, 2) }} sarà eliminato definitivamente.')" class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                        <i class="fas fa-trash mr-2"></i> Elimina
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
