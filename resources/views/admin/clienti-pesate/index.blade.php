@extends('layouts.admin')

@section('titolo', 'Clienti con Pesate')

@section('contenuto')
<div class="p-6">

    <!-- Header -->
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Clienti con Pesate</h2>
        <p class="text-gray-600 mt-1">Visualizza tutti i clienti e le loro pesate</p>
    </div>

    <!-- Statistiche Rapide -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-viola-magia">
            <p class="text-sm text-gray-600 font-medium">Totale Clienti</p>
            <p class="text-2xl font-bold text-gray-800">{{ $statistiche['totale'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-green-500">
            <p class="text-sm text-gray-600 font-medium">Con Pesate</p>
            <p class="text-2xl font-bold text-gray-800">{{ $statistiche['con_pesate'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-orange-500">
            <p class="text-sm text-gray-600 font-medium">Senza Pesate</p>
            <p class="text-2xl font-bold text-gray-800">{{ $statistiche['senza_pesate'] }}</p>
        </div>
    </div>

    <!-- Filtri e Ricerca -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <form method="GET" action="{{ route('admin.clienti-pesate.index') }}" class="flex gap-4">

            <!-- Ricerca -->
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-search mr-1"></i> Cerca Cliente
                </label>
                <input
                    type="text"
                    name="cerca"
                    value="{{ request('cerca') }}"
                    placeholder="Nome, cognome, email..."
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent"
                >
            </div>

            <!-- Pulsante Cerca -->
            <div class="flex items-end">
                <button type="submit"
                        class="px-6 py-2 bg-fucsia-magia text-white rounded-lg hover:bg-viola-magia transition">
                    <i class="fas fa-search mr-2"></i> Cerca
                </button>
            </div>

            <!-- Reset -->
            @if(request('cerca'))
            <div class="flex items-end">
                <a href="{{ route('admin.clienti-pesate.index') }}"
                   class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                    <i class="fas fa-times mr-2"></i> Reset
                </a>
            </div>
            @endif
        </form>
    </div>

    <!-- Tabella Clienti -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Cliente
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Email
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Telefono
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Pesate
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Ultima Pesata
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Azioni
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($clienti as $cliente)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10 bg-gradient-to-br from-fucsia-magia to-viola-magia rounded-full flex items-center justify-center text-white font-bold">
                                    {{ strtoupper(substr($cliente->nome, 0, 1)) }}{{ strtoupper(substr($cliente->cognome, 0, 1)) }}
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $cliente->cognome }} {{ $cliente->nome }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        ID: {{ $cliente->id }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $cliente->email ?? 'N/D' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $cliente->telefono ?? 'N/D' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @if($cliente->totale_pesate > 0)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-green-100 text-green-800">
                                    <i class="fas fa-weight mr-1"></i> {{ $cliente->totale_pesate }}
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-gray-100 text-gray-600">
                                    0
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($cliente->ultima_pesata)
                                <div class="text-sm text-gray-900">
                                    <i class="fas fa-calendar-alt mr-1 text-fucsia-magia"></i>
                                    {{ $cliente->ultima_pesata->format('d/m/Y') }}
                                </div>
                            @else
                                <span class="text-sm text-gray-400">Mai pesata</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('admin.clienti.pesate.index', $cliente->id) }}"
                               class="inline-flex items-center px-3 py-1 bg-fucsia-magia text-white rounded-lg hover:bg-viola-magia transition">
                                <i class="fas fa-weight mr-1"></i> Vedi Pesate
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                            <i class="fas fa-users text-4xl mb-2 text-gray-300"></i>
                            <p>Nessun cliente trovato</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
