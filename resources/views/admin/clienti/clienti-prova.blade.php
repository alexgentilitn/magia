@extends('layouts.admin')

@section('titolo', 'Clienti Giornata di Prova')

@section('contenuto')
<div class="p-6">
    
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Clienti Giornata di Prova</h2>
            <p class="text-gray-600 mt-1">Gestisci le iscrizioni alla giornata di prova gratuita</p>
        </div>
        <div class="mt-4 md:mt-0">
            <a href="{{ route('admin.clienti.index') }}" 
               class="inline-flex items-center px-6 py-3 bg-gray-200 text-gray-700 font-semibold rounded-lg hover:bg-gray-300 transition">
                <i class="fas fa-arrow-left mr-2"></i>
                Torna a Clienti
            </a>
        </div>
    </div>

    <!-- Statistiche Rapide -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-blue-500">
            <p class="text-sm text-gray-600 font-medium">Totale Clienti Prova</p>
            <p class="text-2xl font-bold text-gray-800">{{ $stats['totale'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-green-500">
            <p class="text-sm text-gray-600 font-medium">Ultimi 7 Giorni</p>
            <p class="text-2xl font-bold text-gray-800">{{ $stats['ultimi_7_giorni'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-yellow-500">
            <p class="text-sm text-gray-600 font-medium">Da Convertire</p>
            <p class="text-2xl font-bold text-gray-800">{{ $stats['da_convertire'] }}</p>
        </div>
    </div>

    <!-- Tabella Clienti Prova -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        
        @if(session('success'))
            <div class="bg-green-50 border-l-4 border-green-500 p-4 m-6">
                <div class="flex items-center">
                    <i class="fas fa-check-circle text-green-500 text-xl mr-3"></i>
                    <p class="text-green-700">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-50 border-l-4 border-red-500 p-4 m-6">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle text-red-500 text-xl mr-3"></i>
                    <p class="text-red-700">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        @if($clienti_prova->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gradient-to-r from-viola-magia to-fucsia-magia">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Cliente</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Contatti</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Sede Preferita</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Data Iscrizione</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Stato</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold text-white uppercase tracking-wider">Azioni</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($clienti_prova as $cliente)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-gradient-to-r from-viola-magia to-fucsia-magia flex items-center justify-center text-white font-bold">
                                                {{ strtoupper(substr($cliente->nome, 0, 1)) }}{{ strtoupper(substr($cliente->cognome, 0, 1)) }}
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-semibold text-gray-900">{{ $cliente->nome_completo }}</div>
                                            <div class="text-xs text-gray-500">ID: {{ $cliente->codice_cliente }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">
                                        <i class="fas fa-envelope text-gray-400 mr-1"></i>
                                        {{ $cliente->email }}
                                    </div>
                                    @if($cliente->telefono_mobile)
                                        <div class="text-sm text-gray-500 mt-1">
                                            <i class="fas fa-phone text-gray-400 mr-1"></i>
                                            {{ $cliente->telefono_mobile }}
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if($cliente->sedePreferita)
                                        <span class="text-sm text-gray-900">{{ $cliente->sedePreferita->nome }}</span>
                                    @else
                                        <span class="text-sm text-gray-400 italic">Non specificata</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">
                                        {{ $cliente->data_iscrizione ? $cliente->data_iscrizione->format('d/m/Y') : '-' }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ $cliente->created_at->diffForHumans() }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                        <i class="fas fa-user-clock mr-1"></i>
                                        Cliente di Prova
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center space-x-2">
                                        
                                        <!-- Visualizza -->
                                        <a href="{{ route('admin.clienti.show', $cliente->id) }}" 
                                           class="text-blue-600 hover:text-blue-800 transition"
                                           title="Visualizza Dettagli">
                                            <i class="fas fa-eye text-lg"></i>
                                        </a>

                                        <!-- Converti in Cliente Effettiva -->
                                        <form action="{{ route('admin.clienti.converti', $cliente->id) }}" 
                                              method="POST" 
                                              class="inline"
                                              onsubmit="return confirm('Sei sicuro di voler convertire questa cliente in cliente effettiva? L\'account verrà attivato.');">
                                            @csrf
                                            <button type="submit" 
                                                    class="text-green-600 hover:text-green-800 transition"
                                                    title="Converti in Cliente Effettiva">
                                                <i class="fas fa-user-check text-lg"></i>
                                            </button>
                                        </form>

                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Paginazione -->
            @if($clienti_prova->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $clienti_prova->links() }}
                </div>
            @endif

        @else
            <div class="p-12 text-center">
                <i class="fas fa-users text-6xl text-gray-300 mb-4"></i>
                <p class="text-gray-500 text-lg">Nessun cliente di prova registrato</p>
                <p class="text-gray-400 text-sm mt-2">Quando qualcuno si registra alla giornata di prova, apparirà qui</p>
            </div>
        @endif

    </div>

</div>
@endsection
