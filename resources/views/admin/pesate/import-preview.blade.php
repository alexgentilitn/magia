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

    <!-- Form Conferma -->
    @if($valide > 0)
        <form action="{{ route('admin.pesate.confirm-import') }}" method="POST" id="importForm">
            @csrf
            <input type="hidden" name="sede" value="{{ $sede }}">
    @endif

    <!-- Tabella Anteprima Dettagliata -->
    <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
        <div class="p-4 bg-gray-50 border-b border-gray-200">
            <h3 class="font-bold text-gray-900 flex items-center">
                <i class="fas fa-table text-gray-600 mr-2"></i>
                Anteprima Dettagliata Dati
                <span class="ml-3 text-xs text-gray-500 font-normal">
                    <i class="fas fa-info-circle mr-1"></i>
                    I valori sono modificabili. In caso di omonimia, scegli l'azione appropriata.
                </span>
            </h3>
        </div>
        <div class="overflow-x-auto" style="max-height: 700px;">
            <table class="min-w-full divide-y divide-gray-200 text-xs">
                <thead class="bg-gray-50 sticky top-0 z-10">
                    <tr>
                        <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase w-12">Riga</th>
                        <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase min-w-[200px]">Cliente</th>
                        <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase w-20">Peso (kg)</th>
                        <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase w-20">BMI</th>
                        <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase w-20">Grasso %</th>
                        <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase w-20">Grasso Visc.</th>
                        <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase w-20">Muscolo %</th>
                        <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase w-20">Massa Musc.</th>
                        <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase w-20">Musc. Schel.</th>
                        <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase w-20">BMR (kcal)</th>
                        <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase w-20">Acqua %</th>
                        <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase w-20">Peso s/Grassi</th>
                        <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase w-20">Proteine %</th>
                        <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase w-20">Età Metab.</th>
                        <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase w-24">Data</th>
                        <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase min-w-[150px]">Azioni</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($preview_data as $index => $row)
                        <tr class="{{ !empty($row['errors']) ? 'bg-red-50' : (!empty($row['has_omonimia']) ? 'bg-yellow-50' : 'hover:bg-gray-50') }}">
                            <!-- Hidden fields per questa riga -->
                            <td style="display:none;">
                                <input type="hidden" name="row_number_{{ $index }}" value="{{ $row['row'] }}">
                                <input type="hidden" name="nome_{{ $index }}" value="{{ $row['nome'] }}">
                                <input type="hidden" name="cognome_{{ $index }}" value="{{ $row['cognome'] }}">
                                <input type="hidden" name="codice_fiscale_{{ $index }}" value="{{ $row['codice_fiscale'] ?? '' }}">
                                <input type="hidden" name="has_errors_{{ $index }}" value="{{ !empty($row['errors']) ? '1' : '0' }}">
                                <input type="hidden" name="has_omonimia_{{ $index }}" value="{{ !empty($row['has_omonimia']) ? '1' : '0' }}">
                            </td>

                            <!-- Riga -->
                            <td class="px-2 py-3 text-sm font-bold text-gray-900">{{ $row['row'] }}</td>

                            <!-- Cliente con Dati Anagrafici -->
                            <td class="px-2 py-3">
                                <div class="space-y-1">
                                    <div class="font-bold text-gray-900">
                                        {{ $row['cognome'] }} {{ $row['nome'] }}
                                    </div>

                                    <!-- Badges Status -->
                                    <div class="flex flex-wrap gap-1">
                                        @if(!empty($row['cliente_creato']))
                                            <span class="px-2 py-0.5 bg-green-100 text-green-700 text-xs rounded-full font-semibold">
                                                <i class="fas fa-user-plus mr-1"></i>Nuovo
                                            </span>
                                        @endif

                                        @if(!empty($row['has_omonimia']))
                                            <span class="px-2 py-0.5 bg-yellow-100 text-yellow-800 text-xs rounded-full font-semibold">
                                                <i class="fas fa-exclamation-triangle mr-1"></i>OMONIMIA
                                            </span>
                                        @endif
                                    </div>

                                    <!-- Dati Identificativi -->
                                    <div class="text-xs text-gray-600 space-y-0.5">
                                        @if(!empty($row['codice_fiscale']))
                                            <div><strong>CF:</strong> {{ $row['codice_fiscale'] }}</div>
                                        @endif
                                    </div>

                                    <!-- Gestione Omonimia -->
                                    @if(!empty($row['has_omonimia']) && !empty($row['possibili_clienti']))
                                        <div class="mt-2 p-2 bg-yellow-50 border border-yellow-200 rounded">
                                            <div class="font-semibold text-yellow-900 text-xs mb-2">
                                                <i class="fas fa-users mr-1"></i>
                                                {{ count($row['possibili_clienti']) }} clienti trovati con stesso nome:
                                            </div>
                                            <div class="space-y-1">
                                                @foreach($row['possibili_clienti'] as $pIndex => $possibileCliente)
                                                    <label class="flex items-start p-2 bg-white border border-gray-300 rounded hover:bg-gray-50 cursor-pointer">
                                                        <input type="radio"
                                                               name="cliente_id_{{ $index }}"
                                                               value="{{ $possibileCliente['id'] }}"
                                                               {{ $pIndex === 0 ? 'checked' : '' }}
                                                               class="mt-1 mr-2">
                                                        <div class="flex-1 text-xs">
                                                            <div class="font-semibold text-gray-900">
                                                                {{ $possibileCliente['cognome'] }} {{ $possibileCliente['nome'] }}
                                                            </div>
                                                            @if(!empty($possibileCliente['codice_fiscale']))
                                                                <div class="text-gray-600">CF: {{ $possibileCliente['codice_fiscale'] }}</div>
                                                            @endif
                                                            @if(!empty($possibileCliente['email']))
                                                                <div class="text-gray-600">Email: {{ $possibileCliente['email'] }}</div>
                                                            @endif
                                                            @if(!empty($possibileCliente['telefono']))
                                                                <div class="text-gray-600">Tel: {{ $possibileCliente['telefono'] }}</div>
                                                            @endif
                                                            @if(!empty($possibileCliente['data_iscrizione']))
                                                                <div class="text-gray-500">Iscrizione: {{ \Carbon\Carbon::parse($possibileCliente['data_iscrizione'])->format('d/m/Y') }}</div>
                                                            @endif
                                                        </div>
                                                    </label>
                                                @endforeach

                                                <!-- Opzione Crea Nuovo -->
                                                <label class="flex items-start p-2 bg-green-50 border-2 border-green-300 rounded hover:bg-green-100 cursor-pointer">
                                                    <input type="radio"
                                                           name="cliente_id_{{ $index }}"
                                                           value="new"
                                                           class="mt-1 mr-2">
                                                    <div class="flex-1 text-xs">
                                                        <div class="font-bold text-green-900">
                                                            <i class="fas fa-user-plus mr-1"></i>
                                                            Crea Nuovo Cliente
                                                        </div>
                                                        <div class="text-green-700">
                                                            Crea un nuovo profilo con lo stesso nome
                                                        </div>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Errori -->
                                    @if(!empty($row['errors']))
                                        <div class="mt-2 p-2 bg-red-50 border border-red-200 rounded">
                                            @foreach($row['errors'] as $error)
                                                <div class="text-xs text-red-600">
                                                    <i class="fas fa-exclamation-triangle mr-1"></i>{{ $error }}
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </td>

                            <!-- Campi Misurazione Editabili -->
                            <td class="px-2 py-3">
                                <input type="number"
                                       name="peso_{{ $index }}"
                                       value="{{ $row['peso'] ?? '' }}"
                                       step="0.01"
                                       class="w-full px-2 py-1 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="kg">
                            </td>
                            <td class="px-2 py-3">
                                <input type="number"
                                       name="bmi_{{ $index }}"
                                       value="{{ $row['bmi'] ?? '' }}"
                                       step="0.01"
                                       class="w-full px-2 py-1 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </td>
                            <td class="px-2 py-3">
                                <input type="number"
                                       name="grasso_corporeo_{{ $index }}"
                                       value="{{ $row['grasso_corporeo'] ?? '' }}"
                                       step="0.01"
                                       class="w-full px-2 py-1 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </td>
                            <td class="px-2 py-3">
                                <input type="number"
                                       name="grasso_viscerale_{{ $index }}"
                                       value="{{ $row['grasso_viscerale'] ?? '' }}"
                                       step="0.01"
                                       class="w-full px-2 py-1 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </td>
                            <td class="px-2 py-3">
                                <input type="number"
                                       name="muscolo_{{ $index }}"
                                       value="{{ $row['muscolo'] ?? '' }}"
                                       step="0.01"
                                       class="w-full px-2 py-1 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </td>
                            <td class="px-2 py-3">
                                <input type="number"
                                       name="massa_muscolare_{{ $index }}"
                                       value="{{ $row['massa_muscolare'] ?? '' }}"
                                       step="0.01"
                                       class="w-full px-2 py-1 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </td>
                            <td class="px-2 py-3">
                                <input type="number"
                                       name="muscolo_scheletrico_{{ $index }}"
                                       value="{{ $row['muscolo_scheletrico'] ?? '' }}"
                                       step="0.01"
                                       class="w-full px-2 py-1 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </td>
                            <td class="px-2 py-3">
                                <input type="number"
                                       name="bmr_{{ $index }}"
                                       value="{{ $row['bmr'] ?? '' }}"
                                       step="1"
                                       class="w-full px-2 py-1 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </td>
                            <td class="px-2 py-3">
                                <input type="number"
                                       name="acqua_corporea_{{ $index }}"
                                       value="{{ $row['acqua_corporea'] ?? '' }}"
                                       step="0.01"
                                       class="w-full px-2 py-1 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </td>
                            <td class="px-2 py-3">
                                <input type="number"
                                       name="peso_corporeo_senza_grassi_{{ $index }}"
                                       value="{{ $row['peso_corporeo_senza_grassi'] ?? '' }}"
                                       step="0.01"
                                       class="w-full px-2 py-1 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </td>
                            <td class="px-2 py-3">
                                <input type="number"
                                       name="proteine_{{ $index }}"
                                       value="{{ $row['proteine'] ?? '' }}"
                                       step="0.01"
                                       class="w-full px-2 py-1 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </td>
                            <td class="px-2 py-3">
                                <input type="number"
                                       name="eta_metabolica_{{ $index }}"
                                       value="{{ $row['eta_metabolica'] ?? '' }}"
                                       step="1"
                                       class="w-full px-2 py-1 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </td>
                            <td class="px-2 py-3">
                                <input type="date"
                                       name="data_rilevazione_{{ $index }}"
                                       value="{{ $row['data_rilevazione'] ?? '' }}"
                                       class="w-full px-2 py-1 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </td>

                            <!-- Azioni -->
                            <td class="px-2 py-3 text-center">
                                @if(empty($row['errors']))
                                    <span class="inline-block px-2 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">
                                        <i class="fas fa-check mr-1"></i>Pronto
                                    </span>
                                @else
                                    <span class="inline-block px-2 py-1 bg-red-100 text-red-800 text-xs font-semibold rounded-full">
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

    <!-- Pulsanti Conferma -->
    @if($valide > 0)
            <input type="hidden" name="total_rows" value="{{ count($preview_data) }}">

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
