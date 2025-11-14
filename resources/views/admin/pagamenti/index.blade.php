@extends('layouts.admin')

@section('titolo', 'Gestione Pagamenti')

@section('contenuto')
<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">💰 Gestione Pagamenti</h1>
        <a href="{{ route('admin.pagamenti.create') }}" class="px-4 py-2 bg-fucsia-magia text-white rounded-lg hover:bg-viola-magia transition">
            <i class="fas fa-plus mr-2"></i> Nuovo Pagamento
        </a>
    </div>

    <!-- Statistiche -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-sm text-gray-600">Totale Pagamenti</p>
            <p class="text-2xl font-bold text-gray-800">{{ $statistiche['totale'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-sm text-gray-600">In Attesa</p>
            <p class="text-2xl font-bold text-yellow-600">{{ $statistiche['in_attesa'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-sm text-gray-600">Completati</p>
            <p class="text-2xl font-bold text-green-600">{{ $statistiche['completati'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-sm text-gray-600">Importo Incassato</p>
            <p class="text-2xl font-bold text-gray-800">€ {{ number_format($statistiche['importo_totale'], 2) }}</p>
        </div>
    </div>

    <!-- Lista Pagamenti -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-4 border-b">
            <h2 class="text-lg font-semibold">Elenco Pagamenti</h2>
        </div>
        
        @if($pagamenti->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cliente</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Oggetto</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Importo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stato</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Data</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Azioni</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($pagamenti as $pagamento)
                    <tr>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ $pagamento->cliente->nome }} {{ $pagamento->cliente->cognome }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $pagamento->descrizione_oggetto }}</td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">€ {{ number_format($pagamento->importo, 2) }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs rounded-full {{ $pagamento->badge_stato }}">{{ ucfirst($pagamento->stato) }}</span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $pagamento->data_emissione->format('d/m/Y') }}</td>
                        <td class="px-6 py-4 text-center">
                            <a href="{{ route('admin.pagamenti.show', $pagamento->id) }}" class="text-blue-600 hover:text-blue-800 mr-3">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.pagamenti.edit', $pagamento->id) }}" class="text-green-600 hover:text-green-800">
                                <i class="fas fa-edit"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="p-4 border-t">
            {{ $pagamenti->links() }}
        </div>
        @else
        <div class="p-12 text-center">
            <i class="fas fa-euro-sign text-gray-400 text-5xl mb-4"></i>
            <p class="text-gray-500 font-medium">Nessun pagamento registrato</p>
            <a href="{{ route('admin.pagamenti.create') }}" class="text-fucsia-magia hover:text-viola-magia mt-2 inline-block">
                <i class="fas fa-plus-circle mr-1"></i> Registra primo pagamento
            </a>
        </div>
        @endif
    </div>
</div>
@endsection
