@extends('layouts.admin')

@section('titolo', 'Schede Allenamento')

@section('contenuto')
<div class="p-6">
    
    <!-- Header con Azioni -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Schede Allenamento Personalizzate</h2>
            <p class="text-gray-600 mt-1">Gestisci i programmi di allenamento delle clienti</p>
        </div>
        <div class="mt-4 md:mt-0">
            <a href="{{ route('admin.schede.create') }}" 
               class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-viola-magia to-fucsia-magia text-white font-semibold rounded-lg hover:shadow-lg transition transform hover:-translate-y-0.5">
                <i class="fas fa-dumbbell mr-2"></i>
                Nuova Scheda
            </a>
        </div>
    </div>

    <!-- Statistiche Rapide -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-green-500">
            <p class="text-sm text-gray-600 font-medium">Attive</p>
            <p class="text-2xl font-bold text-gray-800">{{ $schede->where('stato', 'attiva')->count() }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-yellow-500">
            <p class="text-sm text-gray-600 font-medium">Bozze</p>
            <p class="text-2xl font-bold text-gray-800">{{ $schede->where('stato', 'bozza')->count() }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-blue-500">
            <p class="text-sm text-gray-600 font-medium">Completate</p>
            <p class="text-2xl font-bold text-gray-800">{{ $schede->where('stato', 'completata')->count() }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-gray-400">
            <p class="text-sm text-gray-600 font-medium">Archiviate</p>
            <p class="text-2xl font-bold text-gray-800">{{ $schede->where('stato', 'archiviata')->count() }}</p>
        </div>
    </div>

    <!-- Filtri -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <form method="GET" action="{{ route('admin.schede.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            
            <!-- Filtro Cliente -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-user mr-1"></i> Cliente
                </label>
                <select name="cliente_id" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-fucsia-magia">
                    <option value="">Tutte</option>
                    @foreach($clienti as $cliente)
                        <option value="{{ $cliente->id }}" {{ request('cliente_id') == $cliente->id ? 'selected' : '' }}>
                            {{ $cliente->cognome }} {{ $cliente->nome }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Filtro Stato -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-filter mr-1"></i> Stato
                </label>
                <select name="stato" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-fucsia-magia">
                    <option value="">Tutti</option>
                    <option value="bozza" {{ request('stato') == 'bozza' ? 'selected' : '' }}>Bozza</option>
                    <option value="attiva" {{ request('stato') == 'attiva' ? 'selected' : '' }}>Attiva</option>
                    <option value="completata" {{ request('stato') == 'completata' ? 'selected' : '' }}>Completata</option>
                    <option value="archiviata" {{ request('stato') == 'archiviata' ? 'selected' : '' }}>Archiviata</option>
                </select>
            </div>

            <!-- Filtro Professionista -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-user-md mr-1"></i> Professionista
                </label>
                <select name="professionista_id" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-fucsia-magia">
                    <option value="">Tutti</option>
                    @foreach($professionisti as $prof)
                        <option value="{{ $prof->id }}" {{ request('professionista_id') == $prof->id ? 'selected' : '' }}>
                            {{ $prof->nome }} {{ $prof->cognome }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Pulsante Filtra -->
            <div class="flex items-end">
                <button type="submit" class="w-full px-4 py-2 bg-fucsia-magia text-white rounded-lg hover:bg-opacity-90">
                    <i class="fas fa-search mr-2"></i> Filtra
                </button>
            </div>
        </form>
    </div>

    <!-- Tabella Schede -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        @if($schede->count() > 0)
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cliente</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Scheda</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Periodo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stato</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Esercizi</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Azioni</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($schede as $scheda)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $scheda->cliente->cognome }} {{ $scheda->cliente->nome }}
                                        </div>
                                        <div class="text-sm text-gray-500">{{ $scheda->cliente->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $scheda->nome_scheda }}</div>
                                @if($scheda->descrizione)
                                    <div class="text-sm text-gray-500">{{ Str::limit($scheda->descrizione, 50) }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                @if($scheda->data_inizio)
                                    {{ $scheda->data_inizio->format('d/m/Y') }}
                                @endif
                                @if($scheda->data_fine)
                                    <br>→ {{ $scheda->data_fine->format('d/m/Y') }}
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $badges = [
                                        'bozza' => 'bg-gray-100 text-gray-800',
                                        'attiva' => 'bg-green-100 text-green-800',
                                        'completata' => 'bg-blue-100 text-blue-800',
                                        'archiviata' => 'bg-gray-100 text-gray-600'
                                    ];
                                @endphp
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $badges[$scheda->stato] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ ucfirst($scheda->stato) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $scheda->numeroEserciziTotali() }} esercizi<br>
                                <span class="text-xs">{{ $scheda->numeroGiorniAllenamento() }} giorni</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('admin.schede.show', $scheda) }}" class="text-blue-600 hover:text-blue-900 mr-3" title="Visualizza">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.schede.edit', $scheda) }}" class="text-indigo-600 hover:text-indigo-900 mr-3" title="Modifica">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.schede.destroy', $scheda) }}" method="POST" class="inline" onsubmit="return confirm('Sicuro di eliminare questa scheda?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900" title="Elimina">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Paginazione -->
            <div class="px-6 py-4 border-t">
                {{ $schede->links() }}
            </div>
        @else
            <div class="p-8 text-center text-gray-500">
                <i class="fas fa-dumbbell text-6xl mb-4 text-gray-300"></i>
                <p class="text-lg">Nessuna scheda trovata</p>
                <a href="{{ route('admin.schede.create') }}" class="mt-4 inline-block text-fucsia-magia hover:underline">
                    Crea la prima scheda
                </a>
            </div>
        @endif
    </div>

</div>
@endsection
