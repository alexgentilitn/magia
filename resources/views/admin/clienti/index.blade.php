@extends('layouts.admin')

@section('titolo', 'Gestione Clienti')

@section('contenuto')
<div class="p-6">
    
    <!-- Header con Azioni -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Gestione Clienti</h2>
            <p class="text-gray-600 mt-1">Gestisci l'anagrafica completa delle clienti</p>
        </div>
        <div class="mt-4 md:mt-0">
            <a href="{{ route('admin.clienti.create') }}" 
               class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-viola-magia to-fucsia-magia text-white font-semibold rounded-lg hover:shadow-lg transition transform hover:-translate-y-0.5">
                <i class="fas fa-user-plus mr-2"></i>
                Nuova Cliente
            </a>
        </div>
    </div>

    <!-- Statistiche Rapide -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-viola-magia">
            <p class="text-sm text-gray-600 font-medium">Totale</p>
            <p class="text-2xl font-bold text-gray-800">{{ $statistiche['totale'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-green-500">
            <p class="text-sm text-gray-600 font-medium">Attive</p>
            <p class="text-2xl font-bold text-gray-800">{{ $statistiche['attive'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-yellow-500">
            <p class="text-sm text-gray-600 font-medium">Sospese</p>
            <p class="text-2xl font-bold text-gray-800">{{ $statistiche['sospese'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-gray-400">
            <p class="text-sm text-gray-600 font-medium">Inattive</p>
            <p class="text-2xl font-bold text-gray-800">{{ $statistiche['inattive'] }}</p>
        </div>
    </div>

    <!-- Filtri e Ricerca -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <form method="GET" action="{{ route('admin.clienti.index') }}" class="space-y-4">
            
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                
                <!-- Ricerca Generale -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-search mr-1"></i> Cerca Cliente
                    </label>
                    <input 
                        type="text" 
                        name="ricerca" 
                        value="{{ request('ricerca') }}"
                        placeholder="Nome, email, CF, telefono..."
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent"
                    >
                </div>

                <!-- Filtro Stato -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-filter mr-1"></i> Stato
                    </label>
                    <select 
                        name="stato" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent"
                    >
                        <option value="">Tutti</option>
                        <option value="attivo" {{ request('stato') == 'attivo' ? 'selected' : '' }}>Attivo</option>
                        <option value="sospeso" {{ request('stato') == 'sospeso' ? 'selected' : '' }}>Sospeso</option>
                        <option value="inattivo" {{ request('stato') == 'inattivo' ? 'selected' : '' }}>Inattivo</option>
                    </select>
                </div>

                <!-- Filtro Programma -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-dumbbell mr-1"></i> Programma
                    </label>
                    <select 
                        name="programma" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent"
                    >
                        <option value="">Tutti</option>
                        <option value="balla_snella" {{ request('programma') == 'balla_snella' ? 'selected' : '' }}>Balla & Snella</option>
                        <option value="offerta_magia" {{ request('programma') == 'offerta_magia' ? 'selected' : '' }}>Offerta MA.GIA</option>
                    </select>
                </div>

            </div>

            <!-- Bottoni Azione -->
            <div class="flex items-center gap-2">
                <button 
                    type="submit"
                    class="px-6 py-2 bg-fucsia-magia text-white font-medium rounded-lg hover:bg-viola-magia transition"
                >
                    <i class="fas fa-search mr-2"></i> Cerca
                </button>
                <a 
                    href="{{ route('admin.clienti.index') }}"
                    class="px-6 py-2 bg-gray-200 text-gray-700 font-medium rounded-lg hover:bg-gray-300 transition"
                >
                    <i class="fas fa-redo mr-2"></i> Reset
                </a>
                <div class="ml-auto flex gap-2">
                    <button 
                        type="button"
                        class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition"
                    >
                        <i class="fas fa-file-excel mr-2"></i> Excel
                    </button>
                    <button 
                        type="button"
                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition"
                    >
                        <i class="fas fa-file-pdf mr-2"></i> PDF
                    </button>
                </div>
            </div>

        </form>
    </div>

    <!-- Tabella Clienti Desktop -->
    <div class="bg-white rounded-lg shadow overflow-hidden hidden md:block">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cliente</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Contatti</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Programma</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stato</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Iscrizione</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Azioni</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($clienti as $cliente)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="h-10 w-10 flex-shrink-0">
                                    <div class="h-10 w-10 rounded-full bg-gradient-to-r from-viola-magia to-fucsia-magia flex items-center justify-center text-white font-bold">
                                        {{ substr($cliente->nome, 0, 1) }}{{ substr($cliente->cognome, 0, 1) }}
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $cliente->nomeCompleto }}</div>
                                    <div class="text-sm text-gray-500">CF: {{ $cliente->codice_fiscale }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">{{ $cliente->email }}</div>
                            <div class="text-sm text-gray-500">{{ $cliente->telefono_mobile }}</div>
                        </td>
                        <td class="px-6 py-4">
                            @if($cliente->programma_attuale)
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                    {{ ucfirst(str_replace('_', ' ', $cliente->programma_attuale)) }}
                                </span>
                            @else
                                <span class="text-sm text-gray-400">Nessuno</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($cliente->stato_cliente === 'attivo')
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle mr-1"></i> Attivo
                                </span>
                            @elseif($cliente->stato_cliente === 'sospeso')
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                    <i class="fas fa-pause-circle mr-1"></i> Sospeso
                                </span>
                            @else
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                    <i class="fas fa-times-circle mr-1"></i> Inattivo
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ $cliente->created_at->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4 text-right text-sm font-medium space-x-2">
                            <a href="{{ route('admin.clienti.show', $cliente->id) }}" 
                               class="text-blue-600 hover:text-blue-900" title="Visualizza">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.clienti.edit', $cliente->id) }}" 
                               class="text-fucsia-magia hover:text-viola-magia" title="Modifica">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form method="POST" action="{{ route('admin.clienti.destroy', $cliente->id) }}" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        onclick="return confirm('Sei sicuro di voler eliminare questa cliente?')"
                                        class="text-red-600 hover:text-red-900" title="Elimina">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <i class="fas fa-inbox text-gray-400 text-5xl mb-4"></i>
                            <p class="text-gray-500 font-medium">Nessuna cliente trovata</p>
                            <a href="{{ route('admin.clienti.create') }}" class="text-fucsia-magia hover:text-viola-magia mt-2 inline-block">
                                <i class="fas fa-plus-circle mr-1"></i> Aggiungi la prima cliente
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginazione -->
        @if($clienti->hasPages())
        <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
            {{ $clienti->links() }}
        </div>
        @endif
    </div>

    <!-- Card Clienti Mobile -->
    <div class="md:hidden space-y-4">
        @forelse($clienti as $cliente)
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-start justify-between mb-3">
                <div class="flex items-center">
                    <div class="h-12 w-12 rounded-full bg-gradient-to-r from-viola-magia to-fucsia-magia flex items-center justify-center text-white font-bold mr-3">
                        {{ substr($cliente->nome, 0, 1) }}{{ substr($cliente->cognome, 0, 1) }}
                    </div>
                    <div>
                        <p class="font-semibold text-gray-800">{{ $cliente->nomeCompleto }}</p>
                        <p class="text-sm text-gray-500">{{ $cliente->email }}</p>
                    </div>
                </div>
                @if($cliente->stato_cliente === 'attivo')
                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                        Attivo
                    </span>
                @endif
            </div>
            <div class="space-y-2 text-sm mb-3">
                <p class="text-gray-600">
                    <i class="fas fa-phone text-fucsia-magia mr-2"></i>{{ $cliente->telefono_mobile }}
                </p>
                <p class="text-gray-600">
                    <i class="fas fa-dumbbell text-fucsia-magia mr-2"></i>
                    {{ $cliente->programma_attuale ? ucfirst(str_replace('_', ' ', $cliente->programma_attuale)) : 'Nessun programma' }}
                </p>
                <p class="text-gray-600">
                    <i class="fas fa-calendar text-fucsia-magia mr-2"></i>Iscrizione: {{ $cliente->created_at->format('d/m/Y') }}
                </p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.clienti.show', $cliente->id) }}" 
                   class="flex-1 px-4 py-2 bg-blue-500 text-white text-center rounded-lg text-sm font-medium">
                    <i class="fas fa-eye mr-1"></i> Vedi
                </a>
                <a href="{{ route('admin.clienti.edit', $cliente->id) }}" 
                   class="flex-1 px-4 py-2 bg-fucsia-magia text-white text-center rounded-lg text-sm font-medium">
                    <i class="fas fa-edit mr-1"></i> Modifica
                </a>
            </div>
        </div>
        @empty
        <div class="bg-white rounded-lg shadow p-8 text-center">
            <i class="fas fa-inbox text-gray-400 text-5xl mb-4"></i>
            <p class="text-gray-500 font-medium mb-4">Nessuna cliente trovata</p>
            <a href="{{ route('admin.clienti.create') }}" 
               class="inline-block px-6 py-3 bg-gradient-to-r from-viola-magia to-fucsia-magia text-white font-semibold rounded-lg">
                <i class="fas fa-plus-circle mr-2"></i> Aggiungi Cliente
            </a>
        </div>
        @endforelse

        @if($clienti->hasPages())
        <div class="bg-white rounded-lg shadow p-4">
            {{ $clienti->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
