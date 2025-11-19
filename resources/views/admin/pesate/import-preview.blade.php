@extends('layouts.admin')

@section('titolo', 'Anteprima Importazione Pesate')

@section('contenuto')
<div class="p-6">

    <!-- Header -->
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Anteprima Importazione Pesate</h2>
        <p class="text-gray-600 mt-1">Controlla i dati prima di confermare l'importazione</p>
    </div>

    <!-- Statistiche -->
    @php
        $totale = count($preview_data);
        $valide = count(array_filter($preview_data, fn($r) => empty($r['errors'])));
        $errori = $totale - $valide;
        $dataOggi = now()->format('d/m/Y');
    @endphp

    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-blue-500">
            <p class="text-sm text-gray-600 font-medium">Totale Righe</p>
            <p class="text-2xl font-bold text-gray-800">{{ $totale }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-green-500">
            <p class="text-sm text-gray-600 font-medium">Righe Valide</p>
            <p class="text-2xl font-bold text-gray-800">{{ $valide }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-red-500">
            <p class="text-sm text-gray-600 font-medium">Righe con Errori</p>
            <p class="text-2xl font-bold text-gray-800">{{ $errori }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-fucsia-magia">
            <p class="text-sm text-gray-600 font-medium">Sede</p>
            <p class="text-xl font-bold text-gray-800">{{ $sede }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-orange-500">
            <p class="text-sm text-gray-600 font-medium">Data Rilevazione</p>
            <p class="text-lg font-bold text-gray-800">{{ $dataOggi }}</p>
        </div>
    </div>

    <!-- Riepilogo Clienti -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <!-- Clienti Esistenti Trovati -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-4 bg-blue-50 border-b border-blue-200">
                <h3 class="font-bold text-blue-900 flex items-center">
                    <i class="fas fa-users text-blue-600 mr-2"></i>
                    Clienti Esistenti Trovati
                    <span class="ml-2 px-2 py-0.5 bg-blue-200 text-blue-800 text-xs rounded-full font-bold">
                        {{ count($clientiTrovati) }}
                    </span>
                </h3>
            </div>
            <div class="p-4">
                @if(count($clientiTrovati) > 0)
                    <div class="space-y-2 max-h-64 overflow-y-auto">
                        @foreach($clientiTrovati as $cliente)
                            <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg border border-blue-200 hover:bg-blue-100 transition">
                                <div class="flex items-center">
                                    <div class="h-10 w-10 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold mr-3">
                                        {{ strtoupper(substr($cliente['nome'], 0, 1) . substr($cliente['cognome'], 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $cliente['cognome'] }} {{ $cliente['nome'] }}</p>
                                        @if($cliente['codice_fiscale'])
                                            <p class="text-xs text-gray-500">CF: {{ $cliente['codice_fiscale'] }}</p>
                                        @endif
                                    </div>
                                </div>
                                <span class="px-3 py-1 bg-blue-200 text-blue-800 text-xs font-semibold rounded-full">
                                    {{ $cliente['pesate_count'] }} {{ $cliente['pesate_count'] == 1 ? 'pesata' : 'pesate' }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 text-gray-400">
                        <i class="fas fa-user-slash text-4xl mb-3"></i>
                        <p class="text-sm">Nessun cliente esistente trovato</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Clienti Nuovi Creati -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-4 bg-green-50 border-b border-green-200">
                <h3 class="font-bold text-green-900 flex items-center">
                    <i class="fas fa-user-plus text-green-600 mr-2"></i>
                    Clienti Nuovi Creati
                    <span class="ml-2 px-2 py-0.5 bg-green-200 text-green-800 text-xs rounded-full font-bold">
                        {{ count($clientiCreati) }}
                    </span>
                </h3>
            </div>
            <div class="p-4">
                @if(count($clientiCreati) > 0)
                    <div class="space-y-2 max-h-64 overflow-y-auto">
                        @foreach($clientiCreati as $cliente)
                            <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg border border-green-200 hover:bg-green-100 transition">
                                <div class="flex items-center">
                                    <div class="h-10 w-10 bg-green-500 rounded-full flex items-center justify-center text-white font-bold mr-3">
                                        {{ strtoupper(substr($cliente['nome'], 0, 1) . substr($cliente['cognome'], 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $cliente['cognome'] }} {{ $cliente['nome'] }}</p>
                                        @if($cliente['codice_fiscale'])
                                            <p class="text-xs text-gray-500">CF: {{ $cliente['codice_fiscale'] }}</p>
                                        @else
                                            <p class="text-xs text-gray-400 italic">Senza CF</p>
                                        @endif
                                    </div>
                                </div>
                                <span class="px-3 py-1 bg-green-200 text-green-800 text-xs font-semibold rounded-full">
                                    <i class="fas fa-star mr-1"></i>Nuovo
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 text-gray-400">
                        <i class="fas fa-check-circle text-4xl mb-3"></i>
                        <p class="text-sm">Tutti i clienti esistono già</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Info -->
    @if($valide > 0)
        <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded-lg">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-green-600 text-xl mr-3"></i>
                <p class="text-green-800">
                    <strong>{{ $valide }}</strong> pesate verranno importate.
                    @if($errori > 0)
                        <strong>{{ $errori }}</strong> righe verranno saltate a causa di errori.
                    @endif
                </p>
            </div>
        </div>
    @else
        <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-lg">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle text-red-600 text-xl mr-3"></i>
                <p class="text-red-800 font-medium">
                    Impossibile procedere: tutte le righe contengono errori. Correggi il file e riprova.
                </p>
            </div>
        </div>
    @endif

    <!-- Tabella Anteprima -->
    <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
        <div class="overflow-x-auto" style="max-height: 600px;">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50 sticky top-0">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Riga</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cliente</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Peso</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">BMI</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Grasso %</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Data</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stato</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($preview_data as $row)
                        <tr class="{{ !empty($row['errors']) ? 'bg-red-50' : 'hover:bg-gray-50' }}">
                            <td class="px-4 py-3 text-sm text-gray-900">{{ $row['row'] }}</td>
                            <td class="px-4 py-3 text-sm">
                                <div class="font-medium text-gray-900">
                                    {{ $row['cognome'] }} {{ $row['nome'] }}
                                    @if(!empty($row['cliente_creato']))
                                        <span class="ml-2 px-2 py-0.5 bg-green-100 text-green-700 text-xs rounded-full">
                                            <i class="fas fa-user-plus mr-1"></i>Nuovo
                                        </span>
                                    @endif
                                    @if(!empty($row['codice_fiscale']))
                                        <span class="ml-2 text-xs text-gray-500">CF: {{ $row['codice_fiscale'] }}</span>
                                    @endif
                                </div>
                                @if(!empty($row['errors']))
                                    <div class="text-xs text-red-600 mt-1">
                                        @foreach($row['errors'] as $error)
                                            <div><i class="fas fa-exclamation-triangle mr-1"></i>{{ $error }}</div>
                                        @endforeach
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900">{{ $row['peso'] ?? '-' }} kg</td>
                            <td class="px-4 py-3 text-sm text-gray-900">{{ $row['bmi'] ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900">{{ $row['grasso_corporeo'] ?? '-' }}%</td>
                            <td class="px-4 py-3 text-sm text-gray-900">{{ $row['data_rilevazione'] ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm">
                                @if(empty($row['errors']))
                                    <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">
                                        <i class="fas fa-check mr-1"></i>OK
                                    </span>
                                @else
                                    <span class="px-2 py-1 bg-red-100 text-red-800 text-xs font-semibold rounded-full">
                                        <i class="fas fa-times mr-1"></i>Errore
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Form Conferma -->
    @if($valide > 0)
        <form action="{{ route('admin.pesate.confirm-import') }}" method="POST">
            @csrf
            <input type="hidden" name="preview_data" value="{{ json_encode($preview_data) }}">
            <input type="hidden" name="sede" value="{{ $sede }}">

            <div class="flex gap-4">
                <button type="submit"
                        class="flex-1 px-6 py-3 bg-gradient-to-r from-green-500 to-green-600 text-white font-semibold rounded-lg hover:shadow-lg transition transform hover:-translate-y-0.5">
                    <i class="fas fa-check-circle mr-2"></i>
                    Conferma Importazione ({{ $valide }} pesate)
                </button>
                <a href="{{ route('admin.pesate.import') }}"
                   class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Torna Indietro
                </a>
            </div>
        </form>
    @else
        <div class="flex gap-4">
            <a href="{{ route('admin.pesate.import') }}"
               class="flex-1 px-6 py-3 bg-gray-200 text-gray-700 text-center rounded-lg hover:bg-gray-300 transition">
                <i class="fas fa-arrow-left mr-2"></i>
                Torna al Form di Upload
            </a>
        </div>
    @endif

</div>
@endsection
